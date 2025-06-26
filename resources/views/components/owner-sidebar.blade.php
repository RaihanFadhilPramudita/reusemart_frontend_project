<aside class="w-64 min-h-screen bg-green-600 text-white shadow-lg flex flex-col">
  <div class="px-6 py-5 border-b border-green-500">
    <h2 class="text-2xl font-bold">Owner Panel</h2>
    <p class="text-sm text-green-100">Kelola donasi</p>
  </div>

  <nav class="flex-1 px-4 py-6 space-y-2 text-sm">
    <a href="/owner/request_donasi"
      class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-green-500 transition 
       {{ request()->is('owner/request_donasi') ? 'bg-white text-green-700 font-semibold' : '' }}">
      📦 <span>Request Donasi</span>
    </a>
    <a href="/owner/donasi"
      class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-green-500 transition 
       {{ request()->is('owner/donasi') ? 'bg-white text-green-700 font-semibold' : '' }}">
      🎁 <span>Donasi</span>
    </a>
    <a href="/owner/historydonasi"
      class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-green-500 transition 
       {{ request()->is('owner/historydonasi') ? 'bg-white text-green-700 font-semibold' : '' }}">
      📜 <span>History Donasi</span>
    </a>

    <a href="/owner/stok-gudang"
      class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-green-500 transition 
       {{ request()->is('owner/stok-gudang') ? 'bg-white text-green-700 font-semibold' : '' }}">
      📜 <span>Stok Gudang</span>
    </a>

    <a href="/owner/komisi"
      class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-green-500 transition 
       {{ request()->is('owner/komisi') ? 'bg-white text-green-700 font-semibold' : '' }}">
      💸 <span>Komisi</span>
    </a>

    <a href="/owner/penjualan"
      class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-green-500 transition 
       {{ request()->is('owner/penjualan') ? 'bg-white text-green-700 font-semibold' : '' }}">
      📗 <span>Penjualan</span>
    </a>

    <a href="/owner/kategori"
      class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-green-500 transition 
       {{ request()->is('owner/kategori') ? 'bg-white text-green-700 font-semibold' : '' }}">
      📝 <span>Kategori</span>
    </a>

    <a href="/owner/transaksi_penitip"
      class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-green-500 transition 
       {{ request()->is('owner/transaksi_penitip') ? 'bg-white text-green-700 font-semibold' : '' }}">
      📄 <span>Transaksi Penitip</span>
    </a>
  </nav>

  <div class="p-4 border-t border-green-500">
    <button onclick="logout()" class="w-full text-left px-4 py-2 rounded-md text-red-100 hover:bg-red-600 transition">
      🚪 Logout
    </button>
  </div>
</aside>

<script>
  async function logout() {
    const token = localStorage.getItem('token');
    if (!token) return window.location.href = "/login";

    try {
      await axios.post('http://localhost:8000/api/owner/logout', {}, {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = "/login";
    } catch (error) {
      alert("Logout gagal.");
    }
  }
</script>