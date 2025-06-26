<!-- Updated cs-sidebar.blade.php -->
<aside class="w-64 bg-white shadow-md">
    <div class="p-4 text-xl font-bold border-b">Customer Service Panel</div>
    <nav class="p-4 space-y-2">
        <a href="/cs/penitip" class="block p-2 rounded hover:bg-gray-100">Penitip</a>
        <a href="/cs/diskusi" class="block p-2 rounded hover:bg-gray-100">Forum Diskusi</a>
        <a href="/cs/verifikasi" class="block p-2 rounded hover:bg-gray-100">Verifikasi Bukti Bayar</a>
        <a href="/cs/pesanan" class="block p-2 rounded hover:bg-gray-100">Pesanan Diproses</a>
        <a href="/cs/merchandise" class="block p-2 rounded hover:bg-gray-100">Daftar Klaim Merchandise</a>
        <a href="/" onclick="logout()" class="block p-2 rounded text-red-500 hover:bg-red-100">Logout</a>
    </nav>
</aside>

<script>
  function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }
</script>