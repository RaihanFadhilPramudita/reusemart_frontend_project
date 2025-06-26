@extends('layouts.public')

@section('title', 'Detail Barang')

@section('content')
<div class="max-w-4xl mx-auto p-6">
  <div class="mb-4">
    <a href="/" class="inline-flex items-center text-green-600 hover:text-green-800 text-sm font-semibold">
      ← Kembali ke Beranda
    </a>
  </div>
  <div id="detail" class="bg-white rounded shadow p-6"></div>
  
  <div class="mt-8 bg-white rounded shadow p-6">
    <h2 class="text-xl font-bold mb-4">Diskusi Produk</h2>
    
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

  <div class="mt-10">
    <h2 class="text-lg font-semibold mb-3">Produk Serupa</h2>
    <div id="produk-serupa" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4"></div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  const id = '{{ $id }}';

 async function loadDetailBarang() {
  try {
    const res = await axios.get(`http://localhost:8000/api/barang/${id}`);
    const b = res.data.data;
    const container = document.getElementById('detail');

    container.innerHTML = `
      <div class="grid md:grid-cols-2 gap-6">
        <!-- Swiper Carousel -->
        <div>
          <div class="swiper mySwiper w-full h-72 rounded relative">
            <div class="swiper-wrapper">
              ${
                b.GAMBAR_BARANG && Array.isArray(b.GAMBAR_BARANG) && b.GAMBAR_BARANG.length > 0
                  ? b.GAMBAR_BARANG.map(src => `
                      <div class="swiper-slide">
                        <img src="${src}" alt="Foto Barang" class="w-full h-72 object-cover rounded" />
                      </div>
                    `).join('')
                  : `
                    <div class="swiper-slide">
                      <img src="${b.GAMBAR_URL ?? '/img/default.jpg'}" alt="Gambar Barang" class="w-full h-72 object-cover rounded" />
                    </div>
                  `
              }
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
          </div>
        </div>

        <!-- Detail Produk -->
        <div>
          <h1 class="text-2xl font-bold mb-2">${b.NAMA_BARANG}</h1>
          <div class="text-green-600 text-2xl font-bold mb-2">Rp${parseInt(b.HARGA).toLocaleString()}</div>

          <div class="grid gap-1 text-sm text-gray-700 mb-4">
            <div><strong>Kategori:</strong> ${b.KATEGORI}</div>
            <div><strong>Penitip:</strong> ${b.PENITIP.NAMA_PENITIP}</div>
            <div class="flex items-center gap-2">
              <strong>Rating Penitip:</strong>
              ${renderStarRating(b.PENITIP?.RATING ?? 0)}
              <span class="text-sm text-gray-600 ml-1">(${(b.PENITIP.RATING ?? 0).toFixed(1)} / 5)</span>
            </div>
          </div>

          <div class="mb-2 text-gray-600">
            <strong>Status Garansi:</strong> 
            <span class="font-semibold">
              ${b.TANGGAL_GARANSI 
                ? (new Date(b.TANGGAL_GARANSI) >= new Date()
                  ? 'Masih Bergaransi (sampai ' + new Date(b.TANGGAL_GARANSI).toLocaleDateString('id-ID') + ')' 
                  : 'Tidak Bergaransi (berakhir ' + new Date(b.TANGGAL_GARANSI).toLocaleDateString('id-ID') + ')')
                : 'Tidak Bergaransi'}
            </span>
          </div>

          <div class="flex gap-3 mt-4">
            <button onclick="beliBarang()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Beli Sekarang</button>
            <button onclick="addToCart(${b.ID_BARANG})" class="border border-green-600 text-green-600 px-4 py-2 rounded hover:bg-green-100">Tambah Keranjang</button>
          </div>
        </div>
      </div>

      <div class="mt-8">
        <h2 class="text-lg font-semibold mb-2">Deskripsi Produk</h2>
        <div class="text-gray-700 mb-4">${b.DESKRIPSI || '-'}</div>
      </div>
    `;

      setTimeout(() => {
        new Swiper(".mySwiper", {
          loop: true,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
        });
      }, 50); 

    } catch (err) {
      document.getElementById('detail').innerHTML = '<p class="text-red-500">Gagal memuat detail barang.</p>';
      console.error(err);
    }
  }

  function renderStarRating(rating) {
    const fullStars = Math.floor(rating);
    const halfStar = rating - fullStars >= 0.5;
    let html = '';

    for (let i = 0; i < fullStars; i++) {
      html += '<span class="text-yellow-500">★</span>';
    }
    if (halfStar) {
      html += '<span class="text-yellow-500">☆</span>';
    }
    for (let i = fullStars + (halfStar ? 1 : 0); i < 5; i++) {
      html += '<span class="text-gray-300">★</span>';
    }
    return html;
  }


  async function loadProdukSerupa() {
    try {
      const res = await axios.get(`http://localhost:8000/api/barang/${id}/similar`);
      const list = res.data.data;

      if (!list.length) return;

      const html = list.map(item => `
        <div class="bg-white shadow rounded p-2">
        <img src="${
          (item.GAMBAR_BARANG && item.GAMBAR_BARANG.length > 0)
            ? item.GAMBAR_BARANG[0]
            : (item.GAMBAR_URL ?? '/img/default.jpg')
        }" 
      class="w-full h-28 object-cover rounded mb-1" />
          <div class="text-sm font-semibold truncate">${item.NAMA_BARANG}</div>
          <div class="text-green-600 font-bold text-sm">Rp${parseInt(item.HARGA).toLocaleString()}</div>
          <div class="text-xs text-gray-500">${item.STOK ?? '-'} Terjual</div>
          <div class="text-xs text-gray-500">Dikirim dari Jawa Timur</div>
        </div>
      `).join('');

      document.getElementById('produk-serupa').innerHTML = html;
    } catch (err) {
      console.error('Gagal memuat produk serupa:', err);
    }
  }


  async function loadDiskusi() {
    try {
      const response = await axios.get(`http://localhost:8000/api/diskusi/barang/${id}`);
      const diskusi = response.data.data || [];
      const diskusiContainer = document.getElementById('daftar-diskusi');
      
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
      document.getElementById('daftar-diskusi').innerHTML = `
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
  
  // Event listener untuk form diskusi
  document.addEventListener('DOMContentLoaded', function() {
    const formDiskusi = document.getElementById('form-diskusi');
    const loginPrompt = document.getElementById('login-prompt');
    
    // Cek apakah user sudah login
    const token = localStorage.getItem('token');
    if (!token) {
      loginPrompt.classList.remove('hidden');
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
          alert('Anda tidak terdaftar. Silakan login kembali');
          window.location.href = '/login';
        } else {
          alert('Gagal mengirim pesan. Silakan login terlebih dahulu/coba lagi');
        }
      }
    });
  });

  function beliBarang() {
    const token = localStorage.getItem('token');
    if (!token) {
      window.location.href = '/login';
      return;
    }

      axios.post('http://localhost:8000/api/pembeli/cart', 
      { id_barang: id },
      { headers: { Authorization: `Bearer ${token}` }}
    ).then(response => {
      window.location.href = '/checkout';
    }).catch(error => {
      console.error('Gagal menambahkan ke keranjang:', error);
      alert('Gagal menambahkan barang ke keranjang');
    });
  }

  function tambahKeKeranjang(idBarang) {
    const token = localStorage.getItem('token');
    if (!token) {
      window.location.href = '/login';
      return;
    }

    axios.post('http://localhost:8000/api/pembeli/cart', 
      { id_barang: idBarang },
      { headers: { Authorization: `Bearer ${token}` }}
    ).then(response => {
      alert('Barang berhasil ditambahkan ke keranjang');
    }).catch(error => {
      console.error('Gagal menambahkan ke keranjang:', error);
      alert('Gagal menambahkan barang ke keranjang');
    });
}

    function addToCart(idBarang) {
      const token = localStorage.getItem('token');
      
      if (!token) {
          alert('Silakan login terlebih dahulu!');
          window.location.href = '/login';
          return;
      }

      axios.post('http://localhost:8000/api/pembeli/cart', {
          id_barang: idBarang,
          jumlah: 1
      }, {
          headers: {
              Authorization: `Bearer ${token}`
          }
      })
      .then(res => {
          alert('Barang berhasil ditambahkan ke keranjang!');
          console.log(res.data);
      })
      .catch(err => {
          console.error('Gagal menambahkan ke keranjang:', err.response?.data || err);
          alert('Gagal menambahkan barang ke keranjang.\n\n' + (err.response?.data?.message || 'Server error'));
      });
  }


  // Load all data when page loads
  loadDetailBarang();
  loadProdukSerupa();
  loadDiskusi();
</script>
@endsection