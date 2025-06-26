@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('content')
<div class="flex">
  <x-admin-sidebar />

  <main class="flex-1 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Data Pegawai</h2>
      <a href="#" id="btnTambahPegawai" class="bg-green-600 text-white px-4 py-2 rounded">Tambah Pegawai</a>
    </div>

    <form method="GET" action="{{ route('pegawai.index') }}">
      <input type="text" name="search" placeholder="Cari pegawai..." class="border px-3 py-2 rounded mb-4 w-full" value="{{ request('search') }}" />
    </form>

    <div class="overflow-x-auto">
      <div class="bg-white shadow rounded min-w-[900px] divide-y">
        <div class="px-4 py-3 font-semibold grid grid-cols-7 text-sm">
          <div>Jabatan</div>
          <div>Nama</div>
          <div>Email</div>
          <div>Username</div>
          <div>Password</div>
          <div>Tgl Lahir</div>
          <div>Aksi</div>
        </div>

        <div id="pegawai-list" class="divide-y"></div>
      </div>
    </div>

<div id="modalTambahPegawai" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-xl">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-bold">TAMBAH PEGAWAI</h2>
      <button onclick="tutupModalTambah()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
    </div>
    <form id="formTambahPegawai">
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">JABATAN</label>
        <select id="tambahJabatan" class="w-full border rounded px-3 py-2" required></select>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">EMAIL</label>
        <input type="email" id="tambahEmail" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">PASSWORD</label>
        <input type="password" id="tambahPassword" class="w-full border rounded px-3 py-2" required minlength="6">
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">NAMA</label>
        <input type="text" id="tambahNama" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">NO TELEPON</label>
        <input type="text" id="tambahTelepon" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">TANGGAL LAHIR</label>
        <input type="date" id="tambahTglLahir" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">USERNAME</label>
        <input type="text" id="tambahUsername" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="text-right">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">SIMPAN</button>
      </div>
    </form>
  </div>
</div>

<div id="modalEditPegawai" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-xl">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-bold">EDIT PEGAWAI</h2>
      <button onclick="tutupModalEdit()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
    </div>
    <form id="formEditPegawai">
      <input type="hidden" id="editId">
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">JABATAN</label>
        <select id="editJabatan" class="w-full border rounded px-3 py-2" required></select>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">EMAIL</label>
        <input type="email" id="editEmail" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">NAMA</label>
        <input type="text" id="editNama" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">NO TELEPON</label>
        <input type="text" id="editTelepon" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">TANGGAL LAHIR</label>
        <input type="date" id="editTglLahir" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">USERNAME</label>
        <input type="text" id="editUsername" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="text-right">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">SIMPAN</button>
      </div>
    </form>
  </div>
</div>

  </main>
</div>
@endsection

