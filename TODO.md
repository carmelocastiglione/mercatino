# CHANGELOG
- 2026-06-07: Creazione pagina Esportazione Dati per permettere allo staff di esportare i dati in formato CSV per analisi esterne o backup.
- 2026-06-06: Integrazione della sezione "Storico Utente" in "Utenti"
- 2026-06-06: Aggiunta sezione "Utenti" nella dashboard dello staff per visualizzare e gestire gli utenti registrati, con funzionalità di ricerca e filtro per nome, email, e ruolo. Questa sezione permette allo staff di avere una panoramica completa degli utenti e di gestire facilmente le loro informazioni.
- 2026-06-06: Per le scuole con online abilitato, se si crea un nuovo utente dalla schermata di consegna o vendita, i dati di login (email e password) vengono adesso mostrati in un box informativo sulla ricevuta. Questo permette allo staff di comunicare facilmente le credenziali agli studenti e semplifica la gestione degli account.
- 2026-06-06: La condizione di default di un libro prenotato per la consegna è adesso "come nuovo" per semplificare l'inserimento da parte degli studenti. Questo permette di velocizzare il processo di prenotazione e ridurre gli errori di inserimento.
- 2026-06-06: Aggiunto filtro di ricerca per codice transazione nelle pagine dello staff. Rimosso il filtro di ricerca per nome studente (lasciato solo il cognome) per semplificare l'interfaccia e ridurre i tempi di ricerca.
- 2026-06-05: Generazione di un codice a barre univoco per ciascuna ricevuta di acquisizione, vendita, consegna, etc. Il codice a barre è basato su un formato EAN13 e viene visualizzato sia nella pagina di dettaglio dell'acquisizione/vendita/consegna che sulla ricevuta stampata. Questo permette una tracciabilità più efficiente e una gestione semplificata delle operazioni tramite scansione del codice a barre.
- 2026-06-04: Correzione fuso orario per tutte le date (adesso è impostato su Europe/Rome)
- 2026-06-04: Aggiunti filtri di ricerca nelle pagine
- 2026-06-04: Eliminato il box informativo presente nella ricevuto dalla stampa
- 2026-06-04: Se la scuola non usufruisce dell'online, vengono nascoste le funzionalità di prenotazione consegna e ritiro e non vengono più mostrati i dati di login quando si crea un nuovo utente che consegna o vende.
- 2026-06-03: Aggiunte impostazioni specifiche per la scuola nel menu staff: generali, date consegna, date ritiro
- 2026-06-03: Aggiunta gestione delle impostazioni specifiche per ogni scuola (es. abilita online etc)
- 2026-06-03: Inserita lettera di delega al momento della stampa dell'acquisizione dei libri
- 2026-06-03: Riduzione spazi vuoti e compattamento pagine di stampa
- 2026-06-03: Miglioramenti alla pagina storico utente: adesso è possibile accedere ai dettagli di ogni movimento (con informazioni più dettagliate e link alle pagine correlate).
- 2026-06-02: Miglioramenti alla pagina resi
- 2026-06-02: Fix dashboard pagina riscossioni
- 2026-06-02: Migliorato la riscossione dei libri non venduti. Viene adesso generata la ricevuta
- 2026-06-02: Migliorato la riscossione dei soldi per i libri non venduti. Inserito un riepilogo e la stampa della ricevuta
- 2026-06-02: Corretta la funzione di ritiro libri non venduti. Aggiunta lista libri ritirati
- 2026-06-02: Aggiunto prezzo di vendita nella pagina dei libri disponibili
- 2026-06-02: Corretto l'errore nel prezzo di vendita
- 2026-06-01: Nella pagina di vendite, sono visualizzate le vendite per "sessione" come per acquisizioni ed è possibile accedere ai dettagli della singola vendita. Nella pagina di creazione è stato inserito un doppio filtro libro / codice venditore e adesso si può scegliere da una lista con più informazioni. Tolta la colonna pagamento.
- 2026-06-01: Nella pagina acquisizioni, di default il libro è impostato come "come nuovo" per semplificare l'inserimento. 
- 2026-06-01: Miglioramenti per la visualizzazione delle ricevute
- 2026-05-31: Aggiunta possibilità di segnalare problemi tecnici tramite un form dedicato. 
- 2026-05-30: Aggiunta gestione prenotazioni libri (sia lato studente che staff). Aggiunta notifiche per prenotazioni. Aggiornamento dashboard con nuove statistiche.
- 2026-05-30: Miglioramenti alla versione mobile
- 2026-05-27: Versione iniziale con funzionalità base per gestione libri, consegne, acquisizioni, vendite, riscossioni, e storico utente. Implementazione autenticazione e autorizzazione. Creazione dashboard personalizzate per studenti e staff.

