# CHANGELOG
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

# TODO

## Stampa ricevute da controllare:
- Prenotazione consegna (studente FATTO)
- Prenotazione vendita (studente FATTO)
- Acquisizione (staff FATTO) + delega (FATTO)
- Vendita (staff FATTO)
- Riscossione soldi (staff FATTO)
- Riscossione libri non venduti (staff FATTO)
- Resi (staff FATTO)

## Studente
- C'è un problema con le doppie notifiche agli studenti quando si vende un libro prenotato. Da risolvere
- Farsi approvare dai tecnici Viganò l'applicazione da Google Cloud Console per poter utilizzare il login via Google SSO
- Testare il login via Google SSO con studenti reali
- Controllare le pagine libri, vendite, acquisti e riscossioni
- Ampliare le notifiche (esempio: aggiungere reminder soldi da ritirare, etc)
- Impostare condizione del libro di default per la prenotazione di consegna (es: come nuovo)
- Controllare il prezzo di prenotazione online

## Staff
- Togliere informazioni inutili sulla ricevuta
- Aggiungere sulla ricevuta, se le vendite online sono abilitate, le credenziali per accedere al profilo studente (es: codice venditore o codice prenotazione)
- Prenotazione consegne e acquisti: mettere prenotazioni approvate / rifiutate in lista index
- Aggiungere filtri in vendite (es: libro o venditore) e acquisizioni (es: libro o acquirente)
- Aggiungere filtri in libri disponibili (es: libro, venditore)
- Aggiungere filtri in riscossioni
- Controllare che la rendicontazione economica sia filtrata per la scuola di appartenenza
- Aggiungere consegna materiale scolastico (non libri)
- Export dati studenti, libri, vendite, consegne, etc
- Nella sezione ritiri, aggiungere chi deve ancora ritirare i soldi dalle vendite (e inviare una notifica)
- Gestire il fatto che un libro può essere ritirato anche se prenotato per la vendita (esempio: se un libro è prenotato per la vendita ma non è stato ancora venduto, dovrebbe essere possibile ritirarlo)

## Admin
- Import dati da CSV (es. studenti, libri)
- Gestione commissioni scuole

## Miglioramenti Generali
- L'orario è quello del server, impostare fuso orario corretto
- Controllare tutte le pagination
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