@extends('layouts.app')

@section('title', 'Edit Quiz - ' . $quiz->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.courses.show', $course->id) }}" class="hover:opacity-80 text-sm">
                    ← Kembali ke Course
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ $quiz->title }}</h1>
            <p class="text-indigo-100">Mengelola soal quiz</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start">
                <i class="fas fa-check-circle text-green-600 mr-3 mt-0.5"></i>
                <div>
                    <p class="text-green-800 font-semibold">Sukses!</p>
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="col-span-2">
                <!-- Quiz Info Card -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Informasi Quiz</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Durasi:</span>
                            <span class="font-medium text-gray-800">{{ $quiz->duration_minutes }} menit</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Skor Kelulusan:</span>
                            <span class="font-medium text-gray-800">{{ $quiz->passing_score }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium">
                                @if($quiz->is_published)
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs">Dipublikasikan</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs">Draft</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200 flex gap-3">
                        <a href="{{ route('instructor.courses.show', $course->id) }}" 
                           class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg text-center font-medium transition">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                        <form action="{{ route('instructor.quiz.destroy', [$course->id, $quiz->id]) }}" method="POST" class="flex-1" onsubmit="return confirm('Hapus quiz ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg font-medium transition">
                                <i class="fas fa-trash mr-2"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Daftar Soal</h3>
                            <p class="text-sm text-gray-600 mt-1">Total {{ $questions->count() }} soal</p>
                        </div>
                        <a href="{{ route('instructor.quiz.question.create', [$course->id, $quiz->id]) }}" 
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i> Tambah Soal
                        </a>
                    </div>

                    @if($questions->count() > 0)
                        <div class="space-y-3">
                            @foreach($questions as $index => $question)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-indigo-300 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500 mb-1">Soal {{ $index + 1 }}</p>
                                            <p class="font-semibold text-gray-800 mb-2">{{ Str::limit($question->question, 100) }}</p>
                                            <div class="flex gap-2 items-center">
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-medium">
                                                    {{ ucfirst($question->question_type) }}
                                                </span>
                                                @if($question->is_required)
                                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Wajib</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('instructor.quiz.question.edit', [$course->id, $quiz->id, $question->id]) }}" 
                                               class="text-indigo-600 hover:text-indigo-700 p-2" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('instructor.quiz.question.delete', [$course->id, $quiz->id, $question->id]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus soal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700 p-2" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-inbox text-gray-300 text-4xl mb-3 block"></i>
                            <p class="text-gray-600 mb-4">Belum ada soal</p>
                            <a href="{{ route('instructor.quiz.question.create', [$course->id, $quiz->id]) }}" 
                               class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">
                                <i class="fas fa-plus mr-2"></i> Tambah Soal Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar - Quick Stats -->
            <div class="col-span-1">
                <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-6 sticky top-20">
                    <h4 class="font-semibold text-gray-800 mb-4">📊 Statistik</h4>
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-gray-600">Total Soal</p>
                            <p class="text-2xl font-bold text-indigo-600">{{ $questions->count() }}</p>
                        </div>
                        <div class="pt-4 border-t border-indigo-200">
                            <p class="text-gray-600 mb-2">Tipe Soal</p>
                            <div class="space-y-1 text-xs">
                                @php
                                    $questionTypes = $questions->groupBy('question_type')->mapWithKeys(function($group, $type) {
                                        return [$type => $group->count()];
                                    });
                                @endphp
                                @forelse($questionTypes as $type => $count)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ ucfirst($type) }}</span>
                                        <span class="font-semibold text-gray-800">{{ $count }}</span>
                                    </div>
                                @empty
                                    <p class="text-gray-500">-</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
