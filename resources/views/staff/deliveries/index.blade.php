@extends('layouts.app-staff')

@section('title', isset($statusLabel) ? $statusLabel : 'Consegne da Gestire')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">
            @if(isset($statusLabel))
                {{ $statusLabel }}
            @else
                Consegne da Gestire
            @endif
        </h1>
        <p class="text-gray-600 mt-2">
            @if(isset($statusLabel))
                Esamina le consegne {{ strtolower($statusLabel) }}
            @else
                Esamina e approva le consegne dei libri dagli studenti
            @endif
        </p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <a href="{{ route('staff.deliveries.byStatus', 'pending') }}" class="hover:shadow-md transition">
            <x-stats-card label="Da Approvare" :value="$pendingCount" color="yellow" />
        </a>
        <a href="{{ route('staff.deliveries.byStatus', 'submitted') }}" class="hover:shadow-md transition">
            <x-stats-card label="Valutate" :value="$submittedCount" color="green" />
        </a>
    </div>

    @if(isset($statusFilter))
        <div class="mb-6">
            <a href="{{ route('staff.deliveries.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                ← Torna a tutte le consegne
            </a>
        </div>
    @endif

    <!-- SECTION: SEARCH DELIVERIES BY STUDENT -->
    <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">🔍 Ricerca per Studente</h2>
        <p class="text-sm text-gray-600 mb-4">Cerca uno studente per visualizzare rapidamente tutti i suoi libri in consegna. Selezionali per aggiungerli a una nuova acquisizione.</p>
        
        <div class="flex gap-3">
            <div class="relative flex-1">
                <input 
                    type="text" 
                    id="student_search" 
                    placeholder="Cerca studente per codice transazione, cognome, email o codice..." 
                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
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

    @if($batches->count() > 0)
        <!-- Filter Form -->
        <div class="bg-white border border-purple-200 rounded-lg p-6 mb-8">
            <form method="GET" action="{{ route('staff.deliveries.index') }}" class="flex gap-2">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Filtra per codice transazione, cognome, email o codice studente..." 
                    value="{{ $filterQuery }}"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    autocomplete="off"
                />
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition">
                    Filtra
                </button>
                @if($filterQuery)
                    <a href="{{ route('staff.deliveries.index') }}" class="px-6 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition">
                        Reset
                    </a>
                @endif
            </form>
            @if($filterQuery)
                <p class="text-sm text-gray-600 mt-2">Risultati per: <strong>{{ $filterQuery }}</strong> ({{ $batches->total() }} risultati)</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Codice Transazione</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Studente</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">N. Libri</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Totale</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data Richiesta</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data Consegna</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Stato</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($batches as $batch)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-mono font-bold text-gray-900">{{ $batch->ean13 ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $batch->user->name }} {{ $batch->user->surname }}</p>
                                    <p class="text-sm text-gray-600">{{ $batch->user->email }}</p>
                                    <p class="text-sm text-gray-500">{{ $batch->user->code }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $batch->deliveries->count() }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">€ {{ number_format($batch->deliveries->sum('price'), 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $batch->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($batch->scheduledDeliveryDate && $batch->scheduledDeliveryDate->scheduled_date)
                                    {{ $batch->scheduledDeliveryDate->scheduled_date->format('d/m/Y') }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($batch->status === 'pending')
                                    <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">Da Approvare</span>
                                @elseif($batch->status === 'submitted')
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Valutata</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">{{ $batch->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('staff.deliveries.student', $batch->user_id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Rivedi
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $batches->links() }}
            </div>
        </div>

    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg mb-6">
                @if(isset($statusFilter))
                    @switch($statusFilter)
                        @case('pending')
                            Nessun batch da approvare
                        @break
                        @case('submitted')
                            Nessun batch valutato
                        @break
                        @default
                            Nessun batch trovato
                    @endswitch
                @else
                    Nessun batch in attesa di approvazione
                @endif
            </p>
            @if(!isset($statusFilter))
                <p class="text-gray-500 text-sm">Torna qui quando gli studenti ne avranno prenotati</p>
            @else
                <a href="{{ route('staff.deliveries.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium inline-block">
                    Torna a tutte le consegne
                </a>
            @endif
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
        // Reindirizza direttamente alla pagina dei libri dello studente
        window.location.href = `/staff/deliveries/student/${id}`;
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
