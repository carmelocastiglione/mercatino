@extends('layouts.app-staff')

@section('title', 'Prenotazioni Libri')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Prenotazioni Libri</h1>
        <p class="text-gray-600 mt-2">Esamina e gestisci le prenotazioni dei libri dagli studenti</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stats-card label="In Sospeso" :value="$pendingCount" color="yellow" />
        <x-stats-card label="Approvate" :value="$confirmedCount" color="green" />
        <x-stats-card label="Rifiutate" :value="$rejectedCount" color="red" />
    </div>

    <!-- SECTION: SEARCH RESERVATIONS BY STUDENT -->
    <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">🔍 Ricerca per Studente</h2>
        <p class="text-sm text-gray-600 mb-4">Cerca uno studente per visualizzare rapidamente tutte le sue prenotazioni. Approvale o rifiutale singolarmente per creare le vendite.</p>
        
        <div class="flex gap-3">
            <div class="relative flex-1">
                <input 
                    type="text" 
                    id="student_search" 
                    placeholder="Cerca studente per nome, cognome, email o codice..." 
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
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
                                @if ($batch->isPending())
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        In Sospeso
                                    </span>
                                @elseif ($batch->isConfirmed())
                                    <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        Approvata
                                    </span>
                                @elseif ($batch->isRejected())
                                    <span class="inline-block bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        Rifiutata
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2 flex">
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
            <p class="text-gray-600 text-lg">Nessuna prenotazione in attesa</p>
            <p class="text-gray-500 text-sm mt-2">Torna qui quando gli studenti ne avranno prenotate</p>
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
