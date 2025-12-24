@props(['rental'])

@php
    $isOverdue = $rental->isOverdue();
    $isNotStarted = $rental->isNotStarted();
    $isDueToday = $rental->isDueToday();
    $daysOverdue = $rental->days_overdue;
@endphp

<a href="{{ route('returns.show', $rental->id) }}" class="item-card">
    <div class="item-image"
        style="background: {{ $isOverdue ? 'rgba(239, 68, 68, 0.2)' : ($isNotStarted ? 'rgba(99, 102, 241, 0.2)' : 'var(--color-surface-alt)') }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
            style="color: {{ $isOverdue ? 'var(--color-danger)' : ($isNotStarted ? 'var(--color-primary)' : 'var(--color-text-muted)') }}">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
    </div>

    <div class="item-info">
        <div class="item-name">{{ $rental->renter_name }}</div>
        <div class="item-code">{{ $rental->renter_phone }}</div>
        <div class="item-price"
            style="color: {{ $isOverdue ? 'var(--color-danger)' : ($isNotStarted ? 'var(--color-primary)' : 'var(--color-text-muted)') }}">
            @if($isOverdue)
                Telat {{ $daysOverdue }} hari
            @elseif($isNotStarted)
                Mulai: {{ $rental->start_date->format('d M Y') }}
            @else
                Kembali: {{ $rental->end_date->format('d M Y') }}
            @endif
        </div>
    </div>

    <div class="item-status">
        @if($isOverdue)
            <span class="badge badge-overdue">Telat</span>
        @elseif($isNotStarted)
            <span class="badge"
                style="background: rgba(99, 102, 241, 0.15); color: var(--color-primary); border: 1px solid rgba(99, 102, 241, 0.3);">Booking</span>
        @elseif($isDueToday)
            <span class="badge badge-warning"
                style="background: rgba(245, 158, 11, 0.15); color: var(--color-warning); border: 1px solid rgba(245, 158, 11, 0.3);">Hari
                Ini</span>
        @else
            <span class="badge badge-active">Aktif</span>
        @endif
        <span style="font-size: 0.875rem; color: var(--color-text-muted); margin-top: 0.5rem;">
            {{ $rental->items->count() }} barang
        </span>
    </div>
</a>