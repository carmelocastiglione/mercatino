@extends('layouts.app-staff')

@section('title', 'Consegne da Approvare')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Consegne da Approvare</h1>
        <p class="text-gray-600 mt-2">Esamina e approva le consegne dei libri dagli studenti</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stats-card label="Da Approvare" :value="$pendingCount" color="yellow" />
        <x-stats-card label="Approvate" :value="$approvedCount" color="green" />
        <x-stats-card label="Rifiutate" :value="$rejectedCount" color="red" />
    </div>

    <!-- SECTION: SEARCH DELIVERIES BY STUDENT -->
    <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">🔍 Ricerca per Studente</h2>
        <p class="text-sm text-gray-600 mb-4">Cerca uno studente per visualizzare rapidamente tutti i suoi libri in consegna. Selezionali per aggiungerli a una nuova acquisizione.</p>
        
        <div class="flex gap-3">
            <div class="relative flex-1">
                <input 
                    type="text" 
                    id="student_search" 
                    placeholder="Cerca studente per nome, cognome, email o codice..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    autocomplete="off"
                />
                <input type="hidden" id="student_id" value="">
                
                <!-- Dropdown dei risultati -->
                <div id="student_results" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg mt-2 max-h-64 overflow-y-auto hidden z-10"></div>
            </div>
            <button type="button" id="clear_student_btn" onclick="clearStudentSearch()" class="px-4 py-2 bg-gray-400 text-white font-medium rounded-lg hover:bg-gray-500 transition whitespace-nowrap hidden">
                ✕ Resetta
            </button>
        </div>

        <!-- Student Deliveries List -->
        <div id="student_deliveries_box" class="mt-6 hidden">
            <div class="bg-white rounded-lg border border-purple-300 p-4 mb-4">
                <h3 class="font-semibold text-gray-900 mb-3">
                    Libri in Consegna Pending
                    <span id="deliveries_count" class="ml-2 inline-block bg-purple-600 text-white text-xs font-bold rounded-full px-3 py-1">0</span>
                </h3>
                
                <div id="student_deliveries_list" class="divide-y divide-gray-200">
                    <!-- Deliveries will be loaded here -->
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" id="approve_all_btn" onclick="approveAllDeliveries()" class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition" disabled>
                    ✓ Approva Tutti
                </button>
            </div>
        </div>
    </div>

    @if($deliveries->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Studente</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Libro</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Condizioni</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Prezzo</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($deliveries as $delivery)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $delivery->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $delivery->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $delivery->book->title }}</p>
                                    <p class="text-sm text-gray-600">{{ $delivery->book->author ?? 'Autore sconosciuto' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @switch($delivery->condition)
                                        @case('like-new') bg-green-100 text-green-800 @break
                                        @case('good') bg-blue-100 text-blue-800 @break
                                        @case('fair') bg-yellow-100 text-yellow-800 @break
                                        @case('poor') bg-red-100 text-red-800 @break
                                    @endswitch
                                ">
                                    {{ ucfirst(str_replace('-', ' ', $delivery->condition)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">€ {{ number_format($delivery->price, 0) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $delivery->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2 flex">
                                <a href="{{ route('staff.deliveries.show', $delivery) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Rivedi
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg">Nessuna consegna in attesa di approvazione</p>
            <p class="text-gray-500 text-sm mt-2">Torna qui quando gli studenti ne avranno prenotate</p>
        </div>
    @endif

<script>
    let studentDebounceTimer;
    let studentCode = '';  // Variabile per salvare il code dello studente
    let currentStudentId = null;  // Per salvare l'ID dello studente selezionato
    let deliveriesData = [];  // Memorizza i dati completi dei libri

    const studentSearch = document.getElementById('student_search');
    const studentIdInput = document.getElementById('student_id');
    const studentResults = document.getElementById('student_results');
    const clearStudentBtn = document.getElementById('clear_student_btn');
    const studentDeliveriesBox = document.getElementById('student_deliveries_box');
    const studentDeliveriesList = document.getElementById('student_deliveries_list');
    const deliveriesCount = document.getElementById('deliveries_count');
    const approveAllBtn = document.getElementById('approve_all_btn');
    
    let currentStudentDeliveries = [];  // Track delivery IDs for bulk approval

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

    // Student Search Functionality
    studentSearch.addEventListener('input', (e) => {
        clearTimeout(studentDebounceTimer);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            studentResults.classList.add('hidden');
            return;
        }

        studentDebounceTimer = setTimeout(() => {
            fetch(`{{ route('staff.acquisitions.search-students') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(students => {
                    if (students.length === 0) {
                        studentResults.innerHTML = '<div class="p-4 text-gray-500">Nessuno studente trovato</div>';
                        studentResults.classList.remove('hidden');
                        return;
                    }

                    studentResults.innerHTML = students.map(student => `
                        <div class="p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition" onclick="selectStudent(${student.id}, '${student.name.replace(/'/g, "\\'")} ${student.surname.replace(/'/g, "\\'")}', '${student.code}')">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-900">${student.name} ${student.surname}</p>
                                    <p class="text-sm text-gray-500">${student.email}</p>
                                </div>
                                <p class="text-sm font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded">${student.code}</p>
                            </div>
                        </div>
                    `).join('');
                    studentResults.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Errore nella ricerca:', error);
                    studentResults.innerHTML = '<div class="p-4 text-red-500">Errore nella ricerca</div>';
                    studentResults.classList.remove('hidden');
                });
        }, 300);
    });

    function selectStudent(id, name, code) {
        studentIdInput.value = id;
        studentSearch.value = name;
        studentCode = code;  // Salva il code dello studente
        currentStudentId = id;  // Salva l'ID dello studente
        studentResults.classList.add('hidden');
        clearStudentBtn.classList.remove('hidden');
        
        // Carica i pending deliveries dello studente
        fetch(`{{ route('staff.acquisitions.student-deliveries') }}?student_id=${id}`)
            .then(response => response.json())
            .then(data => {
                currentStudentDeliveries = data.deliveries.map(d => d.id);  // Save delivery IDs for bulk approval
                deliveriesData = data.deliveries;  // Salva i dati completi delle consegne
                deliveriesCount.textContent = data.deliveries.length;

                if (data.deliveries.length === 0) {
                    studentDeliveriesList.innerHTML = '<p class="text-gray-500 text-sm py-4">Questo studente non ha consegne pending</p>';
                    studentDeliveriesBox.classList.remove('hidden');
                    approveAllBtn.disabled = true;
                    return;
                }

                studentDeliveriesList.innerHTML = data.deliveries.map((delivery, index) => {
                    const conditionColors = {
                        'like-new': 'bg-green-100 text-green-800',
                        'good': 'bg-blue-100 text-blue-800',
                        'fair': 'bg-yellow-100 text-yellow-800',
                        'poor': 'bg-red-100 text-red-800'
                    };

                    return `
                        <div class="p-4 flex justify-between items-start hover:bg-gray-50 transition">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">${delivery.book.title}</p>
                                <p class="text-sm text-gray-600">${delivery.book.author || 'Autore sconosciuto'}</p>
                                <div class="flex gap-2 mt-2">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded ${conditionColors[delivery.condition]}">${delivery.condition.replace('-', ' ')}</span>
                                    <span class="inline-block px-2 py-1 text-xs font-semibold bg-gray-200 text-gray-800 rounded">€${parseFloat(delivery.price).toFixed(2)}</span>
                                </div>
                            </div>
                            <button type="button" onclick="rejectDelivery(${delivery.id})" class="ml-4 px-3 py-1 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition whitespace-nowrap">
                                ✕ Rifiuta
                            </button>
                        </div>
                    `;
                }).join('');
                
                
                studentDeliveriesBox.classList.remove('hidden');
                approveAllBtn.disabled = currentStudentDeliveries.length === 0;
            })
            .catch(error => {
                console.error('Errore nel caricamento delle consegne:', error);
                showToast('Errore nel caricamento delle consegne', 'error');
            });
    }

    function rejectDelivery(deliveryId) {
        fetch(`{{ route('staff.deliveries.reject-json') }}?delivery_id=${deliveryId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('✓ Consegna rifiutata', 'success');
                // Ricarica la lista
                selectStudent(
                    parseInt(studentIdInput.value),
                    studentSearch.value,
                    studentCode
                );
            } else {
                showToast('Errore nel rifiuto della consegna', 'error');
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            showToast('Errore nel rifiuto della consegna', 'error');
        });
    }

    function approveAllDeliveries() {
        console.log('approveAllDeliveries() called');
        console.log('currentStudentDeliveries:', currentStudentDeliveries);
        
        if (currentStudentDeliveries.length === 0) {
            showToast('Nessuna consegna da approvare', 'error');
            return;
        }

        console.log('Sending fetch request to approve-bulk');
        fetch(`{{ route('staff.deliveries.approve-bulk') }}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ delivery_ids: currentStudentDeliveries })
        })
        .then(response => {
            console.log('Response received:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.success) {
                showToast(`✓ ${data.approved_count} consegne approvate!`, 'success');
                
                // Salva i libri approvati in localStorage per il carrello
                const conditionLabels = {
                    'like-new': 'Like New',
                    'good': 'Good',
                    'fair': 'Fair',
                    'poor': 'Poor'
                };
                
                const deliveriesToAdd = deliveriesData.map(d => ({
                    id: Math.random(),
                    seller_id: currentStudentId,
                    seller_name: studentSearch.value,
                    book_id: d.book.id,
                    book_title: d.book.title,
                    condition: d.condition,
                    condition_label: conditionLabels[d.condition] || d.condition,
                    price: parseFloat(d.price)
                }));
                
                const sellerData = {
                    id: currentStudentId,
                    name: studentSearch.value,
                    code: studentCode
                };
                
                localStorage.setItem('deliveriesToAdd', JSON.stringify(deliveriesToAdd));
                localStorage.setItem('sellerData', JSON.stringify(sellerData));
                
                // Reindirizza ad acquisitions/create dopo 1 secondo
                setTimeout(() => {
                    window.location.href = '{{ route("staff.acquisitions.create") }}';
                }, 1000);
            } else {
                showToast('Errore nell\'approvazione', 'error');
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            showToast('Errore nell\'approvazione', 'error');
        });
    }

    function clearStudentSearch() {
        studentSearch.value = '';
        studentIdInput.value = '';
        studentCode = '';
        currentStudentDeliveries = [];
        studentResults.classList.add('hidden');
        studentDeliveriesList.innerHTML = '';
        studentDeliveriesBox.classList.add('hidden');
        clearStudentBtn.classList.add('hidden');
        approveAllBtn.disabled = true;
    }
</script>
@endsection
