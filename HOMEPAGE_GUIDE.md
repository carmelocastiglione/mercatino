# 🏠 Home Page - Documentazione e Guida

## Panoramica

La home page del Mercatino Libri è una landing page moderna e responsiva progettata per attrarre e guidare gli studenti verso le azioni principali: registrarsi, accedere, scoprire libri.

**File Principali:**
- View: `resources/views/home.blade.php`
- Layout: `resources/views/layouts/app.blade.php`
- CSS: `resources/css/app.css`
- Controller: `app/Http/Controllers/HomeController.php`

---

## 📐 Struttura della Home Page

### 1. Header/Navigation (Sticky)
```
📚 Logo | Links | CTA Buttons
```
- Logo: Emoji 📚 + "Mercatino Libri"
- Navigation: Caratteristiche, Come Funziona, FAQ
- CTA: Accedi, Registrati

**Responsive:**
- Desktop: Tutti gli elementi visibili
- Mobile: Menu hamburger collassato

### 2. Hero Section
```
┌────────────────────────────────┐
│  Main CTA Section              │
│  Titolo + Sottotitolo          │
│  Hero Image + Trust Badges     │
│  Primary CTAs                  │
└────────────────────────────────┘
```

**Elementi:**
- Titolo principale: "Compra e Vendi Libri Scolastici Usati"
- Sottotitolo: Description e value proposition
- Due CTA buttons: "Accedi" e "Registrati"
- Trust badges: Statistiche (500+ libri, 150+ studenti, €2K+ risparmiati)
- Hero image: Card con esempi di libri

### 3. Features Section
```
┌─────────────┬─────────────┬─────────────┐
│   Compra    │   Vendi     │  Community  │
│   Facilmente│ Libri Usati │  Studentesca│
└─────────────┴─────────────┴─────────────┘
```

3 cards con:
- Icona emoji
- Titolo
- Descrizione
- Lista benefici

### 4. How It Works Section
```
┌────────┐  ┌────────┐  ┌────────┐
│ Step 1 │→ │ Step 2 │→ │ Step 3 │
│ Reg.   │  │Compra/ │  │ Pagam. │
│        │  │ Vendi  │  │Consegna│
└────────┘  └────────┘  └────────┘
```

3 step con numero, titolo e dettagli

### 5. Testimonials Section
```
┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│ Testimonianza│ │ Testimonianza│ │ Testimonianza│
│ + Rating     │ │ + Rating     │ │ + Rating     │
│ + Utente     │ │ + Utente     │ │ + Utente     │
└──────────────┘ └──────────────┘ └──────────────┘
```

### 6. FAQ Section
```
❓ Domanda 1 ▼
❓ Domanda 2 ▼
❓ Domanda 3 ▼
❓ Domanda 4 ▼
❓ Domanda 5 ▼
```

Accordion interattivo con 5 domande frequenti

### 7. Final CTA Section
```
┌────────────────────────┐
│ Titolo Finale          │
│ Sottotitolo           │
│ [CTA] [CTA]           │
└────────────────────────┘
```

Gradient background con CTAs finali

### 8. Footer
```
┌────────────────────────────────────┐
│ Logo | Links | Links | Links | Links│
├────────────────────────────────────┤
│ Copyright © 2024                   │
└────────────────────────────────────┘
```

---

## 🎨 Personalizzazione Design

### Cambiare Colori

**Option 1: Via Tailwind Config**
```javascript
// tailwind.config.js
theme: {
  extend: {
    colors: {
      primary: '#667eea',    // Viola
      secondary: '#764ba2',  // Blu scuro
    }
  }
}
```

**Option 2: Via CSS Personalizzato**
```css
/* resources/css/app.css */
.gradient-hero {
    background: linear-gradient(135deg, #YOUR_COLOR_1 0%, #YOUR_COLOR_2 100%);
}
```

**Option 3: Inline nell'HTML**
```blade
<!-- resources/views/home.blade.php -->
<div class="bg-gradient-to-r from-red-600 to-blue-600">
    <!-- Contenuto -->
</div>
```

### Cambiare Font

```html
<!-- resources/views/layouts/app.blade.php -->
<link href="https://fonts.bunny.net/css?family=YOUR_FONT:400,500,600" rel="stylesheet">

<!-- resources/css/app.css -->
@theme {
    --font-sans: 'YOUR_FONT', sans-serif;
}
```

### Aggiungere/Modificare Icone Emoji

```blade
<!-- resources/views/home.blade.php -->
<span class="text-5xl">📚</span>  <!-- Libri -->
<span class="text-5xl">🛒</span>  <!-- Shopping Cart -->
<span class="text-5xl">💰</span>  <!-- Soldi -->
<span class="text-5xl">👥</span>  <!-- Community -->
```

---

## 📱 Responsive Design

### Breakpoints Tailwind
```
sm: 640px   (Mobile orizzontale)
md: 768px   (Tablet)
lg: 1024px  (Desktop piccolo)
xl: 1280px  (Desktop grande)
2xl: 1536px (Desktop extra large)
```

### Classe Responsive nella Home
```blade
<!-- Nasconde su mobile, mostra su lg+ -->
<div class="hidden lg:block">
    Content solo desktop
</div>

<!-- Grid 1 colonna su mobile, 3 su desktop -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div>Card 1</div>
    <div>Card 2</div>
    <div>Card 3</div>
</div>
```

---

## 🔍 SEO Ottimizzazione

