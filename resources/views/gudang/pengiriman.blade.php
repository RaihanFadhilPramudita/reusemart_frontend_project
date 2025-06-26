@extends('layouts.app')

@section('title', 'Data Pengiriman & Pengambilan')

@section('content')
<div class="flex bg-gray-100 min-h-screen overflow-hidden">
    <x-gudang-sidebar />

    <main class="flex-1 p-6">
        <h2 class="text-2xl font-bold mb-4">Data Pengiriman & Pengambilan</h2>

        <div class="overflow-x-auto mb-10">
            <div class="bg-white shadow rounded min-w-[1000px] divide-y">
                <div class="px-4 py-3 font-semibold grid grid-cols-8 gap-2 text-center">
                    <div>Nama Barang</div>
                    <div>Pembeli</div>
                    <div>Jenis</div>
                    <div>Status</div>
                    <div>Jadwal</div>
                    <div>Aksi</div>
                    <div>Nota</div>
                </div>
                <div id="transaksi-list" class="divide-y"></div>
            </div>
        </div>

        <!-- Modal Detail Pesanan -->
        <div id="modalDetail" class="fixed inset-0 hidden bg-black bg-opacity-50 z-50 items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white">
                    <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Detail Pesanan</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeDetailModal()">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>
                <div class="p-6" id="modalContentDetail"></div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Jadwal -->
