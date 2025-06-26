<div class="w-64 bg-white shadow h-screen">
  <div class="p-6 border-b">
    <h1 class="text-lg font-bold">Panel Penitip</h1>
    <p id="sidebarNamaPenitip" class="text-sm text-gray-600">Memuat...</p>
  </div>
  <nav class="p-4 space-y-2">
  <a href="{{ url('/penitip') }}" class="block text-gray-800 hover:text-green-600">Dashboard</a>
  <a href="{{ url('/penitip/profil') }}" class="block text-gray-800 hover:text-green-600">Profil Saya</a>
  <a href="{{ url('/penitip/penitipan') }}" class="block text-gray-800 hover:text-green-600">Barang Titipan</a>
  <button onclick="logout()" class="text-left w-full text-red-600 hover:underline">Logout</button>
</nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const user = JSON.parse(localStorage.getItem('user'));
  const nama = user?.nama_penitip ?? 'Penitip';
  document.getElementById('sidebarNamaPenitip').textContent = nama;
});

function logout() {
  if (confirm('Yakin ingin logout?')) {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '/login';
  }
}
</script>
