<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizSubmission;
use App\Models\Course;
use App\Services\QuizGradingService;
use App\Events\QuizPosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    protected $gradingService;

    public function __construct(QuizGradingService $gradingService)
    {
        $this->gradingService = $gradingService;
    }

    // ==================== INSTRUCTOR ROUTES ====================

    /**
     * Show all quizzes for a course (Instructor)
     */
    public function index($courseId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        
        // Check if instructor owns this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $quizzes = $course->quizzes()->latest()->get();

        return view('instructor.quiz.index', compact('course', 'quizzes'));
    }

    /**
     * Show create quiz form (Instructor)
     */
    public function create($courseId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.quiz.create', compact('course'));
    }

    /**
     * Store new quiz (Instructor)
     */
    public function store(Request $request, $courseId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'passing_score' => 'required|integer|min:0|max:100',
            'available_from' => 'nullable|date|after_or_equal:today',
            'available_until' => 'nullable|date|after:available_from',
            'is_published' => 'boolean'
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration_minutes' => $validated['duration_minutes'],
            'passing_score' => $validated['passing_score'],
            'available_from' => $validated['available_from'],
            'available_until' => $validated['available_until'],
            'is_published' => $validated['is_published'] ?? false
        ]);

        // Dispatch event to notify students if quiz is published
        if ($quiz->is_published) {
            QuizPosted::dispatch($quiz);
        }

        return redirect()->route('instructor.quiz.edit', [$courseId, $quiz->id])
            ->with('success', 'Quiz berhasil dibuat! Sekarang tambahkan soal.');
    }

    /**
     * Show quiz editor (Instructor)
     */
    public function edit($courseId, $quizId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        $questions = $quiz->questions()->orderBy('order')->get();

        return view('instructor.quiz.edit', compact('course', 'quiz', 'questions'));
    }

    /**
     * Update quiz (Instructor)
     */
    public function update(Request $request, $courseId, $quizId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'passing_score' => 'required|integer|min:0|max:100',
            'available_from' => 'nullable|date|after_or_equal:today',
            'available_until' => 'nullable|date|after:available_from',
            'is_published' => 'boolean'
        ]);

        $quiz->update($validated);

        return back()->with('success', 'Quiz berhasil diupdate!');
    }

    /**
     * Delete quiz (Instructor)
     */
    public function destroy($courseId, $quizId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        $quiz->delete();

        return redirect()->route('instructor.quiz.index', $courseId)
            ->with('success', 'Quiz berhasil dihapus!');
    }

    /**
     * Add question to quiz (Instructor)
     */
    public function addQuestion(Request $request, $courseId, $quizId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'question' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'options' => 'required_if:question_type,multiple_choice|json',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1|max:100'
        ]);

        $orderNumber = $quiz->questions()->count() + 1;

        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => $validated['question'],
            'question_type' => $validated['question_type'],
            'options' => $validated['question_type'] === 'multiple_choice' 
                ? json_decode($validated['options'], true) 
                : null,
            'correct_answer' => $validated['correct_answer'],
            'points' => $validated['points'],
            'order' => $orderNumber
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil ditambahkan!',
            'question' => $question
        ]);
    }

    /**
     * Update question (Instructor)
     */
    public function updateQuestion(Request $request, $courseId, $quizId, $questionId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $question = QuizQuestion::findOrFail($questionId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id || $question->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'question' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'options' => 'required_if:question_type,multiple_choice|json',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1|max:100'
        ]);

        $question->update([
            'question' => $validated['question'],
            'question_type' => $validated['question_type'],
            'options' => $validated['question_type'] === 'multiple_choice' 
                ? json_decode($validated['options'], true) 
                : null,
            'correct_answer' => $validated['correct_answer'],
            'points' => $validated['points']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil diupdate!',
            'question' => $question
        ]);
    }

    /**
     * Delete question (Instructor)
     */
    public function deleteQuestion($courseId, $quizId, $questionId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $question = QuizQuestion::findOrFail($questionId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id || $question->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized action.');
        }

        $question->delete();

        return response()->json(['success' => true, 'message' => 'Soal berhasil dihapus!']);
    }

    /**
     * View quiz submissions (Instructor)
     */
    public function submissions($courseId, $quizId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        $submissions = $quiz->submissions()->with('user')->latest()->paginate(15);
        $stats = [
            'total_submissions' => $quiz->submissions()->count(),
            'average_score' => $this->gradingService->getAverageScore($quiz),
            'pass_rate' => $this->gradingService->getPassRate($quiz),
        ];

        return view('instructor.quiz.submissions', compact('course', 'quiz', 'submissions', 'stats'));
    }

    /**
     * View specific submission detail (Instructor)
     */
    public function submissionDetail($courseId, $quizId, $submissionId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $submission = QuizSubmission::findOrFail($submissionId);

        if ($course->instructor_id !== Auth::id() || $quiz->course_id !== $course->id || $submission->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized action.');
        }

        $submission->load('user', 'quiz.questions');

        return view('instructor.quiz.submission-detail', compact('course', 'quiz', 'submission'));
    }

    // ==================== STUDENT ROUTES ====================

    /**
     * Show available quizzes for student in a course
     */
    public function studentQuizzes($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check if student is enrolled
        $registration = Auth::user()->courseRegistrations()
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        $quizzes = $course->quizzes()
            ->where('is_published', true)
            ->with('submissions')
            ->get()
            ->map(function ($quiz) {
                $quiz->user_submission = $quiz->submissions()
                    ->where('user_id', Auth::id())
                    ->first();
                return $quiz;
            });

        return view('student.quiz.index', compact('course', 'quizzes', 'registration'));
    }

    /**
     * Start quiz (Student)
     */
    public function start($courseId, $quizId)
    {
        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        // Check if student is enrolled
        $registration = Auth::user()->courseRegistrations()
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        // Check if quiz is published and available
        if (!$quiz->is_published) {
            abort(403, 'Quiz tidak tersedia.');
        }

        if ($quiz->available_from && $quiz->available_from > now()) {
            abort(403, 'Quiz belum bisa diakses.');
        }

        if ($quiz->available_until && $quiz->available_until < now()) {
            abort(403, 'Quiz sudah ditutup.');
        }

        // Check if student already has ongoing submission
        $ongoingSubmission = $quiz->submissions()
            ->where('user_id', Auth::id())
            ->whereNull('submitted_at')
            ->first();

        if ($ongoingSubmission) {
            return redirect()->route('student.quiz.continue', [$courseId, $quizId, $ongoingSubmission->id]);
        }

        // Create new submission
        $submission = $quiz->submissions()->create([
            'user_id' => Auth::id(),
            'started_at' => now(),
            'answers' => []
        ]);

        return redirect()->route('student.quiz.continue', [$courseId, $quizId, $submission->id]);
    }

    /**
     * Continue/Take quiz (Student)
     */
    public function continue($courseId, $quizId, $submissionId)
    {
        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $submission = QuizSubmission::findOrFail($submissionId);

        // Check if student owns this submission
        if ($submission->user_id !== Auth::id() || $submission->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized access.');
        }

        // Check if already submitted
        if ($submission->submitted_at) {
            return redirect()->route('student.quiz.result', [$courseId, $quizId, $submissionId]);
        }

        $questions = $quiz->questions()->orderBy('order')->get();
        $timeRemaining = $this->getTimeRemaining($submission, $quiz);

        return view('student.quiz.take', compact('course', 'quiz', 'submission', 'questions', 'timeRemaining'));
    }

    /**
     * Submit quiz (Student)
     */
    public function submit(Request $request, $courseId, $quizId, $submissionId)
    {
        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $submission = QuizSubmission::findOrFail($submissionId);

        // Check if student owns this submission
        if ($submission->user_id !== Auth::id() || $submission->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized access.');
        }

        // Check if already submitted
        if ($submission->submitted_at) {
            return response()->json(['success' => false, 'message' => 'Quiz sudah disubmit.']);
        }

        // Validate answers
        $answers = $request->validate([
            'answers' => 'required|array'
        ])['answers'];

        DB::beginTransaction();
        try {
            // Grade submission
            $result = $this->gradingService->gradeSubmission($submission, $answers);

            $submission->update([
                'submitted_at' => now(),
                'answers' => $answers
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz berhasil disubmit!',
                'result' => $result
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * View quiz result (Student)
     */
    public function result($courseId, $quizId, $submissionId)
    {
        $course = Course::findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);
        $submission = QuizSubmission::findOrFail($submissionId);

        // Check if student owns this submission
        if ($submission->user_id !== Auth::id() || $submission->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized access.');
        }

        // Check if submitted
        if (!$submission->submitted_at) {
            abort(403, 'Quiz belum disubmit.');
        }

        $submission->load('quiz.questions');

        return view('student.quiz.result', compact('course', 'quiz', 'submission'));
    }

    // ==================== HELPER METHODS ====================

    /**
     * Calculate time remaining for quiz
     */
    private function getTimeRemaining(QuizSubmission $submission, Quiz $quiz): int
    {
        $startTime = $submission->started_at;
        $durationMinutes = $quiz->duration_minutes;
        $endTime = $startTime->addMinutes($durationMinutes);

        $remainingMinutes = now()->diffInMinutes($endTime);
        
        return max($remainingMinutes * 60, 0); // Return in seconds
    }
}
