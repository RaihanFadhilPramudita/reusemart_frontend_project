@extends('layouts.public')

@section('title', 'Keranjang Belanja')

@section('content')
<div id="toast-container" class="fixed top-4 right-4 z-50"></div>
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
    
    // Inisialisasi
    loadCart();
    
    // Load cart items
    async function loadCart() {
        try {
            const response = await axios.get('http://localhost:8000/api/pembeli/cart', {
                headers: { Authorization: `Bearer ${token}` }
            });
            
            const cartItems = response.data.data || [];
            renderCart(cartItems);
        } catch (error) {
            console.error('Gagal memuat cart:', error);
            showEmptyCart('Terjadi kesalahan saat memuat keranjang');
        }
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
            // Always use price as total since quantity is fixed at 1
            const price = parseInt(item.barang.HARGA);
            const total = price; // No multiplication by quantity since it's always 1
            subtotal += total;
            
            return `
                <div class="p-4 flex items-center" data-id="${item.ID_BARANG}">
                    <div class="w-20 h-20 flex-shrink-0 mr-4 bg-gray-100 rounded">
                        <img src="${item.barang.GAMBAR_URL || '/img/default.jpg'}" 
                            class="w-full h-full object-cover rounded" 
                            alt="${item.barang.NAMA_BARANG}">
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="font-medium">${item.barang.NAMA_BARANG}</h3>
                        <p class="text-green-600 font-bold">Rp${price.toLocaleString()}</p>
                        <div class="text-sm text-gray-500 mt-1">Jumlah: 1</div>
                    </div>
                    
                    <div class="flex flex-col items-end ml-4">
                        <div class="font-bold">Rp${total.toLocaleString()}</div>
                        <button onclick="removeItem(${item.ID_CART})" 
                            class="text-red-500 mt-2 hover:text-red-700">
                            Hapus
                        </button>
                    </div>
                </div>
            `;
        }).join('');
        
        // Update summary - now just counting items
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
                    { id_barang: barangId, jumlah: 1 }, // Always set jumlah to 1
                    { headers: { Authorization: `Bearer ${token}` }}
                );
                
                loadCart();
                alert('Barang berhasil ditambahkan ke keranjang');
            } catch (error) {
                console.error('Gagal menambahkan ke cart:', error);
                alert('Gagal menambahkan barang ke keranjang');
            }
        }
    
    // Remove from cart
 // Remove from cart with custom toast notification
    window.removeItem = async function(cartId) {
        if (!confirm('Yakin ingin menghapus barang ini dari keranjang?')) return;
        
        try {
            // Make API call to delete item
            await axios.delete(`http://localhost:8000/api/pembeli/cart/${cartId}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            
            // Show custom toast notification
            showToast('Barang berhasil dihapus!', 'success');
            
            // Reload cart after item deletion
            await loadCart();
        } catch (error) {
            console.error('Gagal menghapus dari cart:', error);
            showToast('Gagal menghapus barang dari keranjang', 'error');
        }
    }

    // Custom toast notification function
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `mb-3 p-4 rounded shadow-lg flex items-center ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        
        // Add icon based on type
        const icon = type === 'success' ? '✅' : '❌';
        
        // Set content
        toast.innerHTML = `
            <span class="mr-2">${icon}</span>
            <span>${message}</span>
        `;
        
        // Add to container
        container.appendChild(toast);
        
        // Remove after 3 seconds with fade effect
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 500ms';
            setTimeout(() => {
                toast.remove();
            }, 500);
        }, 3000);
    }
    
    // Update quantity
    window.updateQuantity = async function(barangId, newQuantity) {
        if (newQuantity < 1) return;
        
        try {
            await axios.put(`http://localhost:8000/api/pembeli/cart/${barangId}`, 
                { jumlah: newQuantity },
                { headers: { Authorization: `Bearer ${token}` }}
            );
            
            loadCart();
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