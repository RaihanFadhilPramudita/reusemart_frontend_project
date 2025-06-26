{{-- Diskusi Barang Component --}}
<div class="mt-8 bg-white rounded shadow p-6">
  <h2 class="text-xl font-bold mb-4">Diskusi Produk</h2>
  
  <!-- Form Diskusi -->
  <div class="mb-6">
    <form id="form-diskusi" class="flex flex-col space-y-2">
      <textarea id="isi-pesan" placeholder="Tanyakan tentang produk ini..." 
        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500" rows="3"></textarea>
      <div class="flex justify-between items-center">
        <div id="login-prompt" class="text-sm text-gray-500 hidden">
          <a href="/login" class="text-green-600 hover:underline">Login</a> untuk berpartisipasi dalam diskusi
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">
          Kirim Pertanyaan
        </button>
      </div>
    </form>
  </div>

  <!-- Daftar Diskusi -->
  <div id="daftar-diskusi" class="space-y-4 divide-y">
    <div class="text-center py-6 text-gray-500">Memuat diskusi...</div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const barangId = '{{ $id }}';
  const diskusiContainer = document.getElementById('daftar-diskusi');
  const formDiskusi = document.getElementById('form-diskusi');
  const loginPrompt = document.getElementById('login-prompt');
  
  // Cek apakah user sudah login
  const token = localStorage.getItem('token');
  if (!token) {
    loginPrompt.classList.remove('hidden');
  }
  
  // Load diskusi
  loadDiskusi();
  
  // Ambil data diskusi dari API
  async function loadDiskusi() {
    try {
      const response = await axios.get(`http://localhost:8000/api/pembeli/diskusi/barang/${barangId}`);
      const diskusi = response.data.data || [];
      
      if (diskusi.length === 0) {
        diskusiContainer.innerHTML = `
          <div class="text-center py-6 text-gray-500">
            Belum ada diskusi untuk produk ini. Jadilah yang pertama bertanya!
          </div>
        `;
        return;
      }
      
      diskusiContainer.innerHTML = diskusi.map(item => `
        <div class="py-4">
          <div class="flex items-start mb-2">
            <div class="flex-shrink-0 w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
              <span class="text-gray-600 font-semibold">${getInitials(item.pembeli?.NAMA_PEMBELI || item.pegawai?.NAMA_PEGAWAI || 'User')}</span>
            </div>
            <div class="flex-1">
              <div class="flex justify-between">
                <p class="font-medium ${item.pegawai ? 'text-green-600' : 'text-gray-900'}">
                  ${item.pembeli?.NAMA_PEMBELI || item.pegawai?.NAMA_PEGAWAI || 'User'}
                  ${item.pegawai ? ' <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">CS</span>' : ''}
                </p>
              </div>
              <p class="mt-1 text-gray-600">${item.ISI_PESAN}</p>
            </div>
          </div>
        </div>
      `).join('');
      
    } catch (error) {
      console.error('Error loading diskusi:', error);
      diskusiContainer.innerHTML = `
        <div class="text-center py-6 text-red-500">
          Gagal memuat diskusi. Silakan coba lagi nanti.
        </div>
      `;
    }
  }
  
  // Fungsi untuk mendapatkan inisial nama
  function getInitials(name) {
    if (!name || name === 'User') return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
  }
  
  // Handle form submit
    formDiskusi.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const isiPesan = document.getElementById('isi-pesan').value.trim();
        if (!isiPesan) return;
        
        // Cek apakah user sudah login
        if (!token) {
        alert('Silakan login terlebih dahulu untuk bertanya');
        window.location.href = '/login';
        return;
        }
        
        try {
        // This is the correct endpoint for pembeli
        await axios.post('http://localhost:8000/api/pembeli/diskusi', {
            id_barang: id,
            isi_pesan: isiPesan
        }, {
            headers: {
            'Authorization': `Bearer ${token}`
            }
        });
        
        // Reset form dan reload diskusi
        document.getElementById('isi-pesan').value = '';
        loadDiskusi();
        
        } catch (error) {
        console.error('Error sending message:', error);
        if (error.response?.status === 401) {
            alert('Sesi anda telah berakhir. Silakan login kembali');
            window.location.href = '/login';
        } else {
            alert('Gagal mengirim pesan. Silakan login terlebih dahulu/coba lagi');
        }
        }
    });
});
</script>