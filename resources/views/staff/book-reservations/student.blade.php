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
        <div id="error_container" class="hidden mb-6"></div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <x-info-box
                type="info"
                title="Procedura di approvazione delle prenotazioni"
                message="Per continuare è necessario che tutte le prenotazioni di un singolo batch siano state approvate o rifiutate. Una volta approvate, le prenotazioni saranno confermate al cliente."
            />
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Libri Prenotati</h2>
                <span class="inline-block bg-blue-600 text-white text-xs font-bold rounded-full px-3 py-1">{{ $pendingCount }}</span>
            </div>

            <!-- Batches Loop -->
            @foreach($batches as $batch)
                <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                    <!-- Batch Header -->
                    <div class="batch-header bg-gradient-to-r from-purple-50 to-blue-50 px-6 py-4 border-b border-purple-200 cursor-pointer hover:from-purple-100 hover:to-blue-100 transition" data-batch-id="{{ $batch->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="grid grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-600 font-medium">Codice Transazione</p>
                                        <p class="text-gray-900 font-mono text-lg">{{ $batch->ean13 }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 font-medium">N. Libri</p>
                                        <p class="text-gray-900 font-bold text-lg">{{ $batch->bookReservations->count() }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 font-medium">Totale</p>
                                        <p class="text-blue-600 font-bold text-lg">€ {{ number_format($batch->total_price, 2, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 font-medium">Data Richiesta</p>
                                        <p class="text-gray-900 font-medium">{{ $batch->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="batch-toggle ml-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Reservations in this batch -->
                    <div class="batch-content divide-y divide-gray-200">
                        @foreach($batch->bookReservations as $reservation)
                            <div id="reservation_{{ $reservation->id }}" class="p-4 hover:bg-gray-50 transition reservation-item" data-reservation-id="{{ $reservation->id }}" data-batch-id="{{ $batch->id }}">
                                <div class="mb-3">
                                    <!-- Book Information -->
                                    <div>
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
                                </div>

                                <!-- Details Section -->
                                <div class="reservation-details grid grid-cols-2 gap-4">
                                    <!-- Left: Price Details -->
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-3 border border-blue-200">
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Prezzo copertina:</span>
                                                <span class="font-medium">€{{ number_format($reservation->price_data['original_price'], 2) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Prezzo mercatino:</span>
                                                <span class="font-medium">€{{ number_format($reservation->price_data['marketplace_price'], 2) }}</span>
                                            </div>
                                            <div class="flex justify-between pt-2 border-t border-blue-200">
                                                <span class="text-gray-600">Fee scuola:</span>
                                                <span class="font-medium text-blue-600">+€{{ number_format($reservation->price_data['fee'], 2) }}</span>
                                            </div>
                                            <div class="flex justify-between pt-2 border-t-2 border-blue-300 mt-2">
                                                <span class="font-bold text-gray-900">Totale:</span>
                                                <span class="font-bold text-lg text-blue-600">€<span class="price-display">{{ number_format($reservation->price_data['total'], 2) }}</span></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right: Seller & Buttons -->
                                    <div class="flex flex-col gap-3">
                                        <!-- Seller Information -->
                                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg p-3 border border-amber-200">
                                            <h4 class="font-semibold text-gray-900 text-sm mb-2">Venditore</h4>
                                            <div class="space-y-1 text-sm">
                                                <p class="text-gray-700">
                                                    <span class="font-medium">{{ $reservation->bookListing->seller->name }}</span>
                                                    <span>{{ $reservation->bookListing->seller->surname }}</span>
                                                </p>
                                                <p class="text-gray-600">
                                                    <span class="text-xs">Codice: </span>
                                                    <span class="font-mono bg-white px-1 py-0.5 rounded text-xs">{{ $reservation->bookListing->seller->code }}</span>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex gap-2">
                                            <button type="button" onclick="approveReservation({{ $reservation->id }})" class="flex-1 px-3 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition">
                                                ✓ Approva
                                            </button>
                                            <button type="button" onclick="rejectReservation({{ $reservation->id }})" class="flex-1 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition">
                                                ✕ Rifiuta
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Approved Badge -->
                                <div class="reservation-approved hidden bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-3 border border-green-300 mt-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-green-700">Prenotazione approvata</p>
                                            <p class="text-sm text-green-600">Totale: €<span class="reservation-approved-price">0.00</span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Rejected Badge -->
                                <div class="reservation-rejected hidden bg-gradient-to-r from-red-50 to-pink-50 rounded-lg p-3 border border-red-300 mt-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-red-700">Prenotazione rifiutata</p>
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
    let reservationsData = {!! json_encode($reservations->map(fn($r) => ['id' => $r->id])->toArray()) !!};
    let batchesData = {!! json_encode($batchesForJson) !!};

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

    function showError(message) {
        const errorContainer = document.getElementById('error_container');
        const errorHtml = `
            <div class="p-4 bg-red-50 border border-red-300 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-700">${message}</p>
                    </div>
                </div>
            </div>
        `;
        errorContainer.innerHTML = errorHtml;
        errorContainer.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function hideError() {
        const errorContainer = document.getElementById('error_container');
        errorContainer.classList.add('hidden');
    }

    function checkAndCloseBatchIfComplete(reservationId) {
        const reservationElement = document.getElementById(`reservation_${reservationId}`);
        const batchElement = reservationElement.closest('.border');
        const batchHeader = batchElement.querySelector('.batch-header');
        const batchId = batchHeader.getAttribute('data-batch-id');
        
        // Trova tutti i reservation di questo batch
        const allReservationsInBatch = Array.from(batchElement.querySelectorAll('.reservation-item'));
        
        // Controlla se tutti sono elaborati (approvati o rifiutati)
        const allProcessed = allReservationsInBatch.every(reservation => {
            const approvedDiv = reservation.querySelector('.reservation-approved');
            const rejectedDiv = reservation.querySelector('.reservation-rejected');
            return !approvedDiv.classList.contains('hidden') || !rejectedDiv.classList.contains('hidden');
        });
        
        if (allProcessed) {
            // Chiudi e colora il batch
            const batchContent = batchElement.querySelector('.batch-content');
            const toggle = batchHeader.querySelector('.batch-toggle svg');
            
            // Applica colore verde chiaro
            batchHeader.style.background = 'linear-gradient(to right, rgb(240, 253, 244), rgb(236, 253, 245))';
            batchHeader.style.borderBottomColor = 'rgb(134, 239, 172)';
            
            // Nascondi il contenuto
            batchContent.classList.add('hidden');
            toggle.style.transform = 'rotate(0deg)';
        }
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

        // Check if batch is complete
        checkAndCloseBatchIfComplete(reservationId);
    }

    function rejectReservation(reservationId) {
        const reservationElement = document.getElementById(`reservation_${reservationId}`);
        const detailsElements = reservationElement.querySelectorAll('.reservation-details');
        const rejectedElement = reservationElement.querySelector('.reservation-rejected');

        // Hide details and show rejected status
        detailsElements.forEach(el => el.classList.add('hidden'));
        rejectedElement.classList.remove('hidden');

        // Check if batch is complete
        checkAndCloseBatchIfComplete(reservationId);
    }

    function continueProcess() {
        const allReservations = Array.from(document.querySelectorAll('.reservation-item'));

        if (allReservations.length === 0) {
            showError('Nessuna prenotazione da elaborare');
            return;
        }

        hideError();

        // Collect approved and rejected reservations
        const approvedIds = [];
        const rejectedIds = [];
        const unapprovedIds = [];
        const reservationsByBatch = {};

        // Raggruppa per batch
        batchesData.forEach(batchData => {
            const batchId = batchData.batch.id;
            reservationsByBatch[batchId] = {
                batchId: batchId,
                ean13: batchData.batch.ean13,
                approved: [],
                rejected: [],
                unapproved: []
            };

            batchData.reservations.forEach(reservation => {
                const reservationElement = document.getElementById(`reservation_${reservation.id}`);
                if (!reservationElement) return;

                const approvedElement = reservationElement.querySelector('.reservation-approved');
                const rejectedElement = reservationElement.querySelector('.reservation-rejected');

                if (!approvedElement.classList.contains('hidden')) {
                    // È approvato
                    reservationsByBatch[batchId].approved.push(reservation.id);
                    approvedIds.push(reservation.id);
                } else if (!rejectedElement.classList.contains('hidden')) {
                    // È rifiutato
                    reservationsByBatch[batchId].rejected.push(reservation.id);
                    rejectedIds.push(reservation.id);
                } else {
                    // Non è stato elaborato
                    reservationsByBatch[batchId].unapproved.push(reservation.id);
                    unapprovedIds.push(reservation.id);
                }
            });
        });

        // Valida che per ogni batch, o tutti i libri sono elaborati, o nessuno
        const batchesToSubmit = [];
        let hasErrors = false;
        let errorMessages = [];

        for (const batchId in reservationsByBatch) {
            const batch = reservationsByBatch[batchId];
            const totalReservations = batch.approved.length + batch.rejected.length + batch.unapproved.length;
            const elaboratedReservations = batch.approved.length + batch.rejected.length;

            if (elaboratedReservations > 0 && elaboratedReservations < totalReservations) {
                // C'è incoerenza
                hasErrors = true;
                errorMessages.push(`Batch ${batch.ean13}: tutti i ${totalReservations} libri devono essere approvati o rifiutati. Attualmente: ${elaboratedReservations} elaborati, ${batch.unapproved.length} non elaborati.`);
            } else if (elaboratedReservations === totalReservations && elaboratedReservations > 0) {
                // Tutti i libri sono stati elaborati
                batchesToSubmit.push(batchId);
            }
        }

        if (hasErrors) {
            const errorText = errorMessages.join(' ');
            showError(errorText);
            return;
        }

        if (approvedIds.length === 0 && rejectedIds.length === 0) {
            showError('Nessuna prenotazione è stata elaborata. Approva o rifiuta almeno una prenotazione per continuare.');
            return;
        }

        // Procedi con l'approvazione/rifiuto
        if (approvedIds.length > 0) {
            fetch(`{{ route('staff.book-reservations.approve-bulk') }}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    reservation_ids: approvedIds
                })
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Poi rifiuta le prenotazioni marcate come rifiutate
                    if (rejectedIds.length > 0) {
                        rejectMultiple(rejectedIds, approvedIds, batchesToSubmit);
                    } else {
                        // Se non ci sono rifiuti, procedi direttamente
                        submitBatchesAndRedirect(batchesToSubmit, approvedIds);
                    }
                } else {
                    showError('Errore nell\'approvazione: ' + (data.message || 'Sconosciuto'));
                }
            })
            .catch(error => {
                showError('Errore nell\'approvazione');
            });
        } else {
            // Se solo rifiuti, esegui solo i rifiuti
            rejectMultiple(rejectedIds, [], batchesToSubmit);
        }
    }

    function rejectMultiple(rejectedIds, approvedIds, batchesToSubmit) {
        fetch(`{{ route('staff.book-reservations.reject-multiple') }}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                reservation_ids: rejectedIds
            })
        })
        .then(response => {
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (rejectedIds.length > 0) {
                    showToast(`✓ ${rejectedIds.length} prenotazioni rifiutate!`, 'success');
                }
                submitBatchesAndRedirect(batchesToSubmit, approvedIds);
            } else {
                showError('Errore nel rifiuto di alcune prenotazioni: ' + (data.message || 'Sconosciuto'));
            }
        })
        .catch(error => {
            showError('Errore nel rifiuto delle prenotazioni');
        });
    }

    function submitBatchesAndRedirect(batchesToSubmit, approvedIds) {
        // Aggiorna lo stato dei batch a "confirmed"
        if (batchesToSubmit.length > 0) {
            fetch(`{{ route('staff.book-reservations.update-batch-status') }}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    batch_ids: batchesToSubmit,
                    status: 'confirmed'
                })
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (approvedIds.length > 0) {
                        // Redirect to sales creation
                        // Store approved IDs in session before redirecting
                        fetch(`{{ route('staff.book-reservations.store-session-approvals') }}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                approved_reservation_ids: approvedIds,
                                student_id: studentId
                            })
                        })
                        .then(() => {
                            const url = '{{ route('staff.book-reservations.prepare-sales') }}' + '?student_id=' + studentId;
                            window.location.href = url;
                        });
                    } else {
                        // All rejected, go back to index
                        window.location.href = `{{ route('staff.book-reservations.index') }}`;
                    }
                } else {
                    showError('Errore nell\'aggiornamento dello stato dei batch: ' + (data.message || 'Sconosciuto'));
                }
            })
            .catch(error => {
                showError('Errore nell\'aggiornamento dello stato dei batch');
            });
        } else {
            // No batches to submit, redirect directly
            if (approvedIds.length > 0) {
                const url = `{{ route('staff.book-reservations.prepare-sales') }}?student_id=${studentId}`;
                window.location.href = url;
            }
        }
    }

    // Batch Collapsible Functionality
    document.querySelectorAll('.batch-header').forEach(header => {
        header.addEventListener('click', function() {
            const content = this.closest('.border').querySelector('.batch-content');
            const toggle = this.querySelector('.batch-toggle svg');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                toggle.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                toggle.style.transform = 'rotate(0deg)';
            }
        });
    });

    // Start with batches collapsed
    document.querySelectorAll('.batch-content').forEach(content => {
        content.classList.add('hidden');
    });

    document.querySelectorAll('.batch-toggle svg').forEach(svg => {
        svg.style.transform = 'rotate(0deg)';
    });
</script>
@endsection
