@extends('layouts.app-staff')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('staff.reclaims.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">← Torna ai resi</a>
        <h1 class="text-4xl font-bold text-gray-900 mt-4">{{ $title }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Dettagli del libro -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informazioni Libro</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Titolo</label>
                        <p class="text-gray-900 text-lg mt-1">{{ $reclaim->bookListing->book->title }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Autore</label>
                        <p class="text-gray-900 mt-1">{{ $reclaim->bookListing->book->author }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Condizione</label>
                        <div class="mt-1">
                            @php
                                $conditionColors = [
                                    'like-new' => 'bg-green-100 text-green-800',
                                    'good' => 'bg-blue-100 text-blue-800',
                                    'fair' => 'bg-yellow-100 text-yellow-800',
                                    'poor' => 'bg-red-100 text-red-800'
                                ];
                            @endphp
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded {{ $conditionColors[$reclaim->bookListing->condition] ?? 'bg-gray-100' }}">
                                {{ str_replace('-', ' ', $reclaim->bookListing->condition) }}
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prezzo</label>
                        <p class="text-gray-900 text-lg mt-1">€{{ number_format($reclaim->bookListing->price, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Dettagli del reso -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Dettagli Reso</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data Reso</label>
                        <p class="text-gray-900 mt-1">{{ $reclaim->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Note Venditore</label>
                        <p class="text-gray-700 mt-1 bg-gray-50 p-3 rounded">{{ $reclaim->notes ?? 'Nessuna nota' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dettagli Venditore e Azioni -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Venditore</h2>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Nome</p>
                        <p class="text-gray-900">{{ $reclaim->user->name }} {{ $reclaim->user->surname }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-700">Email</p>
                        <p class="text-gray-900">{{ $reclaim->user->email }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-700">Codice</p>
                        <p class="text-gray-900">{{ $reclaim->user->code }}</p>
                    </div>
                </div>
            </div>

            <!-- Azioni -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Reso</h2>
                
                <div>
                    @if ($reclaim->status === 'approved')
                        <span class="inline-block px-3 py-2 bg-green-100 text-green-800 text-sm font-semibold rounded">
                            ✓ Approvato
                        </span>
                    @elseif ($reclaim->status === 'rejected')
                        <span class="inline-block px-3 py-2 bg-red-100 text-red-800 text-sm font-semibold rounded">
                            ✕ Rifiutato
                        </span>
                    @else
                        <span class="inline-block px-3 py-2 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded">
                            ⏳ In sospeso
                        </span>
                    @endif
                </div>

                @if ($reclaim->status === 'rejected' && $reclaim->rejection_reason)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Motivo Rifiuto</label>
                        <p class="text-gray-700 bg-gray-50 p-3 rounded">{{ $reclaim->rejection_reason }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
