@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
    <x-page-header title="Edit Barang" :backUrl="route('items.show', $item)" />

    <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data" id="edit-form">
        @csrf
        @method('PUT')

        <!-- Image Upload -->
        <div class="form-group">
            <label class="form-label">Foto Barang</label>
            <div id="image-upload-area" style="
                    border: 2px dashed var(--color-border);
                    border-radius: var(--radius-xl);
                    padding: 2rem;
                    text-align: center;
                    cursor: pointer;
                    transition: all 0.2s ease;
                ">
                <input type="file" name="image" id="image-input" accept="image/*" style="display: none;">
                <div id="image-preview" style="{{ $item->image ? '' : 'display: none;' }}">
                    <img src="{{ $item->image ? asset('storage/' . $item->image) : '' }}" alt="Preview"
                        style="max-width: 100%; max-height: 200px; border-radius: var(--radius-lg); margin-bottom: 0.5rem;">
                </div>
                <div id="image-placeholder" style="{{ $item->image ? 'display: none;' : '' }}">
                    <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="color: var(--color-text-muted); margin: 0 auto 0.5rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p style="color: var(--color-text-muted); font-size: 0.875rem;">Tap untuk upload foto</p>
                </div>
            </div>
            @error('image')
                <span style="color: var(--color-danger); font-size: 0.75rem;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Item Code (Read-only) -->
        <div class="form-group">
            <label class="form-label">Kode Barang</label>
            <input type="text" class="form-control" value="{{ $item->code }}" disabled
                style="background: var(--color-surface-alt); color: var(--color-text-muted);">
        </div>

        <!-- Name -->
        <div class="form-group">
            <label class="form-label" for="name">Nama Barang *</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Kamera Canon 5D Mark IV"
                value="{{ old('name', $item->name) }}" required>
            @error('name')
                <span style="color: var(--color-danger); font-size: 0.75rem;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Daily Price -->
        <div class="form-group">
            <label class="form-label" for="daily_price">Harga Sewa per Hari *</label>
            <div style="position: relative;">
                <span
                    style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--color-text-muted);">Rp</span>
                <input type="number" name="daily_price" id="daily_price" class="form-control" placeholder="0"
                    value="{{ old('daily_price', $item->daily_price) }}" style="padding-left: 2.5rem;" min="0" required>
            </div>
            @error('daily_price')
                <span style="color: var(--color-danger); font-size: 0.75rem;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Description -->
        <div class="form-group">
            <label class="form-label" for="description">Deskripsi (Opsional)</label>
            <textarea name="description" id="description" class="form-control" rows="3"
                placeholder="Deskripsi barang, kondisi, kelengkapan, dll...">{{ old('description', $item->description) }}</textarea>
            @error('description')
                <span style="color: var(--color-danger); font-size: 0.75rem;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Perubahan
        </button>
    </form>
@endsection

@push('scripts')
    <script>
        const imageUploadArea = document.getElementById('image-upload-area');
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');
        const imagePlaceholder = document.getElementById('image-placeholder');

        imageUploadArea.addEventListener('click', () => imageInput.click());

        imageInput.addEventListener('change', function (e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.querySelector('img').src = e.target.result;
                    imagePreview.style.display = 'block';
                    imagePlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        document.getElementById('edit-form').addEventListener('submit', function () {
            showLoading();
        });
    </script>
@endpush