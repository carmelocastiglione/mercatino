# CHANGELOG
- Aggiunta la possibilità di eliminare un'acquisizione, con la condizione che i libri non siano stati ancora venduti, prenotati o ritirati dallo studente. In questi casi, non sarà possibile eliminare l'acquisizione e verrà mostrato un messaggio di errore.
- Aggiunta la possibilità di eliminare le transazioni di riscossione e ritiro libri non venduti. Adesso lo staff può eliminare una transazione di riscossione o ritiro libri non venduti.
- Aggiunta possibilità di eliminare le vendite effettuate, con la condizione che i libri non siano stati ancora ritirati dallo studente. Se un libro è stato già ritirato, non sarà possibile eliminare la vendita e verrà mostrato un messaggio di errore. Bisognerà prima eliminare la transazione
- Aggiunto disclaimer nella pagina di stampa acquisizioni con le date di ritiro.
- 2026-07-09: Aggiunto export CSV per tutti i libri acquisiti a prescindere dallo status.
- 2026-07-09: Nella pagina delle riscossioni dello staff, adesso è possibile con un solo pulsante riscuotere soldi e libri non venduti
- 2026-07-09: Aggiunto conteggio libri venduti e acquisiti nella pagina dello staff
- 2026-07-01: Sistemata visualizzazione di tutte le ricevute in versione mobile. Adesso le ricevute sono visualizzate correttamente su tutti i dispositivi, senza problemi di layout o di taglio dei contenuti.
- 2026-06-30: Aggiunto tooltip con spiegazione delle condizioni dei libri nella pagina di prenotazione consegna.
- 2026-06-30: Aggiunta faq registrazione utenti in home. Tolto disclaimer su possibile variazione del prezzo in fase di prenotazione consegna
- 2026-06-29: Aggiunta la possibilità di abilitare l'accesso online da una data e ora precisa per le scuole che hanno l'online abilitato. Questo permette di controllare quando gli studenti possono accedere al sistema per prenotare libri o effettuare vendite online. Per la prenotazione dei libri da acquistare è possibile impostare una data differente da quella di accesso, in modo da permettere agli studenti di accedere al sistema prima della data di prenotazione dei libri in vendita. Lo staff e gli admin possono accedere al sistema in qualsiasi momento, indipendentemente dalle date impostate.
- 2026-06-10: Aggiunti grafici pagina staff
- 2026-06-10: Aggiunta nella pagina riscossione la colonna "Libri da Ritirare" che mostra il numero di libri non venduti che lo studente deve ancora ritirare. Questa informazione è utile per lo staff per tenere traccia dei libri che devono essere restituiti agli studenti e per gestire meglio le operazioni di ritiro. E' stata aggiunta anche una pagina con la lista degli studenti che devono ancora ritirare soldi e/o libri.
- 2026-06-10: Riviste le pagine dello staff prenotazione consegne e prenotazione vendite, con filtro per status di approvazione
- 2026-06-09: Rivista la sistemazione grafica delle pagine studente prenotazioni consegne, prenotazione vendite, lista libri, acquisti, vendite e riscossioni
- 2026-06-09: Per il Viganò l'applicazione è stata approvata dai tecnici e adesso gli studenti possono loggarsi con il loro account scolastico senza inserire la password. La loro email deve essere già presente nel database per poter accedere. Per le altre scuole, invece, è ancora necessario inserire email e password per accedere.
- 2026-06-08: Riscritto da zero il sistema di notifiche all'utente
- 2026-06-07: Rivista logica assegnazione codice univoco agli utenti. Adesso il codice è univoco solo all'interno della stessa scuola, permettendo così di avere lo stesso codice per utenti di scuole diverse.
- 2026-06-07: Rivista procedura di approvazione prenotazione vendita
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

## Riassunto notifiche (NotificationService):
- notifyBookSold(): SaleController (Staff) store
- notifyBookReserved(): BookReservationController (Student) store
- notifyBookReservationCancelled(): BookReservationController (Student) destroy
- notifyBookReservationRejected(): BookReservationController (Staff) rejectMultiple

