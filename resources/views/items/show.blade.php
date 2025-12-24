@extends('layouts.app')

@section('title', $item->name)

@section('content')
    <x-page-header :title="$item->name" :backUrl="route('items.index')">
        <x-slot:action>
            <a href="{{ route('items.edit', $item->id) }}" class="btn btn-secondary btn-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
        </x-slot:action>
    </x-page-header>

    <!-- Status Badge -->
    <div style="margin-bottom: 1rem;">
        <span class="badge {{ $item->status === 'available' ? 'badge-available' : 'badge-rented' }}"
            style="font-size: 0.875rem; padding: 0.5rem 1rem;">
            {{ $item->status === 'available' ? '✓ Tersedia' : '⏳ Sedang Disewa' }}
        </span>
    </div>

    <!-- Item Image -->
    @if($item->image)
        <div style="margin-bottom: 1rem; border-radius: var(--radius-xl); overflow: hidden;">
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" style="width: 100%; height: auto;">
        </div>
    @endif

    <!-- Item Details Card -->
    <div class="card" style="margin-bottom: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div>
                <div style="color: var(--color-text-muted); font-size: 0.75rem; margin-bottom: 0.25rem;">Harga Sewa</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-primary);">
                    Rp {{ number_format($item->daily_price, 0, ',', '.') }}
                    <span style="font-size: 0.875rem; font-weight: 400; color: var(--color-text-muted);">/hari</span>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="color: var(--color-text-muted); font-size: 0.75rem; margin-bottom: 0.25rem;">Kode Barang</div>
                <div style="font-family: monospace; font-weight: 600;">{{ $item->code }}</div>
            </div>
        </div>

        @if($item->description)
            <div style="padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <div style="color: var(--color-text-muted); font-size: 0.75rem; margin-bottom: 0.5rem;">Deskripsi</div>
                <p style="color: var(--color-text); font-size: 0.875rem; line-height: 1.6;">{{ $item->description }}</p>
            </div>
        @endif
    </div>

    <!-- QR Code Section -->
    <div class="card" style="margin-bottom: 1rem;">
        <div style="text-align: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 600;">QR Code</h3>
            <p style="color: var(--color-text-muted); font-size: 0.75rem;">Scan kode ini untuk menambahkan ke transaksi sewa
            </p>
        </div>

        <x-qr-code :code="$item->code" />

        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
            <button id="download-qr" class="btn btn-secondary" style="flex: 1;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download
            </button>
            <button id="print-qr" class="btn btn-secondary" style="flex: 1;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak
            </button>
        </div>
    </div>

    <!-- Rental Statistics -->
    <div class="card">
        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Statistik Penyewaan</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div
                style="text-align: center; padding: 1rem; background: var(--color-surface-alt); border-radius: var(--radius-lg);">
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-primary);">
                    {{ $item->rentals_count ?? 0 }}
                </div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted);">Total Disewa</div>
            </div>
            <div
                style="text-align: center; padding: 1rem; background: var(--color-surface-alt); border-radius: var(--radius-lg);">
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-success);">Rp
                    {{ number_format($item->total_revenue ?? 0, 0, ',', '.') }}
                </div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted);">Total Pendapatan</div>
            </div>
        </div>
    </div>

    <!-- Delete Button -->
    @if($item->status === 'available')
        <form action="{{ route('items.destroy', $item->id) }}" method="POST" style="margin-top: 2rem;"
            onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" style="width: 100%;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus Barang
            </button>
        </form>
    @endif
@endsection

@push('scripts')
    <script>
        // Download QR Code
        document.getElementById('download-qr').addEventListener('click', function () {
            const qrContainer = document.querySelector('.qr-container');
            const svg = qrContainer.querySelector('svg');

            if (svg) {
                const svgData = new XMLSerializer().serializeToString(svg);
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();

                img.onload = function () {
                    canvas.width = 300;
                    canvas.height = 350;
                    ctx.fillStyle = 'white';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(img, 50, 25, 200, 200);
                    ctx.fillStyle = 'black';
                    ctx.font = 'bold 16px monospace';
                    ctx.textAlign = 'center';
                    ctx.fillText('{{ $item->code }}', canvas.width / 2, 270);
                    ctx.font = '12px sans-serif';
                    ctx.fillText('{{ $item->name }}', canvas.width / 2, 295);

                    const link = document.createElement('a');
                    link.download = 'QR-{{ $item->code }}.png';
                    link.href = canvas.toDataURL();
                    link.click();
                };

                img.src = 'data:image/svg+xml;base64,' + btoa(svgData);
            }
        });

        // Print QR Code
        document.getElementById('print-qr').addEventListener('click', function () {
            const qrContainer = document.querySelector('.qr-container');
            const printWindow = window.open('', '', 'width=400,height=500');

            printWindow.document.write(`
                    <html>
                    <head>
                        <title>Print QR - {{ $item->code }}</title>
                        <style>
                            body { font-family: sans-serif; text-align: center; padding: 20px; }
                            .qr-code { margin: 20px auto; }
                            .code { font-family: monospace; font-size: 18px; font-weight: bold; margin-top: 10px; }
                            .name { font-size: 14px; color: #666; margin-top: 5px; }
                        </style>
                    </head>
                    <body>
                        ${qrContainer.innerHTML}
                        <div class="name">{{ $item->name }}</div>
                    </body>
                    </html>
                `);

            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        });
    </script>
@endpush