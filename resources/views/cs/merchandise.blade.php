@extends('layouts.app')

@section('title', 'Klaim Merchandise')

@section('content')
<div class="flex">
  <x-cs-sidebar />

  <main class="flex-1 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Daftar Klaim Merchandise</h2>
      <select id="filterStatus" class="border px-3 py-2 rounded">
        <option value="semua">Semua</option>
        <option value="belum">Belum Diambil</option>
      </select>
    </div>

    <div class="overflow-x-auto">
      <div class="bg-white shadow rounded min-w-[1000px] divide-y">
        <div class="px-4 py-3 font-semibold grid grid-cols-6 gap-2">
          <div>Nama Pembeli</div>
          <div>Merchandise</div>
          <div>Tanggal Klaim</div>
          <div>Status</div>
          <div>Tanggal Ambil</div>
          <div>Aksi</div>
        </div>
        <div id="klaim-list" class="divide-y"></div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Input Tanggal Ambil -->
<div id="modalTanggal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-md">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-bold">Isi Tanggal Ambil</h2>
      <button onclick="tutupModalTanggal()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
    </div>
    <form id="formTanggalAmbil">
      <input type="hidden" name="id_redeem" id="idRedeem">
      <input required type="date" name="tanggal_ambil" id="inputTanggal" class="border px-3 py-2 rounded w-full mb-4" />
      <div class="text-right">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
    const baseUrl = 'http://localhost:8000/api/cs/klaim-merchandise';
    let allKlaimData = [];

    async function fetchKlaimData() {
        try {
            const res = await axios.get(baseUrl);
            allKlaimData = res.data.data ?? [];
            filterAndRender('semua');
        } catch (err) {
            console.error('Gagal memuat klaim:', err);
        }
    }

    function filterAndRender(status = 'semua') {
        const container = document.getElementById('klaim-list');
        let filtered = allKlaimData;

        if (status === 'belum') {
            filtered = allKlaimData.filter(item => !item.TANGGAL_AMBIL);
        }

        if (!filtered.length) {
            container.innerHTML = `
            <div class="col-span-6 text-center text-gray-500 py-6">
                Klaim merchandise tidak ada.
            </div>
            `;
            return;
        }

        container.innerHTML = filtered.map(item => `
            <div class="px-4 py-3 grid grid-cols-6 gap-2 items-center text-sm">
            <div>${item.NAMA_PEMBELI}</div>
            <div>${item.NAMA_MERCHANDISE}</div>
            <div>${new Date(item.TANGGAL_REDEEM).toLocaleDateString('id-ID')}</div>
            <div>${item.STATUS}</div>
            <div>${item.TANGGAL_AMBIL ? new Date(item.TANGGAL_AMBIL).toLocaleDateString('id-ID') : '-'}</div>
            <div>
                ${item.TANGGAL_AMBIL ? '' : `
                <button onclick="bukaModalTanggal(${item.ID_REDEEM})" class="bg-blue-600 text-white px-3 py-1 rounded">Isi Tanggal</button>
                `}
            </div>
            </div>
        `).join('');
    }


    function bukaModalTanggal(id) {
        document.getElementById('idRedeem').value = id;
        document.getElementById('modalTanggal').classList.remove('hidden');
        document.getElementById('modalTanggal').classList.add('flex');
    }

    function tutupModalTanggal() {
        document.getElementById('modalTanggal').classList.add('hidden');
        document.getElementById('modalTanggal').classList.remove('flex');
    }

    document.getElementById('formTanggalAmbil').addEventListener('submit', async e => {
        e.preventDefault();
        const id = document.getElementById('idRedeem').value;
        const tanggal = document.getElementById('inputTanggal').value;

        try {
            await axios.put(`${baseUrl}/${id}/tanggal-ambil`, { tanggal_ambil: tanggal });
            alert('Tanggal ambil berhasil diisi!');
            tutupModalTanggal();
            await fetchKlaimData(); // ambil ulang
            filterAndRender(document.getElementById('filterStatus').value); // render ulang
        } catch (err) {
            alert('Gagal menyimpan tanggal ambil');
            console.error(err);
        }
    });


    document.getElementById('filterStatus').addEventListener('change', e => {
        filterAndRender(e.target.value);
    });

    fetchKlaimData();
</script>
@endsection
