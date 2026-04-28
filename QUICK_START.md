# 🚀 Quick Start Guide - Mercatino Libri

Guida rapida per iniziare e testare la home page!

---

## ⚡ Setup Veloce (5 minuti)

### 1️⃣ Installa Dipendenze
```bash
cd c:\Users\carme\Codice\mercatino
composer install
npm install
```

### 2️⃣ Configura Environment
```bash
# Se .env non esiste
copy .env.example .env

# Genera app key
php artisan key:generate
```

### 3️⃣ Avvia Server
```bash
# Terminal 1 - PHP Server
php artisan serve

# Terminal 2 - Vite (hot reload)
npm run dev
```

### 4️⃣ Visita la Home
```
http://localhost:8000
```

---

## ✅ Checklist Testing

### Desktop Testing
- [ ] Hero section visibile e centrato
- [ ] CTA buttons cliccabili
- [ ] Navigation menu funziona
- [ ] Tutte le sezioni scrollabili
- [ ] Footer visibile
- [ ] Link interni funzionano

### Mobile Testing (F12 → Toggle Device Toolbar)
- [ ] Menu hamburger appare
- [ ] Hero section leggibile
- [ ] Card stackate verticalmente
- [ ] Font leggibile
- [ ] Button cliccabili
- [ ] Nessun overflow orizzontale

### Interattività
- [ ] Mobile menu apribile
- [ ] FAQ accordion funziona
- [ ] Hover effects su card
- [ ] Link di scrolling funzionano
- [ ] Nessun errore console (F12)

### SEO (Ctrl+U per source)
- [ ] Meta description presente
- [ ] OG tags presenti
- [ ] H1, H2, H3 in ordine
- [ ] Alt text pronto

---

## 🎨 Modifiche Rapide

### Cambiare Colore Principale
**File**: `tailwind.config.js`
```javascript
colors: {
    primary: '#FF6B35',  // Arancione
    secondary: '#FF8C42', // Arancione scuro
}
```
Poi: `npm run build`

### Aggiungere Testo
**File**: `resources/views/home.blade.php`
```blade
<p>Il mio nuovo testo</p>
```
Salva e ricarica browser (hot reload)

### Aggiungere Icon Emoji
```blade
<span class="text-5xl">❤️</span> <!-- Emoji -->
```

---

## 📱 Browser Testing Consigliati

```
✓ Chrome (Desktop)
✓ Firefox (Desktop)
✓ Safari (Se Mac)
✓ Edge (Windows)
✓ Chrome Mobile (Android)
✓ Safari (iPhone)
```

Usa [BrowserStack](https://www.browserstack.com/) per testare gratuitamente!

---

## 🔧 Comandi Utili

```bash
# Build per produzione
npm run build

# Clear cache
php artisan cache:clear
php artisan view:clear

# Database migrations
php artisan migrate

# Reset database
php artisan migrate:fresh --seed

# Tinker (debug console)
php artisan tinker
```

---

## 📁 File Principali da Modificare

```
resources/views/home.blade.php          ← Contenuto home
resources/views/layouts/app.blade.php   ← Layout/Header/Footer
resources/css/app.css                   ← Stili personalizzati
tailwind.config.js                      ← Configurazione Tailwind
config/mercatino.php                    ← Impostazioni app
```

---

## 🐛 Se Qualcosa Non Funziona

### CSS/Tailwind non applica
```bash
npm run build
# oppure
npm run dev
```

### Mobile menu non funziona
1. Apri DevTools (F12)
2. Console tab
3. Cerca errori JavaScript
4. Verifica elementi HTML

### Errori Laravel
```bash
# Leggi i log
cat storage/logs/laravel.log

# oppure (Windows)
type storage\logs\laravel.log
```

---

## 📚 Documentazione Completa

Leggi questi file per info dettagliate:

1. **SETUP_GUIDE.md** - Setup completo e deployment
2. **ARCHITECTURE.md** - Struttura progetto e patterns
3. **HOMEPAGE_GUIDE.md** - Guida dettagliata home page
4. **IMPLEMENTATION_SUMMARY.md** - Riepilogo implementazione

---

## 🎯 Prossimi Step Consigliati

1. **Testing**
   - Testa su vari dispositivi
   - Verifica SEO con PageSpeed
   - Test browser compatibility

2. **Estensione**
   - Aggiungi pagina libri
   - Implementa autenticazione
   - Aggiungi sistema pagamenti

3. **Personalizzazione**
   - Cambia colori/font
   - Aggiungi loghi della scuola
   - Traduzci contenuti

---

## 💡 Tips Utili

- Use VS Code extension "Tailwind CSS IntelliSense" per autocompletion
- Usa "Laravel Artisan" extension per comodità
- Installa "Live Server" per anteprima HTML statica
- Usa Postman per testare API (quando pronte)

---

## 🔐 Sicurezza (importante)

```php
// Sempre usa Blade per escaping XSS
{{ $variabile }}  // ✓ Safe (escapato)
{!! $html !!}     // ✗ Unsafe (non escapato)

// Usa CSRF token in form
@csrf

// Hash password
Hash::make($password)
```

---

## 📞 Domande Frequenti

**Q: Come cambio il colore dei button?**  
A: Modifica `tailwind.config.js` oppure usa classe Tailwind direttamente.

**Q: Dove aggiungo immagini?**  
A: Copia in `public/images/` e referenzia con `/images/file.jpg`

**Q: Come aggiungo una nuova sezione?**  
A: Copia una sezione esistente in `home.blade.php` e modificala.

**Q: Come testo su iPhone reale?**  
A: Stesso network + `http://YOUR_IP:8000` dal browser del telefono.

---

## ✨ Fatto!

La tua home page è pronta!

```
✅ Modern Design
✅ Fully Responsive  
✅ SEO Optimized
✅ Clean Code
✅ Well Documented
```

Goditi! 🎉

---

**Version**: 1.0.0  
**Last Updated**: Aprile 2026
