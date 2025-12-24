@extends('layouts.app')

@section('title', 'Pengembalian')

@section('content')
    <x-page-header title="Pengembalian" />

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <button class="filter-tab {{ !request('filter') ? 'active' : '' }}" onclick="filterRentals('')">
            Semua
        </button>
        <button class="filter-tab {{ request('filter') === 'notstarted' ? 'active' : '' }}"
            onclick="filterRentals('notstarted')">
            Belum Diambil
        </button>
        <button class="filter-tab {{ request('filter') === 'today' ? 'active' : '' }}" onclick="filterRentals('today')">
            Jatuh Tempo Hari Ini
        </button>
        <button class="filter-tab {{ request('filter') === 'overdue' ? 'active' : '' }}" onclick="filterRentals('overdue')">
            Telat
        </button>
    </div>

    <!-- Search Box -->
    <div class="search-box" style="margin-bottom: 1rem;">
        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input type="text" id="search-input" class="form-control" placeholder="Cari nama atau telepon penyewa...">
    </div>

    <!-- Rentals List -->
    <div id="rentals-container">
        @forelse($rentals as $rental)
            <x-rental-card :rental="$rental" />
        @empty
            <x-empty-state icon="clipboard" title="Tidak ada penyewaan" message="Tidak ada data yang sesuai dengan filter" />
        @endforelse
    </div>

    <!-- Pagination -->
    @if($rentals->hasPages())
        <div style="margin-top: 1rem; display: flex; justify-content: center;">
            {{ $rentals->links() }}
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function filterRentals(filter) {
            const params = new URLSearchParams(window.location.search);
            if (filter) {
                params.set('filter', filter);
            } else {
                params.delete('filter');
            }
            window.location.search = params.toString();
        }

        document.getElementById('search-input').addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.item-card').forEach(card => {
                const name = card.querySelector('.item-name').textContent.toLowerCase();
                const phone = card.querySelector('.item-code').textContent.toLowerCase();
                card.style.display = (name.includes(query) || phone.includes(query)) ? 'flex' : 'none';
            });
        });
    </script>
@endpush