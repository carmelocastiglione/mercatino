@extends('layouts.app-staff')

@section('title', 'Riepilogo Acquisizione')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Riepilogo Acquisizione</h1>
                <p class="text-gray-600 mt-2">Data: {{ $acquisition->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-2 print:hidden">
                <button onclick="handlePrint()" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Stampa
                </button>
                <a href="{{ route('staff.acquisitions.index') }}" class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition">
                    Torna alle acquisizioni
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <x-seller-information-card :acquisition="$acquisition" />

            <x-acquisition-books-table :acquisition="$acquisition" />

            <x-summary-footer :acquisition="$acquisition" />

            <x-information-note message="L'acquisizione è stata registrata correttamente. Stampa questo riepilogo e consegnalo allo studente come ricevuta." />

            <div class="flex gap-4 print:hidden">
                <a href="{{ route('staff.acquisitions.index') }}" class="flex-1 px-6 py-4 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition text-center">
                    Torna alle acquisizioni
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

            /* Compact print layout - smaller fonts and less padding */
            h1 {
                font-size: 1.25rem !important;
                margin-bottom: 0.5rem !important;
            }

            h2 {
                font-size: 1rem !important;
                margin-bottom: 0.25rem !important;
            }

            h3 {
                font-size: 0.9rem !important;
                margin-bottom: 0.25rem !important;
            }

            p, span, td, th, li {
                font-size: 0.85rem !important;
                line-height: 1.3 !important;
            }

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
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Riepilogo Acquisizione</title>
                        <script src="https://cdn.tailwindcss.com"><\/script>
                        <style>
                            body {
                                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                                padding: 40px;
                                background: white;
                            }
                            .print-content {
                                max-width: 900px;
                                margin: 0 auto;
                            }
                            @media print {
                                body {
                                    padding: 0;
                                }
                                @page {
                                    margin: 0.5cm;
                                    size: A4;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="print-content">
                            ${content}
                        </div>
                        <script>
                            // Nascondi il header con i pulsanti quando la tab si apre
                            setTimeout(() => {
                                const header = document.querySelector('.mb-8.flex');
                                if (header) {
                                    header.style.display = 'none';
                                }
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
