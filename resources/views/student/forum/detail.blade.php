@extends('layouts.app')

@section('title', 'Topik Forum - ' . $thread->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('student.forum.index', $thread->forum->course->registrations()->where('user_id', auth()->id())->first()->id ?? '#') }}" class="hover:opacity-80">
                    ← Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ $thread->title }}</h1>
            <p class="text-green-100">by {{ $thread->user->name }} • {{ $thread->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
        <!-- Original Post -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
            <div class="flex items-start gap-4 mb-4">
                @if($thread->user->profile_picture)
                    <img src="{{ Storage::url($thread->user->profile_picture) }}" alt="{{ $thread->user->name }}"
                         class="w-12 h-12 rounded-full object-cover">
                @else
                    <div class="w-12 h-12 rounded-full bg-green-600 flex items-center justify-center text-white font-bold">
                        {{ substr($thread->user->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">{{ $thread->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $thread->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="text-gray-700 whitespace-pre-wrap mb-4">
                {{ $thread->content }}
            </div>

            @if(auth()->id() === $thread->user_id)
            <div class="flex gap-2 pt-4 border-t border-gray-200">
                <a href="{{ route('student.forum.edit', $thread->id) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition">
                    Edit
                </a>
                <form action="{{ route('student.forum.delete', $thread->id) }}" method="POST" 
                      class="inline" onsubmit="return confirm('Hapus topik ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition">
                        Hapus
                    </button>
                </form>
            </div>
            @endif
        </div>

        <!-- Replies Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                {{ $thread->replies()->count() }} Balasan
            </h3>

            <div class="space-y-4">
                @forelse($thread->replies()->latest()->get() as $reply)
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                    <div class="flex items-start gap-4 mb-3">
                        @if($reply->user->profile_picture)
                            <img src="{{ Storage::url($reply->user->profile_picture) }}" alt="{{ $reply->user->name }}"
                                 class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                                {{ substr($reply->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $reply->user->name }}</p>
                            <p class="text-xs text-gray-600">{{ $reply->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <p class="text-gray-700 whitespace-pre-wrap">{{ $reply->content }}</p>

                    @if(auth()->id() === $reply->user_id)
                    <div class="flex gap-2 mt-3 pt-3 border-t border-gray-300">
                        <button type="button" onclick="editReply({{ $reply->id }})" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition">
                            Edit
                        </button>
                        <form action="{{ route('student.forum.delete-reply', $reply->id) }}" method="POST" 
                              class="inline" onsubmit="return confirm('Hapus balasan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-gray-600 text-center py-8">Belum ada balasan. Jadilah yang pertama!</p>
                @endforelse
            </div>
        </div>

        <!-- Reply Form -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">✍️ Berikan Balasan</h3>

            <form action="{{ route('student.forum.reply', $thread->id) }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <p class="text-red-800 text-sm">{{ $errors->first() }}</p>
                    </div>
                @endif

                <textarea name="content" required rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          placeholder="Tulis balasan Anda..."></textarea>

                <div class="flex gap-4 mt-4">
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        💬 Kirim Balasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
