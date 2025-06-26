@extends('layouts.app')

@section('title', 'Data Penitip')

@section('content')
<div class="flex">
  <x-cs-sidebar />

  <main class="flex-1 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Data Penitip</h2>
      <a href="#" id="btnTambahPenitip" class="bg-green-600 text-white px-4 py-2 rounded">Tambah Penitip</a>
    </div>

    <form id="formCari">
      <input type="text" name="search" id="inputSearch" placeholder="Cari penitip..." class="border px-3 py-2 rounded mb-4 w-full" />
    </form>


    <div class="overflow-x-auto">
      <div class="bg-white shadow rounded min-w-[1200px] divide-y">
        <div class="px-4 py-3 font-semibold grid grid-cols-9 gap-2 min-w-max">
          <div>Nama</div>
          <div>Email</div>
          <div>No Telepon</div>
          <div>No KTP</div>
          <div>Tgl Lahir</div>
          <div>Title</div>
          <div>Foto KTP</div>
          <div>Aksi</div>
        </div>
        <div id="penitip-list" class="divide-y"></div>
      </div>
    </div>

    <div id="modalTambahPenitip" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-xl">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-bold">TAMBAH PENITIP</h2>
          <button onclick="tutupModalTambah()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
        </div>
        <form id="formTambahPenitip" enctype="multipart/form-data">
          <div class="grid grid-cols-1 gap-3">
            <input required name="nama_penitip" type="text" placeholder="Nama" class="border px-3 py-2 rounded" />
            <input required name="email" type="email" placeholder="Email" class="border px-3 py-2 rounded" />
            <input required name="password" type="password" placeholder="Password" class="border px-3 py-2 rounded" />
            <input required name="no_telepon" type="text" placeholder="No Telepon" class="border px-3 py-2 rounded" />
            <input required name="no_ktp" type="text" placeholder="No KTP" class="border px-3 py-2 rounded" />
            <input required name="tanggal_lahir" type="date" class="border px-3 py-2 rounded" />
            <input required name="foto_ktp" type="file" accept="image/*" class="border px-3 py-2 rounded" />
          </div>
          <div class="text-right mt-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">SIMPAN</button>
          </div>
        </form>
      </div>
    </div>

    <div id="modalEditPenitip" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-xl">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-bold">EDIT PENITIP</h2>
          <button onclick="tutupModalEdit()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
        </div>
        <form id="formEditPenitip" enctype="multipart/form-data">
          <input type="hidden" name="id" id="editId">
          <div class="grid grid-cols-1 gap-3">
            <input required name="nama_penitip" id="editNama" type="text" class="border px-3 py-2 rounded" />
            <input required name="email" id="editEmail" type="email" class="border px-3 py-2 rounded" />
            <input required name="no_telepon" id="editTelepon" type="text" class="border px-3 py-2 rounded" />
            <input required name="no_ktp" id="editKtp" type="text" class="border px-3 py-2 rounded" />
            <input required name="tanggal_lahir" id="editTglLahir" type="date" class="border px-3 py-2 rounded" />
            <label class="text-sm">Foto KTP (isi hanya jika ingin mengganti)</label>
            <input name="foto_ktp" id="editFotoKtp" type="file" accept="image/*" class="border px-3 py-2 rounded" />
          </div>
          <div class="text-right mt-4">
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
  const baseUrl = 'http://localhost:8000/api/cs/penitip';

  async function loadPenitip(keyword = '') {
    try {
      const url = keyword ? `${baseUrl}/search?q=${encodeURIComponent(keyword)}` : baseUrl;
      const res = await axios.get(url);
      const list = res.data.data?.data ?? res.data.data ?? [];

      const container = document.getElementById('penitip-list');

      if (!list.length) {
        container.innerHTML = '<div class="p-4 text-gray-500">Tidak ada data</div>';
        return;
      }

      container.innerHTML = list.map(item => `
        <div class="px-4 py-3 grid grid-cols-9 gap-2 items-start text-sm whitespace-normal break-words">
            <div>${item.NAMA_PENITIP}</div>
            <div>${item.EMAIL}</div>
            <div>${item.NO_TELEPON}</div>
            <div>${item.NO_KTP}</div>
            <div>${item.TANGGAL_LAHIR}</div>
            <div>${item.BADGE}</div>
            <div>${item.NO_KTP}</div>
            <div><img src="http://localhost:8000/storage/${item.FOTO_KTP}" class="w-20 h-auto" /></div>
            <div class="space-x-2">
              <button onclick='bukaModalEdit(${JSON.stringify(item)})' class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
              <button onclick="hapusPenitip(${item.ID_PENITIP})" class="bg-red-600 text-white px-3 py-1 rounded">Hapus</button>
            </div>
        </div>
        `).join('');
    } catch (err) {
      console.error("Gagal muat penitip:", err);
    }
  }


  document.getElementById('formTambahPenitip').addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const namaPenitip = formData.get('nama_penitip');

      await axios.post(baseUrl, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });

      const searchRes = await axios.get(`${baseUrl}/search?q=${encodeURIComponent(namaPenitip)}`);
      const jumlah = (searchRes.data.data?.length ?? searchRes.data.data?.data?.length ?? 0);

      alert(`Penitip "${namaPenitip}" berhasil disimpan di dalam database. Ditemukan ${jumlah} orang dengan nama "${namaPenitip}"`);

      tutupModalTambah();
      loadPenitip();
    } catch (err) {
      console.error("Gagal tambah:", err.response?.data || err.message);
      alert("Gagal menambahkan penitip");
    }
  });

  async function hapusPenitip(id) {
    if (!confirm('Yakin ingin menghapus penitip ini?')) return;
    try {
      await axios.delete(`${baseUrl}/${id}`);
      alert("Berhasil dihapus!");
      loadPenitip();
    } catch (err) {
      alert("Gagal menghapus penitip");
      console.error(err);
    }
  }

  function bukaModalEdit(item) {
    document.getElementById('editId').value = item.ID_PENITIP;
    document.getElementById('editNama').value = item.NAMA_PENITIP;
    document.getElementById('editEmail').value = item.EMAIL;
    document.getElementById('editTelepon').value = item.NO_TELEPON;
    document.getElementById('editKtp').value = item.NO_KTP;
    document.getElementById('editTglLahir').value = item.TANGGAL_LAHIR;

    const modal = document.getElementById('modalEditPenitip');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function tutupModalEdit() {
    document.getElementById('modalEditPenitip').classList.add('hidden');
    document.getElementById('modalEditPenitip').classList.remove('flex');
  }

  document.getElementById('formEditPenitip').addEventListener('submit', async e => {
    e.preventDefault();
    const id = document.getElementById('editId').value;
    const form = e.target;
    const formData = new FormData(form);

    try {
      await axios.post(`${baseUrl}/${id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
          'X-HTTP-Method-Override': 'PUT'
        }
      });
      alert('Berhasil mengedit penitip!');
      tutupModalEdit();
      loadPenitip();
    } catch (err) {
      console.error("Gagal edit:", err.response?.data || err.message);
      alert("Gagal mengedit penitip");
    }
  });

  document.getElementById('formCari').addEventListener('submit', e => {
    e.preventDefault();
    const keyword = document.getElementById('inputSearch').value.trim();
    loadPenitip(keyword);
  });



  function tutupModalTambah() {
    document.getElementById('modalTambahPenitip').classList.add('hidden');
    document.getElementById('modalTambahPenitip').classList.remove('flex');
  }

  document.getElementById('btnTambahPenitip').addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('modalTambahPenitip').classList.remove('hidden');
    document.getElementById('modalTambahPenitip').classList.add('flex');
  });

  loadPenitip();
</script>
@endsection