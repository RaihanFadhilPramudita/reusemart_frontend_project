@extends('layouts.public')

@section('title', 'Checkout')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>
    
    <div id="loading" class="py-10 text-center">
        <p class="text-gray-500">Memuat data checkout...</p>
    </div>

    <div id="checkout-content" class="hidden">
        <form id="checkout-form" class="flex flex-col md:flex-row gap-6">
            <!-- Informasi Pengiriman -->
            <div class="w-full md:w-2/3 space-y-6">
                <div class="bg-white rounded shadow p-6">
                    <h2 class="font-bold text-lg mb-4">Alamat Pengiriman</h2>
                    
                    <div id="alamat-list" class="space-y-4">
                        <!-- Daftar alamat akan dimuat via JavaScript -->
                        <p class="text-gray-500">Memuat alamat...</p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="/pembeli/profile/alamat" class="text-green-600 hover:underline text-sm">
                            + Tambah Alamat Baru
                        </a>
                    </div>
                </div>
                
                <div class="bg-white rounded shadow p-6">
                    <h2 class="font-bold text-lg mb-4">Metode Pengiriman</h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center space-x-3 p-3 border rounded cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="metode_pengiriman" value="Antar" checked>
                            <div>
                                <p class="font-medium">Dikirim ke Alamat</p>
                                <p class="text-sm text-gray-500">Dikirim oleh kurir ReuseMart</p>
                            </div>
                        </label>
                        
                        <label class="flex items-center space-x-3 p-3 border rounded cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="metode_pengiriman" value="Ambil">
                            <div>
                                <p class="font-medium">Ambil di Tempat</p>
                                <p class="text-sm text-gray-500">Ambil di gudang ReuseMart</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="bg-white rounded shadow p-6">
                    <h2 class="font-bold text-lg mb-4">Metode Pembayaran</h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center space-x-3 p-3 border rounded cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="metode_pembayaran" value="transfer" checked>
                            <div>
                                <p class="font-medium">Transfer Bank</p>
                                <p class="text-sm text-gray-500">Silakan transfer ke rekening yang tertera</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="bg-white rounded shadow p-6">
                    <h2 class="font-bold text-lg mb-4">Barang yang Dibeli</h2>
                    
                    <div id="cart-items" class="divide-y">
                        <!-- Item keranjang akan dimuat via JavaScript -->
                        <p class="text-gray-500">Memuat barang...</p>
                    </div>
                </div>
            </div>
            
            <!-- Ringkasan Belanja -->
            <div class="w-full md:w-1/3">
                <div class="bg-white rounded shadow p-6 sticky top-4">
                    <h2 class="font-bold text-lg mb-4">Ringkasan Belanja</h2>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between">
                            <span>Total Harga (<span id="total-items">0</span> barang)</span>
                            <span id="subtotal">Rp0</span>
                        </div>
                        <div class="flex justify-between items-center" id="shipping-row">
                            <span>Ongkos Kirim</span>
                            <span id="shipping-cost">Rp0</span>
                        </div>
                        
                        <!-- Penggunaan Poin -->
                      <!-- Penggunaan Poin -->
                        <div class="pt-2 border-t">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium">Gunakan Poin</span>
                                <span id="available-points">0 poin tersedia</span>
                            </div>
                            <div id="points-control" class="mb-2">
                                <div class="flex items-center">
                                    <input type="range" id="points-slider" min="0" max="0" step="100" value="0" 
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <div>
                                        <span id="points-to-use">0</span> poin
                                        <span id="points-error" class="hidden text-red-500 text-xs">Poin harus kelipatan 100</span>
                                    </div>
                                    <span id="points-discount">Rp0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <div class="flex justify-between font-bold">
                            <span>Total Bayar</span>
                            <span id="grand-total">Rp0</span>
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            Poin yang akan didapat: <span id="points-earned">0</span>
                        </div>
                    </div>
                    
                    <button type="submit" id="place-order-btn" 
                            class="w-full bg-green-600 text-white py-3 px-4 rounded mt-4 hover:bg-green-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Buat Pesanan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    
    // Redirect ke login jika belum login
    if (!token) {
        window.location.href = '/login';
        return;
    }
    
    // Element references
    const loading = document.getElementById('loading');
    const checkoutContent = document.getElementById('checkout-content');
    const alamatList = document.getElementById('alamat-list');
    const cartItems = document.getElementById('cart-items');
    const totalItems = document.getElementById('total-items');
    const subtotal = document.getElementById('subtotal');
    const shippingRow = document.getElementById('shipping-row');
    const shippingCost = document.getElementById('shipping-cost');
    const grandTotal = document.getElementById('grand-total');
    // const usePointsCheckbox = document.getElementById('use-points');
    // const pointsDetails = document.getElementById('points-details');
    const availablePoints = document.getElementById('available-points');
    // const pointsUsed = document.getElementById('points-used');
    const pointsDiscount = document.getElementById('points-discount');
    const pointsEarned = document.getElementById('points-earned');
    
    // State variables
    let cartData = [];
    let addressData = [];
    let userPoints = 0;
    let selectedAddressId = null;
    let isPickup = false;
    let subTotalAmount = 0;
    let shippingAmount = 0;
    let discountAmount = 0;
    let grandTotalAmount = 0;
    let estimatedPoints = 0;
    let selectedPoints = 0;
    
    // Initialize
    loadCheckoutData();
    
    // Load checkout data (cart, addresses, user points)
    async function loadCheckoutData() {
        try {
            // Fetch data in parallel
            const [cartResponse, addressResponse, profileResponse] = await Promise.all([
                axios.get('http://localhost:8000/api/pembeli/cart', { headers: { Authorization: `Bearer ${token}` } }),
                axios.get('http://localhost:8000/api/pembeli/alamat/show', { headers: { Authorization: `Bearer ${token}` } }),
                axios.get('http://localhost:8000/api/pembeli/profile', { headers: { Authorization: `Bearer ${token}` } })
            ]);
            
            // Process cart data
            cartData = cartResponse.data.data || [];
            if (!cartData.length) {
                alert('Keranjang belanja kosong');
                window.location.href = '/';
                return;
            }
            
            // Process address data
            addressData = addressResponse.data.data || [];
            if (!addressData.length) {
                alert('Anda belum memiliki alamat. Silahkan tambahkan alamat terlebih dahulu.');
                window.location.href = '/pembeli/profile/alamat';
                return;
            }
            
            // Process user points
            userPoints = profileResponse.data.data?.POIN || 0;
            
            // Show content, hide loading
            loading.classList.add('hidden');
            checkoutContent.classList.remove('hidden');
            
            // Render everything
           // Render everything
            renderAddresses();
            renderCartItems();
            updateSummary(); // Panggil updateSummary() terlebih dahulu
            setupPointsSlider(); // Kemudian setup slider
            
            // Setup event listeners
            setupEventListeners();
            
        } catch (error) {
            console.error('Error loading checkout data:', error);
            alert('Terjadi kesalahan saat memuat data checkout');
        }
    }
    
    // Render addresses
    function renderAddresses() {
        if (!addressData.length) {
            alamatList.innerHTML = `
                <p class="text-yellow-600">Anda belum memiliki alamat. Silahkan tambahkan alamat terlebih dahulu.</p>
            `;
            return;
        }
        
        // Get the first address as default
        selectedAddressId = addressData[0].ID_ALAMAT;
        alamatList.innerHTML = addressData.map((addr, index) => `
            <label class="block border rounded p-4 cursor-pointer hover:bg-gray-50 ${index === 0 ? 'border-green-500' : ''}">
                <div class="flex items-start">
                    <input type="radio" name="alamat" value="${addr.ID_ALAMAT}" 
                           ${index === 0 ? 'checked' : ''} 
                           class="mt-1 mr-3">
                    <div>
                        <p class="font-semibold">${addr.NAMA_ALAMAT || 'Alamat ' + (index + 1)}</p>
                        <p>${addr.ALAMAT_LENGKAP || ''}</p>
                        <p class="text-sm text-gray-500">
                            ${[addr.KECAMATAN, addr.KOTA, addr.KODE_POS].filter(Boolean).join(', ')}
                        </p>
                    </div>
                </div>
            </label>
        `).join('');
    }
    
    // Render cart items
    function renderCartItems() {
        if (!cartData.length) {
            cartItems.innerHTML = `
                <p class="text-gray-500">Tidak ada barang di keranjang</p>
            `;
            return;
        }
        
        cartItems.innerHTML = cartData.map(item => {
            const gambar =
                Array.isArray(item.barang.GAMBAR) && item.barang.GAMBAR.length > 0
                    ? 'http://localhost:8000/storage/' + item.barang.GAMBAR[0]
                    : '/img/default.jpg';

                return `
            <div class="py-4 flex">
                <div class="w-16 h-16 flex-shrink-0 mr-4 bg-gray-100 rounded">
                    <img src="${gambar}" 
                         class="w-full h-full object-cover rounded" 
                         alt="${item.barang.NAMA_BARANG}">
                </div>
                
                <div class="flex-1">
                    <h3 class="font-medium">${item.barang.NAMA_BARANG}</h3>
                    <p class="text-sm text-gray-500">${item.JUMLAH} barang</p>
                </div>
                
                <div class="text-right">
                    <p class="font-bold">Rp${(parseInt(item.barang.HARGA) * item.JUMLAH).toLocaleString()}</p>
                </div>
            </div>
        `}).join('');
        }
            

    
    
    // Update order summary
    function updateSummary() {
            // Calculate subtotal
            subTotalAmount = cartData.reduce((total, item) => 
                total + (parseInt(item.barang.HARGA) * item.JUMLAH), 0);
            
            // Update shipping cost based on selection
            shippingAmount = isPickup ? 0 : 
                (subTotalAmount >= 1500000 ? 0 : 100000);
            
            // Calculate points discount if applicable
            discountAmount = Math.floor(selectedPoints / 100) * 10000;
            
            // Calculate grand total
            grandTotalAmount = subTotalAmount + shippingAmount - discountAmount;
            
            // Calculate earned points (1 point per Rp10,000, +20% bonus if over Rp500,000)
            estimatedPoints = Math.floor(grandTotalAmount / 10000);
            if (grandTotalAmount > 500000) {
                estimatedPoints += Math.floor(estimatedPoints * 0.2);
            }
            
            // Update UI
            totalItems.textContent = cartData.length;
            subtotal.textContent = `Rp${subTotalAmount.toLocaleString()}`;
            shippingCost.textContent = shippingAmount > 0 ? 
                `Rp${shippingAmount.toLocaleString()}` : 'GRATIS';
            grandTotal.textContent = `Rp${grandTotalAmount.toLocaleString()}`;
            
            // Update points display
            document.getElementById('points-to-use').textContent = selectedPoints;
            document.getElementById('points-discount').textContent = `Rp${discountAmount.toLocaleString()}`;
            
            // Tambahkan baris ini untuk memperbarui tampilan poin yang akan didapat
            pointsEarned.textContent = estimatedPoints;
            
            // Show/hide shipping row based on delivery method
            shippingRow.style.display = isPickup ? 'none' : 'flex';
        }

    // Setup point slider
    function setupPointsSlider() {
        const pointsSlider = document.getElementById('points-slider');
        const pointsToUse = document.getElementById('points-to-use');
        
        // Set the maximum value based on user points and order total
        const maxPointsValue = Math.min(
            userPoints, 
            Math.floor((subTotalAmount + shippingAmount) / 10000) * 100
        );
        
        // Update slider attributes
        pointsSlider.max = maxPointsValue;
        pointsSlider.value = 0;
        
        // Set available points text
        document.getElementById('available-points').textContent = `${userPoints} poin tersedia`;
        
        // Show error if user has less than 100 points
        if (userPoints < 100) {
            document.getElementById('points-error').classList.remove('hidden');
            pointsSlider.disabled = true;
        } else {
            document.getElementById('points-error').classList.add('hidden');
            pointsSlider.disabled = false;
        }
        
        // Add event listener to slider
        pointsSlider.addEventListener('input', function() {
            selectedPoints = parseInt(this.value);
            pointsToUse.textContent = selectedPoints;
            updateSummary();
        });
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Address selection
        document.querySelectorAll('input[name="alamat"]').forEach(radio => {
            radio.addEventListener('change', function() {
                selectedAddressId = this.value;
                
                // Update UI
                document.querySelectorAll('#alamat-list label').forEach(label => {
                    label.classList.remove('border-green-500');
                });
                this.closest('label').classList.add('border-green-500');
            });
        });
        
        // Delivery method selection
        document.querySelectorAll('input[name="metode_pengiriman"]').forEach(radio => {
            radio.addEventListener('change', function() {
                isPickup = this.value === 'Ambil';
                updateSummary();
            });
        });
        
        
        // Form submission
        document.getElementById('checkout-form').addEventListener('submit', handleSubmitOrder);
    }
    
    // Handle order submission
    async function handleSubmitOrder(e) {
        e.preventDefault();
        
        // Validate required fields
        if (!selectedAddressId && !isPickup) {
            alert('Silahkan pilih alamat pengiriman');
            return;
        }
         if (selectedPoints > 0) {
            if (selectedPoints % 100 !== 0) {
                alert('Poin harus kelipatan 100');
                return;
            }
            
            if (selectedPoints > userPoints) {
                alert('Poin yang digunakan melebihi poin yang tersedia');
                return;
            }
        }
            
        // Disable submit button to prevent multiple submissions
        const submitBtn = document.getElementById('place-order-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memproses...';
        
        try {
            // Get user data from localStorage
            const user = JSON.parse(localStorage.getItem('user')) || {};
            
            // Prepare order data with all required fields
            const orderData = {
                id_alamat: isPickup ? null : selectedAddressId,
                metode_pengiriman: isPickup ? 'Ambil' : 'Antar',
                metode_pembayaran: 'transfer',
                use_points: selectedPoints > 0,
                points_used: selectedPoints,
                total_harga: subTotalAmount,
                ongkos_kirim: shippingAmount,
                potongan_poin: discountAmount,
                total_akhir: grandTotalAmount,
                // Add the missing required fields
                jenis_delivery: isPickup ? 'Ambil' : 'Antar',
                id_pembeli: user.ID_PEMBELI || user.id_pembeli,
                id_pegawai: 2, // Default to a Customer Service employee (ID 2)
                items: cartData.map(item => ({
                    id_barang: item.ID_BARANG || item.id_barang,
                    jumlah: item.JUMLAH || item.jumlah || 1
                }))
            };
            
            console.log('Sending order data:', orderData); // Debug log
            
            // Submit order
            const response = await axios.post(
                'http://localhost:8000/api/pembeli/transaksi', 
                orderData,
                { headers: { Authorization: `Bearer ${token}` } }
            );
            
            // Redirect to payment page
            const transaksiId = response.data.data.ID_TRANSAKSI;
            window.location.href = `/pembayaran/${transaksiId}`;
            
        } catch (error) {
            console.error('Error creating order:', error);
            
            // Show more specific error message if available
            if (error.response && error.response.data) {
                console.error('Server response:', error.response.data);
                alert('Gagal membuat pesanan: ' + 
                    (error.response.data.message || 'Terjadi kesalahan pada server'));
            } else {
                alert('Terjadi kesalahan saat membuat pesanan');
            }
            
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = 'Buat Pesanan';
        }
    }
    });
</script>
@endsection