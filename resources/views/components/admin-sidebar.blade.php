<aside class="w-64 bg-white shadow-md">
    <div class="p-4 text-xl font-bold border-b">Admin Panel</div>
    <nav class="p-4 space-y-2">
        <a href="/admin/organisasi" class="block p-2 rounded hover:bg-gray-100">Organisasi</a>
        <a href="/admin/pegawai" class="block p-2 rounded hover:bg-gray-100">Pegawai</a>
        <a href="/admin/jabatan" class="block p-2 rounded hover:bg-gray-100">Jabatan</a>
        <a href="/admin/merchandise" class="block p-2 rounded hover:bg-gray-100">Merchandise</a>
        <a href="/admin/penitip_admin" class="block p-2 rounded hover:bg-gray-100">Penitip</a>
        <a href="/" onclick="logout()" class="block p-2 rounded text-red-500 hover:bg-red-100">Logout</a>
    </nav>
</aside>

<script>
  function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }
</script>
