@extends('layouts.app-student')

@section('title', 'Riepilogo Prenotazione Libri')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Riepilogo Prenotazione</h1>
                <p class="text-gray-600 mt-2">Data: {{ $batch->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex flex-col md:flex-row gap-2 print:hidden">
                <button onclick="handlePrint()" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Stampa
                </button>
                <a href="{{ route('student.book-reservations.index') }}" class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition text-center">
                    Torna alle prenotazioni
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">

            <x-student-information-card :batch="$batch" />

            <x-book-reservations-table :batch="$batch" />

            <x-summary-footer :batch="$batch" type="book-reservation" />

            <x-information-note message="La tua prenotazione è stata registrata. Stampa questa ricevuta e presentala al momento del ritiro. Grazie!" />

            <!-- Footer Actions -->
            <div class="flex gap-4 print:hidden">
                <a href="{{ route('student.book-reservations.index') }}" class="flex-1 px-6 py-4 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition text-center">
                    Torna alle prenotazioni
                </a>
                <button onclick="handlePrint()" class="flex-1 px-6 py-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Stampa Riepilogo
                </button>
            </div>
        </div>

        <!-- Print Styles -->
        <style media="print">
            @page {
                margin: 0.5cm;
                size: A4;
            }

            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                background: white;
            }

            body {
                overflow: visible !important;
            }

            /* Hide ALL navigation and sidebar elements */
            nav,
            aside,
            [class*="sidebar"],
            [class*="navbar"],
            [class*="header"],
            [class*="fixed"],
            [style*="fixed"],
            [style*="position: fixed"],
            .print\:hidden {
                display: none !important;
            }

            /* Hide body padding/margins that might show sidebar */
            body > * {
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Ensure main content area */
            main {
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            /* Content area */
            .max-w-4xl {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Optimize tables */
            table {
                page-break-inside: avoid;
                width: 100%;
            }

            tr {
                page-break-inside: avoid;
            }

            /* Reduce font sizes for print */
            h1 { font-size: 1.25rem !important; line-height: 1.3; }
            h2 { font-size: 1rem !important; line-height: 1.3; }
            h3 { font-size: 0.9rem !important; line-height: 1.3; }
            p { font-size: 0.85rem !important; line-height: 1.3; }
            td, th { font-size: 0.85rem !important; }
            span { font-size: 0.85rem !important; }

            /* Reduce spacing */
            .mb-8 { margin-bottom: 0.5rem !important; }
            .mb-6 { margin-bottom: 0.4rem !important; }
            .mb-4 { margin-bottom: 0.3rem !important; }
            .mb-3 { margin-bottom: 0.2rem !important; }
            .mb-2 { margin-bottom: 0.1rem !important; }
            .mt-2 { margin-top: 0.1rem !important; }
            .mt-4 { margin-top: 0.2rem !important; }

            .space-y-8 > * + * { margin-top: 0.5rem !important; }
            .space-y-6 > * + * { margin-top: 0.4rem !important; }
            .space-y-4 > * + * { margin-top: 0.3rem !important; }

            /* Reduce padding */
            .p-8 { padding: 0.5rem !important; }
            .p-6 { padding: 0.4rem !important; }
            .p-4 { padding: 0.3rem !important; }
            .px-6 { padding-left: 0.4rem !important; padding-right: 0.4rem !important; }
            .py-4 { padding-top: 0.2rem !important; padding-bottom: 0.2rem !important; }
            .py-3 { padding-top: 0.2rem !important; padding-bottom: 0.2rem !important; }

            /* Reduce gap */
            .gap-6 { gap: 0.4rem !important; }
            .gap-4 { gap: 0.3rem !important; }

            /* Table optimization */
            table {
                margin-bottom: 0.3rem !important;
            }

            th, td {
                padding: 0.3rem !important;
            }

            /* Remove background colors and rounded corners from badges only */
            span.rounded-full {
                background-color: transparent !important;
                border-radius: 0 !important;
            }

            /* Clean colors for print */
            * {
                color: black !important;
                background: white !important;
                box-shadow: none !important;
                border-color: black !important;
            }

            a {
                text-decoration: none !important;
                color: black !important;
            }
        </style>

        <script>
            function handlePrint() {
                // Apri una nuova tab
                const printWindow = window.open('', '_blank');
                
                // Ottieni il contenuto principale (escluso header con pulsanti)
                const content = document.querySelector('.max-w-4xl').innerHTML;
                
                // Scrivi l'HTML nella nuova tab con stili
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Riepilogo Prenotazione Libri</title>
                        <script src="https://cdn.tailwindcss.com"><\/script>
                        <style>
                            * {
                                box-sizing: border-box;
                            }
                            html, body {
                                width: 100%;
                                height: 100%;
                            }
                            body {
                                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                                padding: 40px;
                                background: white;
                                margin: 0;
                            }
                            .print-content {
                                max-width: 900px;
                                margin: 0 auto;
                            }
                            /* Forza SEMPRE il layout desktop indipendentemente dal viewport */
                            .grid {
                                display: grid !important;
                            }
                            .grid-cols-1 {
                                grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
                            }
                            .md\\:grid-cols-3 {
                                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                            }
                            .md\\:col-span-2 {
                                grid-column: span 2 !important;
                            }
                            .md\\:grid-cols-2 {
                                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                            }
                            .flex {
                                display: flex !important;
                            }
                            .flex-col {
                                flex-direction: column;
                            }
                            .md\\:flex-row {
                                flex-direction: row !important;
                            }
                            .md\\:items-center {
                                align-items: center !important;
                            }
                            .md\\:justify-between {
                                justify-content: space-between !important;
                            }
                            @media print {
                                body {
                                    padding: 0;
                                }
                                @page {
                                    margin: 0.5cm;
                                    size: A4;
                                }
                                * {
                                    color: black !important;
                                    background: white !important;
                                    box-shadow: none !important;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="print-content">
                            ${content}
                        </div>
                        <script>
                            // Forza il layout desktop modificando il DOM
                            setTimeout(() => {
                                // Trova il header e nascondilo
                                const header = document.querySelector('.mb-8');
                                if (header && header.classList.contains('flex')) {
                                    header.style.display = 'none';
                                }
                                
                                // Forza flex-row per i container flex
                                document.querySelectorAll('.md\\\\:flex-row').forEach(el => {
                                    el.style.flexDirection = 'row';
                                });
                                
                                // Forza grid columns per i grid
                                document.querySelectorAll('.md\\\\:grid-cols-3').forEach(el => {
                                    el.style.gridTemplateColumns = 'repeat(3, minmax(0, 1fr))';
                                });
                                
                                document.querySelectorAll('.md\\\\:grid-cols-2').forEach(el => {
                                    el.style.gridTemplateColumns = 'repeat(2, minmax(0, 1fr))';
                                });
                                
                                document.querySelectorAll('.md\\\\:col-span-2').forEach(el => {
                                    el.style.gridColumn = 'span 2';
                                });
                                
                                window.print();
                            }, 500);

                            // Chiudi la tab quando il dialogo di stampa si chiude (stampa o annulla)
                            window.addEventListener('afterprint', () => {
                                window.close();
                            });
                        <\/script>
                    </body>
                    </html>
                `);
                
                printWindow.document.close();
            }
        </script>
    </div>
@endsection
