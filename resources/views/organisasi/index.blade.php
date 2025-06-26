@extends('layouts.app')

@section('title', 'Data Organisasi')

@section('content')
<div class="flex">
  <x-admin-sidebar />

  <main class="flex-1 p-6">
    <form id="searchForm" class="mb-4">
      <input type="text" id="searchInput" name="search" placeholder="Cari organisasi..." class="border px-3 py-2 rounded w-full" />
    </form>

    <div class="overflow-x-auto">
      <div class="bg-white shadow rounded min-w-[1200px] divide-y">
        <div class="px-4 py-3 font-semibold grid grid-cols-9 gap-2 min-w-max">
          <div>ID</div>
          <div>Nama</div>
          <div>Alamat</div>
          <div>Email</div>
          <div>ID Pegawai</div>
          <div>Username</div>
          <div>Password</div>
          <div>No. Telepon</div>
          <div>Aksi</div>
        </div>

        <div id="organisasi-list" class="divide-y"></div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Edit -->
<div id="modalEditOrganisasi" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-xl">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-bold">EDIT ORGANISASI</h2>
      <button onclick="tutupModalEdit()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
    </div>
    <form id="formEditOrganisasi">
      <input type="hidden" name="ID_ORGANISASI" id="editId">
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">NAMA ORGANISASI</label>
        <input type="text" id="editNama" name="NAMA_ORGANISASI" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">ALAMAT</label>
        <input type="text" id="editAlamat" name="ALAMAT" class="w-full border rounded px-3 py-2">
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">EMAIL</label>
        <input type="email" id="editEmail" name="EMAIL" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">USERNAME</label>
        <input type="text" id="editUsername" name="USERNAME" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">NO TELEPON</label>
        <input type="text" id="editTelepon" name="NO_TELEPON" class="w-full border rounded px-3 py-2">
      </div>
      <div class="text-right">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">SIMPAN</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  const baseUrl = 'http://localhost:8000/api/admin/organisasi';
  const searchUrl = `${baseUrl}/search`;

  async function loadOrganisasi(url) {
    try {
      const res = await axios.get(url, {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });

      const list =
        Array.isArray(res.data) ? res.data :
        Array.isArray(res.data.data) ? res.data.data :
        Array.isArray(res.data.data?.data) ? res.data.data.data :
        [];

      const container = document.getElementById('organisasi-list');

      if (!list.length) {
        container.innerHTML = '<div class="p-4 text-gray-500">Tidak ada data</div>';
        return;
      }

      container.innerHTML = list.map(item => `
        <div class="px-4 py-3 grid grid-cols-9 gap-2 items-center text-sm">
          <div>${item.ID_ORGANISASI}</div>
          <div>${item.NAMA_ORGANISASI}</div>
          <div>${item.ALAMAT}</div>
          <div>${item.EMAIL}</div>
          <div>${item.ID_PEGAWAI}</div>
          <div>${item.USERNAME}</div>
          <div>••••••••</div>
          <div>${item.NO_TELEPON}</div>
          <div class="space-x-2">
            <button onclick='bukaModalEdit(${JSON.stringify(item)})' class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
            <button onclick="hapusOrganisasi(${item.ID_ORGANISASI})" class="bg-red-600 text-white px-3 py-1 rounded">Hapus</button>
          </div>
        </div>
      `).join('');
    } catch (err) {
      console.error("Gagal memuat data organisasi:", err);
    }
  }

  async function hapusOrganisasi(id) {
    if (!confirm('Yakin ingin menghapus organisasi ini?')) return;
    try {
      await axios.delete(`${baseUrl}/${id}`, {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });
      alert('Organisasi berhasil dihapus!');
      loadOrganisasiFromIndex();
    } catch (err) {
      if (err.response && err.response.status === 422) {
        alert(err.response.data.message || 'Organisasi tidak dapat dihapus karena masih memiliki relasi data.');
      } else {
        alert("Gagal menghapus organisasi");
        console.error("Gagal hapus:", err);
      }
    }
  }

  function bukaModalEdit(data) {
    document.getElementById('editId').value = data.ID_ORGANISASI;
    document.getElementById('editNama').value = data.NAMA_ORGANISASI;
    document.getElementById('editAlamat').value = data.ALAMAT;
    document.getElementById('editEmail').value = data.EMAIL;
    document.getElementById('editUsername').value = data.USERNAME;
    document.getElementById('editTelepon').value = data.NO_TELEPON;

    const modal = document.getElementById('modalEditOrganisasi');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function tutupModalEdit() {
    const modal = document.getElementById('modalEditOrganisasi');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  document.getElementById('formEditOrganisasi').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = document.getElementById('editId').value;

    try {
      await axios.put(`${baseUrl}/${id}`, {
        nama_organisasi: document.getElementById('editNama').value,
        alamat: document.getElementById('editAlamat').value,
        email: document.getElementById('editEmail').value,
        username: document.getElementById('editUsername').value,
        no_telepon: document.getElementById('editTelepon').value,
      }, {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });


      alert('Berhasil mengubah organisasi!');
      tutupModalEdit();
      loadOrganisasiFromIndex();
    } catch (err) {
      if (err.response?.data?.message) {
        alert(`Gagal edit: ${err.response.data.message}`);
      } else {
        console.error("Gagal update:", err);
        alert("Terjadi kesalahan saat update.");
      }
    }
  });

  document.getElementById('searchForm').addEventListener('submit', e => {
    e.preventDefault();
    const keyword = document.getElementById('searchInput').value.trim();
    if (keyword.length === 0) {
      loadOrganisasiFromIndex();
    } else {
      loadOrganisasiFromSearch(keyword);
    }
  });

  async function loadOrganisasiFromIndex() {
    await loadOrganisasi(baseUrl);
  }

  async function loadOrganisasiFromSearch(keyword) {
    const url = `${searchUrl}?q=${encodeURIComponent(keyword)}`;
    await loadOrganisasi(url);
  }

  loadOrganisasiFromIndex();
</script>
@endsection
