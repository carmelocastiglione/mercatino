@props(['message' => 'La tua prenotazione è stata registrata e sarà esaminata dallo staff della scuola. Riceverai una notifica quando lo stato dei tuoi libri cambierà. Grazie!'])

<div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-6 mt-8">
    <p class="text-sm font-bold text-blue-900 mb-3">ℹ️ INFORMAZIONI SULLA RICEVUTA</p>
    <p class="text-sm text-blue-800 leading-relaxed">
        {{ $message }}
    </p>
</div>