### Meta Tags Implementati
```html
<title>Mercatino Libri Scolastici - Compra e Vendi Libri Usati</title>
<meta name="description" content="...">
<meta name="keywords" content="...">
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta name="robots" content="index, follow">
```

### Come Migliorare il SEO

1. **Heading Hierarchy** (già implementato)
   - H1: Titolo principale una sola volta
   - H2: Titoli sezioni
   - H3: Sottotitoli

2. **Alt Text per Immagini**
   ```blade
   <img src="logo.png" alt="Mercatino Libri - Piattaforma per libri usati">
   ```

3. **Structured Data** (da aggiungere)
   ```html
   <script type="application/ld+json">
   {
     "@context": "https://schema.org",
     "@type": "Organization",
     "name": "Mercatino Libri",
     "url": "https://mercatinolibri.it"
   }
   </script>
   ```

4. **Internal Links**
   ```blade
   <a href="/books">Sfoglia i nostri libri</a>
   ```

---

## 🔄 Interattività JavaScript

### Mobile Menu Toggle
```javascript
// Già implementato in app.blade.php
document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
});
```

### FAQ Accordion
```javascript
// Già implementato in home.blade.php
document.querySelectorAll('.faq-toggle').forEach(button => {
    button.addEventListener('click', function() {
        // Toggle FAQ content
    });
});
```

### Aggiungere Più Interattività

```javascript
// Smooth scroll su clic di link
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        target?.scrollIntoView({ behavior: 'smooth' });
    });
});
```

---

## 📝 Modificare Contenuti

### Cambiare Titolo Principale
```blade
<!-- resources/views/home.blade.php -->
<h1>Il tuo nuovo titolo</h1>
```

### Aggiungere Nuova Sezione Feature
```blade
<div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition p-8 border border-gray-100">
    <div class="text-5xl mb-4">🎉</div>
    <h3 class="text-2xl font-bold text-gray-900 mb-3">Titolo Feature</h3>
    <p class="text-gray-600 mb-6">Descrizione</p>
    <ul class="space-y-2 text-sm text-gray-600">
        <li>✓ Punto 1</li>
        <li>✓ Punto 2</li>
    </ul>
</div>
```

### Aggiungere Nuovo FAQ
```blade
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <button class="faq-toggle w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition" data-target="faq-6">
        <h3 class="font-semibold text-gray-900 text-left">Nuova domanda?</h3>
        <span class="text-gray-600 transition">▼</span>
    </button>
    <div id="faq-6" class="faq-content hidden px-6 py-4 bg-gray-50 border-t border-gray-200">
        <p class="text-gray-700">La risposta alla domanda</p>
    </div>
</div>
```

---

## 🚀 Performance

### Ottimizzazioni Implementate
✅ Lazy loading immagini  
✅ CSS minimizzato via Vite  
✅ Font preconnect per Google Fonts  
✅ Favicon SVG inline (leggero)  
✅ Zero JavaScript nei componenti critici  

### Ulteriori Ottimizzazioni
```blade
<!-- Lazy load immagini -->
<img src="image.jpg" loading="lazy" alt="Description">

<!-- Preload risorse critiche -->
<link rel="preload" as="image" href="hero.jpg">

<!-- Compress images -->
<!-- Usa strumenti come TinyPNG online -->
```

---

## 🧪 Testing Responsive

### Strumenti Consigliati
1. Chrome DevTools (F12 → Toggle Device Toolbar)
2. [Responsively App](https://responsively.app/)
3. [BrowserStack](https://www.browserstack.com/)

### Device da Testare
- iPhone 12 (390x844)
- Samsung Galaxy S21 (360x800)
- iPad Air (820x1180)
- Desktop 1920x1080
- Desktop 2560x1440 (ultra-wide)

---

## 📊 Analytics (Da Implementare)

### Google Analytics
```html
<!-- Aggiungi prima del </head> -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

### Event Tracking
```javascript
// Traccia click su CTA
document.querySelectorAll('[data-event="cta-click"]').forEach(button => {
    button.addEventListener('click', function() {
        gtag('event', 'cta_click', {
            'button_name': this.textContent,
            'page_location': window.location.href
        });
    });
});
```

---

## 🔗 Link Utili Interni

### Rotte Necessarie da Implementare
```php
// routes/web.php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
```

### Aggiorna i Link nella Home
```blade
<a href="{{ route('home') }}">Home</a>
<a href="{{ route('books.index') }}">Sfoglia Libri</a>
<a href="{{ route('login') }}">Accedi</a>
<a href="{{ route('register') }}">Registrati</a>
```

---

## 🐛 Troubleshooting

### Tailwind CSS non funziona
```bash
npm run dev  # Avvia il dev server
# oppure
npm run build  # Build per produzione
```

### Immagini non appaiono
- Verifica che siano in `public/images/`
- Usa path assoluto: `/images/file.jpg`
- Verifica permessi file

### Mobile menu non funziona
- Controlla che il JavaScript sia caricato
- Apri console (F12) e cerca errori
- Verifica gli IDs nel template

### Stili strani
- Cancella cache: `php artisan view:clear`
- Riavvia `npm run dev`
- Controlla specificity CSS

---

## 📚 Risorse Aggiuntive

- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Blade Template Guide](https://laravel.com/docs/blade)
- [Web Accessibility](https://www.w3.org/WAI/)
- [SEO Best Practices](https://developers.google.com/search)

---

**Versione**: 1.0.0  
**Ultimo aggiornamento**: Aprile 2026  
**Autore**: Mercatino Libri Team
