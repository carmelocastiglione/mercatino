@extends('layouts.app-staff')

@section('title', isset($statusLabel) ? $statusLabel : 'Prenotazioni Libri')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">
            @if(isset($statusLabel))
                {{ $statusLabel }}
            @else
                Prenotazioni Libri
            @endif
        </h1>
        <p class="text-gray-600 mt-2">
            @if(isset($statusLabel))
                Esamina le prenotazioni {{ strtolower($statusLabel) }}
            @else
                Esamina e gestisci le prenotazioni dei libri dagli studenti
            @endif
        </p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <a href="{{ route('staff.book-reservations.byStatus', 'pending') }}" class="hover:shadow-md transition">
            <x-stats-card label="In Attesa" :value="$pendingCount" color="yellow" />
        </a>
        <a href="{{ route('staff.book-reservations.byStatus', 'confirmed') }}" class="hover:shadow-md transition">
            <x-stats-card label="Valutate" :value="$confirmedCount" color="green" />
        </a>
        <a href="{{ route('staff.book-reservations.byStatus', 'cancelled') }}" class="hover:shadow-md transition">
            <x-stats-card label="Cancellate" :value="$cancelledCount" color="red" />
        </a>
    </div>

    @if(isset($statusFilter))
        <div class="mb-6">
            <a href="{{ route('staff.book-reservations.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                ← Torna a tutte le prenotazioni
            </a>
        </div>
    @endif

    <!-- SECTION: SEARCH RESERVATIONS BY STUDENT -->
    <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">🔍 Ricerca per Studente</h2>
        <p class="text-sm text-gray-600 mb-4">Cerca uno studente per visualizzare rapidamente tutte le sue prenotazioni. Approvale o rifiutale singolarmente per creare le vendite.</p>
        
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
    </div>

    @if($batches->count() > 0)
        <!-- Filter Form -->
        <div class="bg-white border border-purple-200 rounded-lg p-6 mb-8">
            <form method="GET" action="{{ route('staff.book-reservations.index') }}" class="flex gap-2">
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
                    <a href="{{ route('staff.book-reservations.index') }}" class="px-6 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition">
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
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data Prenotazione</th>
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
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $batch->bookReservations->count() }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">€ {{ number_format($batch->total_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $batch->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($batch->status === 'pending')
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        In Attesa
                                    </span>
                                @elseif ($batch->status === 'confirmed')
                                    <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        Valutata
                                    </span>
                                @elseif ($batch->status === 'cancelled')
                                    <span class="inline-block bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        Cancellata
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('staff.book-reservations.student', $batch->user_id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Rivedi
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $batches->links() }}
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
                            Nessuna prenotazione in attesa
                        @break
                        @case('confirmed')
                            Nessuna prenotazione valutata
                        @break
                        @case('cancelled')
                            Nessuna prenotazione cancellata
                        @break
                        @default
                            Nessuna prenotazione trovata
                    @endswitch
                @else
                    Nessuna prenotazione trovata
                @endif
            </p>
            @if(!isset($statusFilter))
                <p class="text-gray-500 text-sm">Torna qui quando gli studenti ne avranno prenotate</p>
            @else
                <a href="{{ route('staff.book-reservations.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium inline-block">
                    Torna a tutte le prenotazioni
                </a>
            @endif
        </div>
    @endif

<script>
    let studentDebounceTimer;
    let currentStudentId = null;

    const studentSearch = document.getElementById('student_search');
    const studentIdInput = document.getElementById('student_id');
    const studentResults = document.getElementById('student_results');
    const clearStudentBtn = document.getElementById('clear_student_btn');

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
            fetch(`{{ route('staff.book-reservations.search-students') }}?q=${encodeURIComponent(query)}`)
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
        // Redirect to student reservations page
        window.location.href = `/staff/book-reservations/student/${id}`;
    }

    function clearStudentSearch() {
        studentSearch.value = '';
        studentIdInput.value = '';
        currentStudentId = null;
        studentResults.classList.add('hidden');
        clearStudentBtn.classList.add('hidden');
    }
</script>
@endsection
