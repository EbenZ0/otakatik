@extends('layouts.app')

@section('title', 'Submission Detail - ' . $quiz->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.quiz.submissions', [$course->id, $quiz->id]) }}" class="hover:opacity-80 text-sm">
                    ← Kembali ke Submissions
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">Detail Submission</h1>
            <p class="text-indigo-100">{{ $submission->user->name ?? 'Unknown' }} - {{ $quiz->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        <!-- Student Info & Score -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Student Info -->
            <div class="md:col-span-2 bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Informasi Siswa</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        @if($submission->user && $submission->user->profile_picture && Storage::disk('public')->exists($submission->user->profile_picture))
                            <img src="{{ Storage::disk('public')->url($submission->user->profile_picture) }}" alt="{{ $submission->user->name }}" class="w-16 h-16 rounded-full">
                        @else
                            <div class="w-16 h-16 rounded-full bg-indigo-500 text-white flex items-center justify-center text-2xl font-bold">
                                {{ substr($submission->user->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Nama Siswa</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $submission->user->name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="text-gray-800">{{ $submission->user->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status Submission</p>
                        <p class="mt-2">
                            @if($submission->status === 'completed')
                                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i> Selesai
                                </span>
                            @elseif($submission->status === 'submitted')
                                <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    <i class="fas fa-paper-plane mr-1"></i> Submitted
                                </span>
                            @else
                                <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Score Card -->
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg border border-indigo-200 p-6">
                <h3 class="text-lg font-semibold text-indigo-700 mb-6">Nilai</h3>
                <div class="text-center">
                    @if($submission->score !== null)
                        <div class="text-5xl font-bold text-indigo-600 mb-2">{{ $submission->score }}</div>
                        <p class="text-indigo-700 font-medium mb-4">/ 100</p>
                        @if($submission->score >= $quiz->passing_score)
                            <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full font-semibold">
                                <i class="fas fa-check-circle mr-2"></i>LULUS
                            </div>
                        @else
                            <div class="inline-block bg-red-100 text-red-800 px-4 py-2 rounded-full font-semibold">
                                <i class="fas fa-times-circle mr-2"></i>TIDAK LULUS
                            </div>
                        @endif
                    @else
                        <p class="text-indigo-600 text-lg">-</p>
                        <p class="text-indigo-600 text-sm mt-2">Belum dinilai</p>
                    @endif
                </div>

                <!-- Time Info -->
                <div class="mt-6 pt-6 border-t border-indigo-200 space-y-3 text-sm">
                    @if($submission->submitted_at)
                        <div>
                            <p class="text-indigo-600">Waktu Submit</p>
                            <p class="text-indigo-800 font-medium">{{ $submission->submitted_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                    @if($submission->time_spent)
                        <div>
                            <p class="text-indigo-600">Waktu Pengerjaan</p>
                            <p class="text-indigo-800 font-medium">{{ $submission->time_spent }} menit</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Questions & Answers -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">Pertanyaan & Jawaban</h3>
            <div class="space-y-6">
                @if($submission->answers && count($submission->answers) > 0)
                    @foreach($submission->answers as $index => $answer)
                        @php
                            $question = $quiz->questions()->find($answer['question_id'] ?? null);
                        @endphp
                        @if($question)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <!-- Question -->
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-1">Soal {{ $index + 1 }}</p>
                                    <p class="text-lg font-semibold text-gray-800">{{ $question->question }}</p>
                                </div>

                                <!-- Student Answer -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <p class="text-sm text-blue-700 font-semibold mb-2">Jawaban Siswa:</p>
                                    @if($question->question_type === 'multiple_choice')
                                        @php
                                            $selectedIndex = $answer['selected_answer'] ?? null;
                                            $selectedOption = isset($question->options[$selectedIndex]) ? $question->options[$selectedIndex] : 'Tidak dijawab';
                                        @endphp
                                        <p class="text-blue-900">
                                            {{ $selectedIndex !== null ? chr(65 + $selectedIndex) . ': ' : '' }}{{ $selectedOption }}
                                        </p>
                                    @elseif($question->question_type === 'true_false')
                                        @php
                                            $answer_text = $answer['selected_answer'] === 'true' ? 'Benar' : ($answer['selected_answer'] === 'false' ? 'Salah' : 'Tidak dijawab');
                                        @endphp
                                        <p class="text-blue-900">{{ $answer_text }}</p>
                                    @else
                                        <p class="text-blue-900">{{ $answer['selected_answer'] ?? 'Tidak dijawab' }}</p>
                                    @endif
                                </div>

                                <!-- Correct Answer & Points -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <p class="text-sm text-green-700 font-semibold mb-2">Jawaban Benar:</p>
                                        @if($question->question_type === 'multiple_choice')
                                            @php
                                                $correctOption = $question->options[$question->correct_answer] ?? 'Unknown';
                                            @endphp
                                            <p class="text-green-900">
                                                {{ chr(65 + $question->correct_answer) }}: {{ $correctOption }}
                                            </p>
                                        @elseif($question->question_type === 'true_false')
                                            @php
                                                $correct_text = $question->correct_answer === 'true' ? 'Benar' : 'Salah';
                                            @endphp
                                            <p class="text-green-900">{{ $correct_text }}</p>
                                        @else
                                            <p class="text-green-900 text-sm">Lihat di bawah</p>
                                        @endif
                                    </div>
                                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                        <p class="text-sm text-indigo-700 font-semibold mb-2">Nilai:</p>
                                        @php
                                            $isCorrect = isset($answer['is_correct']) ? $answer['is_correct'] : false;
                                            $points = isset($answer['points']) ? $answer['points'] : 0;
                                        @endphp
                                        <p class="text-indigo-900">
                                            @if($isCorrect)
                                                <span class="text-green-600 font-bold">✓ {{ $points }} poin</span>
                                            @else
                                                <span class="text-red-600 font-bold">✗ 0 poin</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 block text-gray-300"></i>
                        <p>Tidak ada data jawaban</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
