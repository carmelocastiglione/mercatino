@extends('layouts.app-staff')

@section('title', 'Dettagli Reso')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Dettagli Reso</h1>
                <p class="text-gray-600 mt-2">Data: {{ $reclaim->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-2 print:hidden">
                <button onclick="handlePrint()" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Stampa
                </button>
                <a href="{{ route('staff.reclaims.index') }}" class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition">
                    Torna ai resi
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <x-reclaim-information-card :reclaim="$reclaim" />

            <x-reclaim-book-card :reclaim="$reclaim" />

            <x-reclaim-summary-card :reclaim="$reclaim" />

            <x-information-note message="Se approvi il reso, il libro sarà rimesso in vendita e il compratore riceverà la conferma. Se rifiuti, libro e vendita restano invariati." />

            <div class="flex gap-4 print:hidden">
                <a href="{{ route('staff.reclaims.index') }}" class="flex-1 px-6 py-4 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition text-center">
                    Torna ai resi
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

            body > * {
                margin: 0 !important;
                padding: 0 !important;
            }

            main {
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            .max-w-4xl {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            table {
                page-break-inside: avoid;
                width: 100%;
            }

            tr {
                page-break-inside: avoid;
            }

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
                const printWindow = window.open('', '_blank');
                const content = document.querySelector('.max-w-4xl').innerHTML;
                
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Dettagli Reso</title>
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
                            setTimeout(() => {
                                const header = document.querySelector('.mb-8.flex');
                                if (header) {
                                    header.style.display = 'none';
                                }
                                window.print();
                            }, 500);

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
