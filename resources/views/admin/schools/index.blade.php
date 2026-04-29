@extends('layouts.app-dashboard')

@section('dashboard-content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Gestione Scuole</h1>
            <p class="text-gray-600">Amministra le scuole della piattaforma</p>
        </div>
        <a href="{{ route('admin.schools.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition">
            + Nuova Scuola
        </a>
    </div>

    <!-- Alerts -->
    @if ($message = session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            {{ $message }}
        </div>
    @endif

    @if ($message = session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            {{ $message }}
        </div>
    @endif

    <!-- Schools Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Città</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Telefono</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($schools as $school)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $school->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $school->city ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $school->email ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $school->phone ?? '—' }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.schools.edit', $school) }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm">Modifica</a>
                                <form method="POST" action="{{ route('admin.schools.delete', $school) }}" class="inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa scuola?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm">Elimina</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <p class="text-lg">Nessuna scuola trovata</p>
                                <p class="text-sm mt-1">
                                    <a href="{{ route('admin.schools.create') }}" class="text-blue-600 hover:text-blue-900 font-medium">Crea la prima scuola</a>
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if ($schools->hasPages())
        <div class="mt-6">
            {{ $schools->links() }}
        </div>
    @endif
@endsection
