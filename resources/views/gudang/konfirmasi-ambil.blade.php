@extends('layouts.app')

@section('title', 'Konfirmasi Ambil Barang')

@section('content')
<div class="flex">
    @include('components.gudang-sidebar')

    <main class="flex-1 p-6">
        <h2 class="text-2xl font-bold mb-4">Konfirmasi Pengambilan Barang</h2>
        <div id="listBarang" class="space-y-4"></div>
    </main>
</div>
@endsection

@section('scripts')
<script>
    const API = "http://localhost:8000/api/gudang";

    function loadBarangMenunggu() {
        axios.get(`${API}/barang-menunggu-konfirmasi`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        }).then(res => {
            const data = res.data.data;
            const list = document.getElementById("listBarang");
            list.innerHTML = "";

            if (!data.length) {
                list.innerHTML = `<div class="text-gray-500">Tidak ada barang menunggu konfirmasi.</div>`;
                return;
            }

            data.forEach(barang => {
                const div = document.createElement("div");
                div.className = "p-4 border rounded bg-white shadow flex justify-between items-center";
                div.innerHTML = `
                    <div>
                        <h4 class="font-bold text-lg">${barang.NAMA_BARANG}</h4>
                        <p class="text-sm text-gray-600">${barang.DESKRIPSI}</p>
                        <p class="text-sm">Penitip: <strong>${barang.penitip?.NAMA_PENITIP || '-'}</strong></p>
                        <p class="text-sm">Kategori: ${barang.kategori?.NAMA_KATEGORI || '-'}</p>
                    </div>
                    <button onclick="konfirmasiAmbil(${barang.ID_BARANG})" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Konfirmasi Ambil
                    </button>
                `;
                list.appendChild(div);
            });
        }).catch(err => {
            console.error("Gagal load:", err);
        });
    }

    function konfirmasiAmbil(id) {
        if (!confirm("Yakin ingin mengkonfirmasi pengambilan barang ini?")) return;

        axios.post(`${API}/barang/${id}/konfirmasi-ambil`, {}, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        }).then(res => {
            alert("Barang berhasil dikonfirmasi diambil.");
            loadBarangMenunggu();
        }).catch(err => {
            alert("Gagal konfirmasi: " + (err.response?.data?.message || 'error'));
        });
    }

    document.addEventListener("DOMContentLoaded", loadBarangMenunggu);
</script>
@endsection