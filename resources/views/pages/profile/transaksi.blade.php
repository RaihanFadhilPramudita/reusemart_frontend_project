<!-- Updated transaksi.blade.php -->
<h2 class="text-xl font-semibold mb-4">Transaksi Saya</h2>

<div class="flex flex-wrap gap-3 border-b pb-3 mb-4">
  <button onclick="filterStatus('diproses')" class="filter-btn">Diproses</button>
  <button onclick="filterStatus('dikirim')" class="filter-btn">Dikirim</button>
  <button onclick="filterStatus('selesai')" class="filter-btn">Selesai</button>
  <button onclick="filterStatus('dibatalkan')" class="filter-btn">Dibatalkan</button>
</div>

<div id="transaksiContainer" class="space-y-4 text-sm text-gray-700">
  <p>Memuat data...</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const container = document.getElementById('transaksiContainer');
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));
    let transaksiData = [];

    function setActiveStatus(status) {
      document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.innerText.toLowerCase() === status) {
          btn.classList.add('active');
        }
      });
    }

    async function loadTransaksi(status = '') {
      container.innerHTML = '<p>Memuat data...</p>';
      try {
        const res = await axios.get('http://localhost:8000/api/pembeli/transaksi', {
          headers: { Authorization: `Bearer ${token}` }
        });

        transaksiData = res.data.data;

        if (status) {
          transaksiData = transaksiData.filter(trx =>
            trx.STATUS_TRANSAKSI.toLowerCase() === status
          );
          setActiveStatus(status);
        }

        if (transaksiData.length === 0) {
          container.innerHTML = `
            <div class="text-center text-gray-500 mt-20">
              <img src="/assets/empty-order.png" class="mx-auto mb-4 w-28" alt="Empty">
              <p class="text-sm">Belum ada pesanan</p>
            </div>`;
          return;
        }

        container.innerHTML = transaksiData.map(trx => `
          <div class="bg-white shadow p-4 rounded border">
            <div class="flex justify-between items-center">
              <div>
                <p class="font-semibold">Transaksi #${trx.ID_TRANSAKSI}</p>
                <p class="text-sm text-gray-500">${new Date(trx.WAKTU_PESAN).toLocaleDateString('id-ID')}</p>
              </div>
              <span class="text-sm px-3 py-1 rounded-full bg-gray-100">${trx.STATUS_TRANSAKSI}</span>
            </div>
            <p class="mt-2">Total: <strong>Rp${Number(trx.TOTAL_AKHIR).toLocaleString('id-ID')}</strong></p>
          </div>
        `).join('');
      } catch (err) {
        console.error(err);
        container.innerHTML = '<p class="text-red-500">Gagal memuat data transaksi.</p>';
      }
    }

    function filterStatus(status) {
      loadTransaksi(status.toLowerCase());
    }

    loadTransaksi();
  </script>