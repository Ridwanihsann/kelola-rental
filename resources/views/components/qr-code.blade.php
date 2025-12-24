@props(['code', 'size' => 200])

<div class="qr-container">
    <div class="qr-code" style="width: {{ $size }}px; height: {{ $size }}px; margin: 0 auto;">
        {!! QrCode::size($size)->generate($code) !!}
    </div>
    <div class="qr-item-code">{{ $code }}</div>
</div>