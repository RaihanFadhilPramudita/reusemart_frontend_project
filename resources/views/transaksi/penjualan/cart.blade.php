@extends('layouts.public')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Keranjang Belanja</h1>

    <div id="empty-cart" class="hidden text-center py-10">
        <img src="/img/empty-cart.svg" alt="Keranjang Kosong" class="w-32 mx-auto mb-4">
        <p class="text-gray-500">Keranjang belanja Anda kosong</p>
        <a href="/" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
            Belanja Sekarang
        </a>
    </div>

    <div id="cart-content" class="flex flex-col md:flex-row gap-6">
        <!-- Daftar Item -->
        <div class="w-full md:w-2/3">
            <!-- Filter Sorting -->
            <div class="bg-white rounded shadow p-4 mb-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-medium text-gray-700">Urutkan berdasarkan tanggal titip:</h3>
                    <select id="sort-filter" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">-- Pilih Urutan --</option>
                        <option value="newest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                    </select>
                </div>
            </div>

            <div id="cart-items" class="bg-white rounded shadow divide-y">
                <!-- Item akan dirender melalui JavaScript -->
                <div class="p-4 text-center text-gray-500">Memuat...</div>
            </div>
        </div>

        <!-- Ringkasan Belanja -->
        <div class="w-full md:w-1/3">
            <div class="bg-white rounded shadow p-4 sticky top-4">
                <h2 class="font-bold text-lg mb-4">Ringkasan Belanja</h2>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span>Total Harga (<span id="total-items">0</span> barang)</span>
                        <span id="subtotal">Rp0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span class="text-gray-500">Ditentukan saat checkout</span>
                    </div>
                </div>
                <div class="border-t pt-4">
                    <div class="flex justify-between font-bold">
                        <span>Total</span>
                        <span id="grand-total">Rp0</span>
                    </div>
                </div>
                <button id="checkout-button" 
                    class="w-full bg-green-600 text-white py-2 px-4 rounded mt-4 hover:bg-green-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                    Checkout
                </button>
            </div>
        </div>
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
    
    // State variables
    let originalCartData = []; // Store original data
    let currentSortOrder = ''; // Track current sort order
    
    // Element references
    const sortFilter = document.getElementById('sort-filter');
    
    // Inisialisasi
    loadCart();
    
    // Setup event listeners
    sortFilter.addEventListener('change', function() {
        currentSortOrder = this.value;
        applySorting();
    });
    
    // Load cart items
    async function loadCart() {
        try {
            const response = await axios.get('http://localhost:8000/api/pembeli/cart', {
                headers: { Authorization: `Bearer ${token}` }
            });
            
            const cartItems = response.data.data || [];
            originalCartData = cartItems; // Store original data
            renderCart(cartItems);
        } catch (error) {
            console.error('Gagal memuat cart:', error);
            showEmptyCart('Terjadi kesalahan saat memuat keranjang');
        }
    }
    
    // Apply sorting based on selected filter
    function applySorting() {
        if (!originalCartData.length) return;
        
        let sortedData = [...originalCartData];
        
        if (currentSortOrder === 'newest') {
            // Sort by newest date first (descending)
            sortedData.sort((a, b) => {
                const dateA = new Date(a.barang.TANGGAL_MASUK || '1970-01-01');
                const dateB = new Date(b.barang.TANGGAL_MASUK || '1970-01-01');
                return dateB - dateA; // Descending (newest first)
            });
        } else if (currentSortOrder === 'oldest') {
            // Sort by oldest date first (ascending)
            sortedData.sort((a, b) => {
                const dateA = new Date(a.barang.TANGGAL_MASUK || '1970-01-01');
                const dateB = new Date(b.barang.TANGGAL_MASUK || '1970-01-01');
                return dateA - dateB; // Ascending (oldest first)
            });
        }
        // If no sort order selected, use original data
        
        renderCart(sortedData);
    }
    
    // Render cart items
    function renderCart(items) {
        const cartItemsContainer = document.getElementById('cart-items');
        const emptyCart = document.getElementById('empty-cart');
        const cartContent = document.getElementById('cart-content');
        
        if (!items.length) {
            emptyCart.classList.remove('hidden');
            cartContent.classList.add('hidden');
            return;
        }
        
        emptyCart.classList.add('hidden');
        cartContent.classList.remove('hidden');
        
        let subtotal = 0;
        
        cartItemsContainer.innerHTML = items.map(item => {
            const price = parseInt(item.barang.HARGA);
            const total = price * item.JUMLAH;
            subtotal += total;
            
            // Parse gambar dari JSON jika ada
            let gambar = '/img/default.jpg';
            if (item.barang.GAMBAR) {
                try {
                    const gambarArray = JSON.parse(item.barang.GAMBAR);
                    if (Array.isArray(gambarArray) && gambarArray.length > 0) {
                        gambar = 'http://localhost:8000/storage/' + gambarArray[0];
                    }
                } catch (e) {
                    // Jika gagal parse JSON, gunakan default
                    gambar = '/img/default.jpg';
                }
            }
            
            // Format tanggal titip
            const tanggalTitip = item.barang.TANGGAL_MASUK 
                ? new Date(item.barang.TANGGAL_MASUK).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short', 
                    year: 'numeric'
                })
                : 'Tidak diketahui';
            
            return `
                <div class="p-4 flex items-center" data-id="${item.ID_BARANG}">
                    <div class="w-20 h-20 flex items-center justify-center bg-white border rounded-md overflow-hidden mr-4">
                        <img src="${gambar}" alt="${item.barang.NAMA_BARANG}" class="w-full h-full object-contain">
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="font-medium">${item.barang.NAMA_BARANG}</h3>
                        <p class="text-green-600 font-bold">Rp${price.toLocaleString()}</p>
                        <p class="text-sm text-gray-500 mt-1">Tanggal titip: ${tanggalTitip}</p>
                        <div class="flex items-center mt-2">
                            <button onclick="updateQuantity(${item.ID_BARANG}, ${item.JUMLAH - 1})" 
                                class="w-8 h-8 flex items-center justify-center border rounded-l" 
                                ${item.JUMLAH <= 1 ? 'disabled' : ''}>
                                -
                            </button>
                            <input type="number" value="${item.JUMLAH}" min="1" max="99" readonly
                                class="w-12 h-8 border-t border-b text-center">
                            <button onclick="updateQuantity(${item.ID_BARANG}, ${item.JUMLAH + 1})" 
                                class="w-8 h-8 flex items-center justify-center border rounded-r">
                                +
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end ml-4">
                        <div class="font-bold">Rp${total.toLocaleString()}</div>
                        <button onclick="removeItem(${item.ID_BARANG})" 
                            class="text-red-500 mt-2 hover:text-red-700">
                            Hapus
                        </button>
                    </div>
                </div>
            `;
        }).join('');
        
        // Update summary
        document.getElementById('total-items').textContent = items.length;
        document.getElementById('subtotal').textContent = `Rp${subtotal.toLocaleString()}`;
        document.getElementById('grand-total').textContent = `Rp${subtotal.toLocaleString()}`;
        
        // Enable/disable checkout button
        document.getElementById('checkout-button').disabled = !items.length;
    }
    
    // Show empty cart with optional message
    function showEmptyCart(message = 'Keranjang belanja Anda kosong') {
        const emptyCart = document.getElementById('empty-cart');
        const cartContent = document.getElementById('cart-content');
        
        emptyCart.querySelector('p').textContent = message;
        emptyCart.classList.remove('hidden');
        cartContent.classList.add('hidden');
    }
    
    // Add to cart
    window.addToCart = async function(barangId) {
        try {
            await axios.post('http://localhost:8000/api/pembeli/cart', 
                { id_barang: barangId },
                { headers: { Authorization: `Bearer ${token}` }}
            );
            
            await loadCart(); // Reload cart after adding
            alert('Barang berhasil ditambahkan ke keranjang');
        } catch (error) {
            console.error('Gagal menambahkan ke cart:', error);
            alert('Gagal menambahkan barang ke keranjang');
        }
    }
    
    // Remove from cart
    window.removeItem = async function(barangId) {
        if (!confirm('Yakin ingin menghapus barang ini dari keranjang?')) return;
        
        try {
            await axios.delete(`http://localhost:8000/api/pembeli/cart/${barangId}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            
            await loadCart(); // Reload cart after removing
        } catch (error) {
            console.error('Gagal menghapus dari cart:', error);
            alert('Gagal menghapus barang dari keranjang');
        }
    }
    
    // Update quantity
    window.updateQuantity = async function(barangId, newQuantity) {
        if (newQuantity < 1) return;
        
        try {
            await axios.put(`http://localhost:8000/api/pembeli/cart/${barangId}`, 
                { jumlah: newQuantity },
                { headers: { Authorization: `Bearer ${token}` }}
            );
            
            await loadCart(); // Reload cart after updating
        } catch (error) {
            console.error('Gagal mengubah jumlah:', error);
            alert('Gagal mengubah jumlah barang');
        }
    }
    
    // Handle checkout
    document.getElementById('checkout-button').addEventListener('click', () => {
        window.location.href = '/checkout';
    });
});
</script>
@endsection