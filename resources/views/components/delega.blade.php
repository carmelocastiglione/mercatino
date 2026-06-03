@props([
    'school' => 'Scuola Superiore', 
    'referring_name' => 'COMITATO GENITORI DELLA SCUOLA', 'city' => 'Merate'])
    <style media="print">

        * {
            box-sizing: border-box;
        }

        .delega_container {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.25;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .delega_container {
            width: 100%;
        }

        .delega_title {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            margin-bottom: 5px;
        }

        .delega_subtitle {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 15px;
        }

        .delega_section {
            margin-bottom: 8px;
        }

        .delega_field {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 250px;
            height: 18px;
            vertical-align: bottom;
        }

        .delega_field-small {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 120px;
            height: 18px;
            vertical-align: bottom;
        }

        .delega_field-mini {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 60px;
            height: 18px;
            vertical-align: bottom;
        }

        .delega_center {
            text-align: center;
        }

        .delega {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 8px;
            margin: 15px 0;
        }

        .delega_container p {
            margin: 6px 0;
            text-align: justify;
        }

        .delega_signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .delega_signature-box {
            width: 40%;
            text-align: center;
        }

        .delega_signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
        }

        @media print {
            body {
                margin: 0;
            }

            .delega_container {
                page-break-inside: avoid;
            }
        }
    </style>

<div class="delega_container">

    <div class="delega_title">MERCATINO DEI LIBRI 2026</div>
    <div class="delega_subtitle">
        {{ html_entity_decode($school) }}<br>
        Anno scolastico 2025/2026
    </div>

    <h3 class="delega_center">LETTERA DI DELEGA</h3>

    <p>
        Il/La sottoscritto/a
        <span class="delega_field" style="width:400px;"></span>
    </p>

    <p>
        nato/a a <span class="delega_field-small"></span>
        il <span class="delega_field-small"></span>
    </p>

    <p>
        residente in Via
        <span class="delega_field" style="width:320px;"></span>
        n° <span class="delega_field-mini"></span>
    </p>

    <p>
        Cap <span class="delega_field-mini"></span>
        Città <span class="delega_field" style="width:220px;"></span>
        Provincia (<span class="delega_field-mini"></span>)
    </p>

    <p>
        Recapito telefonico <span class="delega_field-small"></span>
        Mail <span class="delega_field" style="width:250px;"></span>
    </p>

    <p>
        Documento d'identità
        <span class="delega_field-small"></span>
        numero <span class="delega_field-small"></span>
    </p>

    <p>
        rilasciato da
        <span class="delega_field" style="width:250px;"></span>
        il <span class="delega_field-small"></span>
    </p>

    <p>
        <em>(Allegare copia documento d'identità del delegante)</em>
    </p>

    <p>
        in relazione al ritiro di quanto dovuto, susseguente alla vendita dei libri
        consegnati in conto vendita a {{ html_entity_decode($referring_name) }}
    </p>

    <div class="delega">DELEGA</div>

    <p>
        Il Sig./Sig.ra
        <span class="delega_field" style="width:260px;"></span>
        nato/a a
        <span class="delega_field-small"></span>
    </p>

    <p>
        il <span class="delega_field-small"></span>
        nelle date 22/07/26 e 23/07/26, al ritiro sia del corrispettivo dei libri
        venduti associato al codice
        <span class="delega_field-small"></span>
        che al ritiro dei libri non venduti (se richiesto in fase di consegna)
        durante le attività del mercatino dei libri di testo usati, promosso dal {{ html_entity_decode($referring_name) }}.
    </p>

    <p>
        Il delegato dovrà presentare documento di identità al momento del ritiro.
    </p>

    <p style="margin-top:25px;">
        {{ html_entity_decode($city) }}, <span class="delega_field-small" style="width:180px;"></span> (data)
    </p>

    <div class="delega_signatures">
        <div class="delega_signature-box">
            <div class="delega_signature-line">
                Firma del delegante
            </div>
        </div>

        <div class="delega_signature-box">
            <div class="delega_signature-line">
                Firma del delegato
            </div>
        </div>
    </div>

</div>