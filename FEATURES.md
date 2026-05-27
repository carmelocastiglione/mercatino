# Mercatino Libri - Caratteristiche Generali

## Dati di Login

### Istituto Tecnico Viganò
- **Studente 1**: `studente@issvigano.org` | Password: `mercatino`
- **Studente 2**: `studente2@issvigano.org` | Password: `mercatino`
- **Staff**: `staff@issvigano.org` | Password: `mercatino`

### Liceo Agnesi
- **Studente 1**: `studente@liceoagnesi.edu.it` | Password: `mercatino`
- **Studente 2**: `studente2@liceoagnesi.edu.it` | Password: `mercatino`
- **Staff**: `staff@liceoagnesi.edu.it` | Password: `mercatino`

### Admin
- **Admin**: `admin@issvigano.org` | Password: `mercatino`

## Libri nel Catalogo

### Italiano
- **I Promessi Sposi** - Alessandro Manzoni (ISBN: 978-8842652815) - Classe 3° - €15.99
- **Divina Commedia** - Dante Alighieri (ISBN: 978-8817154277) - Classe 4° - €18.50
- **Il Decameron** - Giovanni Boccaccio (ISBN: 978-8845903953) - Classe 3° - €14.99

### Matematica
- **Matematica Blu 2.0** - Massimo Bergamini (ISBN: 978-8884883529) - Classe 2° - €32.50

### Fisica
- **Fisica per i Licei Scientifici** - Claudio Romeni (ISBN: 978-8856864236) - Classe 3° - €38.00

### Chimica
- **La Chimica Organica** - Paula Yurkanis Bruice (ISBN: 978-8893853552) - Classe 4° - €45.99

### Storia
- **Storia Medioevale e Moderna** - Giorgio Candeloro (ISBN: 978-8817031639) - Classe 2° - €28.00

### Inglese
- **New Horizons** - Caroline Krantz (ISBN: 978-8808330900) - Classe 1° - €24.50

### Francese
- **Français Écho** - Jacky Girardet (ISBN: 978-2090334456) - Classe 1° - €22.99

### Educazione Civica
- **Etica Civica e Costituzione** - Maurizio Bocchini (ISBN: 978-8847236097) - Classe 2° - €19.50

## Panoramica
Mercatino Libri è una piattaforma web per la gestione di un mercato di libri usati scolastico, che consente agli studenti di consegnare, acquistare e vendere libri in modo semplice e organizzato. La piattaforma supporta molteplici ruoli (studenti, staff, admin) con funzionalità specializzate per ciascuno.

## Ruoli Utente

### Studenti
- Prenotare consegne di libri tramite interfaccia dedicata
- Visualizzare lo stato delle proprie consegne (in sospeso, approvate, rifiutate)
- Acquistare libri disponibili nel mercatino
- Vendere libri tramite il sistema
- Riscuotere il denaro dalle vendite
- Ricevere notifiche sui libri venduti
- Visualizzare storico transazioni

### Staff
- Dashboard con panoramica delle operazioni
- Gestione prenotazioni online di consegne (approvazione/rifiuto)
- Registro delle consegne acquisite dagli studenti
- Registrazione vendite al mercatino
- Gestione riscossioni (ritiri contanti e bonifici)
- Gestione resi/reclami di libri
- Visualizzazione libri disponibili nel catalogo
- Ricerca e storico completo degli utenti
- Configurazione date di consegna per la scuola
- Registrazione rapida di nuovi studenti

### Admin
- Gestione scuole (creazione, modifica, eliminazione)
- Gestione utenti della piattaforma
- Gestione catalogo libri scolastici
- Configurazione generale della piattaforma
- Accesso ai log di sistema

## Flussi Principali

### Ciclo Consegna Libri
1. Studente prenota consegna di libri
2. Seleziona data di consegna disponibile (se configurata)
3. Staff approva o rifiuta la consegna in presenza
4. Libri entrano nel catalogo disponibile
5. Alternativamente, i libri possono essere consegnati direttamente allo staff senza prenotazione, e lo staff li registra manualmente

### Ciclo Acquisizione
1. Staff registra acquisto di libri da uno studente
2. Il sistema rende disponibili i libri per la vendita
3. Studenti possono acquistare i libri disponibili

### Ciclo Vendita
1. Staff registra vendita libro
2. Libro viene marcato come venduto
3. Proprietario del libro riceve notifica "Libro venduto"
4. Denaro disponibile per riscossione

### Ciclo Riscossione
1. Studente richiede ritiro denaro dalle vendite
2. Staff processa la riscossione
3. Sistema traccia importo

## Funzionalità Principali

### Gestione Consegne
- Prenotazione consegne in batch
- Date di consegna configurabili per scuola
- Stato consegne (in sospeso, approvate, rifiutate)
- Visualizzazione dettagliata per consegna
- Approvazione/rifiuto bulk e singolo

