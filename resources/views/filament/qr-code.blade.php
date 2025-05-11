@if ($recordExists && $qrCodeUrl)
    <img src="{{ $qrCodeUrl }}" alt="QR Code" class="mx-auto">
@elseif ($recordExists)
    <p class="text-center">QR Code Generado</p>
@else
    <p class="text-center">QR Code se generará al guardar</p>
@endif