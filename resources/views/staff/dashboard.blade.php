@extends('layouts.app-staff')

@section('title', 'Dashboard Staff')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Dashboard Staff</h1>
        <p class="text-gray-600 mt-2">Panoramica della gestione delle consegne</p>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Financial Metrics Bar Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Metriche Finanziarie</h3>
            <div style="max-height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="financialChart"></canvas>
            </div>
        </div>

        <!-- Activity Bar Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Stato Disponibilità Libri</h3>
            <div style="max-height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="bookStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Main Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Libri in catalogo -->
        <x-dashboard-card 
            href="{{ route('staff.books.index') }}"
            title="Libri in catalogo"
            description="Gestisci i libri del tuo catalogo scolastico"
            count="{{ $totalBooks ?? 0 }}"
            bgColor="teal"
            label="IN CATALOGO"
        />

        <!-- Libri disponibili -->
        <x-dashboard-card 
            href="{{ route('staff.book-listings.index') }}"
            title="Libri disponibili"
            description="Visualizza tutti i libri disponibili nel mercatino"
            count="{{ $availableBooks ?? 0 }}"
            bgColor="purple"
            label="IN CATALOGO"
        />

        <!-- Gestione Utenti -->
        <x-dashboard-card 
            href="{{ route('staff.users.index') }}"
            title="Gestione utenti"
            description="Gestisci gli studenti, il loro storico e lo staff della tua scuola"
            count="{{ ($totalStudents ?? 0) + ($totalStaff ?? 0) }}"
            bgColor="orange"
            label="TOTALE"
        />

        @if($enableOnlineSales)
            <!-- Prenotazioni consegne -->
            <x-dashboard-card 
                href="{{ route('staff.deliveries.index') }}"
                title="Prenotazioni consegne"
                description="Esamina e approva le prenotazioni degli studenti"
                count="{{ $pendingDeliveryBatches ?? 0 }}"
                bgColor="yellow"
                label="DA ESAMINARE"
            />

            <!-- Prenotazioni Acquisti -->
            <x-dashboard-card 
                href="{{ route('staff.book-reservations.index') }}"
                title="Prenotazioni acquisti"
                description="Gestisci le prenotazioni dei libri acquisiti"
                count="{{ $pendingReservations ?? 0 }}"
                bgColor="pink"
                label="DA ESAMINARE"
            />
        @endif

        <!-- Acquisizioni -->
        <x-dashboard-card 
            href="{{ route('staff.acquisitions.index') }}"
            title="Acquisizioni"
            description="Numero totale di acquisizioni registrate"
            count="{{ $totalAcquisitions ?? 0 }}"
            bgColor="blue"
            label="TOTALE"
        />

        <!-- Vendite -->
        <x-dashboard-card 
            href="{{ route('staff.sales.index') }}"
            title="Vendite"
            description="Libri venduti al mercatino (totale)"
            count="{{ $totalSales ?? 0 }}"
            bgColor="green"
            label="INCASSO"
        />

        <!-- Riscossioni -->
        <x-dashboard-card 
            href="{{ route('staff.withdrawals.index') }}"
            title="Riscossioni"
            description="Gestisci i prelievi denaro dei venditori"
            count="{{ $totalWithdrawals ?? 0 }}"
            bgColor="indigo"
            label="RITIRATE"
        />

        <!-- Resi -->
        <x-dashboard-card 
            href="{{ route('staff.reclaims.index') }}"
            title="Resi"
            description="Gestisci i resi dei libri venduti"
            count="{{ $pendingReclaims ?? 0 }}"
            bgColor="red"
            label="PENDENTI"
        />

        <!-- Esporta dati -->
        <x-dashboard-card 
            href="{{ route('staff.export.index') }}"
            title="Esporta dati"
            description="Scarica i dati della tua scuola in formato CSV"
            count="∞"
            bgColor="cyan"
            label="ESPORTAZIONE"
        />

    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Chart.js DataLabels Plugin for showing labels on pie/doughnut charts -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        // Financial Metrics Stacked Bar Chart
        const financialCtx = document.getElementById('financialChart').getContext('2d');
        const financialData = {
            withdrawn: {{ $financialData['withdrawn'] ?? 0 }},
            stillToCollect: {{ $financialData['stillToCollect'] ?? 0 }},
            gain: {{ $financialData['gain'] ?? 0 }},
            totalEarned: {{ $financialData['totalEarned'] ?? 0 }},
            totalToCollect: {{ $financialData['totalToCollect'] ?? 0 }},
        };

        new Chart(financialCtx, {
            type: 'bar',
            data: {
                labels: [''],
                datasets: [
                    {
                        label: 'Riscosso',
                        data: [financialData.withdrawn],
                        backgroundColor: '#10b981',
                        borderRadius: 4,
                    },
                    {
                        label: 'Ancora da riscuotere',
                        data: [financialData.stillToCollect],
                        backgroundColor: '#f59e0b',
                        borderRadius: 4,
                    },
                    {
                        label: 'Guadagno',
                        data: [financialData.gain],
                        backgroundColor: '#3b82f6',
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 12 },
                            padding: 15,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': €' + context.parsed.x.toLocaleString('it-IT', {minimumFractionDigits: 2});
                            },
                            afterLabel: function(context) {
                                if (context.datasetIndex === 0 || context.datasetIndex === 1) {
                                    return 'Totale da riscuotere: €' + financialData.totalToCollect.toLocaleString('it-IT', {minimumFractionDigits: 2});
                                } else if (context.datasetIndex === 2) {
                                    return 'Totale Ricavi Vendite: €' + financialData.totalEarned.toLocaleString('it-IT', {minimumFractionDigits: 2});
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '€' + value.toLocaleString('it-IT');
                            }
                        }
                    },
                    y: {
                        stacked: true,
                    },
                },
            },
        });

        // Book Status Doughnut Chart
        const bookStatusCtx = document.getElementById('bookStatusChart').getContext('2d');
        const bookStatusData = {
            available: {{ $bookStatusData['available'] ?? 0 }},
            reserved: {{ $bookStatusData['reserved'] ?? 0 }},
            sold: {{ $bookStatusData['sold'] ?? 0 }},
            withdrawn: {{ $bookStatusData['withdrawn'] ?? 0 }},
            reclaim: {{ $bookStatusData['reclaim'] ?? 0 }},
            archived: {{ $bookStatusData['archived'] ?? 0 }},
        };

        new Chart(bookStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Disponibili', 'Prenotati', 'Venduti', 'Riscossi', 'Ritirati', 'Archiviati'],
                datasets: [{
                    data: [
                        bookStatusData.available,
                        bookStatusData.reserved,
                        bookStatusData.sold,
                        bookStatusData.withdrawn,
                        bookStatusData.reclaim,
                        bookStatusData.archived,
                    ],
                    backgroundColor: [
                        '#10b981', // green - available
                        '#f59e0b', // amber - reserved
                        '#3b82f6', // blue - sold
                        '#8b5cf6', // purple - withdrawn
                        '#ef4444', // red - reclaim
                        '#6b7280', // gray - archived
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { size: 12 },
                            padding: 15,
                        }
                    },
                },
            },
        });
    </script>
@endsection