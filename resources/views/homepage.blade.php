@extends('layouts.public')
@section('title', 'Beranda')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
  <h2 class="text-2xl font-bold mb-4">Produk Terbaru</h2>
 
  <div class="overflow-x-auto">
    <div id="barang-list" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 min-w-[1000px]">
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  function detectUserRole() {
    const userStr = localStorage.getItem('user');
    if (!userStr) return null;
    
    try {
      const user = JSON.parse(userStr);
      
      if (user.NAMA_PEMBELI) {
        return 'pembeli';
      } else if (user.NAMA_PENITIP) {
        return 'penitip';
      } else if (user.NAMA_ORGANISASI) {
        return 'organisasi';
      } else if (user.jabatan) {
        const jabatan = user.jabatan.NAMA_JABATAN?.toLowerCase();
        if (jabatan === 'owner') {
          return 'owner';
        } else if (jabatan === 'customer service') {
          return 'cs';
        } else if (jabatan === 'admin') {
          return 'admin';
        } else if (jabatan === 'gudang') {
          return 'gudang';
        } else if (jabatan === 'kurir') {
          return 'kurir';
        } else if (jabatan === 'hunter') {
          return 'hunter';
        }
      }
      
      return null; // No recognizable role
    } catch (error) {
      console.error("Error parsing user data:", error);
      return null;
    }
  }

  function updateProfileLink() {
    const token = localStorage.getItem('token');
    if (!token) return; // Not logged in
    
    const userRole = detectUserRole();
    if (!userRole) return; // No role detected
    
    const profileLink = document.querySelector('header a[href="/login"]');
    if (profileLink) {
      const profileRoutes = {
        'pembeli': 'pembeli/profile',
        'penitip': '/penitip/profile',
        'organisasi': '/organisasi/profile',
        'admin': '/admin/dashboard',
        'cs': '/cs/dashboard',
        'gudang': '/gudang/orga',
        'kurir': '/kurir/dashboard',
        'owner': '/owner/dashboard',
        'hunter': '/hunter/dashboard'
      };
      
      profileLink.href = profileRoutes[userRole] || '/profile';
      console.log(`Updated profile link to: ${profileLink.href}`);
    }
  }

  async function loadBarang(keyword = '') {
    const container = document.getElementById('barang-list');
    
    try {
      const url = keyword
        ? `http://localhost:8000/api/barang/search?q=${encodeURIComponent(keyword)}`
        : `http://localhost:8000/api/barang`;

      const res = await axios.get(url);
      let list = res.data.data?.data || res.data.data || [];

      list = list.filter(item => item.STATUS_BARANG === 'Tersedia');

      if (!list.length) {
        container.innerHTML = '<div class="col-span-full text-gray-500">Tidak ada barang tersedia.</div>';
        return;
      }

      container.innerHTML = list.map(item => {
        let gambarArray = [];
        try {
          gambarArray = JSON.parse(item.GAMBAR);
        } catch (e) {
          console.error('Gagal parsing GAMBAR:', e);
        }

        const thumbnail = gambarArray?.[0]
          ? `http://localhost:8000/storage/${gambarArray[0].replace(/\\/g, '')}`
          : `http://localhost:8000/img/default.jpg`;

        return `
          <div class="bg-white rounded shadow p-4 flex flex-col hover:shadow-md transition">
            <img src="${thumbnail}" alt="${item.NAMA_BARANG}" class="w-full h-40 object-cover rounded mb-2">
            <div class="font-bold truncate">${item.NAMA_BARANG}</div>
            <div class="text-green-600 font-bold">Rp${parseInt(item.HARGA).toLocaleString()}</div>
            <div class="text-gray-500 text-xs">Status: ${item.STATUS_BARANG}</div>
            <a href="/barang/${item.ID_BARANG}" class="mt-auto bg-green-600 text-white px-3 py-1 text-center rounded hover:bg-green-700 transition mt-3">Lihat Detail</a>
          </div>
        `;
      }).join('');
    } catch (err) {
      console.error("Gagal memuat data barang:", err);
      container.innerHTML = '<div class="col-span-full text-red-500">Gagal memuat data. Silakan coba lagi nanti.</div>';
    }
  }

 
  document.addEventListener('DOMContentLoaded', function() {
    updateProfileLink();
    
    const searchButton = document.querySelector('header button');
    const searchInput = document.querySelector('header input[type="text"]');
    
    if (searchButton && searchInput) {
      searchButton.addEventListener('click', () => {
        const keyword = searchInput.value.trim();
        loadBarang(keyword);
      });
      
      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          searchButton.click();
        }
      });
    }
    
    loadBarang();
  });
</script>
@endsection

