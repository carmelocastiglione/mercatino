@extends('layouts.app-staff')

@section('title', 'Esporta Dati')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Esporta Dati</h1>
        <p class="text-gray-600 mt-2">Scarica i dati della tua scuola in formato CSV</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($exportTypes as $type)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $type['label'] }}</h3>
                        <p class="text-gray-600 text-sm mt-2">Scarica tutti i {{ strtolower($type['label']) }} in CSV</p>
                    </div>
                    <svg class="w-12 h-12 text-blue-600 opacity-30" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                </div>
                
                <a href="{{ route('staff.export.download', $type['key']) }}" 
                   class="mt-6 block w-full px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-center">
                    ⬇️ Scarica CSV
                </a>
            </div>
        @endforeach
    </div>
@endsection
