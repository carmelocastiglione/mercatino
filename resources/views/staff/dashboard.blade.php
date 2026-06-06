@extends('layouts.app-staff')

@section('title', 'Dashboard Staff')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Dashboard Staff</h1>
        <p class="text-gray-600 mt-2">Panoramica della gestione delle consegne</p>
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

    </div>
@endsection
