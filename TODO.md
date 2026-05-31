# CHANGELOG
- 2026-05-30: Aggiunta gestione prenotazioni libri (sia lato studente che staff). Aggiunta notifiche per prenotazioni. Aggiornamento dashboard con nuove statistiche.
- 2026-05-30: Miglioramenti alla versione mobile
- 2026-05-27: Versione iniziale con funzionalità base per gestione libri, consegne, acquisizioni, vendite, riscossioni, e storico utente. Implementazione autenticazione e autorizzazione. Creazione dashboard personalizzate per studenti e staff.

# TODO

## Studente
- Farsi approvare dai tecnici Viganò l'applicazione da Google Cloud Console per poter utilizzare il login via Google SSO
- Testare il login via Google SSO con studenti reali
- Migliorare la pagina di riepilogo e di stampa con informazioni più chiare e un layout più ordinato
- Ampliare le notifiche (esempio: aggiungere reminder soldi da ritirare, etc)
- Controllare la modifica del libro prenotato

## Staff
- Aggiungere riepilogo stampa vendite
- Aggiungere consegna materiale scolastico (non libri)
- Export dati studenti, libri, vendite, consegne, etc
- Aggiungere delega nella ricevuta di acquisizione per permettere a genitori o altri di ritirare i libri al posto dello studente
- Sezione economica
- Nella sezione ritiri, aggiungere chi deve ancora ritirare i soldi dalle vendite (e inviare una notifica)
- Permettere di impostare una data e ora per la riconsegna soldi
- Migliorare visualizzazione pagine di ogni categoria (rinominare le pagina iniziale in home, creare un index per la lista completa)
- Rivedere pagina riscossioni (ricerca nella pagine principale)
- Aggiungere filtri alle pagine
- Controllare storico utente (potrebbero esserci errori)

## Admin
- Import dati da CSV (es. studenti, libri)
- Gestione fee scuole

## Miglioramenti Generali
- Creare interfaccia comune per la stampa
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