<div id="modalJadwal" class="fixed inset-0 hidden bg-black/50 z-50 items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-lg">
        <h3 class="text-lg font-semibold mb-4" id="modalTitle">Jadwalkan</h3>
        <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal Pengiriman</label>
        <input type="date" id="jadwalTanggal" class="w-full border px-3 py-2 rounded mb-4" />

        <label class="block mb-2 text-sm font-medium text-gray-700">Jam Pengiriman</label>
        <input type="time" id="jadwalJam" class="w-full border px-3 py-2 rounded mb-4" />

        <div class="flex justify-end gap-2">
            <button onclick="submitJadwal()" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
            <button onclick="tutupModal()" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const config = {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
        }
    };

    let currentTransaksiId = null;
    let currentJenis = null;

    function bukaModal(id, jenis) {
        currentTransaksiId = id;
        currentJenis = jenis;
        document.getElementById('modalTitle').innerText = jenis === 'antar' ? 'Jadwal Kurir (Antar)' : 'Jadwal Pengiriman';
        document.getElementById('jadwalTanggal').value = '';
        document.getElementById('jadwalJam').value = '';
        document.getElementById('modalJadwal').classList.remove('hidden');
        document.getElementById('modalJadwal').classList.add('flex');
    }

    function tutupModal() {
        currentTransaksiId = null;
        currentJenis = null;
        document.getElementById('modalJadwal').classList.add('hidden');
        document.getElementById('modalJadwal').classList.remove('flex');
    }

    async function submitJadwal() {
        const tanggal = document.getElementById('jadwalTanggal').value;
        const jam = document.getElementById('jadwalJam').value;

        if (!tanggal || !jam) {
            alert('Tanggal dan jam harus diisi.');
            return;
        }

        const waktu = `${tanggal} ${jam}`;
        const now = new Date();
        const inputDateTime = new Date(`${tanggal}T${jam}`);
        const isSameDay = now.toDateString() === inputDateTime.toDateString();

        const jamInput = inputDateTime.getHours();
        const menitInput = inputDateTime.getMinutes();
        const totalMenit = jamInput * 60 + menitInput;

        if (currentJenis === 'Antar' && isSameDay && totalMenit >= 16 * 60) {
            alert('Pengiriman tidak bisa dijadwalkan di hari yang sama jika sudah lewat pukul 16:00.');
            return;
        }

        try {
            if (currentJenis === 'Antar') {
                await axios.post('http://localhost:8000/api/pegawai/jadwal-pengiriman', {
                    id_transaksi: currentTransaksiId,
                    waktu_pengiriman: waktu
                }, config);
            } else if (currentJenis === 'Ambil') {
                await axios.post('http://localhost:8000/api/pegawai/jadwal-ambil', {
                    id_transaksi: currentTransaksiId,
                    waktu_ambil: waktu
                }, config);
            }

            alert('Jadwal berhasil disimpan.');
            tutupModal();
            await loadTransaksi(); // ⬅ ini penting untuk update data di layar

        } catch (err) {
            console.error('Gagal menyimpan jadwal:', err);
            alert('Gagal menyimpan jadwal. Silakan periksa input atau koneksi.');
        }
    }

    async function konfirmasiTerima(id) {
        if (!confirm("Konfirmasi bahwa barang telah diterima oleh pembeli?")) return;

        try {
            await axios.post('http://localhost:8000/api/pegawai/gudang/konfirmasi-terima', {
                id_transaksi: id
            }, config);
            alert("Transaksi selesai. Notifikasi dikirim.");
            loadTransaksi();
        } catch (err) {
            console.error("Gagal konfirmasi:", err);
        }
    }

    async function loadTransaksi() {
        try {
            const res = await axios.get('http://localhost:8000/api/gudang/pegawai/pesanan-diproses', config);
            const data = res.data.data || [];
            const container = document.getElementById('transaksi-list');
            container.innerHTML = '';

            if (!data.length) {
                container.innerHTML = '<div class="p-4 text-gray-500">Tidak ada pesanan diproses</div>';
                return;
            }

            data.forEach(item => {
                const barang = item.detail_transaksi?.[0]?.barang;
                const waktuJadwal = item.JENIS_DELIVERY === 'Antar' ? item.WAKTU_KIRIM : item.WAKTU_AMBIL;
                const jadwalText = (!waktuJadwal || waktuJadwal === '0000-00-00 00:00:00' || waktuJadwal.includes('1970')) ?
                    '-' : formatDate(waktuJadwal);


                const row = `
                                <div class="px-4 py-3 grid grid-cols-8 gap-2 items-center text-sm whitespace-normal">
                                    <div class="text-left">${barang?.NAMA_BARANG || 'undefined'}</div>
                                    <div class="text-left">${item.pembeli?.NAMA_PEMBELI || '-'}</div>
                                    <div class="text-center">${item.JENIS_DELIVERY}</div>
                                    <div class="text-center">${item.STATUS_TRANSAKSI}</div>
                                    <div class="text-center">${jadwalText}</div>

                                    <div class="flex flex-col gap-1">
                                    <button onclick="showOrderDetail(${item.ID_TRANSAKSI})"
                                        class="bg-gray-500 text-white px-2 py-1 rounded">Lihat Detail</button>
                                    <button onclick="bukaModal(${item.ID_TRANSAKSI}, '${item.JENIS_DELIVERY}')"
                                        class="bg-green-600 text-white px-2 py-1 rounded">Jadwal</button>
                                    </div>

                                    <div class="flex flex-col gap-1">
                                    <button onclick="downloadNotaKurir(${item.ID_TRANSAKSI})"
                                        class="bg-blue-600 text-white px-2 py-1 rounded">Nota Kurir</button>
                                    <button onclick="downloadNotaPembeli(${item.ID_TRANSAKSI})"
                                        class="bg-purple-600 text-white px-2 py-1 rounded">Nota Pembeli</button>
                                    </div>
                                </div>
                            `;
                container.innerHTML += row;
            });
        } catch (error) {
            console.error('Gagal load pesanan:', error);
        }
    }

    async function showOrderDetail(orderId) {
        try {
            const token = localStorage.getItem('token');
            const res = await axios.get(`http://localhost:8000/api/gudang/pesanan/${orderId}`, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            });

            const order = res.data.data;
            document.getElementById('modalTitle').innerText = `Detail Pesanan #${orderId}`;

            document.getElementById('modalContentDetail').innerHTML = `
      <div class="space-y-6">
        <div>
          <h4 class="font-semibold text-gray-900 mb-2">Informasi Pesanan</h4>
          <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div><p class="text-gray-500">Tanggal Pesanan</p><p class="font-medium">${formatDate(order.WAKTU_PESAN)}</p></div>
            <div><p class="text-gray-500">Status</p><p class="font-medium">${order.STATUS_TRANSAKSI}</p></div>
            <div><p class="text-gray-500">Metode Pengiriman</p><p class="font-medium">${order.JENIS_DELIVERY}</p></div>
            <div><p class="text-gray-500">Total</p><p class="font-bold text-green-600">Rp${formatPrice(order.TOTAL_AKHIR)}</p></div>
          </div>
        </div>

        <div>
          <h4 class="font-semibold text-gray-900 mb-2">Informasi Pembeli</h4>
          <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div><p class="text-gray-500">Nama</p><p class="font-medium">${order.pembeli?.NAMA_PEMBELI || '-'}</p></div>
            <div><p class="text-gray-500">Email</p><p class="font-medium">${order.pembeli?.EMAIL || '-'}</p></div>
            <div><p class="text-gray-500">No Telepon</p><p class="font-medium">${order.pembeli?.NO_TELEPON || '-'}</p></div>
          </div>
        </div>

        <div>
          <h4 class="font-semibold text-gray-900 mb-2">Alamat Pengiriman</h4>
          <p class="text-sm">${order.alamat?.ALAMAT_LENGKAP || '-'}</p>
          <p class="text-sm">${order.alamat?.KECAMATAN || ''} ${order.alamat?.KOTA || ''} ${order.alamat?.KODE_POS || ''}</p>
        </div>

        <div>
          <h4 class="font-semibold text-gray-900 mb-2">Detail Produk</h4>
          <div class="border rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                ${(order.detail_transaksi || []).map(item => `
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap flex items-center gap-2">
                      <img src="${item.barang?.GAMBAR_URL || '/img/default.jpg'}" class="h-10 w-10 object-cover rounded">
                      ${item.barang?.NAMA_BARANG || '-'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp${formatPrice(item.barang?.HARGA)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.JUMLAH}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp${formatPrice(item.HARGA_SUBTOTAL)}</td>
                  </tr>`).join('')}
              </tbody>
            </table>
          </div>
        </div>

        ${order.bukti_transfer ? `
          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Bukti Pembayaran</h4>
            <a href="${order.bukti_transfer}" target="_blank" class="block text-center">
              <img src="${order.bukti_transfer}" class="max-h-64 mx-auto" alt="Bukti">
              <p class="text-sm mt-2 text-green-600 underline">Klik untuk melihat gambar</p>
            </a>
          </div>` : ''}
      </div>
    `;

            document.getElementById('modalDetail').classList.remove('hidden');
            document.getElementById('modalDetail').classList.add('flex');

        } catch (err) {
            console.error('Gagal load detail pesanan:', err);
            alert('Terjadi kesalahan saat mengambil detail pesanan.');
        }
    }

    function closeDetailModal() {
        document.getElementById('modalDetail').classList.remove('flex');
        document.getElementById('modalDetail').classList.add('hidden');
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    async function downloadNotaKurir(id) {
        try {
            const token = localStorage.getItem('token');
            const response = await axios.get(`http://localhost:8000/api/gudang/nota/${id}`, {
                responseType: 'blob',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            const blob = new Blob([response.data], {
                type: 'application/pdf'
            });
            const url = window.URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `nota_kurir_${id}.pdf`);
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


    async function downloadNotaPembeli(id) {
        try {
            const token = localStorage.getItem('token');
            const response = await axios.get(`http://localhost:8000/api/gudang/nota-pembeli/${id}`, {
                responseType: 'blob',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            const blob = new Blob([response.data], {
                type: 'application/pdf'
            });
            const url = window.URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `nota_pembeli_${id}.pdf`);
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



    function formatPrice(price) {
        if (!price) return '0';
        return parseInt(price).toLocaleString('id-ID');
    }

    loadTransaksi();
</script>
@endsection