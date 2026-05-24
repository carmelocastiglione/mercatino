# Mercatino Studenti

Piattaforma di compravendita di libri scolastici per gli studenti dell'Istituto Tecnico Francesco Viganò e del Liceo Agnesi di Merate. Gli studenti possono vendere i loro libri usati e acquistare libri da altri compagni a prezzi vantaggiosi.

## Caratteristiche

- **Organizzazione per scuola** - Gestione separata di Agnesi e Viganò con dati isolati
- **Catalogo libri** - Browse e ricerca dinamica dei libri disponibili
- **Compravendita** - Listing di libri in vendita e gestione acquisti
- **Autenticazione studenti** - Accesso sicuro con email scolastica
- **Consegne** - Tracking delle richieste di consegna
- **Prelievi fondi** - Ritiro denaro dalle vendite
- **Pannello staff** - Gestione ordini, consegne e controversie per staff scolastico

## Setup & Avvio Rapido

### Prerequisiti

- **PHP 8.2+** - [Download](https://www.php.net/downloads)
- **Docker & Docker Compose** - Per PostgreSQL
- **Node.js & npm** - Per gestire assets e development server
- **Composer** - Per dipendenze PHP

### Clonare il Repository

```bash
git clone https://github.com/carmelocastiglione/mercatino.git
cd mercatino
```

### Installazione

#### 1. Installare dipendenze PHP
```bash
composer install
```

#### 2. Avviare il database con Docker
```bash
docker-compose up -d
```

Questo avvia PostgreSQL in background. Per verificare lo stato:
```bash
docker-compose ps
```

#### 3. Configurare l'ambiente
```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Preparare il database
```bash
php artisan migrate --seed
```

Questo crea le tabelle e popola con dati di test (2 scuole, utenti, catalogo libri).

#### 5. Installare dipendenze frontend
```bash
npm install
```

### Avvio del Progetto

Aprire **due terminali** nella cartella del progetto:

**Terminal 1 - Laravel Backend (Vite)**
```bash
npm run dev
```

**Terminal 2 - Laravel Development Server**
```bash
php artisan serve
```

L'applicazione sarà disponibile a: **http://localhost:8000**

## 🔧 Comandi Utili

```bash
# Eseguire migrazioni
php artisan migrate

# Eseguire seed (dati di test)
php artisan db:seed

# Reset database (attenzione: elimina tutto)
php artisan migrate:fresh --seed

# Build assets per produzione
npm run build

# Eseguire tests
php artisan test

# Lanciare linter/formatter
./vendor/bin/pint
```

## Struttura del Progetto

```
app/
├── Models/              # Modelli Eloquent (Book, User, BookSale, etc.)
├── Http/Controllers/    # Controller (Staff, Student)
└── Helpers/            # Funzioni helper

database/
├── migrations/         # Schema database
└── seeders/           # Dati di test

resources/
├── views/             # Blade templates
├── css/               # Tailwind CSS
└── js/                # JavaScript vanilla & Vite

config/
└── mercatino.php      # Configurazione app
```

## Utenti di Test

Dopo il seed, sono disponibili questi account. La password per tutti è: **mercatino**

### Scuola: Viganò
- **Studente** - studente@issvigano.org
- **Studente** - studente2@issvigano.org
- **Staff** - staff@issvigano.org

### Scuola: Liceo Agnesi
- **Studente** - studente@liceoagnesi.edu.it
- **Studente** - studente2@liceoagnesi.edu.it
- **Staff** - staff@liceoagnesi.edu.it

### Admin Globale
- **Admin** - admin@issvigano.org

## Contribuire

Vogliamo il tuo aiuto! Ecco come contribuire:

### Flusso di Contribuzione

1. **Fork il repository** su GitHub
   ```bash
   # Su GitHub, clicca "Fork" in alto a destra
   ```

2. **Clona il tuo fork**
   ```bash
   git clone https://github.com/TUO_USERNAME/mercatino.git
   cd mercatino
   ```

3. **Crea un branch per la tua feature**
   ```bash
   git checkout -b feature/nome-feature
   # Oppure per bugfix
   git checkout -b bugfix/nome-bug
   ```

4. **Fai i tuoi cambiamenti** e testa

5. **Commit i tuoi cambiamenti**
   ```bash
   git add .
   git commit -m "Descrizione chiara dei cambiamenti"
   ```

6. **Push al tuo fork**
   ```bash
   git push origin feature/nome-feature
   ```

7. **Apri una Pull Request** su GitHub
   - Vai a https://github.com/carmelocastiglione/mercatino
   - Clicca "New Pull Request"
   - Seleziona il tuo branch e descrivi i cambiamenti
   - Attendi review e feedback

### Linee Guida

- Testa i tuoi cambiamenti prima di aprire la PR
- Scrivi messaggi di commit chiari e descrittivi
- Commenta il codice se complesso
- Segui lo stile di codice del progetto
