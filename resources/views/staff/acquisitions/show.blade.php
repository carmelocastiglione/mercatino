@extends('layouts.app-staff')

@section('title', 'Riepilogo Acquisizione')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Riepilogo Acquisizione</h1>
                <p class="text-gray-600 mt-2">Data: {{ $acquisition->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex flex-col md:flex-row gap-2 print:hidden">
                <button onclick="handlePrint()" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-center">
                    Stampa
                </button>
                <a href="{{ route('staff.acquisitions.index') }}" class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition text-center">
                    Torna alle acquisizioni
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <x-seller-information-card :acquisition="$acquisition" />

            <!-- Login Credentials Box (shown only if school has online sales and credentials are in session) -->
            @php
                $sellerEmail = $acquisition->seller->email;
                $sellerPassword = session()->pull('seller_login_credentials.password');
                $enableOnlineSales = auth()->user()->school->getSetting('enable_online_sales');
            @endphp
            
            @if($enableOnlineSales && $sellerPassword)
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg p-8 shadow-lg print:bg-white print:border-2 print:border-green-600">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Credenziali di Primo Accesso</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print-credentials-grid">
                        <div class="text-center flex flex-col items-center">
                            <p class="text-xs font-semibold text-gray-700 uppercase mb-2">Email</p>
                            <div class="bg-white border-2 border-green-300 rounded-lg p-4 w-full flex items-center justify-center">
                                <p class="font-mono text-lg text-green-700 break-all">{{ $sellerEmail }}</p>
                            </div>
                        </div>
                        
                        <div class="text-center flex flex-col items-center">
                            <p class="text-xs font-semibold text-gray-700 uppercase mb-2">Password</p>
                            <div class="bg-white border-2 border-green-300 rounded-lg p-4 w-full flex items-center justify-center">
                                <code id="password_field" class="font-mono text-lg text-green-700">{{ $sellerPassword }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <x-acquisition-books-table :acquisition="$acquisition" />

            <x-summary-footer :acquisition="$acquisition" />

            <x-information-note message="L'acquisizione è stata registrata correttamente. Stampa questo riepilogo e consegnalo allo studente come ricevuta." />

            <!-- Withdrawal and Return Notice -->
            <div class="bg-white border-l-4 border-blue-500 rounded-lg p-8 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Modalità di ritiro</h3>
                </div>
                <div class="text-gray-700 space-y-3 text-sm leading-relaxed">
                    <p>
                        Il ritiro dell'incasso e dell'invenduto avverrà nei giorni <strong>{{ $withdrawDatesText }}</strong> con il foglio e, eventualmente, muniti di delega.
                        Dopo tale data non ci saranno ulteriori modalità di ritiro di denaro e libri invenduti.
                        Non si risponde per eventuali furti.
                    </p>
                    @if($referringName)
                        <p>
                            Per info scrivere a <strong>{{ $referringName }}</strong>
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex gap-4 print:hidden">
                <a href="{{ route('staff.acquisitions.index') }}" class="flex-1 px-6 py-4 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition text-center">
                    Torna alle acquisizioni
                </a>
                <button onclick="handlePrint()" class="flex-1 px-6 py-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Stampa Riepilogo
                </button>
            </div>

            <!-- Delega - visible only in print -->
            <div class="hidden print:block" style="page-break-before: always; padding-top: 20px;">
                <x-delega :school="$school" :referring_name="$referringName" :city="$city" :withdraw-dates="$withdrawDates" />
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

            /* Print layout for credentials - side by side with equal height */
            .print-credentials-grid {
                display: flex !important;
                gap: 0.4rem !important;
            }

            .print-credentials-grid > div {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
            }

            .print-credentials-grid > div > div:last-child {
                flex-grow: 1 !important;
                display: flex !important;
                align-items: center !important;
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
                        <title>Riepilogo Acquisizione</title>
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