# TODO

## Studente
- Aggiunta scelta giorno ritiro libri prenotati per la consegna (es. se la scuola ha più giorni di ritiro, lo studente può scegliere il giorno in cui ritirare i libri)
- Controllare numero libri venduti dagli studenti nella pagina del suo riepilogo (forse c'è un bug)

- Quando uno studente prenota un libro, nel caso ci sia una lunga lista di libri disponibili, cosa fare?
- Controllare cosa succede se vendo un libro prenotato online, se viene tolto dalla vendita o se viene ritirato dallo studente

## Staff
- Notificare lo staff se una prenotazione è stata cancellata (serve? attualmente è impossibile visto che una prenotazione cancellata viene eliminata direttamente dal database)
- Creare pagina report (?) prenotazioni di acquisto per velocizzare la preparazione dei libri (non esiste già la lista delle prenotazioni?)
- Nella pagina riscossini, aggiungere conferme sui bottoni di riscossione e ritiro libri non venduti (es. "Sei sicuro di voler riscossione i soldi per i libri non venduti?") per evitare errori
- Prezzo fisso camice 8€ (come?)

- Aggiungere consegna materiale scolastico (non libri): se una scuola vuole consegnare anche materiale scolastico (es. camici) dovrebbe essere possibile farlo tramite una prenotazione simile a quella dei libri. Chiedere se serve
- Quando si approva una prenotazione di vendita, se si esce dalla schermata di vendita il libro rimane come "riservato" mentre la prenotazione risulta approvata. Da risolvere (esempio: se si approva una prenotazione di vendita ma non si completa la vendita, il libro dovrebbe tornare disponibile)
- Gestire il fatto che un libro può essere ritirato anche se prenotato per la vendita (esempio: se un libro è prenotato per la vendita ma non è stato ancora venduto, dovrebbe essere possibile ritirarlo)

## Admin
- Import dati da CSV (es. studenti, libri)
- Gestione commissioni scuole

## Miglioramenti Generali
- Aggiungere log nel codice per facilitare il debug e monitorare le operazioni
- Ampliare sistema di notifiche (scegliere quali)
- Nel layout, mettere le notifice di successo o errore sotto il titolo e non sopra (esempio: quando si approva una prenotazione, mostrare la notifica sotto il titolo "Prenotazioni")
- Per ogni libro, specificare il tipo (esempio: fisico, digitale). Chiedere: chi decide la tipologia del libro? Lo staff, lo studente quando lo inserisce, o deciso al momento dell'import? Inoltre, tipi differenti non hanno isbn differenti?
- Controllare che la versione mobile non abbia problemi di visualizzazione

## Ulteriori miglioramenti anni successivi
- Utilizzare un framework JS più robusto per la gestione del carrello (es. Vue o React)
- Gestione turni volontari
- Mettere anno scolastico dinamico nella delega
- Creare interfaccia comune per la stampa
- Fare in modo che le query siano filtrate automaticamente per scuola (es: quando cerco un libro, cerco solo tra i libri della mia scuola) per realizzare la multi-tenancy
- Aggiungere filtri per libro
- Ogni libro dovrebbe essere associato ad un anno scolastico (serve? alternativa: ogni anno ripulire tutto il database e ricominciare da zero)
- Aggiungere possibilità di contattare in qualche modo (notifica, mail) gli studenti che devono ancora ritirare libri o riscuotere soldi
- Implementare notifiche via email (Mailtrap: 150 email al giorno gratis, 4000 al mese)
- Pagina profilo dove modificare le proprie informazioni (quali? password e?)
- Controllare la generazione del codice per staff e admin: è necessaria? Adesso bisogna prima importare gli studenti e poi creare staff e admin, altrimenti il codice generato per staff e admin potrebbe essere uguale a quello di uno studente.