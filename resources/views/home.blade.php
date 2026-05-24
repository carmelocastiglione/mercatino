@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="gradient-hero text-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="animate-fade-in-up text-center mx-auto max-w-2xl lg:col-span-2 lg:text-center">
                    <h1 class="text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                        Compra e vendi libri scolastici usati
                    </h1>
                    <p class="text-xl mb-8 text-gray-100 leading-relaxed">
                        Il mercatino online della tua scuola. Risparmia fino al 50% sui libri di testo e guadagna vendendo i tuoi libri usati.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('login') }}" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-bold hover:shadow-lg transition text-center">
                            Accedi
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Perché vendere i tuoi libri usati?</h2>
                <p class="text-xl text-gray-600">Le soluzioni migliori per studenti consapevoli</p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1: Comprare -->
                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl hover:scale-105 transition-all duration-300 p-8 border border-gray-100">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-4xl mb-6">
                            🛒
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Compra facilmente</h3>
                        <p class="text-gray-600 mb-6">
                            Scegli tra centinaia di libri usati dagli studenti della tua scuola. Prezzi convenienti e trasparenti.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600 w-full text-left">
                            <li>✓ Fino al 50% di sconto</li>
                            <li>✓ Testi in buone condizioni</li>
                            <li>✓ Hai subito i libri che ti servono</li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 2: Vendere -->
                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl hover:scale-105 transition-all duration-300 p-8 border border-gray-100">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-4xl mb-6">
                            💰
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Vendi libri usati</h3>
                        <p class="text-gray-600 mb-6">
                            Liberati dei libri che non ti servono più e guadagna vendendo i tuoi libri. Processo semplice e immediato.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600 w-full text-left">
                            <li>✓ Evita sprechi</li>
                            <li>✓ Non devi preoccuparti della vendita</li>
                            <li>✓ Ritira i tuoi soldi al termine</li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 3: Sostenibilità -->
                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl hover:scale-105 transition-all duration-300 p-8 border border-gray-100">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-4xl mb-6">
                            ♻️
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Sostenibilità</h3>
                        <p class="text-gray-600 mb-6">
                            Riduci i rifiuti e aiuta l'ambiente riusando libri. Ogni acquisto sostenibile è un gesto consapevole per il futuro.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600 w-full text-left">
                            <li>✓ Economia circolare</li>
                            <li>✓ Riduci la carta e i rifiuti</li>
                            <li>✓ Aiuta l'ambiente</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="gradient-hero text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Come Funziona</h2>
                <p class="text-xl text-purple-100">Tre semplici step per iniziare</p>
            </div>

            <!-- Steps -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white text-purple-600 rounded-full font-bold text-2xl mb-6 mx-auto">
                        1
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Prenota la consegna</h3>
                    <p class="text-gray-100 mb-4">
                        Prenota la consegna dei tuoi libri online <span class="font-bold">(solo per studenti del Viganò)</span>.
                    </p>
                    <div class="bg-white/20 p-4 rounded-lg text-sm text-white">
                        <p>✓ Scegli i libri da vendere <br> ✓ Stampa la ricevuta <br> ✓ Porta i libri al mercatino</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white text-purple-600 rounded-full font-bold text-2xl mb-6 mx-auto">
                        2
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Porta o acquista</h3>
                    <p class="text-gray-100 mb-4">
                        Porta i tuoi libri da vendere il giorno stabilito o recati al mercatino per comprare.
                    </p>
                    <div class="bg-white/20 p-4 rounded-lg text-sm text-white">
                        <p>✓ Consegna i libri <br> ✓ Compra i libri che ti servono <br> ✓ Avrai i tuoi soldi al termine delle vendite</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white text-purple-600 rounded-full font-bold text-2xl mb-6 mx-auto">
                        3
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Ritira i tuoi soldi</h3>
                    <p class="text-gray-100 mb-4">
                        Ritira i tuoi soldi dalla vendita dei libri in modo semplice e trasparente.
                    </p>
                    <div class="bg-white/20 p-4 rounded-lg text-sm text-white">
                        <p>✓ Ritiro facile <br> ✓ Trasparente <br> ✓ Veloce</p>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center mt-16">
                <p class="text-purple-100 mb-6">Pronto a iniziare il tuo percorso nel mercatino?</p>
                <a href="{{ route('login') }}" class="inline-block bg-purple-500 text-white px-8 py-4 rounded-lg font-bold hover:bg-purple-700 transition text-lg border-2 border-white">
                    Inizia Ora - È Gratis!
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Domande Frequenti</h2>
                <p class="text-xl text-gray-600">Troverai tutte le risposte qui</p>
            </div>

            <!-- FAQ Accordion -->
            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <button class="faq-toggle w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition" data-target="faq-1">
                        <h3 class="font-semibold text-gray-900 text-left">Come mi registro?</h3>
                        <span class="text-gray-600 transition">▼</span>
                    </button>
                    <div id="faq-1" class="faq-content hidden px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700">
                            La registrazione è disabilitata. Se sei uno studente del Viganò, puoi accedere ai servizi del mercatino usando il login tramite l'account della scuola. 
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <button class="faq-toggle w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition" data-target="faq-2">
                        <h3 class="font-semibold text-gray-900 text-left">É sicuro vendere/comprare libri?</h3>
                        <span class="text-gray-600 transition">▼</span>
                    </button>
                    <div id="faq-2" class="faq-content hidden px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700">
                            Sì! Tutte gli scambi sono gestiti dal comitato genitori della scuola.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <button class="faq-toggle w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition" data-target="faq-3">
                        <h3 class="font-semibold text-gray-900 text-left">Quali metodi di pagamento accettate?</h3>
                        <span class="text-gray-600 transition">▼</span>
                    </button>
                    <div id="faq-3" class="faq-content hidden px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700">
                            Esclusivamente contanti. I soldi vengono consegnati al termine del mercatino, dopo la vendita dei libri.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <button class="faq-toggle w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition" data-target="faq-4">
                        <h3 class="font-semibold text-gray-900 text-left">Quanto costa vendere i libri?</h3>
                        <span class="text-gray-600 transition">▼</span>
                    </button>
                    <div id="faq-4" class="faq-content hidden px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700">
                            Ad ogni acquisto o vendita è applicata una piccola commissione per coprire i costi di gestione del mercatino.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <button class="faq-toggle w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition" data-target="faq-5">
                        <h3 class="font-semibold text-gray-900 text-left">Come posso consegnare i libri venduti?</h3>
                        <span class="text-gray-600 transition">▼</span>
                    </button>
                    <div id="faq-5" class="faq-content hidden px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700">
                            Puoi consegnare i libri al mercatino il giorno stabilito. Se hai prenotato la consegna online, porta con te la ricevuta stampata per facilitare il processo.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="gradient-hero text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-6">Vuoi collaborare con noi?</h2>
            <p class="text-xl mb-10 text-purple-100 max-w-2xl mx-auto">
                Richiedi di far parte del team del mercatino come volontario. Siamo sempre alla ricerca di persone appassionate che vogliono contribuire a rendere il mercatino un successo per la nostra comunità scolastica.
            </p>

            <p class="text-xl text-purple-100 max-w-2xl mx-auto">
                Sai programmare? Hai esperienza con Laravel o Tailwind? Il codice di questo sito è open source su <a href="https://github.com/carmelocastiglione/mercatino" class="text-white underline">GitHub</a>! Contribuisci con miglioramenti, nuove funzionalità o semplicemente segnalando bug. Ogni contributo è benvenuto!
            </p>
        </div>
    </section>

    <!-- FAQ Toggle Script -->
    <script>
        document.querySelectorAll('.faq-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const target = document.getElementById(this.dataset.target);
                const isHidden = target.classList.contains('hidden');
                
                // Close all other FAQs
                document.querySelectorAll('.faq-content').forEach(content => {
                    content.classList.add('hidden');
                });
                document.querySelectorAll('.faq-toggle span').forEach(span => {
                    span.style.transform = 'rotate(0deg)';
                });
                
                // Toggle current FAQ
                if (isHidden) {
                    target.classList.remove('hidden');
                    this.querySelector('span').style.transform = 'rotate(180deg)';
                }
            });
        });
    </script>
@endsection
