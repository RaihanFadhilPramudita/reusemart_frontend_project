@extends('layouts.app')

@section('title', 'Diskusi Produk')

@section('content')
<div class="flex">
  <x-cs-sidebar />

  <main class="flex-1 p-6">
    <div class="bg-white rounded shadow p-6">
      <div id="barang-info" class="mb-6"></div>

      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Komentar</h3>
        <ul id="komentarList" class="space-y-3 mt-4"></ul>
      </div>

      <form id="formKomentar" class="space-y-3">
        <textarea id="inputKomentar" rows="3" class="w-full border rounded p-2" placeholder="Tulis komentar..."></textarea>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Kirim</button>
      </form>
    </div>
  </main>
</div>
@endsection

@section('scripts')
<script>
  const idBarang = window.location.pathname.split('/').pop();
  const barangInfo = document.getElementById('barang-info');
  const komentarList = document.getElementById('komentarList');
  const form = document.getElementById('formKomentar');

  // Ambil token CS dari localStorage
    //   const token = localStorage.getItem('token'); // pastikan token tersimpan saat login pegawai (CS)

  async function loadDetailBarang() {
    try {
      const res = await axios.get(`http://localhost:8000/api/barang/${idBarang}`);
      const item = res.data.data;

      barangInfo.innerHTML = `
        <img src="http://localhost:8000/storage/${item.FOTO_BARANG}" class="w-48 mb-4">
        <h2 class="text-xl font-bold">${item.NAMA_BARANG}</h2>
        <p>${item.DESKRIPSI}</p>
      `;
    } catch (err) {
      barangInfo.innerHTML = '<p class="text-red-500">Gagal memuat barang.</p>';
    }
  }

  async function loadKomentar() {
    try {
        const res = await axios.get(`http://localhost:8000/api/diskusi/produk/${idBarang}`);
        const data = res.data.data;
        console.log('Komentar diterima:', data); // log data komentar

        komentarList.innerHTML = data.map(d => `
        <li class="border rounded p-3">
            <strong>${d.pembeli?.NAMA_PEMBELI ?? d.pegawai?.NAMA_PEGAWAI ?? 'Anonim'}</strong>
            <p>${d.ISI_PESAN}</p>
            <span class="text-sm text-gray-500">${new Date(d.created_at).toLocaleString()}</span>
        </li>
        `).join('');
    } catch (err) {
        console.error('Gagal load komentar:', err);
        komentarList.innerHTML = '<p class="text-red-500">Gagal memuat komentar.</p>';
    }
    }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const isiPesan = document.getElementById('inputKomentar').value;
    if (!isiPesan.trim()) return;

    try {
     console.log("Token:", localStorage.getItem('token'));
      await axios.post(`http://localhost:8000/api/diskusi`, {
        id_barang: idBarang,
        isi_pesan: isiPesan
      }, {
        headers: {
           Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      });

      document.getElementById('inputKomentar').value = '';
      loadKomentar();
    } catch (err) {
      alert('Gagal mengirim komentar. Pastikan Anda login sebagai CS.');
    }
  });

  loadDetailBarang();
  loadKomentar();
</script>
@endsection
