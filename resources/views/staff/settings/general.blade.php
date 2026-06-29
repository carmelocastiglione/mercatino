@extends('layouts.app-staff')

@section('title', 'Impostazioni Generali')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Impostazioni Generali</h1>
        <p class="text-gray-600 mt-2">Gestisci le impostazioni generali della scuola {{ $school->name }}</p>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-4 gap-6">
            <x-settings-sidebar />

            <!-- Content Area -->
            <div class="col-span-3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <form method="POST" action="{{ route('staff.settings.save') }}" class="space-y-8 divide-y divide-gray-200">
                        @csrf
                        
                        <!-- Enable Online Sales -->
                        <div class="pt-6 first:pt-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Vendite Online</h3>
                                    <p class="text-sm text-gray-600 mt-1">Abilita o disabilita le vendite online per la tua scuola</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="enable_online_sales" value="false">
                                    <input type="checkbox" id="enable_online_sales" name="enable_online_sales" value="true" class="sr-only peer" @if($school->hasFeatureEnabled('enable_online_sales')) checked @endif>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>

                        <!-- Referring Name -->
                        <div class="pt-6">
                            <div class="flex items-center gap-4 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 whitespace-nowrap">Nome Ente Responsabile</h3>
                                <input type="text" name="referring_name" placeholder="Es: COMITATO GENITORI" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ $school->getSetting('referring_name', '') }}">
                            </div>
                            <p class="text-sm text-gray-600">Nome che appare nei documenti e ricevute</p>
                        </div>

                        <!-- School Online Dates -->
                        <div class="pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Date Prenotazioni Online</h3>
                            <p class="text-sm text-gray-600 mb-6">Configura le date di apertura per le prenotazioni online</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Apertura Negozio Online -->
                                <div>
                                    <label for="online_opening_date" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Apertura Negozio Online
                                    </label>
                                    <input 
                                        type="datetime-local" 
                                        id="online_opening_date" 
                                        name="online_opening_date" 
                                        value="{{ $school->getSetting('online_opening_date', '') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50"
                                        @if(!$school->hasFeatureEnabled('enable_online_sales')) disabled @endif
                                    />
                                    <p class="text-sm text-gray-600 mt-2">Data e ora di apertura del negozio online per l'acquisto dei libri</p>
                                </div>

                                <!-- Apertura Prenotazione Libri da Acquistare -->
                                <div>
                                    <label for="online_booking_date" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Apertura Prenotazione Libri da Acquistare
                                    </label>
                                    <input 
                                        type="datetime-local" 
                                        id="online_booking_date" 
                                        name="online_booking_date" 
                                        value="{{ $school->getSetting('online_booking_date', '') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50"
                                        @if(!$school->hasFeatureEnabled('enable_online_sales')) disabled @endif
                                    />
                                    <p class="text-sm text-gray-600 mt-2">Data e ora di apertura delle prenotazioni per i libri da acquistare online</p>
                                </div>
                            </div>

                            <!-- Info Box -->
                            <div class="bg-blue-50 border border-blue-300 rounded-lg p-4 mt-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 7a1 1 0 000 2h6a1 1 0 000-2H8zm0 4a1 1 0 000 2h6a1 1 0 000-2H8z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-800">
                                            <span class="font-medium">💡 Suggerimento:</span> Lasciate vuote le date per consentire le prenotazioni online in qualsiasi momento. Se impostate, le prenotazioni saranno disponibili solo dopo le date indicate.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="pt-6">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                Salva Impostazioni
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <script>
        // Toggle online dates fields based on enable_online_sales checkbox
        const enableOnlineSalesCheckbox = document.getElementById('enable_online_sales');
        const acquisitionDateInput = document.getElementById('online_opening_date');
        const withdrawalDateInput = document.getElementById('online_booking_date');

        function updateDateFieldsState() {
            const isEnabled = enableOnlineSalesCheckbox.checked;
            acquisitionDateInput.disabled = !isEnabled;
            withdrawalDateInput.disabled = !isEnabled;
        }

        // Set initial state
        updateDateFieldsState();

        // Listen for changes
        enableOnlineSalesCheckbox.addEventListener('change', updateDateFieldsState);
    </script>
@endsection
