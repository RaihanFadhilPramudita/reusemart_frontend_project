@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
<div class="flex">
    <x-gudang-sidebar />

    <main class="flex-1 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Data Barang</h2>
            <button onclick="bukaModal()" class="bg-green-600 text-white px-4 py-2 rounded">
                + Tambah Barang
            </button>
        </div>


        <form id="formCari" method="GET">
            <input type="text" name="search" id="inputSearch" placeholder="Cari nama barang..." class="border px-3 py-2 rounded mb-4 w-full" />
        </form>

         <div class="px-4 py-3 font-semibold">
            <select id="filterGaransi" class="border px-3 py-2 rounded">
                <option value="">Semua Garansi</option>
                <option value="ada">Barang Bergaransi</option>
                <option value="tidak">Barang Tidak Bergaransi</option>
            </select>
        </div>

        <div class="overflow-x-auto mb-10">
          <div class="bg-white shadow rounded min-w-[1000px] divide-y">
              <div class="px-4 py-3 font-semibold grid grid-cols-9 gap-2 min-w-[800px]">
                <div>Nama</div>
                <div>Kategori</div>
                <div>Deskripsi</div>
                <div>Foto</div>
                <div>Tanggal Masuk</div>
                <div>Tanggal Garansi</div>
                <div>Tanggal Kadaluarsa</div>
                <div>Petugas QC</div>
                <div>Aksi</div>
              </div>
              <div id="barang-list" class="divide-y"></div>
          </div>
        </div>

        <h2 class="text-lg font-bold mb-2">Barang Habis (Stok 0)</h2>
        <div class="overflow-x-auto mb-10">
          <div class="bg-white shadow rounded min-w-[1000px] divide-y">
              <div class="px-4 py-3 font-semibold grid grid-cols-9 gap-2 min-w-[800px]">
                <div>Nama</div>
                <div>Kategori</div>
                <div>Deskripsi</div>
                <div>Foto</div>
                <div>Tanggal Masuk</div>
                <div>Tanggal Garansi</div>
                <div>Tanggal Kadaluarsa</div>
                <div>Petugas QC</div>
                <div>Aksi</div>
              </div>
              <div id="barang-habis" class="divide-y"></div>
          </div>
        </div>

        <div id="modalBarang" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
            <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold" id="modalTitle">Tambah Barang</h2>
            <button onclick="tutupModal()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
            </div>
            <form id="formBarang">
            <input type="hidden" id="barangId">
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                  <label class="block text-sm font-semibold">Nama Barang</label>
                  <input type="text" id="inputNama" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                  <label class="block text-sm font-semibold">Kategori</label>
                  <select id="inputKategori" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Pilih Kategori --</option>
                  </select>
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-semibold">Deskripsi</label>
                  <textarea id="inputDeskripsi" class="w-full border rounded px-3 py-2" rows="3" required></textarea>
                </div>
                <div>
                  <label class="block text-sm font-semibold">Harga</label>
                  <input type="number" id="inputHarga" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="hidden">
                  <label class="block text-sm font-semibold">Tanggal Garansi</label>
                  <input type="date" id="inputGaransi" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                  <label class="block text-sm font-semibold">Penitip</label>
                  <select id="selectPenitip" class="w-full border rounded px-3 py-2" required>
                      <option value="">-- Pilih Penitip --</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-semibold">Pegawai Hunter (opsional)</label>
                  <select id="selectHunter" class="w-full border rounded px-3 py-2">
                      <option value="">-- Pilih Pegawai Hunter --</option>
                  </select>
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-semibold">Gambar Barang (bisa lebih dari 1)</label>
                  <input type="file" id="inputGambar" class="w-full" multiple required>
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
            </form>
        </div>
        </div>

        <div id="modalDetailBarang" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
          <div class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-lg font-bold">Detail Barang</h2>
              <button onclick="tutupModalDetail()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
            </div>
            <div id="detailContent" class="space-y-3 text-sm text-gray-700">
            </div>
          </div>
        </div>

    </main>
</div>
@endsection