## Riassunto stampa ricevute:
- Prenotazione consegna (studenti): book_delivery_batches
- Prenotazione vendita (studenti): book_reservation_batches
- Acquisizione: acquisitions
- Vendita: book_sales_batches
- Riscossione soldi: withdrawal_batches
- Riscossione libri non venduti: pickup_batches
- Resi: reclaims

## Riassunto pagine di ricerca:
- Prenotazione consegna: staff/deliveries
- Prenotazione vendita: staff/reservations
- Acquisizioni: staff/acquisitions
- Vendite: staff/sales
- Riscossioni: staff/withdrawals
- Resi: staff/reclaims
- Storico utente: staff/storico

# TODO

## Studente
- C'è un problema con le doppie notifiche agli studenti quando si vende un libro prenotato. Da risolvere
- Farsi approvare dai tecnici Viganò l'applicazione da Google Cloud Console per poter utilizzare il login via Google SSO
- Testare il login via Google SSO con studenti reali
- Controllare le pagine libri, vendite, acquisti e riscossioni
- Ampliare le notifiche (esempio: aggiungere reminder soldi da ritirare, etc)
- Controllare cosa succede se vendo un libro prenotato online, se viene tolto dalla vendita o se viene ritirato dallo studente

## Staff
- Controllare che nell'approvazione delle prenotazioni vendite, possa continuare anche se non ho libri approvati o rifiutati (es: se ho 3 prenotazioni e ne approvo solo 1, dovrei poter continuare con la vendita di quel libro senza dover approvare o rifiutare le altre 2 perché magari il libro sarà disponibile più avanti)
- Prenotazione consegne e acquisti: mettere prenotazioni approvate / rifiutate in lista index
- Aggiungere filtri per libro
- Controllare che sia possibile generare codice ripetuti tra scuole diverse (es: MR.0001 in scuola A e MR.0001 in scuola B) -> è necessario?
- Aggiungere consegna materiale scolastico (non libri)
- Nella sezione ritiri, aggiungere chi deve ancora ritirare i soldi dalle vendite (e inviare una notifica)
- Gestire il fatto che un libro può essere ritirato anche se prenotato per la vendita (esempio: se un libro è prenotato per la vendita ma non è stato ancora venduto, dovrebbe essere possibile ritirarlo)

## Admin
- Import dati da CSV (es. studenti, libri)
- Gestione commissioni scuole

## Miglioramenti Generali
- Pagina profilo dove modificare le proprie informazioni
- Ampliare sistema di notifiche
- Implementare notifiche via email (Mailtrap: 150 email al giorno gratis, 4000 al mese)
- Per ogni libro, specificare il tipo (esempio: fisico, digitale). **Chiedere: chi decide la tipologia del libro? Lo staff, lo studente quando lo inserisce, o deciso al momento dell'import**?
- Ogni libro dovrebbe essere associato ad un anno scolastico
- Generare codici a barre univoci per ogni consegna / vendita / acquisto
- Controllare che la versione mobile non abbia problemi di visualizzazione

## Ulteriori miglioramenti anni successivi
- Utilizzare un framework JS più robusto per la gestione del carrello (es. Vue o React)
- Gestione turni volontari
- Mettere anno scolastico dinamico nella delega
- Creare interfaccia comune per la stampa
- Fare in modo che i le query siano filtrate automaticamente per scuola (es: quando cerco un libro, cerco solo tra i libri della mia scuola) per realizzare la multi-tenancy