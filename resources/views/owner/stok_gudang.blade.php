@extends('layouts.app')

@section('title', 'Stok Barang Gudang')

@section('content')
<div class="flex">
    <x-owner-sidebar />

    <main class="flex-1 p-6">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-xl font-bold">Stok Barang di Gudang</h2>
            <div class="flex flex-col space-y-2">
                <button onclick="cetakLaporanStok()" class="bg-green-600 text-white px-4 py-2 rounded shadow w-full">
                    🖨 Cetak Laporan Stok
                </button>
                <button onclick="cetakLaporanKadaluarsa()" class="bg-green-600 text-white px-4 py-2 rounded shadow w-full">
                    🖨 Cetak Laporan Barang Kadaluarsa
                </button>
            </div>
        </div>


        <form id="formCari">
            <input type="text" name="search" id="inputSearch" placeholder="Cari nama barang..." class="border px-3 py-2 rounded mb-4 w-full" />
        </form>

        <div class="overflow-x-auto">
            <div class="bg-white shadow rounded min-w-[1000px] divide-y">
                <div class="px-4 py-3 font-semibold grid grid-cols-7 gap-2">
                    <div>Nama</div>
                    <div>Kategori</div>
                    <div>Deskripsi</div>
                    <div>Status</div>
                    <div>Tanggal Masuk</div>
                    <div>Foto</div>
                </div>
                <div id="barang-list" class="divide-y"></div>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
    const config = {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    };

    const baseUrl = 'http://localhost:8000/api/owner/stok-barang'; // Buatkan endpoint ini di backend khusus owner

    async function loadBarang(keyword = '') {
        try {
            const url = keyword ? `${baseUrl}/search?q=${encodeURIComponent(keyword)}` : baseUrl;
            const res = await axios.get(url, config);
            const data = res.data.data || [];

            const container = document.getElementById('barang-list');
            if (!data.length) {
                container.innerHTML = '<div class="p-4 text-gray-500 col-span-7">Tidak ada data barang</div>';
                return;
            }

            container.innerHTML = '';
            data.forEach(item => {
                const gambarArr = JSON.parse(item.GAMBAR || '[]');
                const gambarHTML = gambarArr.length
                ? gambarArr.map(path =>
                    `<img src="http://localhost:8000/storage/${path}" class="w-16 h-auto mr-2 inline-block border rounded" />`
                    ).join('')
                : 'Tidak ada';

                const row = `
                    <div class="px-4 py-3 grid grid-cols-7 gap-2 items-start text-sm whitespace-normal break-words">
                        <div>${item.NAMA_BARANG}</div>
                        <div>${item.kategori?.NAMA_KATEGORI || '-'}</div>
                        <div>${item.DESKRIPSI}</div>
                        <div>${item.STATUS_BARANG}</div>
                        <div>${item.TANGGAL_MASUK ? new Date(item.TANGGAL_MASUK).toLocaleDateString('id-ID') : '-'}</div>
                        <div>${gambarHTML}</div>
                    </div>
                `;
                container.innerHTML += row;
            });
        } catch (err) {
            console.error("Gagal muat barang:", err);
        }
    }

    document.getElementById('formCari').addEventListener('submit', e => {
        e.preventDefault();
        const keyword = document.getElementById('inputSearch').value.trim();
        loadBarang(keyword);
    });

    async function cetakLaporanStok() {
        try {
            const response = await axios.get('http://localhost:8000/api/owner/laporan/stok-barang', {
            responseType: 'blob',
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
            });

            const blob = new Blob([response.data], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'laporan-stok-barang.pdf');
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (error) {
            if (error.response && error.response.data instanceof Blob) {
            const reader = new FileReader();
            reader.onload = function () {
                const errorText = reader.result;
                console.error("Isi pesan error dari server:", errorText);
                alert("Gagal unduh laporan stok:\n" + errorText);
            };
            reader.readAsText(error.response.data); 
            } else {
            console.error('Gagal download laporan stok:', error);
            alert('Gagal unduh laporan stok.');
            }
        }
    }

    async function cetakLaporanKadaluarsa() {
        try {
            const response = await axios.get('http://localhost:8000/api/owner/laporan/barang-kadaluarsa', {
            responseType: 'blob',
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
            });

            const blob = new Blob([response.data], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'laporan_barang_kadaluarsa.pdf');
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (error) {
            if (error.response && error.response.data instanceof Blob) {
            const reader = new FileReader();
            reader.onload = function () {
                const errorText = reader.result;
                console.error("Isi pesan error dari server:", errorText);
                alert("Gagal unduh laporan kadaluarsa:\n" + errorText);
            };
            reader.readAsText(error.response.data); 
            } else {
            console.error('Gagal download laporan kadaluarsa:', error);
            alert('Gagal unduh laporan kadaluarsa.');
            }
        }
        }

    loadBarang();
</script>
@endsection
