@extends('layouts.app')

@section('title', 'Laporan Transaksi Penitip')

@section('content')
<div class="flex min-h-screen">
    {{-- Sidebar Owner --}}
    @include('components.owner-sidebar')

    {{-- Main Content --}}
    <main class="flex-1 bg-white p-8">
        <div class="max-w-6xl mx-auto text-gray-800">
            {{-- Header --}}
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-green-700">ReUse Mart</h2>
                <p class="text-sm">Jl. Green Eco Park No. 456 Yogyakarta</p>
                <h3 class="text-xl font-semibold mt-2">Laporan Transaksi Penitip</h3>
            </div>

            {{-- Filter + Cetak --}}
            <div class="flex flex-wrap gap-4 items-end justify-between border-b pb-4 mb-6">
                <div class="flex gap-4 flex-wrap items-end">
                    <div>
                        <label class="text-sm font-medium">Penitip</label>
                        <select id="selectPenitip" class="border rounded px-2 py-1 text-sm min-w-[200px]"></select>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Bulan</label>
                        <select id="selectBulan" class="border rounded px-2 py-1 text-sm">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == now()->month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                                @endfor
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Tahun</label>
                        <select id="selectTahun" class="border rounded px-2 py-1 text-sm">
                            @for ($i = now()->year; $i >= now()->year - 5; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <button onclick="unduhPDF()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow text-sm">
                    Cetak PDF
                </button>
            </div>

            {{-- Tabel Transaksi --}}
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-left">Kode Produk</th>
                            <th class="border px-3 py-2 text-left">Nama Produk</th>
                            <th class="border px-3 py-2 text-center">Tanggal Masuk</th>
                            <th class="border px-3 py-2 text-center">Tanggal Laku</th>
                            <th class="border px-3 py-2 text-right">Harga Jual Bersih</th>
                            <th class="border px-3 py-2 text-right">Bonus Terjual Cepat</th>
                            <th class="border px-3 py-2 text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyTransaksi"></tbody>
                </table>
            </div>

            <p class="text-xs text-right mt-6 text-gray-500">Tanggal cetak: {{ now()->translatedFormat('d F Y') }}</p>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
    const headers = {
        Authorization: `Bearer ${localStorage.getItem('token')}`
    };

    async function fetchPenitipList() {
        try {
            const res = await axios.get('http://localhost:8000/api/owner/penitip/list', {
                headers
            });
            const select = document.getElementById('selectPenitip');
            select.innerHTML = `<option disabled selected value="">-- Pilih Penitip --</option>`;

            const list = res.data?.data || [];
            list.forEach(penitip => {
                const option = document.createElement('option');
                option.value = penitip.ID_PENITIP;
                option.textContent = penitip.NAMA_PENITIP;
                select.appendChild(option);
            });

            // ❗ hanya panggil fetchLaporan() jika ada penitip pertama
            if (list.length > 0) {
                select.value = list[0].ID_PENITIP;
                fetchLaporan();
            }
        } catch (err) {
            alert('Gagal memuat daftar penitip');
            console.log(err);
        }
    }


    async function fetchLaporan() {
        const id_penitip = document.getElementById('selectPenitip').value;
        const bulan = document.getElementById('selectBulan').value;
        const tahun = document.getElementById('selectTahun').value;
        try {
            const res = await axios.get('http://localhost:8000/api/owner/laporan/transaksi-penitip', {
                headers,
                params: {
                    id_penitip,
                    bulan,
                    tahun
                }
            });

            const data = res.data.data;
            const tbody = document.getElementById('tbodyTransaksi');
            tbody.innerHTML = '';

            let totalBersih = 0,
                totalBonus = 0,
                totalPendapatan = 0;

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Tidak ada data.</td></tr>';
                return;
            }

            data.forEach(item => {
                totalBersih += Number(item.harga_jual_bersih || 0);
                totalBonus += Number(item.bonus_terjual_cepat || 0);
                totalPendapatan += Number(item.pendapatan || 0);
                tbody.innerHTML += `
                <tr>
                    <td class="border px-3 py-2">${item.kode_produk}</td>
                    <td class="border px-3 py-2">${item.nama_produk}</td>
                    <td class="border px-3 py-2 text-center">${item.tanggal_masuk ?? '-'}</td>
                    <td class="border px-3 py-2 text-center">${item.tanggal_laku ?? '-'}</td>
                    <td class="border px-3 py-2 text-right">${formatRupiah(item.harga_jual_bersih || 0)}</td>
                    <td class="border px-3 py-2 text-right">${formatRupiah(item.bonus_terjual_cepat || 0)}</td>
                    <td class="border px-3 py-2 text-right">${formatRupiah(item.pendapatan || 0)}</td>
                </tr>
            `;
            });

            tbody.innerHTML += `
            <tr class="bg-gray-50 font-semibold">
                <td colspan="4" class="text-right border px-3 py-2">TOTAL</td>
                <td class="border px-3 py-2 text-right">${formatRupiah(totalBersih)}</td>
                <td class="border px-3 py-2 text-right">${formatRupiah(totalBonus)}</td>
                <td class="border px-3 py-2 text-right">${formatRupiah(totalPendapatan)}</td>
            </tr>
        `;
        } catch (err) {
            alert('Gagal memuat laporan transaksi');
            console.log(err);
        }
    }

    function formatRupiah(value) {
        return Number(value || 0).toLocaleString('id-ID');
    }

    async function unduhPDF() {
        const id_penitip = document.getElementById('selectPenitip').value;
        const bulan = document.getElementById('selectBulan').value;
        const tahun = document.getElementById('selectTahun').value;

        if (!id_penitip || !bulan || !tahun) {
            alert("Harap pilih penitip, bulan dan tahun terlebih dahulu.");
            return;
        }

        try {
            const response = await axios.get('http://localhost:8000/api/owner/laporan/transaksi-penitip/export', {
                responseType: 'blob',
                headers,
                params: {
                    id_penitip,
                    bulan,
                    tahun
                }
            });

            const blob = new Blob([response.data], {
                type: 'application/pdf'
            });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;

            const namaFile = `laporan-transaksi-penitip-${id_penitip}-${bulan}-${tahun}.pdf`;
            link.setAttribute('download', namaFile);

            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (error) {
            if (error.response && error.response.data instanceof Blob) {
                const reader = new FileReader();
                reader.onload = function() {
                    const errorText = reader.result;
                    alert("Gagal unduh laporan:\n" + errorText);
                };
                reader.readAsText(error.response.data);
            } else {
                alert('Gagal unduh laporan PDF.');
                console.error(error);
            }
        }
    }

    fetchPenitipList();
    document.getElementById('selectPenitip').addEventListener('change', fetchLaporan);
    document.getElementById('selectBulan').addEventListener('change', fetchLaporan);
    document.getElementById('selectTahun').addEventListener('change', fetchLaporan);
</script>
@endsection