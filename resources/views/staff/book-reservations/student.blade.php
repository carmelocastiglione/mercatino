@extends('layouts.app-staff')

@section('title', 'Prenotazioni di ' . $student->name)

@section('content')
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('staff.book-reservations.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Torna alle prenotazioni</a>
        </div>
        <h1 class="text-4xl font-bold text-gray-900">Prenotazioni di {{ $student->name }} {{ $student->surname }}</h1>
        <p class="text-gray-600 mt-2">Email: {{ $student->email }} | Codice: <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $student->code }}</span></p>
    </div>

    @if($pendingCount > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Libri Prenotati</h2>
                <span class="inline-block bg-blue-600 text-white text-xs font-bold rounded-full px-3 py-1">{{ $pendingCount }}</span>
            </div>

            <!-- Batches Loop -->
            @foreach($batches as $batch)
                <div class="mb-8 pb-8 border-b border-gray-200 last:border-b-0">
                    <!-- Batch Header -->
                    <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-4 rounded-lg mb-4 border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-600">Batch Prenotazione</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Data: {{ $batch->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-lg text-purple-600">{{ $batch->bookReservations->count() }} {{ $batch->bookReservations->count() === 1 ? 'libro' : 'libri' }}</p>
                                <p class="text-sm text-gray-600">€ {{ number_format($batch->getTotalPrice(), 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Books in Batch -->
                    <div id="batch_{{ $batch->id }}" class="divide-y divide-gray-200 mb-6">
                        @foreach($batch->bookReservations as $reservation)
                            <div id="reservation_{{ $reservation->id }}" class="p-4 hover:bg-gray-50 transition reservation-item" data-reservation-id="{{ $reservation->id }}" data-batch-id="{{ $batch->id }}">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $reservation->bookListing->book->title }}</p>
                                        <p class="text-sm text-gray-600">{{ $reservation->bookListing->book->author ?? 'Autore sconosciuto' }}</p>
                                        <p class="text-sm text-gray-600">ISBN: {{ $reservation->bookListing->book->isbn ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-600 mt-2">
                                            Condizione: <span class="font-semibold
                                                @switch($reservation->bookListing->condition)
                                                    @case('like-new')
                                                        text-green-700
                                                    @break
                                                    @case('good')
                                                        text-blue-700
                                                    @break
                                                    @case('fair')
                                                        text-yellow-700
                                                    @break
                                                    @case('poor')
                                                        text-red-700
                                                    @break
                                                @endswitch
                                            ">
                                                @switch($reservation->bookListing->condition)
                                                    @case('like-new')
                                                        Come Nuovo
                                                    @break
                                                    @case('good')
                                                        Buona
                                                    @break
                                                    @case('fair')
                                                        Discreta
                                                    @break
                                                    @case('poor')
                                                        Scarsa
                                                    @break
                                                @endswitch
                                            </span>
                                        </p>
                                    </div>
                                    <div class="ml-4 text-right">
                                        <!-- Approved Badge -->
                                        <div class="reservation-approved hidden">
                                            <p class="text-lg font-bold text-green-600">✓ Approvato</p>
                                            <p class="text-3xl font-bold text-blue-600 mt-1">€<span class="reservation-approved-price">0.00</span></p>
                                        </div>
                                        <!-- Rejected Badge -->
                                        <div class="reservation-rejected hidden">
                                            <p class="text-lg font-bold text-red-600">✕ Rifiutato</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Details Section -->
                                <div class="reservation-details grid grid-cols-2 gap-4">
                                    <!-- Left: Price Details -->
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-3 border border-blue-200">
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Prezzo:</span>
                                                <span class="font-medium">€{{ number_format($reservation->bookListing->price, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between pt-2 border-t-2 border-blue-300 mt-2">
                                                <span class="font-bold text-gray-900">Prezzo vendita:</span>
                                                <span class="font-bold text-lg text-blue-600">€<span class="price-display">{{ number_format($reservation->bookListing->price, 2) }}</span></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right: Action Buttons -->
                                    <div class="flex flex-col gap-3">
                                        <!-- Action Buttons -->
                                        <div class="flex gap-2 flex-1">
                                            <button type="button" onclick="approveReservation({{ $reservation->id }})" class="flex-1 px-3 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition">
                                                ✓ Approva
                                            </button>
                                            <button type="button" onclick="rejectReservation({{ $reservation->id }})" class="flex-1 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition">
                                                ✕ Rifiuta
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="flex gap-3">
                <button type="button" onclick="continueProcess()" class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                    → Continua
                </button>
                <a href="{{ route('staff.book-reservations.index') }}" class="px-6 py-3 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
                    Annulla
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg">Questo studente non ha prenotazioni in sospeso</p>
            <a href="{{ route('staff.book-reservations.index') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                Torna alle prenotazioni
            </a>
        </div>
    @endif

<script>
    let studentId = {{ $student->id }};
    let studentCode = '{{ $student->code }}';
    let studentName = '{{ $student->name }} {{ $student->surname }}';

    function showToast(message, type = 'success') {
        const bgColor = type === 'error' ? 'bg-red-600' : 'bg-green-600';
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    function approveReservation(reservationId) {
        const reservationElement = document.getElementById(`reservation_${reservationId}`);
        const detailsElements = reservationElement.querySelectorAll('.reservation-details');
        const approvedElement = reservationElement.querySelector('.reservation-approved');
        const priceDisplay = reservationElement.querySelector('.price-display');
        const price = priceDisplay.textContent;

        // Hide details and show approved status
        detailsElements.forEach(el => el.classList.add('hidden'));
        approvedElement.classList.remove('hidden');
        approvedElement.querySelector('.reservation-approved-price').textContent = price;

        // Make AJAX call to approve
        fetch(`{{ route('staff.book-reservations.approve-single') }}?reservation_id=${reservationId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .catch(error => {
            showToast('Errore nell\'approvazione', 'error');
        });
    }

    function rejectReservation(reservationId) {
        const reservationElement = document.getElementById(`reservation_${reservationId}`);
        const detailsElements = reservationElement.querySelectorAll('.reservation-details');
        const rejectedElement = reservationElement.querySelector('.reservation-rejected');

        // Hide details and show rejected status
        detailsElements.forEach(el => el.classList.add('hidden'));
        rejectedElement.classList.remove('hidden');

        // Make AJAX call to reject
        fetch(`{{ route('staff.book-reservations.reject-single') }}?reservation_id=${reservationId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .catch(error => {
            showToast('Errore nel rifiuto', 'error');
        });
    }

    function continueProcess() {
        // Collect all reservation IDs from all batches
        const allReservations = document.querySelectorAll('[data-reservation-id]');
        const reservationIds = Array.from(allReservations).map(el => parseInt(el.getAttribute('data-reservation-id')));

        if (reservationIds.length === 0) {
            showToast('Nessuna prenotazione da elaborare', 'error');
            return;
        }

        // Collect approved and rejected reservations
        const approvedReservations = [];
        const rejectedReservations = [];

        reservationIds.forEach(reservationId => {
            const reservationElement = document.getElementById(`reservation_${reservationId}`);
            const approvedElement = reservationElement.querySelector('.reservation-approved');
            const rejectedElement = reservationElement.querySelector('.reservation-rejected');

            if (!approvedElement.classList.contains('hidden')) {
                approvedReservations.push(reservationId);
            } else if (!rejectedElement.classList.contains('hidden')) {
                rejectedReservations.push(reservationId);
            }
        });

        if (approvedReservations.length === 0 && rejectedReservations.length === 0) {
            showToast('Nessuna prenotazione è stata elaborata', 'error');
            return;
        }

        // If there are approved reservations, redirect to sales.create form
        if (approvedReservations.length > 0) {
            window.location.href = `{{ route('staff.book-reservations.prepare-sales') }}?student_id=${studentId}`;
        } else {
            showToast('Nessuna prenotazione approvata da elaborare', 'error');
        }
    }
</script>
@endsection
