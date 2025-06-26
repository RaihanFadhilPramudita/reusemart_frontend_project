<aside class="w-64 bg-white shadow-md">
    <div class="p-4 text-xl font-bold border-b">Gudang Panel</div>
    <nav class="p-4 space-y-2">
        <a href="/gudang/barang" class="block p-2 rounded hover:bg-gray-100">Barang</a>
        <a href="/gudang/pengiriman" class="block p-2 rounded hover:bg-gray-100">Pengiriman</a>
        <a href="/gudang/pesanan" class="block p-2 rounded hover:bg-gray-100">Kelola Pesanan</a>
        <a href="/gudang/konfirmasi" class="block p-2 rounded hover:bg-gray-100">Konfirmasi Ambil</a>
        <a href="/" onclick="logout()" class="block p-2 rounded text-red-500 hover:bg-red-100">Logout</a>
    </nav>
</aside>

<script>
    function logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
    }
</script>