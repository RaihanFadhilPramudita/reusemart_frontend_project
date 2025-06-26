@extends('layouts.public')

@section('title', 'Pembayaran')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="bg-white rounded shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-green-600 text-white p-6">
            <h1 class="text-2xl font-bold">Pembayaran Pesanan</h1>
            <p class="text-sm opacity-90">Selesaikan pembayaran Anda dalam <span id="countdown" class="font-mono">15:00</span></p>
        </div>

        <!-- Loading state -->
        <div id="loading" class="p-10 text-center">
            <p class="text-gray-500">Memuat detail pembayaran...</p>
        </div>

        <!-- Content -->
        <div id="payment-content" class="hidden p-6">
            <div class="space-y-6">
                <!-- Order summary -->
                <div>
                    <h2 class="font-bold text-lg mb-3">Ringkasan Pesanan</h2>
                    <div class="border rounded">
                        <div class="p-4 border-b">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor Pesanan</span>
                                <span class="font-medium" id="order-number">-</span>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-gray-600">Total Pembayaran</span>
                                <span class="font-bold text-lg text-green-600" id="order-total">Rp0</span>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50" id="order-items">
                            <!-- Items will be rendered here -->
                        </div>
                    </div>
                </div>

                <!-- Payment Instructions -->
                <div>
                    <h2 class="font-bold text-lg mb-3">Instruksi Pembayaran</h2>
                    <div class="border rounded">
                        <div class="p-4 border-b bg-gray-50">
                            <h3 class="font-semibold">Transfer Bank</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Bank</p>
                                <p class="font-medium">Bank BCA</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Nomor Rekening</p>
                                <div class="flex items-center">
                                    <span class="font-mono font-medium text-lg mr-2">8721093355</span>
                                    <button onclick="copyToClipboard('8721093355')" class="text-green-600 text-sm hover:text-green-700">
                                        Salin
                                    </button>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Nama Rekening</p>
                                <p class="font-medium">PT. REUSE MART INDONESIA</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Pembayaran</p>
                                <div class="flex items-center">
                                    <span class="font-mono font-medium text-lg mr-2" id="payment-amount">Rp0</span>
                                    <button onclick="copyPaymentAmount()" class="text-green-600 text-sm hover:text-green-700">
                                        Salin
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload payment proof -->
                <div>
                    <h2 class="font-bold text-lg mb-3">Unggah Bukti Pembayaran</h2>
                    <div class="border rounded p-4">
                        <div id="upload-container" class="border-2 border-dashed border-gray-300 rounded p-6 text-center">
                            <input type="file" id="bukti-pembayaran" class="hidden" accept="image/*">
                            <label for="bukti-pembayaran" class="cursor-pointer">
                                <div class="text-gray-500 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p>Klik untuk memilih file bukti pembayaran</p>
                                    <p class="text-xs mt-1">Format: JPEG, PNG, atau PDF (Maks. 2MB)</p>
                                </div>
                            </label>
                        </div>
                        
                        <div id="preview-container" class="hidden">
                            <div class="relative mt-2">
                                <img id="preview-image" class="max-h-64 mx-auto border rounded" src="" alt="Preview bukti pembayaran">
                                <button type="button" id="remove-preview" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                                    &times;
                                </button>
                            </div>
                        </div>
                        
                        <button type="button" id="upload-button" class="w-full mt-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                            Konfirmasi Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    
    // Redirect to login if not logged in
    if (!token) {
        window.location.href = '/login';
        return;
    }
    
    // Get transaction ID from URL
    const path = window.location.pathname;
    const transaksiId = path.split('/').pop();
    
    // Element references
    const loading = document.getElementById('loading');
    const paymentContent = document.getElementById('payment-content');
    const orderNumber = document.getElementById('order-number');
    const orderTotal = document.getElementById('order-total');
    const orderItems = document.getElementById('order-items');
    const paymentAmount = document.getElementById('payment-amount');
    const countdownEl = document.getElementById('countdown');
    const uploadButton = document.getElementById('upload-button');
    const buktiPembayaran = document.getElementById('bukti-pembayaran');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    const uploadContainer = document.getElementById('upload-container');
    const removePreview = document.getElementById('remove-preview');
    
    // State variables
    let transactionData = null;
    let countdownInterval = null;
    let countdownTime = 15 * 60; // 15 minutes in seconds
    let selectedFile = null;
    
    // Load transaction data
    loadTransactionData();
    
    // Load transaction data from API
    async function loadTransactionData() {
        try {
            const response = await axios.get(
                `http://localhost:8000/api/pembeli/transaksi/${transaksiId}`,
                { headers: { Authorization: `Bearer ${token}` } }
            );
            
            transactionData = response.data.data;
            
            // Check if transaction is already paid
            if (transactionData.STATUS_TRANSAKSI !== 'Belum dibayar') {
                alert('Transaksi ini sudah dibayar atau dibatalkan');
                window.location.href = '/pembeli/profile';
                return;
            }
            
            // Calculate remaining time if WAKTU_PESAN exists
            if (transactionData.WAKTU_PESAN) {
                const orderTime = new Date(transactionData.WAKTU_PESAN);
                const expiryTime = new Date(orderTime.getTime() + 60 * 1000); // 1 minute later
                const now = new Date();
                
                if (now > expiryTime) {
                    // Expired, redirect to profile page
                    alert('Waktu pembayaran telah habis. Pesanan dibatalkan.');
                    window.location.href = '/pembeli/profile';
                    return;
                }
                
                // Set countdown time
                countdownTime = Math.floor((expiryTime - now) / 1000);
            }
            
            // Show content, hide loading
            loading.classList.add('hidden');
            paymentContent.classList.remove('hidden');
            
            // Render transaction details
            renderTransactionDetails();
            
            // Start countdown
            startCountdown();
            
        } catch (error) {
            console.error('Error loading transaction data:', error);
            alert('Terjadi kesalahan saat memuat data transaksi');
            window.location.href = '/pembeli/profile';
        }
    }
    // Start countdown timer
    function startCountdown() {
        countdownInterval = setInterval(() => {
            countdownTime--;
            
            if (countdownTime <= 0) {
                clearInterval(countdownInterval);
                alert('Waktu pembayaran telah habis. Pesanan dibatalkan.');
                window.location.href = '/pembeli/profile';
                return;
            }
            
            const minutes = Math.floor(countdownTime / 60);
            const seconds = countdownTime % 60;
            countdownEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }
        
    // Render transaction details
    function renderTransactionDetails() {
        if (!transactionData) return;
        
        // Set order number and total
        orderNumber.textContent = transactionData.NO_NOTA || transaksiId;
        orderTotal.textContent = `Rp${parseInt(transactionData.TOTAL_AKHIR).toLocaleString()}`;
        paymentAmount.textContent = `Rp${parseInt(transactionData.TOTAL_AKHIR).toLocaleString()}`;
        
        // Render order items
        if (transactionData.detail_transaksi && transactionData.detail_transaksi.length) {
            orderItems.innerHTML = transactionData.detail_transaksi.map(item => {
                const gambar =
                    item.barang &&
                    Array.isArray(item.barang.GAMBAR) &&
                    item.barang.GAMBAR.length > 0
                        ? item.barang.GAMBAR[0]
                        : '/img/default.jpg';

                return `
                <div class="flex items-center py-2 ${item !== transactionData.detail_transaksi[transactionData.detail_transaksi.length - 1] ? 'border-b' : ''}">
                    <div class="w-12 h-12 bg-gray-200 rounded mr-3 overflow-hidden">
                        <img src="${gambar}" 
                             class="w-full h-full object-cover" 
                             alt="${item.barang.NAMA_BARANG || 'Produk'}">
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">${item.barang.NAMA_BARANG || 'Produk'}</p>
                        <p class="text-sm text-gray-500">${item.JUMLAH} barang</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">Rp${parseInt(item.barang.HARGA || 0).toLocaleString()}</p>
                    </div>
                </div>
            `}).join('');
        } else {
            orderItems.innerHTML = `<p class="text-gray-500 text-center py-4">Tidak ada detail barang</p>`;
        }
    }
    
    // Start countdown timer
    function startCountdown() {
        countdownInterval = setInterval(() => {
            countdownTime--;
            
            if (countdownTime <= 0) {
                clearInterval(countdownInterval);
                alert('Waktu pembayaran telah habis. Pesanan dibatalkan.');
                window.location.href = '/pembeli/profile';
                return;
            }
            
            const minutes = Math.floor(countdownTime / 60);
            const seconds = countdownTime % 60;
            countdownEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }
    
    // Copy text to clipboard
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text)
            .then(() => alert('Nomor rekening berhasil disalin'))
            .catch(err => console.error('Error copying text: ', err));
    };
    
    // Copy payment amount to clipboard
    window.copyPaymentAmount = function() {
        if (transactionData) {
            const amount = parseInt(transactionData.TOTAL_AKHIR).toString();
            navigator.clipboard.writeText(amount)
                .then(() => alert('Jumlah pembayaran berhasil disalin'))
                .catch(err => console.error('Error copying text: ', err));
        }
    };
    
    // Handle file input change
    buktiPembayaran.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (!file) return;
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        if (!validTypes.includes(file.type)) {
            alert('File harus berupa gambar (JPEG, PNG) atau PDF');
            this.value = '';
            return;
        }
        
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            this.value = '';
            return;
        }
        
        // Store file for later upload
        selectedFile = file;
        
        // Preview image (if image)
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove('hidden');
                uploadContainer.classList.add('border-green-500');
                uploadButton.disabled = false;
            };
            reader.readAsDataURL(file);
        } else {
            // For PDF, just show a placeholder
            previewImage.src = '/img/pdf-icon.png';
            previewContainer.classList.remove('hidden');
            uploadContainer.classList.add('border-green-500');
            uploadButton.disabled = false;
        }
    });
    
    // Remove preview
    removePreview.addEventListener('click', function() {
        previewContainer.classList.add('hidden');
        uploadContainer.classList.remove('border-green-500');
        buktiPembayaran.value = '';
        selectedFile = null;
        uploadButton.disabled = true;
    });
    
    // Handle upload button click
    uploadButton.addEventListener('click', async function() {
        if (!selectedFile) return;
        
        // Disable button and show loading state
        this.disabled = true;
        this.textContent = 'Mengunggah...';
        
        try {
            // Create form data
            const formData = new FormData();
            formData.append('bukti_transfer', selectedFile);
            
            // Upload payment proof
            await axios.post(
                `http://localhost:8000/api/pembeli/transaksi/${transaksiId}/upload-bukti`,
                formData,
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data'
                    }
                }
            );
            
            // Show success message
            alert('Bukti pembayaran berhasil diunggah. Silahkan tunggu konfirmasi dari CS.');
            
            // Redirect to profile page
            window.location.href = '/pembeli/profile';
            
        } catch (error) {
            console.error('Error uploading payment proof:', error);
            alert('Terjadi kesalahan saat mengunggah bukti pembayaran');
            
            // Re-enable button
            this.disabled = false;
            this.textContent = 'Konfirmasi Pembayaran';
        }
    });
    
    // Clean up when leaving the page
    window.addEventListener('beforeunload', () => {
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
    });
});
</script>
@endsection