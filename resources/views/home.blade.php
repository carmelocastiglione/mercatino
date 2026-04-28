@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="gradient-hero text-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="animate-fade-in-up">
                    <h1 class="text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                        Compra e Vendi Libri Scolastici Usati
                    </h1>
                    <p class="text-xl mb-8 text-gray-100 leading-relaxed">
                        Il mercatino online della tua scuola. Risparmia fino al 50% sui libri di testo e guadagna vendendo i tuoi libri usati.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="/login" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-bold hover:shadow-lg transition text-center">
                            🔐 Accedi
                        </a>
                    </div>

                    <!-- Trust Badges -->
                    <div class="mt-12 flex items-center space-x-6">
                        <div class="text-sm">
                            <div class="font-bold text-2xl">500+</div>
                            <div class="text-purple-100">Libri Disponibili</div>
                        </div>
                        <div class="w-px h-12 bg-purple-400"></div>
                        <div class="text-sm">
                            <div class="font-bold text-2xl">150+</div>
                            <div class="text-purple-100">Studenti Attivi</div>
                        </div>
                        <div class="w-px h-12 bg-purple-400"></div>
                        <div class="text-sm">
                            <div class="font-bold text-2xl">€2K+</div>
                            <div class="text-purple-100">Risparmiati</div>
                        </div>
                    </div>
                </div>

                <!-- Right Hero Image -->
                <div class="relative animate-fade-in-up delay-200 hidden lg:block">
                    <div class="bg-purple-400 rounded-2xl p-8 shadow-2xl transform hover:scale-105 transition">
                        <div class="bg-white rounded-lg p-6">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between bg-purple-50 p-4 rounded">
                                    <div>
                                        <p class="font-semibold text-gray-900">Matematica 4ª</p>
                                        <p class="text-sm text-gray-600">€8,50</p>
                                    </div>
                                    <span class="text-2xl">📐</span>
                                </div>
                                <div class="flex items-center justify-between bg-blue-50 p-4 rounded">
                                    <div>
                                        <p class="font-semibold text-gray-900">Italiano</p>
                                        <p class="text-sm text-gray-600">€6,00</p>
                                    </div>
                                    <span class="text-2xl">📖</span>
                                </div>
                                <div class="flex items-center justify-between bg-green-50 p-4 rounded">
                                    <div>
                                        <p class="font-semibold text-gray-900">Scienze</p>
                                        <p class="text-sm text-gray-600">€7,25</p>
                                    </div>
                                    <span class="text-2xl">🧪</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wave Divider -->
        
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Perché scegliere Mercatino Libri?</h2>
                <p class="text-xl text-gray-600">Le soluzioni migliori per studenti consapevoli</p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1: Comprare -->
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition p-8 border border-gray-100">
                    <div class="text-5xl mb-4">🛒</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Compra Facilmente</h3>
                    <p class="text-gray-600 mb-6">
                        Sfoglia migliaia di libri usati dagli studenti della tua scuola. Prezzi convenienti e trasparenti, nessuna sorpresa.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>✓ Fino al 50% di sconto</li>
                        <li>✓ Testi in buone condizioni</li>
                        <li>✓ Consegna rapida</li>
                    </ul>
                </div>

                <!-- Feature 2: Vendere -->
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition p-8 border border-gray-100">
                    <div class="text-5xl mb-4">💰</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Vendi Libri Usati</h3>
                    <p class="text-gray-600 mb-6">
                        Libera spazio dalle vecchie edizioni e guadagna vendendo i tuoi libri. Processo semplice e immediato.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>✓ Iscrizione gratuita</li>
                        <li>✓ Carica foto in pochi click</li>
                        <li>✓ Guadagna subito</li>
                    </ul>
                </div>

                <!-- Feature 3: Community -->
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition p-8 border border-gray-100">
                    <div class="text-5xl mb-4">👥</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Community Studentesca</h3>
                    <p class="text-gray-600 mb-6">
                        Connettiti con altri studenti della tua scuola. Scambia libri, fai amicizie, condividi esperienze.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>✓ Sicuro e affidabile</li>
                        <li>✓ Metodi di pagamento vari</li>
                        <li>✓ Supporto attivo 24/7</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Come Funziona</h2>
                <p class="text-xl text-gray-600">Tre semplici step per iniziare</p>
            </div>

            <!-- Steps -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-primary text-white rounded-full font-bold text-2xl mb-6 mx-auto">
                        1
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Registrati</h3>
                    <p class="text-gray-600 mb-4">
                        Crea un account in pochi secondi con la tua email della scuola. Verificati istantaneamente.
                    </p>
                    <div class="bg-purple-50 p-4 rounded-lg text-sm text-purple-900">
                        <p>✓ Email <br> ✓ Password <br> ✓ Dati scuola</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-primary text-white rounded-full font-bold text-2xl mb-6 mx-auto">
                        2
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Compra o Vendi</h3>
                    <p class="text-gray-600 mb-4">
                        Sfoglia la nostra libreria o carica i tuoi libri da vendere. Descrivi le condizioni e fissa il prezzo.
                    </p>
                    <div class="bg-blue-50 p-4 rounded-lg text-sm text-blue-900">
                        <p>✓ Ricerca libri <br> ✓ Filtri avanzati <br> ✓ Carichi foto</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-primary text-white rounded-full font-bold text-2xl mb-6 mx-auto">
                        3
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Scambia & Paga</h3>
                    <p class="text-gray-600 mb-4">
                        Contatta i venditori, negozia se necessario, e completa l'acquisto. Pagamento sicuro garantito.
                    </p>
                    <div class="bg-green-50 p-4 rounded-lg text-sm text-green-900">
                        <p>✓ Chat diretta <br> ✓ Pagamenti sicuri <br> ✓ Consegna</p>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center mt-16">
                <p class="text-gray-600 mb-6">Pronto a iniziare il tuo percorso nel mercatino?</p>
                <a href="/register" class="inline-block bg-gradient-primary text-white px-8 py-4 rounded-lg font-bold hover:shadow-lg transition text-lg">
                    Inizia Ora - È Gratis! 🚀
                </a>
            </div>
        </div>
    </section>

    <!-- Student Testimonials Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Cosa Dicono gli Studenti</h2>
                <p class="text-xl text-gray-600">Storie di risparmio e guadagno</p>
            </div>

            <!-- Testimonials Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl p-8 border border-purple-100">
                    <div class="flex items-center mb-4">
                        <div class="flex space-x-1">
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-4">
                        "Ho risparmiato €80 sui libri del primo anno! Qualità eccellente e consegna velocissima. Consigliato!"
                    </p>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-primary rounded-full flex items-center justify-center text-white font-bold">
                            M
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Marco T.</p>
                            <p class="text-sm text-gray-600">Classe 3ª Liceo</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-8 border border-green-100">
                    <div class="flex items-center mb-4">
                        <div class="flex space-x-1">
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-4">
                        "Ho venduto i miei libri usati e guadagnato €120! Facilissimo: foto, carico e... soldi in banca!"
                    </p>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                            G
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Giulia P.</p>
                            <p class="text-sm text-gray-600">Classe 4ª Scientifico</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl p-8 border border-pink-100">
                    <div class="flex items-center mb-4">
                        <div class="flex space-x-1">
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                            <span class="text-yellow-400">⭐</span>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-4">
                        "La community della mia scuola è fantastica! Oltre a risparmiare, ho conosciuto nuovi compagni di classe."
                    </p>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                            S
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Sofia M.</p>
                            <p class="text-sm text-gray-600">Classe 2ª Classico</p>
                        </div>
                    </div>
                </div>
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
                            La registrazione è semplicissima! Basta cliccare su "Registrati", inserire la tua email della scuola, scegliere una password e completare i tuoi dati. Riceverai un'email di conferma e potrai iniziare subito a comprare e vendere.
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
                            Sì! Tutti gli utenti sono verificati tramite email della scuola. I pagamenti avvengono tramite sistemi sicuri (PayPal, Stripe) e i dati personali sono sempre protetti. Abbiamo un team di moderatori che verifica le transazioni.
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
                            Accettiamo PayPal, Carte di Credito/Debito, Bonifico Bancario e Satispay. Per chi vende, i soldi vengono accreditati nel conto di tua scelta entro 2-3 giorni lavorativi.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <button class="faq-toggle w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition" data-target="faq-4">
                        <h3 class="font-semibold text-gray-900 text-left">Quanto costa aprire un account?</h3>
                        <span class="text-gray-600 transition">▼</span>
                    </button>
                    <div id="faq-4" class="faq-content hidden px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700">
                            L'iscrizione è completamente gratuita! Non ci sono costi nascosti. Quando vendi un libro, applichiamo una commissione del 10% sul prezzo finale per mantenere il servizio.
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
                            Puoi scegliere il metodo di consegna: incontro in scuola, spedizione con Poste Italiane, o ritiro presso i nostri punti di raccolta. I costi di spedizione sono a carico dell'acquirente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="gradient-hero text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-6">Inizia a Risparmiare Oggi!</h2>
            <p class="text-xl mb-10 text-purple-100 max-w-2xl mx-auto">
                Unisciti a centinaia di studenti che hanno già scoperto il modo più conveniente per comprare e vendere libri scolastici usati.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/login" class="bg-purple-500 text-white px-8 py-4 rounded-lg font-bold hover:bg-purple-700 transition text-lg border-2 border-white">
                    Accedi al Tuo Account
                </a>
            </div>

            <p class="mt-8 text-purple-100">
                💬 Domande? Contattaci a: <strong>info@mercatinolibri.it</strong>
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
