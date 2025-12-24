@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')
    <x-page-header title="Barang">
        <x-slot:action>
            <a href="{{ route('items.create') }}" class="btn btn-primary btn-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </a>
        </x-slot:action>
    </x-page-header>

    <!-- Search Box -->
    <div class="search-box" style="margin-bottom: 1rem;">
        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input type="text" id="search-input" class="form-control" placeholder="Cari nama atau kode barang..."
            value="{{ request('search') }}">
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <button class="filter-tab {{ !request('status') ? 'active' : '' }}" data-status="">
            Semua
        </button>
        <button class="filter-tab {{ request('status') === 'available' ? 'active' : '' }}" data-status="available">
            Tersedia
        </button>
        <button class="filter-tab {{ request('status') === 'rented' ? 'active' : '' }}" data-status="rented">
            Disewa
        </button>
    </div>

    <!-- Items List -->
    <div id="items-container">
        @forelse($items as $item)
            <x-item-card :item="$item" />
        @empty
            <x-empty-state icon="box" title="Belum ada barang" message="Tambahkan barang pertama Anda untuk mulai menyewakan">
                <a href="{{ route('items.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    Tambah Barang
                </a>
            </x-empty-state>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($items->hasPages())
        <div style="margin-top: 1rem; display: flex; justify-content: center;">
            {{ $items->links() }}
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // Search functionality
        const searchInput = document.getElementById('search-input');
        let searchTimeout;

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const params = new URLSearchParams(window.location.search);
                if (this.value) {
                    params.set('search', this.value);
                } else {
                    params.delete('search');
                }
                window.location.search = params.toString();
            }, 500);
        });

        // Filter tabs
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function () {
                const params = new URLSearchParams(window.location.search);
                const status = this.dataset.status;

                if (status) {
                    params.set('status', status);
                } else {
                    params.delete('status');
                }

                window.location.search = params.toString();
            });
        });
    </script>
@endpush