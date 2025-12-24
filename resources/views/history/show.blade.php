@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <x-page-header title="Detail Transaksi" :backUrl="route('history.index')" />

    <!-- Transaction Status -->
    <div style="
        background: rgba(34, 197, 94, 0.15);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: var(--radius-lg);
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    ">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            style="color: var(--color-success); flex-shrink: 0;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <div style="font-weight: 600; color: var(--color-success);">Selesai</div>
            <div style="font-size: 0.875rem; color: var(--color-text-muted);">
                Dikembalikan: {{ $history->actual_return_date->format('d M Y, H:i') }}
            </div>
        </div>
    </div>

    <!-- Customer Info Card -->
    <div class="card" style="margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="
                width: 3rem;
                height: 3rem;
                border-radius: var(--radius-full);
                background: var(--color-surface-alt);
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="color: var(--color-text-muted);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <div style="font-weight: 600;">{{ $history->rental->renter_name }}</div>
                <div style="color: var(--color-text-muted); font-size: 0.875rem;">
                    {{ $history->rental->renter_phone }}
                </div>
            </div>
        </div>

        <div
            style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
            <div>
                <div style="color: var(--color-text-muted); font-size: 0.75rem; margin-bottom: 0.25rem;">Tanggal Sewa</div>
                <div style="font-weight: 500; font-size: 0.875rem;">{{ $history->rental->start_date->format('d M') }}</div>
            </div>
            <div>
                <div style="color: var(--color-text-muted); font-size: 0.75rem; margin-bottom: 0.25rem;">Jatuh Tempo</div>
                <div style="font-weight: 500; font-size: 0.875rem;">{{ $history->rental->end_date->format('d M') }}</div>
            </div>
            <div>
                <div style="color: var(--color-text-muted); font-size: 0.75rem; margin-bottom: 0.25rem;">Dikembalikan</div>
                <div
                    style="font-weight: 500; font-size: 0.875rem; color: {{ $history->penalty_fee > 0 ? 'var(--color-danger)' : 'inherit' }};">
                    {{ $history->actual_return_date->format('d M') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Items List -->
    <div class="card" style="margin-bottom: 1rem;">
        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Barang Disewa
            ({{ $history->rental->items->count() }})</h3>

        @foreach($history->rental->items as $item)
            <div style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 0;
                {{ !$loop->last ? 'border-bottom: 1px solid var(--color-border);' : '' }}
            ">
                <div>
                    <div style="font-weight: 500;">{{ $item->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--color-text-muted);">{{ $item->code }}</div>
                </div>
                <div style="font-weight: 500;">{{ 'Rp ' . number_format($item->daily_price, 0, ',', '.') }}/hari</div>
            </div>
        @endforeach
    </div>

    <!-- Price Breakdown -->
    <div class="price-summary" style="margin-bottom: 1.5rem;">
        <div class="price-row">
            <span>Durasi Sewa</span>
            <span class="amount">{{ $history->rental->start_date->diffInDays($history->rental->end_date) + 1 }} hari</span>
        </div>
        <div class="price-row">
            <span>Subtotal Sewa</span>
            <span class="amount">Rp {{ number_format($history->rental->total_price, 0, ',', '.') }}</span>
        </div>
        @if($history->penalty_fee > 0)
            <div class="price-row penalty">
                <span>Denda Keterlambatan</span>
                <span class="amount">+ Rp {{ number_format($history->penalty_fee, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="price-row total">
            <span>Total Dibayar</span>
            <span class="amount" style="color: var(--color-success);">Rp
                {{ number_format($history->final_total_price, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Print Receipt Button -->
    <button onclick="printReceipt()" class="btn btn-secondary" style="width: 100%;">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Cetak Struk
    </button>
@endsection

@push('scripts')
    <script>
        function printReceipt() {
            const printWindow = window.open('', '', 'width=400,height=600');

            printWindow.document.write(`
                <html>
                <head>
                    <title>Struk - {{ $history->rental->renter_name }}</title>
                    <style>
                        body { font-family: 'Courier New', monospace; padding: 20px; max-width: 300px; margin: 0 auto; }
                        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px dashed #000; padding-bottom: 10px; }
                        .header h1 { font-size: 18px; margin: 0; }
                        .header p { font-size: 12px; margin: 5px 0 0; }
                        .section { margin-bottom: 15px; }
                        .row { display: flex; justify-content: space-between; font-size: 12px; margin: 5px 0; }
                        .item { border-bottom: 1px dotted #ccc; padding: 5px 0; }
                        .total { border-top: 1px dashed #000; margin-top: 10px; padding-top: 10px; font-weight: bold; }
                        .footer { text-align: center; font-size: 10px; margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>{{ config('app.name') }}</h1>
                        <p>STRUK PENGEMBALIAN</p>
                    </div>

                    <div class="section">
                        <div class="row"><span>Nama:</span><span>{{ $history->rental->renter_name }}</span></div>
                        <div class="row"><span>Telepon:</span><span>{{ $history->rental->renter_phone }}</span></div>
                        <div class="row"><span>Tgl Sewa:</span><span>{{ $history->rental->start_date->format('d/m/Y') }}</span></div>
                        <div class="row"><span>Tgl Kembali:</span><span>{{ $history->actual_return_date->format('d/m/Y') }}</span></div>
                    </div>

                    <div class="section">
                        <div style="font-weight: bold; margin-bottom: 5px;">BARANG:</div>
                        @foreach($history->rental->items as $item)
                            <div class="item">
                                <div>{{ $item->name }}</div>
                                <div class="row"><span>{{ $item->code }}</span><span>Rp {{ number_format($item->daily_price, 0, ',', '.') }}/hari</span></div>
                            </div>
                        @endforeach
                    </div>

                    <div class="section">
                        <div class="row"><span>Subtotal:</span><span>Rp {{ number_format($history->rental->total_price, 0, ',', '.') }}</span></div>
                        @if($history->penalty_fee > 0)
                            <div class="row"><span>Denda:</span><span>Rp {{ number_format($history->penalty_fee, 0, ',', '.') }}</span></div>
                        @endif
                        <div class="row total"><span>TOTAL:</span><span>Rp {{ number_format($history->final_total_price, 0, ',', '.') }}</span></div>
                    </div>

                    <div class="footer">
                        <p>Terima kasih telah menggunakan layanan kami</p>
                        <p>{{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </body>
                </html>
            `);

            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }
    </script>
@endpush