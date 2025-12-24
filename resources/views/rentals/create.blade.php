@extends('layouts.app')

@section('title', 'Sewa Barang')

@section('content')
    <x-page-header title="Sewa Barang" :backUrl="route('items.index')" />

    <!-- Scan Mode Toggle -->
    <div class="filter-tabs" style="margin-bottom: 1rem;">
        <button class="filter-tab active" id="scan-tab" onclick="showScanMode()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                style="margin-right: 0.25rem;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            Scan QR
        </button>
        <button class="filter-tab" id="manual-tab" onclick="showManualMode()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                style="margin-right: 0.25rem;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Cari Manual
        </button>
    </div>

    <!-- QR Scanner Section -->
    <div id="scan-section">
        <div class="scanner-container" id="qr-reader">
            <div class="scanner-overlay">
                <div class="scanner-frame">
                    <div class="scanner-line"></div>
                </div>
            </div>
        </div>
        <p style="text-align: center; color: var(--color-text-muted); font-size: 0.875rem; margin-top: 1rem;">
            Arahkan kamera ke QR code barang
        </p>
    </div>

    <!-- Manual Search Section -->
    <div id="manual-section" style="display: none;">
        <div class="search-box" style="margin-bottom: 1rem;">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" id="item-search" class="form-control" placeholder="Cari nama atau kode barang...">
        </div>

        <div id="search-results">
            <!-- Available items will be shown here -->
        </div>
    </div>

    <!-- Cart Section -->
    <div id="cart-section" style="margin-top: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 600;">Barang Dipilih</h3>
            <span id="cart-count" class="badge badge-active">0 barang</span>
        </div>

        <div id="cart-items">
            <x-empty-state icon="box" title="Belum ada barang" message="Scan QR atau cari barang untuk ditambahkan" />
        </div>
    </div>

    <!-- Continue Button (Fixed at bottom) -->
    <div id="continue-section"
        style="display: none; position: fixed; bottom: 5.5rem; left: 1rem; right: 1rem; z-index: 50;">
        <button id="continue-btn" class="btn btn-primary" style="width: 100%;" onclick="proceedToForm()">
            Lanjutkan ke Data Penyewa
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </button>
    </div>

    <!-- Customer Form Section (Initially Hidden) -->
    <div id="customer-section" style="display: none; margin-top: 1.5rem;">
        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Data Penyewa</h3>

        <form id="rental-form" action="{{ route('rentals.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="items" id="items-input">

            <div class="form-group">
                <label class="form-label" for="renter_name">Nama Penyewa *</label>
                <input type="text" name="renter_name" id="renter_name" class="form-control" placeholder="Nama lengkap"
                    required>
            </div>

            <div class="form-group">
                <label class="form-label" for="renter_phone">Nomor Telepon *</label>
                <input type="tel" name="renter_phone" id="renter_phone" class="form-control" placeholder="08xxxxxxxxxx"
                    required>
            </div>

            <div class="form-group">
                <label class="form-label" for="renter_ktp">Foto KTP (Opsional)</label>
                <div id="ktp-preview-container" style="display: none; margin-bottom: 0.5rem;">
                    <img id="ktp-preview"
                        style="width: 100%; max-height: 200px; object-fit: cover; border-radius: var(--radius-lg);">
                </div>
                <label for="renter_ktp" class="btn btn-secondary" style="width: 100%; cursor: pointer;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <circle cx="12" cy="13" r="3" stroke-width="2" />
                    </svg>
                    <span id="ktp-label">Ambil Foto KTP</span>
                </label>
                <input type="file" name="renter_ktp" id="renter_ktp" accept="image/*" capture="environment"
                    style="display: none;" onchange="previewKtpImage(this)">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="start_date">Tanggal Sewa *</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="end_date">Tanggal Kembali *</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                </div>
            </div>

            <!-- Price Summary -->
            <div class="price-summary" style="margin-bottom: 1rem;">
                <div class="price-row">
                    <span>Durasi Sewa</span>
                    <span id="duration-display" class="amount">0 hari</span>
                </div>
                <div class="price-row">
                    <span>Total Harga Barang/hari</span>
                    <span id="daily-total-display" class="amount">Rp 0</span>
                </div>
                <div class="price-row total">
                    <span>Total</span>
                    <span id="grand-total-display" class="amount">Rp 0</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Proses Sewa
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        // Cart state
        let cart = [];
        let html5QrCode = null;

        // Initialize QR Scanner
        function initScanner() {
            html5QrCode = new Html5Qrcode("qr-reader");

            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.log("Camera error:", err);
                showToast("Tidak dapat mengakses kamera", "error");
            });
        }

        function onScanSuccess(decodedText) {
            // Vibrate for feedback
            if (navigator.vibrate) navigator.vibrate(100);

            // Check if item already in cart
            if (cart.find(item => item.code === decodedText)) {
                showToast("Barang sudah ada di keranjang", "error");
                return;
            }

            // Fetch item data
            fetch(`/api/items/code/${decodedText}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.item.status === 'available') {
                        addToCart(data.item);
                        showToast(`${data.item.name} ditambahkan`);
                    } else if (data.item?.status === 'rented') {
                        showToast("Barang sedang disewa", "error");
                    } else {
                        showToast("Barang tidak ditemukan", "error");
                    }
                })
                .catch(() => showToast("Error mencari barang", "error"));
        }

        function onScanFailure(error) {
            // Ignore failures
        }

        // Add item to cart
        function addToCart(item) {
            cart.push(item);
            renderCart();
            updateCartCount();
        }

        // Remove item from cart
        function removeFromCart(code) {
            cart = cart.filter(item => item.code !== code);
            renderCart();
            updateCartCount();
        }

        // Render cart items
        function renderCart() {
            const container = document.getElementById('cart-items');

            if (cart.length === 0) {
                container.innerHTML = `
                            <div class="empty-state">
                                <svg width="80" height="80" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-text-muted); opacity: 0.5;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <h3 style="margin-bottom: 0.5rem;">Belum ada barang</h3>
                                <p style="color: var(--color-text-muted);">Scan QR atau cari barang untuk ditambahkan</p>
                            </div>
                        `;
                document.getElementById('continue-section').style.display = 'none';
                return;
            }

            container.innerHTML = cart.map(item => `
                        <div class="cart-item">
                            <div class="item-info">
                                <div class="item-name">${item.name}</div>
                                <div class="item-price">${formatRupiah(item.daily_price)}/hari</div>
                            </div>
                            <button class="remove-btn" onclick="removeFromCart('${item.code}')">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    `).join('');

            document.getElementById('continue-section').style.display = 'block';
        }

        function updateCartCount() {
            document.getElementById('cart-count').textContent = `${cart.length} barang`;
        }

        // Mode switching
        function showScanMode() {
            document.getElementById('scan-section').style.display = 'block';
            document.getElementById('manual-section').style.display = 'none';
            document.getElementById('scan-tab').classList.add('active');
            document.getElementById('manual-tab').classList.remove('active');

            if (!html5QrCode) initScanner();
        }

        function showManualMode() {
            document.getElementById('scan-section').style.display = 'none';
            document.getElementById('manual-section').style.display = 'block';
            document.getElementById('scan-tab').classList.remove('active');
            document.getElementById('manual-tab').classList.add('active');

            if (html5QrCode) {
                html5QrCode.stop().catch(err => console.log(err));
                html5QrCode = null;
            }

            loadAvailableItems();
        }

        // Load available items for manual selection
        function loadAvailableItems() {
            fetch('/api/items?status=available')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('search-results');

                    if (data.items.length === 0) {
                        container.innerHTML = `
                                    <div class="empty-state">
                                        <h3>Tidak ada barang tersedia</h3>
                                        <p style="color: var(--color-text-muted);">Semua barang sedang disewa</p>
                                    </div>
                                `;
                        return;
                    }

                    container.innerHTML = data.items.map(item => `
                                <div class="item-card" onclick="selectItem('${item.code}')" style="cursor: pointer;">
                                    <div class="item-image">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div class="item-info">
                                        <div class="item-name">${item.name}</div>
                                        <div class="item-code">${item.code}</div>
                                        <div class="item-price">${formatRupiah(item.daily_price)}/hari</div>
                                    </div>
                                    <div style="color: var(--color-success);">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                </div>
                            `).join('');
                });
        }

        function selectItem(code) {
            if (cart.find(item => item.code === code)) {
                showToast("Barang sudah ada di keranjang", "error");
                return;
            }

            fetch(`/api/items/code/${code}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        addToCart(data.item);
                        showToast(`${data.item.name} ditambahkan`);
                    }
                });
        }

        // Proceed to customer form
        function proceedToForm() {
            if (cart.length === 0) {
                showToast("Pilih minimal 1 barang", "error");
                return;
            }

            // Hide scan/manual sections
            document.getElementById('scan-section').style.display = 'none';
            document.getElementById('manual-section').style.display = 'none';
            document.querySelector('.filter-tabs').style.display = 'none';
            document.getElementById('continue-section').style.display = 'none';

            // Show customer form
            document.getElementById('customer-section').style.display = 'block';

            // Set today as start date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').value = today;
            document.getElementById('start_date').min = today;
            document.getElementById('end_date').min = today;

            // Stop scanner
            if (html5QrCode) {
                html5QrCode.stop().catch(err => console.log(err));
            }
        }

        // Calculate pricing
        function calculatePricing() {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);

            if (isNaN(startDate) || isNaN(endDate)) return;

            const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
            const dailyTotal = cart.reduce((sum, item) => sum + item.daily_price, 0);
            const grandTotal = dailyTotal * days;

            document.getElementById('duration-display').textContent = `${days} hari`;
            document.getElementById('daily-total-display').textContent = formatRupiah(dailyTotal);
            document.getElementById('grand-total-display').textContent = formatRupiah(grandTotal);
        }

        // Event listeners
        document.getElementById('start_date')?.addEventListener('change', calculatePricing);
        document.getElementById('end_date')?.addEventListener('change', calculatePricing);

        document.getElementById('rental-form')?.addEventListener('submit', function (e) {
            document.getElementById('items-input').value = JSON.stringify(cart.map(item => item.id));
            showLoading();
        });

        // Search filter
        document.getElementById('item-search')?.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#search-results .item-card').forEach(card => {
                const name = card.querySelector('.item-name').textContent.toLowerCase();
                const code = card.querySelector('.item-code').textContent.toLowerCase();
                card.style.display = (name.includes(query) || code.includes(query)) ? 'flex' : 'none';
            });
        });

        // Preview KTP image
        function previewKtpImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('ktp-preview').src = e.target.result;
                    document.getElementById('ktp-preview-container').style.display = 'block';
                    document.getElementById('ktp-label').textContent = 'Ganti Foto KTP';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Initialize scanner on page load
        document.addEventListener('DOMContentLoaded', () => {
            initScanner();
        });
    </script>
@endpush