@section('scripts')
<script>
  const baseUrl = 'http://localhost:8000/api/gudang/barang';
  const apiKategori = 'http://localhost:8000/api/gudang/kategori'; 
  const apiPenitip = 'http://localhost:8000/api/gudang/penitip';
  const apiHunter = 'http://localhost:8000/api/gudang/pegawai-hunter';

  const config = {
    headers: { Authorization: `Bearer ${token}` }
  };

  async function loadHunterDropdown() {
    try {
      const res = await axios.get(apiHunter, config);
      const select = document.getElementById('selectHunter');
      select.innerHTML = '<option value="">-- Pilih Pegawai Hunter --</option>';
      res.data.data.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.ID_PEGAWAI;
        opt.textContent = p.NAMA_PEGAWAI;
        select.appendChild(opt);
      });
    } catch (error) {
      console.error('Gagal load pegawai hunter:', error);
    }
  }

  async function loadBarang(keyword = '', garansi = '') {
    try {
      const url = keyword ? `${baseUrl}/search?q=${encodeURIComponent(keyword)}` : baseUrl;
      const res = await axios.get(url, config);
      let data = res.data.data || [];

      if (garansi === 'ada') {
        data = data.filter(item => !!item.TANGGAL_GARANSI); 
      } else if (garansi === 'tidak') {
        data = data.filter(item => !item.TANGGAL_GARANSI); 
      }

      const container = document.getElementById('barang-list');
      const habisContainer = document.getElementById('barang-habis');

      if (!data.length) {
        container.innerHTML = '<div class="p-4 text-gray-500">Tidak ada data barang</div>';
        habisContainer.innerHTML = '';
        return;
      }

      container.innerHTML = '';
      habisContainer.innerHTML = '';

      data.forEach(item => {
        const gambarArr = JSON.parse(item.GAMBAR || '[]');
        const gambarHTML = gambarArr.length
          ? gambarArr.map(path =>
              `<img src="http://localhost:8000/storage/${path}" class="w-16 h-auto mr-2 inline-block border rounded" />`
            ).join('')
          : 'Tidak ada';

        const namaQC = item.pegawai?.NAMA_PEGAWAI || 'Tidak diketahui';
        const tanggalMasuk = item.TANGGAL_MASUK
          ? new Date(item.TANGGAL_MASUK).toLocaleDateString('id-ID')
          : '-';

        const tanggalGaransi = item.TANGGAL_GARANSI
          ? new Date(item.TANGGAL_GARANSI).toLocaleDateString('id-ID')
          : '-';

        const tanggalKadaluarsa = item.TANGGAL_KADALUARSA
          ? new Date(item.TANGGAL_KADALUARSA).toLocaleDateString('id-ID')
          : '-';



        const row = `
          <div class="px-4 py-3 grid grid-cols-9 gap-2 items-start text-sm whitespace-normal break-words min-w-[1000px]">
            <div>${item.NAMA_BARANG}</div>
            <div>${item.kategori?.NAMA_KATEGORI || '-'}</div>
            <div>${item.DESKRIPSI}</div>
            <div>${gambarHTML}</div>
            <div>${tanggalMasuk}</div>
            <div>${tanggalGaransi !== '-' ? tanggalGaransi : 'Tidak ada garansi'}</div>
            <div>${tanggalKadaluarsa}</div>
            <div>${namaQC}</div>
            <div class="space-y-1">
              <button onclick='tampilkanDetailBarang(${JSON.stringify(item)})'
                class="bg-blue-600 text-white px-2 py-1 rounded w-full">Detail</button>
              <button onclick='bukaModal(true, ${JSON.stringify(item)})' 
                class="bg-yellow-500 text-white px-2 py-1 rounded w-full">Edit</button>
              ${item.ID_PENITIPAN ? `
                <button onclick='cetakNota(${item.ID_PENITIPAN})'
                  class="bg-purple-600 text-white px-2 py-1 rounded w-full">Cetak Nota</button>
              ` : ''}
              ${(() => {
                const statusTidakBoleh = ['Sold Out', 'Didonasikan', 'Barang untuk Donasi'];
                const tanggalKadaluarsa = item.detail_penitipan?.penitipan?.TANGGAL_KADALUARSA;
                const isKadaluarsa = tanggalKadaluarsa && new Date(tanggalKadaluarsa) < new Date();
                const bolehAmbil = item.STATUS_BARANG === 'Siap Diambil' && isKadaluarsa && !statusTidakBoleh.includes(item.STATUS_BARANG);
                return bolehAmbil
                  ? `<button onclick="prosesAmbil(${item.ID_BARANG})" class="bg-indigo-600 text-white px-2 py-1 rounded w-full">Ambil</button>`
                  : '';
              })()}
            </div>
          </div>
        `;

        if (item.STATUS_BARANG !== 'Tersedia') {
          habisContainer.innerHTML += row;
        } else {
          container.innerHTML += row;
        }
      });

    } catch (err) {
      console.error("Gagal muat barang:", err);
    }
  }

  document.getElementById('filterGaransi').addEventListener('change', () => {
    const keyword = document.getElementById('inputSearch').value.trim();
    const garansi = document.getElementById('filterGaransi').value;
    loadBarang(keyword, garansi);
  });


  async function loadKategoriDropdown() {
    try {
      const res = await axios.get(apiKategori, config);
      const select = document.getElementById('inputKategori');
      select.innerHTML = '<option value="">-- Pilih Kategori --</option>';

      res.data.data.forEach(k => {
        const opt = document.createElement('option');
        opt.value = k.ID_KATEGORI;
        opt.textContent = k.NAMA_KATEGORI; 
        select.appendChild(opt);
      });

      select.dispatchEvent(new Event('change'));
    } catch (error) {
      console.error('Gagal load kategori:', error);
    }
  }



  function bukaModal(isEdit = false, data = null) {
    document.getElementById('modalBarang').classList.remove('hidden');
    document.getElementById('modalBarang').classList.add('flex');
    document.getElementById('formBarang').reset();
    document.getElementById('barangId').value = '';

    document.getElementById('modalTitle').innerText = isEdit ? 'Edit Barang' : 'Tambah Barang';

    const fieldIds = ['inputNama', 'inputDeskripsi', 'inputHarga', 'inputKategori', 'selectPenitip', 'inputGambar', 'inputGaransi'];
    fieldIds.forEach(id => {
      const field = document.getElementById(id);
      if (field) {
        if (isEdit) {
          field.removeAttribute('required');
        } else {
          field.setAttribute('required', 'required');
        }
      }
    });

    const garansiField = document.getElementById('inputGaransi');
    if (garansiField) {
      garansiField.removeAttribute('required');
      garansiField.value = '';
    }

    if (isEdit && data) {
      document.getElementById('barangId').value = data.ID_BARANG;
      document.getElementById('inputNama').value = data.NAMA_BARANG;
      document.getElementById('inputDeskripsi').value = data.DESKRIPSI;
      document.getElementById('inputHarga').value = data.HARGA;
      document.getElementById('inputGaransi').value = data.TANGGAL_GARANSI || '';
      document.getElementById('inputKategori').value = data.ID_KATEGORI;
      document.getElementById('selectPenitip').value = data.ID_PENITIP;
      document.getElementById('selectHunter').value = data.detailPenitipan?.penitipan?.PEGAWAI_HUNTER || '';
    }
  }

  document.getElementById('inputKategori').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const kategoriText = selectedOption.textContent.trim().toLowerCase();
    const garansiFieldWrapper = document.getElementById('inputGaransi').closest('div');

    if (kategoriText === 'elektronik & gadget') {
      garansiFieldWrapper.classList.remove('hidden');
    } else {
      garansiFieldWrapper.classList.add('hidden');
      document.getElementById('inputGaransi').value = ''; 
    }
  });

  function tutupModal() {
    document.getElementById('modalBarang').classList.add('hidden');
    document.getElementById('modalBarang').classList.remove('flex');
  }

  async function loadPenitipDropdown() {
    try {
      const res = await axios.get(apiPenitip, config);
      const select = document.getElementById('selectPenitip');
      select.innerHTML = '<option value="">-- Pilih Penitip --</option>';
      res.data.data.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.ID_PENITIP;
        opt.textContent = p.NAMA_PENITIP;
        select.appendChild(opt);
      });
    } catch (error) {
      console.error('Gagal load penitip:', error);
    }
  }

  async function loadKategoriDropdown() {
  try {
    const res = await axios.get(apiKategori, config);
    const select = document.getElementById('inputKategori');
    select.innerHTML = ''; 
    res.data.data.forEach(k => {
      const opt = document.createElement('option');
      opt.value = k.ID_KATEGORI;
      opt.textContent = k.NAMA_KATEGORI;
      select.appendChild(opt);
    });
  } catch (error) {
    console.error('Gagal load kategori:', error);
  }
}


  document.getElementById('formBarang').addEventListener('submit', async function (e) {
    e.preventDefault();

    const id = document.getElementById('barangId').value;
    const formData = new FormData();
    formData.append('nama_barang', document.getElementById('inputNama').value);
    formData.append('deskripsi', document.getElementById('inputDeskripsi').value);
    formData.append('harga', document.getElementById('inputHarga').value);
    const tanggalGaransi = document.getElementById('inputGaransi').value.trim();
    if (tanggalGaransi !== '') {
      formData.append('tanggal_garansi', tanggalGaransi);
    }

    formData.append('id_kategori', document.getElementById('inputKategori').value);
    formData.append('id_penitip', document.getElementById('selectPenitip').value);
    const hunterVal = document.getElementById('selectHunter').value;
    if (hunterVal !== '') {
      formData.append('pegawai_hunter', hunterVal);
    }




    const files = document.getElementById('inputGambar').files;
    for (let i = 0; i < files.length; i++) {
      formData.append(`gambar[]`, files[i]);
    }

    try {
      if (id) {
        const confirmUpdate = confirm('Apakah data Anda sudah benar?');
        if (!confirmUpdate) return;

        await axios.post(`${baseUrl}/${id}?_method=PUT`, formData, {
          ...config,
          headers: {
            ...config.headers,
            'Content-Type': 'multipart/form-data'
          }
        });
      } else {
        await axios.post(baseUrl, formData, {
          ...config,
          headers: {
            ...config.headers,
            'Content-Type': 'multipart/form-data'
          }
        });
      }

      alert('Berhasil disimpan!');
      tutupModal();
      loadBarang();
    } catch (error) {
      console.error('Gagal simpan:', error);
      alert('Gagal menyimpan data.');
    }
  });


  document.getElementById('formCari').addEventListener('submit', e => {
    e.preventDefault();
    const keyword = document.getElementById('inputSearch').value.trim();
    loadBarang(keyword);
  });

  async function hapusBarang(id) {
    if (!confirm('Yakin ingin menghapus barang ini?')) return;
    try {
        await axios.delete(`${baseUrl}/${id}`, config);
        alert('Barang berhasil dihapus');
        loadBarang();
    } catch (error) {
        console.error('Gagal hapus barang:', error);
        alert('Gagal menghapus barang.');
    }
  }

  async function cetakNota(idPenitipan) {
    try {
      const token = localStorage.getItem('token');
      const response = await axios.get(`http://localhost:8000/api/gudang/penitipan/nota-download/${idPenitipan}`, {
        responseType: 'blob',
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      const blob = new Blob([response.data], { type: 'application/pdf' });
      const url = window.URL.createObjectURL(blob);

      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `nota-penitipan-${idPenitipan}.pdf`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.URL.revokeObjectURL(url);
    } catch (error) {
      if (error.response && error.response.data instanceof Blob) {
        const text = await error.response.data.text();
        console.error('Isi respons error:', text); // 👈 akan tampilkan pesan asli Laravel
      } else {
        console.error('Error umum:', error);
      }
      alert('Gagal download nota. Periksa console.');
    }
  }

  async function prosesAmbil(id) {
    if (!confirm('Yakin barang ini sudah diambil oleh pemilik?')) return;

    try {
      await axios.post(`http://localhost:8000/api/gudang/barang/${id}/ambil`, {}, config);
      alert('Status barang berhasil diperbarui.');
      loadBarang();
    } catch (err) {
      alert('Gagal memproses pengambilan barang.');
    }
  }

  function tampilkanDetailBarang(data) {
    const gambarArr = JSON.parse(data.GAMBAR || '[]');
    const gambarHTML = gambarArr.map(path =>
      `<img src="http://localhost:8000/storage/${path}" class="w-20 h-auto mr-2 mb-2 inline-block border rounded" />`
    ).join('');

    const tanggal = (tgl) => tgl ? new Date(tgl).toLocaleDateString('id-ID') : '-';

    const konten = `
      <p><strong>Nama Barang:</strong> ${data.NAMA_BARANG}</p>
      <p><strong>Deskripsi:</strong> ${data.DESKRIPSI}</p>
      <p><strong>Harga:</strong> Rp ${Number(data.HARGA).toLocaleString('id-ID')}</p>
      <p><strong>Status Barang:</strong> ${data.STATUS_BARANG}</p>
      <p><strong>Kategori:</strong> ${data.kategori?.NAMA_KATEGORI || '-'}</p>
      <p><strong>Tanggal Masuk:</strong> ${tanggal(data.TANGGAL_MASUK)}</p>
      <p><strong>Tanggal Garansi:</strong> ${tanggal(data.TANGGAL_GARANSI)}</p>
      <p><strong>Tanggal Kadaluarsa:</strong> ${tanggal(data.TANGGAL_KADALUARSA)}</p>
      <p><strong>Penitip:</strong> ${data.penitip?.NAMA_PENITIP || '-'}</p>
      <p><strong>Pegawai Hunter:</strong> ${data.detailPenitipan?.penitipan?.hunter?.NAMA_PEGAWAI || '-'}</p>
      <p><strong>Gambar:</strong><br>${gambarHTML}</p>
    `;

    document.getElementById('detailContent').innerHTML = konten;
    document.getElementById('modalDetailBarang').classList.remove('hidden');
    document.getElementById('modalDetailBarang').classList.add('flex');
  }

  function tutupModalDetail() {
    document.getElementById('modalDetailBarang').classList.add('hidden');
    document.getElementById('modalDetailBarang').classList.remove('flex');
  }


    loadPenitipDropdown();
    loadKategoriDropdown();
    loadBarang();
    loadHunterDropdown()

</script>
@endsection
