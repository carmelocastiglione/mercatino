@extends('layouts.app-staff')

@section('title', 'Consegne di ' . $student->name)

@section('content')
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('staff.deliveries.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Torna alle consegne</a>
        </div>
        <h1 class="text-4xl font-bold text-gray-900">Consegne di {{ $student->name }} {{ $student->surname }}</h1>
        <p class="text-gray-600 mt-2">Email: {{ $student->email }} | Codice: <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $student->code }}</span></p>
    </div>

    @if($pendingCount > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Libri in Consegna</h2>
                <span class="inline-block bg-blue-600 text-white text-xs font-bold rounded-full px-3 py-1">{{ $pendingCount }}</span>
            </div>

            <div id="deliveries_list" class="divide-y divide-gray-200 mb-6">
                @foreach($deliveries as $delivery)
                    <div id="delivery_{{ $delivery->id }}" class="p-4 hover:bg-gray-50 transition delivery-item" data-delivery-id="{{ $delivery->id }}">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $delivery->book->title }}</p>
                                <p class="text-sm text-gray-600">{{ $delivery->book->author ?? 'Autore sconosciuto' }}</p>
                                <p class="text-sm text-gray-600">ISBN: {{ $delivery->book->isbn ?? 'N/A' }}</p>
                            </div>
                            <div class="ml-4 text-right">
                                <!-- Approved Badge -->
                                <div class="delivery-approved hidden">
                                    <p class="text-lg font-bold text-green-600">✓ Approvato</p>
                                    <p class="text-3xl font-bold text-blue-600 mt-1">€<span class="delivery-approved-price">0.00</span></p>
                                </div>
                                <!-- Rejected Badge -->
                                <div class="delivery-rejected hidden">
                                    <p class="text-lg font-bold text-red-600">✕ Rifiutato</p>
                                </div>
                            </div>
                        </div>

                        <!-- Two Column Layout -->
                        <div class="delivery-details grid grid-cols-2 gap-4">
                            <!-- Left: Price Details -->
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-3 border border-blue-200">
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Prezzo copertina:</span>
                                        <span class="font-medium">€{{ number_format($delivery->price_data['original_price'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Prezzo mercatino:</span>
                                        <span class="font-medium">€{{ number_format($delivery->price_data['marketplace_price'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-blue-200">
                                        <span class="text-gray-600">Fee scuola:</span>
                                        <span class="font-medium text-red-600">-€{{ number_format($delivery->price_data['fee'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t-2 border-blue-300 mt-2">
                                        <span class="font-bold text-gray-900">Prezzo finale:</span>
                                        <span class="font-bold text-lg text-blue-600">€{{ number_format($delivery->price_data['total'], 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Inputs and Buttons -->
                            <div class="flex flex-col gap-3">
                                <!-- Condition Select -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Condizione</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm delivery-condition" data-delivery-id="{{ $delivery->id }}" value="{{ $delivery->condition }}">
                                        <option value="like-new" @selected($delivery->condition === 'like-new')>Come Nuovo</option>
                                        <option value="good" @selected($delivery->condition === 'good')>Buona</option>
                                        <option value="fair" @selected($delivery->condition === 'fair')>Discreta</option>
                                        <option value="poor" @selected($delivery->condition === 'poor')>Scarsa</option>
                                    </select>
                                </div>

                                <!-- Price Input -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Prezzo finale (€)</label>
                                    <input type="number" step="0.50" min="0" class="w-full px-3 py-2 border border-blue-300 rounded-lg text-sm font-semibold delivery-price" data-delivery-id="{{ $delivery->id }}" value="{{ number_format($delivery->price_data['total'], 2, '.', '') }}" oninput="this.value = parseFloat(this.value || 0).toFixed(2)">
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2 flex-1">
                                    <button type="button" onclick="approveDelivery({{ $delivery->id }})" class="flex-1 px-3 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition">
                                        ✓ Approva
                                    </button>
                                    <button type="button" onclick="rejectDelivery({{ $delivery->id }})" class="flex-1 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition">
                                        ✕ Rifiuta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="continueProcess()" class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                    → Continua
                </button>
                <a href="{{ route('staff.deliveries.index') }}" class="px-6 py-3 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
                    Annulla
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg">Questo studente non ha libri in consegna</p>
            <a href="{{ route('staff.deliveries.index') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                Torna alle consegne
            </a>
        </div>
    @endif

<script>
    let studentId = {{ $student->id }};
    let studentCode = '{{ $student->code }}';
    let studentName = '{{ $student->name }} {{ $student->surname }}';
    let deliveriesData = {!! json_encode($deliveries) !!};

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

    function approveDelivery(deliveryId) {
        const deliveryElement = document.getElementById(`delivery_${deliveryId}`);
        const detailsElements = deliveryElement.querySelectorAll('.delivery-details');
        const approvedElement = deliveryElement.querySelector('.delivery-approved');
        const priceInput = deliveryElement.querySelector(`.delivery-price[data-delivery-id="${deliveryId}"]`);
        const priceValue = parseFloat(priceInput.value).toFixed(2);

        // Nascondi i dettagli e mostra lo stato approvato con il prezzo
        detailsElements.forEach(el => el.classList.add('hidden'));
        approvedElement.classList.remove('hidden');
        approvedElement.querySelector('.delivery-approved-price').textContent = priceValue;
    }

    function rejectDelivery(deliveryId) {
        const deliveryElement = document.getElementById(`delivery_${deliveryId}`);
        const detailsElements = deliveryElement.querySelectorAll('.delivery-details');
        const rejectedElement = deliveryElement.querySelector('.delivery-rejected');

        // Nascondi i dettagli e mostra lo stato rifiutato
        detailsElements.forEach(el => el.classList.add('hidden'));
        rejectedElement.classList.remove('hidden');
    }

    function continueProcess() {
        const deliveryIds = {!! json_encode($deliveries->pluck('id')->toArray()) !!};

        if (deliveryIds.length === 0) {
            showToast('Nessuna consegna da elaborare', 'error');
            return;
        }

        // Raccogli i valori modificati e lo stato di approvazione/rifiuto
        const approvedDeliveries = [];
        const rejectedIds = [];

        deliveryIds.forEach(deliveryId => {
            const deliveryElement = document.getElementById(`delivery_${deliveryId}`);
            const approvedElement = deliveryElement.querySelector('.delivery-approved');
            const rejectedElement = deliveryElement.querySelector('.delivery-rejected');
            const conditionSelect = document.querySelector(`.delivery-condition[data-delivery-id="${deliveryId}"]`);
            const priceInput = document.querySelector(`.delivery-price[data-delivery-id="${deliveryId}"]`);

            if (!approvedElement.classList.contains('hidden')) {
                // È approvato
                approvedDeliveries.push({
                    id: deliveryId,
                    condition: conditionSelect ? conditionSelect.value : null,
                    price: priceInput ? parseFloat(priceInput.value) : null
                });
            } else if (!rejectedElement.classList.contains('hidden')) {
                // È rifiutato
                rejectedIds.push(deliveryId);
            }
        });

        if (approvedDeliveries.length === 0 && rejectedIds.length === 0) {
            showToast('Nessuna consegna è stata elaborata', 'error');
            return;
        }

        // Innanzitutto approva i libri selezionati
        if (approvedDeliveries.length > 0) {
            fetch(`{{ route('staff.deliveries.approve-bulk') }}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    delivery_ids: approvedDeliveries.map(d => d.id),
                    modified_deliveries: approvedDeliveries
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Poi rifiuta i libri marcati come rifiutati
                    if (rejectedIds.length > 0) {
                        rejectMultiple(rejectedIds, approvedDeliveries);
                    } else {
                        // Se non ci sono rifiuti, procedi direttamente ad acquisizioni
                        saveAndRedirect(approvedDeliveries);
                    }
                } else {
                    showToast('Errore nell\'approvazione', 'error');
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                showToast('Errore nell\'approvazione', 'error');
            });
        } else {
            // Se solo rifiuti, esegui solo i rifiuti
            rejectMultiple(rejectedIds, []);
        }
    }

    function rejectMultiple(rejectedIds, approvedDeliveries) {
        const promises = rejectedIds.map(deliveryId => 
            fetch(`{{ route('staff.deliveries.reject-json') }}?delivery_id=${deliveryId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
        );

        Promise.all(promises)
            .then(results => {
                const allSuccessful = results.every(r => r.success);
                if (allSuccessful) {
                    showToast(`✓ ${rejectedIds.length} consegne rifiutate!`, 'success');
                    if (approvedDeliveries.length > 0) {
                        saveAndRedirect(approvedDeliveries);
                    } else {
                        // Se solo rifiuti, ricarica la pagina
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                } else {
                    showToast('Errore nel rifiuto di alcune consegne', 'error');
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                showToast('Errore nel rifiuto delle consegne', 'error');
            });
    }

    function saveAndRedirect(approvedDeliveries) {
        // Salva i libri approvati in localStorage per il carrello
        const conditionLabels = {
            'like-new': 'Like New',
            'good': 'Good',
            'fair': 'Fair',
            'poor': 'Poor'
        };
        
        const deliveriesToAdd = approvedDeliveries.map(mod => {
            const originalDelivery = deliveriesData.find(d => d.id === mod.id);
            return {
                id: Math.random(),
                seller_id: studentId,
                seller_name: studentName,
                book_id: originalDelivery.book.id,
                book_title: originalDelivery.book.title,
                book_author: originalDelivery.book.author,
                book_isbn: originalDelivery.book.isbn,
                condition: mod.condition,
                condition_label: conditionLabels[mod.condition] || mod.condition,
                price: parseFloat(mod.price)
            };
        });
        
        const sellerData = {
            id: studentId,
            name: studentName,
            code: studentCode
        };
        
        localStorage.setItem('deliveriesToAdd', JSON.stringify(deliveriesToAdd));
        localStorage.setItem('sellerData', JSON.stringify(sellerData));
        
        showToast(`✓ ${approvedDeliveries.length} consegne approvate!`, 'success');
        
        // Reindirizza ad acquisitions/create dopo 1 secondo
        setTimeout(() => {
            window.location.href = '{{ route("staff.acquisitions.create") }}';
        }, 1000);
    }
</script>
@endsection
