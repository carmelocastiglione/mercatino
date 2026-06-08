@extends('layouts.app-student')

@section('title', 'Notifiche')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Notifiche</h1>
        <p class="text-gray-600 mt-2">Leggi tutti gli aggiornamenti sul tuo account</p>
    </div>

    <!-- Action Buttons -->
    @if ($unreadCount > 0)
        <div class="mb-6 flex gap-3">
            <form action="{{ route('student.notifications.mark-all-read') }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition">
                    Segna tutto come letto
                </button>
            </form>
        </div>
    @endif

    @if ($notifications->count() > 0)
        <div class="space-y-3">
            @foreach ($notifications as $notification)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition {{ $notification->is_read ? 'opacity-75' : 'bg-blue-50 border-blue-300' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-900">{{ $notification->title }}</h3>
                                @if (!$notification->is_read)
                                    <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                @endif
                            </div>
                            <p class="text-gray-600 text-sm mt-1">{{ $notification->description }}</p>
                            <p class="text-gray-500 text-xs mt-2">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Action buttons -->
                        <div class="flex gap-2 ml-4">
                            @if (!$notification->is_read)
                                <form action="{{ route('student.notifications.mark-as-read', $notification) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded transition">
                                        Leggi
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('student.notifications.delete', $notification) }}" method="POST" onsubmit="return confirm('Eliminare questa notifica?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 text-xs font-medium text-red-600 hover:bg-red-50 rounded transition">
                                    Elimina
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $notifications->links() }}
        </div>

        <!-- Delete all read notifications -->
        @if ($notifications->where('read_at', '!=', null)->count() > 0)
            <div class="mt-8 text-center">
                <form action="{{ route('student.notifications.delete-all-read') }}" method="POST" onsubmit="return confirm('Eliminare tutte le notifiche lette?');">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                        Elimina tutte le notifiche lette
                    </button>
                </form>
            </div>
        @endif
    @else
        <div class="bg-gray-50 border-2 border-gray-300 rounded-lg p-12 text-center">
            <p class="text-gray-600 font-medium">Non hai notifiche</p>
            <p class="text-sm text-gray-500 mt-1">Tornerai qui quando avrà degli aggiornamenti</p>
        </div>
    @endif
@endsection