### Catalogo Libri
- ISBN tracciato per ogni libro
- Titolo, autore, prezzi configurabili
- Condizione libro (eccellente, buona, accettabile, povera)
- Filtri per ricerca avanzata

### Vendite e Acquisizioni
- Registrazione vendite batch
- Tracciamento seller/buyer per ogni vendita
- Storico transazioni con dettagli
- Calcolo automatico totali
- Generazione ricevute

### Riscossioni
- Ritiro contante
- Tracciamento completo dei prelievi
- Storico riscossioni per utente

### Notifiche
- Sistema di notifiche in app
- Badge con conteggio notifiche non lette
- Notifiche automatiche su vendita libro
- Aggiornamento real-time (ogni 30 secondi)
- Segna come letto/non letto
- Eliminazione notifiche

### Storico Utente
- Ricerca utente per nome, cognome, email, codice
- Timeline cronologica di tutti i movimenti
- Visualizza consegne prenotate
- Visualizza acquisizioni
- Visualizza libri acquistati
- Visualizza libri venduti
- Visualizza resi/reclami
- Visualizza riscossioni

### Autorizzazione e Sicurezza
- Autenticazione robusta
- Ruoli basati su autorizzazione (Policy)
- Isolamento dati per scuola
- Protezione da accesso non autorizzato

### Ricerca e Filtri
- Ricerca libri per titolo, autore, ISBN
- Ricerca utenti per nome, cognome, email, codice
- Filtri per stato (acquisito, disponibile, venduto)
- Paginazione risultati

### Dashboard Personalizzate
- Dashboard studente con panoramica
- Dashboard staff con statistiche
- Dashboard admin con gestione globale
- Menu laterale contestuale per ruolo

## Dati Tracciati

### Per Libro
- Titolo, autore, ISBN
- Scuola di appartenenza
- Condizione
- Prezzo
- Data di inserimento

### Per Consegna
- Studente + data prenotazione
- Data consegna programmata
- Stato (in sospeso/approvato/rifiutato)
- Motivo rifiuto (se rifiutato)
- Numero libri

### Per Acquisizione
- Studente venditore
- Staff che ha registrato
- Data acquisizione
- Totale pagato
- Numero libri

### Per Vendita
- Libro venduto
- Buyer
- Staff che ha registrato
- Data vendita
- Prezzo

### Per Riscossione
- Studente
- Importo
- Metodo (contante/bonifico)
- Data
- Stato completamento

### Per Notifica
- Tipo (book_sold, ecc.)
- Utente destinatario
- Contenuto (titolo, descrizione)
- Stato lettura
- Data creazione

## Interfaccia Utente

### Template
- **Layout Staff**: Sidebar con navigazione, contenuto principale, tema bianco/grigio
- **Layout Studente**: Sidebar specifica per studente, accesso rapido a funzioni chiave
- **Layout Admin**: Interfaccia amministrativa con pannello di controllo

### Componenti
- Tabelle paginabili
- Form validati con feedback
- Dropdown per ricerca/selezione
- Badge e indicatori visivi
- Toast notifiche
- Modal per conferme
- Timeline per storico
- Icone e emoji per chiarezza visiva

## Integrazioni Tecniche

### Database
- PostgreSQL con proper relationships
- Migrations con versioning
- Seeders per dati test
- Indici per performance

### Backend
- Laravel framework (routing, ORM, policies, events)
- Events & Listeners per notifiche
- JSON APIs per frontend
- Validation completa
- Authorization checks

### Frontend
- Tailwind CSS v4 per styling
- Vanilla JavaScript per interattività
- Fetch API per AJAX
- Blade templating server-side

### Autenticazione
- Laravel Auth con middleware
- Ruoli (student, staff, admin)
- Google SSO (configurato)

## Statistiche Tracciabili

- Numero totale vendite
- Importo totale revenue
- Libri acquisiti vs venduti
- Numero consegne approvate/rifiutate
- Denaro riscosso per utente
- Notifiche non lette per utente

## Validazioni e Verifiche

- Carrello non vuoto prima di consegna
- Data consegna obbligatoria se disponibili
- Verifica appartenenza libro a scuola staff
- Autorizzazione scuola su operazioni
- ISBN unico per libro
- Email unica per utente
- Codice univoco per studente
- Prezzo valido per transazione

## Funzionalità Future (Potenziali)

- [ ] Export report (PDF, Excel)
- [ ] Statistiche avanzate e grafici
- [ ] Prenotazione libri
- [ ] Notifiche email automatiche
- [ ] App mobile
- [ ] Integrazione pagamenti online
- [ ] QR code per libri
- [ ] Review e rating libri
- [ ] Wishlist
- [ ] Sistema di ranking utenti

## Note Importanti

- La piattaforma è multi-scuola (ogni scuola ha dati isolati)
- Lo staff è limitato ai dati della propria scuola
- Gli admin hanno accesso globale
- Il sistema è auditabile (tutti i movimenti sono tracciati)
- Nessun dato viene cancellato, solo marcato come inattivo dove possibile