@section('scripts')
<script>
  const baseUrl = 'http://localhost:8000/api/admin/pegawai';
  const jabatanUrl = 'http://localhost:8000/api/admin/jabatan';

  let daftarJabatan = [];

  async function loadJabatanDropdown() {
    const res = await axios.get(jabatanUrl);
    daftarJabatan = res.data.data || res.data;

    const jabatanOptions = daftarJabatan.map(j => `<option value="${j.ID_JABATAN}">${j.NAMA_JABATAN}</option>`).join('');
    document.getElementById('editJabatan').innerHTML = jabatanOptions;
    document.getElementById('tambahJabatan').innerHTML = jabatanOptions;
  }

  async function loadPegawai(keyword = '') {
    const url = keyword ? `${baseUrl}/search?q=${encodeURIComponent(keyword)}` : baseUrl;

    try {
      const res = await axios.get(url);
      const list = res.data.data?.data || res.data.data || res.data;
      const container = document.getElementById('pegawai-list');

      if (!list.length) {
        container.innerHTML = '<div class="p-4 text-gray-500">Tidak ada data</div>';
        return;
      }

      container.innerHTML = list.map(item => `
        <div class="px-4 py-3 grid grid-cols-7 gap-2 items-start text-sm whitespace-normal break-words">
          <div>${item.jabatan?.NAMA_JABATAN || '-'}</div>
          <div>${item.NAMA_PEGAWAI}</div>
          <div>${item.EMAIL}</div>
          <div>${item.USERNAME}</div>
          <div>••••••••</div>
          <div>${item.TANGGAL_LAHIR}</div>
          <div class="space-x-2">
            <button onclick='bukaModalEdit(${JSON.stringify(item)})' class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
            <button onclick="hapusPegawai(${item.ID_PEGAWAI})" class="bg-red-600 text-white px-3 py-1 rounded">Hapus</button>
          </div>
        </div>
      `).join('');
    } catch (err) {
      console.error("Gagal memuat data pegawai", err);
    }
  }


  document.querySelector('form').addEventListener('submit', e => {
    e.preventDefault();
    const keyword = e.target.search.value.trim();
    loadPegawai(keyword);
  });

  document.getElementById('formTambahPegawai').addEventListener('submit', async e => {
    e.preventDefault();
    try {
      await axios.post(baseUrl, {
        id_jabatan: document.getElementById('tambahJabatan').value,
        email: document.getElementById('tambahEmail').value,
        password: document.getElementById('tambahPassword').value,
        nama_pegawai: document.getElementById('tambahNama').value,
        no_telepon: document.getElementById('tambahTelepon').value,
        tanggal_lahir: document.getElementById('tambahTglLahir').value,
        username: document.getElementById('tambahUsername').value,
      });

      alert('Berhasil menambahkan pegawai!');
      tutupModalTambah();
      loadPegawai();
    } catch (err) {
      console.error("Gagal tambah:", err.response?.data || err.message || err);
      alert("Gagal menambahkan pegawai");
    }
  });

  document.getElementById('formEditPegawai').addEventListener('submit', async e => {
    e.preventDefault();
    const id = document.getElementById('editId').value;
    try {
      await axios.put(`${baseUrl}/${id}`, {
        id_jabatan: document.getElementById('editJabatan').value,
        email: document.getElementById('editEmail').value,
        nama_pegawai: document.getElementById('editNama').value,
        no_telepon: document.getElementById('editTelepon').value,
        tanggal_lahir: formatTanggal(document.getElementById('editTglLahir').value), 
        username: document.getElementById('editUsername').value,
      });
      alert('Berhasil mengedit pegawai!');
      tutupModalEdit();
      loadPegawai();
    } catch (err) {
      console.error("Gagal edit:", err);
      alert("Gagal mengedit pegawai");
    }
  });

  async function hapusPegawai(id) {
    if (!confirm('Yakin ingin menghapus pegawai ini?')) return;
    try {
      await axios.delete(`${baseUrl}/${id}`);
      alert('Berhasil dihapus!');
      loadPegawai();
    } catch (err) {
      alert("Gagal menghapus pegawai");
      console.error(err);
    }
  }

  function bukaModalEdit(item) {
    document.getElementById('editId').value = item.ID_PEGAWAI;
    document.getElementById('editEmail').value = item.EMAIL;
    document.getElementById('editNama').value = item.NAMA_PEGAWAI;
    document.getElementById('editTelepon').value = item.NO_TELEPON;
    document.getElementById('editTglLahir').value = item.TANGGAL_LAHIR;
    document.getElementById('editUsername').value = item.USERNAME;
    document.getElementById('editJabatan').value = item.ID_JABATAN || item.jabatan?.ID_JABATAN;

    const modal = document.getElementById('modalEditPegawai');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function formatTanggal(input) {
    const date = new Date(input);
    const yyyy = date.getFullYear();
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const dd = String(date.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
  }


  function tutupModalEdit() {
    document.getElementById('modalEditPegawai').classList.add('hidden');
    document.getElementById('modalEditPegawai').classList.remove('flex');
  }

  function tutupModalTambah() {
    document.getElementById('modalTambahPegawai').classList.add('hidden');
    document.getElementById('modalTambahPegawai').classList.remove('flex');
  }

  document.getElementById('btnTambahPegawai').addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('modalTambahPegawai').classList.remove('hidden');
    document.getElementById('modalTambahPegawai').classList.add('flex');
  });

  loadJabatanDropdown();
  loadPegawai();
</script>
@endsection
