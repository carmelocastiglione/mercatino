# CHANGELOG
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
- Prenotazione consegna (studente)
- Prenotazione vendita (studente)
- Acquisizione (staff) + delega
- Vendita (staff)
- Riscossione soldi (staff)
- Riscossione libri non venduti (staff)
- Resi (staff)

## Studente
- C'è un problema con le doppie notifiche agli studenti quando si vende un libro prenotato. Da risolvere
- Farsi approvare dai tecnici Viganò l'applicazione da Google Cloud Console per poter utilizzare il login via Google SSO
- Testare il login via Google SSO con studenti reali
- Controllare le pagine libri, vendite, acquisti e riscossioni
- Ampliare le notifiche (esempio: aggiungere reminder soldi da ritirare, etc)

## Staff
- Aggiungere filtri in vendite (es: libro o venditore) e acquisizioni (es: libro o acquirente)
- Aggiungere filtri in libri disponibili (es: libro, venditore)
- Aggiungere filtri in riscossioni
- Eliminare i dati di login quando si crea un utente e i link alle prenotazioni di consegne e vendite per chi non usufruisce dell'online
- Aggiungere delega nella ricevuta di acquisizione per permettere a genitori o altri di ritirare i soldi al posto dello studente
- Ridimensionare tutte le stampe
- Togliere informazioni sulla ricevuta e mettere il discaimer della scuola (acquisizioni)
- Aggiungere riepilogo stampa vendite
- Aggiungere consegna materiale scolastico (non libri)
- Export dati studenti, libri, vendite, consegne, etc
- Aggiungere delega nella ricevuta di acquisizione per permettere a genitori o altri di ritirare i libri al posto dello studente
- Sezione economica
- Nella sezione ritiri, aggiungere chi deve ancora ritirare i soldi dalle vendite (e inviare una notifica)
- Permettere di impostare una data e ora per la riconsegna soldi
- Migliorare visualizzazione pagine di ogni categoria
- Rivedere pagina riscossioni (ricerca nella pagine principale)
- Aggiungere filtri alle pagine
- Controllare storico utente (potrebbero esserci errori)
- Gestire il fatto che un libro può essere ritirato anche se prenotato per la vendita (esempio: se un libro è prenotato per la vendita ma non è stato ancora venduto, dovrebbe essere possibile ritirarlo)

## Admin
- Import dati da CSV (es. studenti, libri)
- Gestione fee scuole

## Miglioramenti Generali
- L'orario è quello del server, impostare fuso orario corretto
- Creare interfaccia comune per la stampa
- Controllare tutte le pagination
- Ampliare sistema di notifiche
- Implementare notifiche via email (Mailtrap: 150 email al giorno gratis, 4000 al mese)
- Per ogni libro, specificare il tipo (esempio: fisico, digitale). **Chiedere: chi decide la tipologia del libro? Lo staff, lo studente quando lo inserisce, o deciso al momento dell'import**?
- Ogni libro dovrebbe essere associato ad un anno scolastico
- Controllare che nella stampa ogni libro sia su una riga
- Generare codici a barre univoci per ogni consegna / vendita / acquisto
- Controllare che la versione mobile non abbia problemi di visualizzazione

## Ulteriori miglioramenti anni successivi
- Utilizzare un framework JS più robusto per la gestione del carrello (es. Vue o React)
- Gestione turni volontari