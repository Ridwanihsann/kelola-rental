@extends('layouts.app')

@section('title', 'Detail Penyewaan')

@php
    $isOverdue = $rental->isOverdue();
    $isNotStarted = $rental->isNotStarted();
    $isDueToday = $rental->isDueToday();
    $daysOverdue = $rental->days_overdue;
@endphp

@section('content')
    <x-page-header title="Detail Penyewaan" :backUrl="route('returns.index')" />

    <!-- Status Alert -->
    @if($isOverdue)
        <div style="
                                    background: rgba(239, 68, 68, 0.15);
                                    border: 1px solid rgba(239, 68, 68, 0.3);
                                    border-radius: var(--radius-lg);
                                    padding: 1rem;
                                    margin-bottom: 1rem;
                                    display: flex;
                                    align-items: center;
                                    gap: 0.75rem;
                                ">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                style="color: var(--color-danger); flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <div style="font-weight: 600; color: var(--color-danger);">Telat {{ $daysOverdue }} hari</div>
                <div style="font-size: 0.875rem; color: var(--color-text-muted);">Mohon segera dikembalikan</div>
            </div>
        </div>
    @elseif($isNotStarted)
        <div style="
                                    background: rgba(99, 102, 241, 0.15);
                                    border: 1px solid rgba(99, 102, 241, 0.3);
                                    border-radius: var(--radius-lg);
                                    padding: 1rem;
                                    margin-bottom: 1rem;
                                    display: flex;
                                    align-items: center;
                                    gap: 0.75rem;
                                ">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                style="color: var(--color-primary); flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <div>
                <div style="font-weight: 600; color: var(--color-primary);">Belum Diambil</div>
                <div style="font-size: 0.875rem; color: var(--color-text-muted);">Dijadwalkan mulai
                    {{ $rental->start_date->format('d M Y') }}
                </div>
            </div>
        </div>
    @elseif($isDueToday)
        <div style="
                                    background: rgba(245, 158, 11, 0.15);
                                    border: 1px solid rgba(245, 158, 11, 0.3);
                                    border-radius: var(--radius-lg);
                                    padding: 1rem;
                                    margin-bottom: 1rem;
                                    display: flex;
                                    align-items: center;
                                    gap: 0.75rem;
                                ">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                style="color: var(--color-warning); flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <div style="font-weight: 600; color: var(--color-warning);">Jatuh Tempo Hari Ini</div>
                <div style="font-size: 0.875rem; color: var(--color-text-muted);">Mohon dikembalikan hari ini</div>
            </div>
        </div>
    @else
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
                <div style="font-weight: 600; color: var(--color-success);">Sedang Disewa</div>
                <div style="font-size: 0.875rem; color: var(--color-text-muted);">Batas kembali:
                    {{ $rental->end_date->format('d M Y') }}
                </div>
            </div>
        </div>
    @endif

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
                <div style="font-weight: 600;">{{ $rental->renter_name }}</div>
                <a href="tel:{{ $rental->renter_phone }}" style="color: var(--color-primary); font-size: 0.875rem;">
                    {{ $rental->renter_phone }}
                </a>
                @if($rental->renter_ktp)
                    <a href="{{ asset('storage/' . $rental->renter_ktp) }}" target="_blank"
                        style="font-size: 0.75rem; color: var(--color-primary); margin-top: 0.25rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        Lihat Foto KTP
                    </a>
                @endif
            </div>
        </div>

        <div
            style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
            <div>
                <div style="color: var(--color-text-muted); font-size: 0.75rem; margin-bottom: 0.25rem;">Tanggal Sewa</div>
                <div style="font-weight: 500;">{{ $rental->start_date->format('d M Y') }}</div>
            </div>
            <div>
                <div style="color: var(--color-text-muted); font-size: 0.75rem; margin-bottom: 0.25rem;">Batas Kembali
                </div>
                <div style="font-weight: 500; color: {{ $isOverdue ? 'var(--color-danger)' : 'inherit' }};">
                    {{ $rental->end_date->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Items List -->
    <div class="card" style="margin-bottom: 1rem;">
        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Barang Disewa ({{ $rental->items->count() }})
        </h3>

        @foreach($rental->items as $item)
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
                <div style="font-weight: 500;">{{ 'Rp ' . number_format($item->pivot->daily_price, 0, ',', '.') }}/hari</div>
            </div>
        @endforeach
    </div>

    <!-- Price Summary -->
    <div class="price-summary" style="margin-bottom: 1.5rem;">
        <div class="price-row">
            <span>Durasi Sewa</span>
            <span class="amount">{{ $rental->duration }} hari</span>
        </div>
        <div class="price-row total">
            <span>Total Biaya Sewa</span>
            <span class="amount">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Process Return Button -->
    <form action="{{ route('returns.process', $rental->id) }}" method="POST" style="padding-bottom: 8rem;">
        @csrf

        <button type="submit" class="btn btn-success" style="width: 100%; position: relative; z-index: 50;"
            onclick="this.disabled=true; this.innerHTML='Memproses...'; this.form.submit();">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Proses Pengembalian
        </button>
    </form>
@endsection