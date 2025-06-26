@extends('layouts.app')

@section('title', 'Laporan Penjualan Bulanan')

@section('content')
  <div class="flex">
    <x-owner-sidebar />

    <main class="flex-1 p-6">
      <div class="flex items-center gap-4 mb-4">
        <h2 class="text-xl font-bold">Laporan Penjualan Bulanan</h2>

        @php
          $tahunAwal = date('Y', 0); // = "1970"
        @endphp

        <select id="tahunSelect" onchange="fetchLaporan()" class="border px-3 py-1 rounded text-sm">
          <option value="">2025
          </option>
          @for ($y = now()->year; $y >= $tahunAwal; $y--)
            <option value="{{ $y }}">{{ $y }}</option>
          @endfor
        </select>

        <button onclick="unduhLaporanPDF()" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded shadow">
          🖨 Cetak PDF
        </button>
      </div>

      <div class="overflow-x-auto bg-white shadow rounded mb-6">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-100 text-left font-semibold">
            <tr>
              <th class="px-4 py-3">Bulan</th>
              <th class="px-4 py-3">Jumlah Barang Terjual</th>
              <th class="px-4 py-3">Jumlah Penjualan Kotor</th>
            </tr>
          </thead>
          <tbody id="laporanBody" class="divide-y divide-gray-100"></tbody>
        </table>
      </div>

      <div class="bg-white shadow rounded p-4">
        <canvas id="laporanChart"></canvas>
      </div>
    </main>
  </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const headers = {
    Authorization: `Bearer ${localStorage.getItem('token')}`
  };

  let chartInstance = null; // simpan instance chart global

  async function fetchLaporan() {
    try {
      const tahun = document.getElementById('tahunSelect').value || new Date().getFullYear();
      const tbody = document.getElementById('laporanBody');
      const res = await axios.get(`http://localhost:8000/api/owner/laporan/transaksi-bulanan?tahun=${tahun}`, { headers });
      const laporan = res.data.data;

      let totalBarang = 0;
      let totalPenjualan = 0;
      let chartLabels = [], chartData = [];

      tbody.innerHTML = laporan.map(item => {
        const jumlah = Number(item.jumlah_terjual || 0);
        const penjualan = Number(item.penjualan_kotor || 0);

        totalBarang += jumlah;
        totalPenjualan += penjualan;
        chartLabels.push(item.bulan);
        chartData.push(penjualan);

        return `
          <tr>
            <td class="px-4 py-2">${item.bulan}</td>
            <td class="px-4 py-2">${jumlah}</td>
            <td class="px-4 py-2">Rp ${formatRupiah(penjualan)}</td>
          </tr>
        `;
      }).join('');

      // Append total row
      tbody.innerHTML += `
        <tr class="font-semibold bg-gray-50">
          <td class="px-4 py-2">Total</td>
          <td class="px-4 py-2">${totalBarang}</td>
          <td class="px-4 py-2">Rp ${formatRupiah(totalPenjualan)}</td>
        </tr>
      `;

      renderChart(chartLabels, chartData, tahun);

    } catch (err) {
      console.error('Gagal memuat laporan:', err);
      alert('Gagal memuat laporan penjualan.');
    }
  }

  function renderChart(labels, data, tahun) {
    const ctx = document.getElementById('laporanChart').getContext('2d');

    // Hancurkan chart lama jika ada
    if (chartInstance) {
      chartInstance.destroy();
    }

    chartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Penjualan Kotor',
          data: data,
          backgroundColor: 'rgba(54, 162, 235, 0.7)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: `Grafik Penjualan Bulanan - ${tahun}`
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: value => 'Rp ' + value.toLocaleString('id-ID')
            }
          }
        }
      }
    });
  }

  function formatRupiah(value) {
    return Number(value || 0).toLocaleString('id-ID');
  }

  async function unduhLaporanPDF() {
    const tahun = document.getElementById('tahunSelect').value || new Date().getFullYear();
    try {
      const res = await axios.get(`http://localhost:8000/api/owner/laporan/transaksi-bulanan/export?tahun=${tahun}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Accept': 'application/pdf'
        },
        responseType: 'blob'
      });

      const blob = new Blob([res.data], { type: 'application/pdf' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `laporan_penjualan_bulanan_${tahun}.pdf`;
      link.click();
      URL.revokeObjectURL(url);
    } catch (error) {
      if (
        error.response &&
        error.response.data instanceof Blob &&
        error.response.headers['content-type']?.includes('application/json')
      ) {
        const text = await error.response.data.text();
        console.error('Isi Error:', text);
        const json = JSON.parse(text);
        alert(`Gagal export: ${json.message || 'Terjadi error saat generate PDF.'}`);
      } else {
        console.error('Gagal unduh PDF:', error);
        alert('Gagal mengunduh laporan PDF.');
      }
    }
  }

  // Jalankan pertama kali
  document.addEventListener('DOMContentLoaded', fetchLaporan);
</script>
@endsection
