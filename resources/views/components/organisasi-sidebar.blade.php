<aside class="w-64 bg-white shadow-md">
    <div class="p-4 text-xl font-bold border-b">Organisasi Panel</div>
    <nav class="p-4 space-y-2">
        <a href="/cs/penitip" class="block p-2 rounded hover:bg-gray-100">Request Donasi</a>
        <a href="/" onclick="logout()" class="block p-2 rounded text-red-500 hover:bg-red-100">Logout</a>
    </nav>
</aside>

<script>
  async function logout() {
    const token = localStorage.getItem('token');
    if (!token) return window.location.href = "/login";

    try {
      await axios.post('http://localhost:8000/api/organisasi/logout', {}, {
        headers: { Authorization: `Bearer ${token}` }
      });
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = "/login";
    } catch (error) {
      alert("Logout gagal.");
    }
  }
</script>
