@props(['item'])

<a href="{{ route('items.show', $item->id) }}" class="item-card">
    <div class="item-image">
        @if($item->image)
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
        @else
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
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