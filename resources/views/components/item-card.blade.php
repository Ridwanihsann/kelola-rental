@props(['item'])

<a href="{{ route('items.show', $item->id) }}" class="item-card">
    <div class="item-image">
        @if($item->image)
            {{-- Menggunakan Storage::url adalah cara paling standar Laravel --}}
            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}">
        @else
            {{-- SVG Placeholder --}}
        @endif
    </div>

    <div class="item-info">
        <div class="item-name">{{ $item->name }}</div>
        <div class="item-code">{{ $item->code }}</div>
        <div class="item-price">{{ 'Rp ' . number_format($item->daily_price, 0, ',', '.') }}/hari</div>
    </div>

    <div class="item-status">
        <span class="badge {{ $item->status === 'available' ? 'badge-available' : 'badge-rented' }}">
            {{ $item->status === 'available' ? 'Tersedia' : 'Disewa' }}
        </span>
    </div>
</a>