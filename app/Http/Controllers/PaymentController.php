<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Payment;
use App\Models\CourseRegistration;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Show checkout page
     */
    public function checkout($courseId)
    {
        $course = Course::where('is_active', true)->findOrFail($courseId);
        $user = Auth::user();

        // Check if user already enrolled
        $existingRegistration = CourseRegistration::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->first();

        if ($existingRegistration) {
            return redirect()->route('course.show.detail', $courseId)
                ->with('error', 'You are already enrolled in this course!');
        }

        // Calculate final price
        $finalPrice = $course->price;
        $discountAmount = 0;

        return view('checkout', compact('course', 'user', 'finalPrice', 'discountAmount'));
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, $courseId)
    {
        $request->validate([
            'payment_method' => 'required|in:bank_transfer,credit_card,gopay,shopeepay',
            'voucher_code' => 'nullable|string|max:50'
        ]);

        $course = Course::where('is_active', true)->findOrFail($courseId);
        $user = Auth::user();

        DB::beginTransaction();
        try {
            // Calculate final price with voucher
            $finalPrice = $this->calculateFinalPrice($course, $request->voucher_code);
            
            // Create order ID
            $orderId = Payment::generateOrderId();

            // Create registration record
            $registration = CourseRegistration::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'order_id' => $orderId,
                'nama_lengkap' => $user->name,
                'ttl' => 'Auto-generated',
                'tempat_tinggal' => 'Auto-generated', 
                'gender' => 'Laki-laki',
                'price' => $course->price,
                'final_price' => $finalPrice,
                'discount_code' => $request->voucher_code,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'progress' => 0,
            ]);

            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'order_id' => $orderId,
                'gross_amount' => $finalPrice,
                'payment_type' => $request->payment_method,
                'transaction_status' => 'pending',
                'status_message' => 'Waiting for payment'
            ]);

            // Prepare Midtrans transaction
            $transactionDetails = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $finalPrice,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
                'item_details' => [
                    [
                        'id' => $course->id,
                        'price' => (int) $finalPrice,
                        'quantity' => 1,
                        'name' => $course->title,
                    ]
                ]
            ];

            // Get Snap token from Midtrans
            $midtransResponse = $this->midtransService->createTransaction($transactionDetails);

            if (!$midtransResponse['success']) {
                throw new \Exception('Payment gateway error: ' . $midtransResponse['message']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $midtransResponse['snap_token'],
                'order_id' => $orderId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment notification from Midtrans (webhook)
     */
    public function handleNotification(Request $request)
    {
        $notification = $request->all();
        
        try {
            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? '';

            // Find payment record
            $payment = Payment::where('order_id', $orderId)->firstOrFail();
            $registration = CourseRegistration::where('order_id', $orderId)->firstOrFail();

            // Update payment status
            $payment->update([
                'transaction_status' => $transactionStatus,
                'transaction_id' => $notification['transaction_id'] ?? null,
                'transaction_time' => $notification['transaction_time'] ?? null,
                'settlement_time' => $notification['settlement_time'] ?? null,
                'status_code' => $notification['status_code'] ?? null,
                'status_message' => $notification['status_message'] ?? null,
                'payment_data' => $notification
            ]);

            // Handle successful payment
            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $this->handleSuccessfulPayment($payment, $registration);
                }
            } 
            // Handle failed payment
            elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $this->handleFailedPayment($payment, $registration);
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            \Log::error('Payment notification error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Simulate payment success (for development)
     */
    public function simulateSuccess($orderId)
    {
        try {
            $payment = Payment::where('order_id', $orderId)->firstOrFail();
            $registration = CourseRegistration::where('order_id', $orderId)->firstOrFail();

            $this->handleSuccessfulPayment($payment, $registration);

            return redirect()->route('purchase.history')
                ->with('success', 'Payment successful! You are now enrolled in the course.');

        } catch (\Exception $e) {
            return redirect()->route('purchase.history')
                ->with('error', 'Payment simulation failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessfulPayment(Payment $payment, CourseRegistration $registration)
    {
        DB::transaction(function () use ($payment, $registration) {
            // Update registration status
            $registration->update([
                'status' => 'paid',
                'paid_at' => now(),
                'enrolled_at' => now()
            ]);

            // Update payment
            $payment->update([
                'transaction_status' => 'settlement',
                'settlement_time' => now()
            ]);

            // Update course enrollment count
            $registration->course->increment('current_enrollment');
        });
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment(Payment $payment, CourseRegistration $registration)
    {
        $registration->update(['status' => 'cancelled']);
        // You might want to send notification to user here
    }

    /**
     * Calculate final price with voucher
     */
    private function calculateFinalPrice(Course $course, $voucherCode = null)
    {
        $finalPrice = $course->price;

        if ($voucherCode) {
            // Apply voucher logic here
            if ($voucherCode === 'DISKON10') {
                $finalPrice = $course->price * 0.9; // 10% discount
            } elseif ($voucherCode === 'DISKON20') {
                $finalPrice = $course->price * 0.8; // 20% discount
            }
            // Add more voucher codes as needed
        }

        return $finalPrice;
    }

    /**
     * Check voucher validity
     */
    public function checkVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
            'course_id' => 'required|exists:courses,id'
        ]);

        $course = Course::find($request->course_id);
        $finalPrice = $this->calculateFinalPrice($course, $request->voucher_code);
        $discountAmount = $course->price - $finalPrice;

        if ($finalPrice < $course->price) {
            return response()->json([
                'valid' => true,
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice,
                'message' => 'Voucher applied successfully!'
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Invalid voucher code'
        ], 400);
    }
}