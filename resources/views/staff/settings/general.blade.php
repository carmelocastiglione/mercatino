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
                                    <input type="checkbox" name="enable_online_sales" value="true" class="sr-only peer" @if($school->hasFeatureEnabled('enable_online_sales')) checked @endif>
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
@endsection
