@extends('layouts.app')

@section('title', $registration->course->title . ' - My Course')

@section('content')
<div class="bg-gray-50 min-h-screen pt-20">
    <!-- Course Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-12">
        <div class="max-w-6xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('student.courses') }}" class="hover:opacity-80 text-white font-medium">
                    Back to My Courses
                </a>
            </div>
            <h1 class="text-4xl font-bold mb-3">{{ $registration->course->title }}</h1>
            <p class="text-lg opacity-90">{{ $registration->course->description }}</p>
            <div class="mt-6 flex items-center gap-6">
                <div>
                    <p class="text-sm opacity-75">Progress</p>
                    <div class="w-64 bg-white bg-opacity-20 rounded-full h-3 mt-2">
                        <div class="bg-white h-3 rounded-full" style="width: {{ $registration->progress ?? 0 }}%"></div>
                    </div>
                    <p class="text-sm mt-1">{{ $registration->progress ?? 0 }}% Complete</p>
                </div>
                <div>
                    <p class="text-sm opacity-75">Enrolled</p>
                    <p class="font-semibold">{{ $registration->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="max-w-6xl mx-auto px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- About Course -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">About This Course</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $registration->course->description }}</p>
                </div>

                <!-- Course Materials -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Course Materials</h2>
                    @if($registration->course->publishedMaterials->count() > 0)
                        <div class="space-y-3">
                            @foreach($registration->course->publishedMaterials as $material)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-400 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-3">
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $material->title }}</h4>
                                            @if($material->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $material->description }}</p>
                                            @endif
                                            <p class="text-xs text-gray-500 mt-2">{{ number_format($material->file_size / 1024, 0) }} KB</p>
                                        </div>
                                    </div>
                                    <a href="/storage/{{ $material->file_path }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Download
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No materials available yet</p>
                    @endif
                </div>

                <!-- Assignments -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Assignments</h2>
                    @if($registration->course->publishedAssignments->count() > 0)
                        <div class="space-y-4">
                            @foreach($registration->course->publishedAssignments as $assignment)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $assignment->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">Due: <strong>{{ $assignment->due_date->format('M d, Y H:i') }}</strong></p>
                                    </div>
                                    @php
                                        $submission = $assignment->submissions()->where('user_id', auth()->id())->first();
                                    @endphp
                                    @if($submission)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">
                                            Submitted
                                        </span>
                                    @else
                                        <span class="bg-orange-100 text-orange-800 text-xs font-medium px-3 py-1 rounded-full">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                                @if($assignment->description)
                                <p class="text-gray-700 text-sm mb-3">{{ $assignment->description }}</p>
                                @endif
                                <div class="flex gap-2">
                                    @if(!$submission)
                                        <a href="{{ route('student.assignment.submit.form', $assignment->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            Submit Assignment
                                        </a>
                                    @else
                                        <a href="{{ route('student.assignment.view', $assignment->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            View Submission
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No assignments yet</p>
                    @endif
                </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- Instructor Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Instructor</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($registration->course->instructor->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $registration->course->instructor->name }}</p>
                            <p class="text-sm text-gray-600">{{ $registration->course->instructor->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Course Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Course Info</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Type</p>
                            <p class="font-semibold text-gray-800">{{ $registration->course->display_type }}</p>
                        </div>
                        @if($registration->course->duration_days)
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Duration</p>
                            <p class="font-semibold text-gray-800">{{ $registration->course->duration_days }} days</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Students Enrolled</p>
                            <p class="font-semibold text-gray-800">{{ $registration->course->registrations()->where('status', 'paid')->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="bg-white rounded-lg shadow-md p-6 space-y-2">
                    <a href="{{ route('student.quiz.index', $registration->course->id) }}" 
                       class="flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium p-2 rounded hover:bg-blue-50 transition">
                        Take a Quiz
                    </a>
                    <a href="{{ route('student.forum.index', $registration->course->id) }}" 
                       class="flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium p-2 rounded hover:bg-blue-50 transition">
                        Discussion Forum
                    </a>
                    @if($registration->status === 'paid' && !$refund)
                    <a href="{{ route('refund.create', $registration->id) }}" 
                       class="flex items-center gap-2 text-red-600 hover:text-red-800 font-medium p-2 rounded hover:bg-red-50 transition">
                        Request Refund
                    </a>
                    @endif
                </div>

                <!-- Refund Status -->
                @if($refund)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm"><strong>Refund Status:</strong> {{ ucfirst($refund->status) }}</p>
                    @if($refund->status === 'rejected')
                        <p class="text-xs text-red-600 mt-2">{{ $refund->rejection_reason ?? 'No reason provided' }}</p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
