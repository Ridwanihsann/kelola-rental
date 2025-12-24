@extends('layouts.app')

@section('title', 'Riwayat')

@section('content')
    <x-page-header title="Riwayat Transaksi" />

    <!-- Date Filter -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
        <input type="date" id="date-from" class="form-control" value="{{ request('from') }}" style="flex: 1;">
        <span style="align-self: center; color: var(--color-text-muted);">—</span>
        <input type="date" id="date-to" class="form-control" value="{{ request('to') }}" style="flex: 1;">
        <button class="btn btn-secondary btn-icon" onclick="filterByDate()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
        </button>
    </div>

    <!-- Search Box -->
    <div class="search-box" style="margin-bottom: 1rem;">
        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input type="text" id="search-input" class="form-control" placeholder="Cari nama penyewa...">
    </div>

    <!-- Summary Stats -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem;">
        <div class="card" style="text-align: center; padding: 1rem;">
            <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-primary);">{{ $histories->total() }}</div>
            <div style="font-size: 0.75rem; color: var(--color-text-muted);">Total Transaksi</div>
        </div>
        <div class="card" style="text-align: center; padding: 1rem;">
            <div style="font-size: 1.25rem; font-weight: 700; color: var(--color-success);">Rp
                {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
            </div>
            <div style="font-size: 0.75rem; color: var(--color-text-muted);">Total Pendapatan</div>
        </div>
    </div>

    <!-- History List -->
    <div id="history-container">
        @forelse($histories as $history)
            <a href="{{ route('history.show', $history->id) }}" class="item-card">
                <div class="item-image" style="background: var(--color-surface-alt);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-text-muted);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>

                <div class="item-info">
                    <div class="item-name">{{ $history->rental->renter_name }}</div>
                    <div class="item-code">{{ $history->actual_return_date->format('d M Y') }}</div>
                    <div class="item-price">{{ $history->rental->items->count() }} barang</div>
                </div>

                <div class="item-status">
                    <span style="font-weight: 600; color: var(--color-success);">Rp
                        {{ number_format($history->final_total_price, 0, ',', '.') }}</span>
                    @if($history->penalty_fee > 0)
                        <span style="font-size: 0.75rem; color: var(--color-danger); margin-top: 0.25rem;">
                            + Denda
                        </span>
                    @endif
                </div>
            </a>
        @empty
            <x-empty-state icon="clipboard" title="Belum ada riwayat" message="Transaksi yang selesai akan muncul di sini" />
        @endforelse
    </div>

    <!-- Pagination -->
    @if($histories->hasPages())
        <div style="margin-top: 1rem; display: flex; justify-content: center;">
            {{ $histories->links() }}
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function filterByDate() {
            const from = document.getElementById('date-from').value;
            const to = document.getElementById('date-to').value;
            const params = new URLSearchParams(window.location.search);

            if (from) params.set('from', from);
            else params.delete('from');

            if (to) params.set('to', to);
            else params.delete('to');

            window.location.search = params.toString();
        }

        document.getElementById('search-input').addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.item-card').forEach(card => {
                const name = card.querySelector('.item-name').textContent.toLowerCase();
                card.style.display = name.includes(query) ? 'flex' : 'none';
            });
        });
    </script>
@endpush