@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="flex">
  <x-cs-sidebar />

  <main class="flex-1 p-6">
    <h2 class="text-2xl font-bold mb-6">Verifikasi Pembayaran</h2>

    <div id="verification-list" class="space-y-4">
      <div class="text-center py-10 text-gray-500">
        <p>Memuat data transaksi...</p>
      </div>
    </div>
  </main>

  <!-- Modal untuk preview bukti pembayaran -->
  <div id="modal-preview" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold">Bukti Pembayaran</h3>
        <button onclick="closePreviewModal()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
      </div>
      <div class="flex justify-center">
        <img id="bukti-preview" src="" alt="Bukti Pembayaran" class="max-h-[70vh] object-contain">
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
(function() { // Tambahkan IIFE (Immediately Invoked Function Expression) di sini
  const verificationToken = localStorage.getItem('token'); // Ganti nama variabel
  const container = document.getElementById('verification-list');

  async function loadVerifications() {
    try {
      const res = await axios.get('http://localhost:8000/api/cs/verifikasi', {
        headers: { Authorization: `Bearer ${verificationToken}` } // Gunakan nama variabel baru
      });
      
      const transactions = res.data.data || [];
      
      if (!transactions.length) {
        container.innerHTML = `
          <div class="bg-white rounded shadow p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500">Tidak ada transaksi yang perlu diverifikasi</p>
          </div>
        `;
        return;
      }
      
      container.innerHTML = transactions.map(tx => `
        <div class="bg-white rounded shadow overflow-hidden" data-id="${tx.ID_TRANSAKSI}">
          <div class="p-4 border-b">
            <div class="flex justify-between items-center">
              <div>
                <h3 class="font-bold">No. Transaksi: ${tx.NO_NOTA || tx.ID_TRANSAKSI}</h3>
                <p class="text-sm text-gray-600">Pembeli: ${tx.pembeli?.NAMA_PEMBELI || 'Unknown'}</p>
                <p class="text-sm text-gray-600">Tanggal: ${new Date(tx.WAKTU_PESAN).toLocaleString('id-ID')}</p>
              </div>
              <div class="text-right">
                <p class="font-bold text-lg text-green-600">Rp${parseInt(tx.TOTAL_AKHIR).toLocaleString('id-ID')}</p>
                <p class="text-sm text-yellow-600">Menunggu Verifikasi</p>
              </div>
            </div>
          </div>
          
          <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-1 flex flex-col items-center justify-center">
              <img 
                src="${tx.BUKTI_TRANSFER ? `http://localhost:8000/storage/payment_proofs/${tx.BUKTI_TRANSFER}` : '/img/default.jpg'}" 
                class="w-32 h-32 object-cover cursor-pointer border"
                onclick="openPreviewModal('${tx.BUKTI_TRANSFER ? `http://localhost:8000/storage/payment_proofs/${tx.BUKTI_TRANSFER}` : '/img/default.jpg'}')"
                alt="Bukti Pembayaran"
              >
              <p class="text-sm text-center mt-2">Klik untuk memperbesar</p>
            </div>
            
            <div class="md:col-span-2">
              <h4 class="font-semibold mb-2">Detail Pembelian:</h4>
              <ul class="text-sm space-y-1 mb-4">
                ${(tx.detail_transaksi || []).map(detail => `
                  <li>
                    ${detail.barang?.NAMA_BARANG || 'Unknown Product'} 
                    x${detail.JUMLAH} (Rp${parseInt(detail.barang?.HARGA || 0).toLocaleString('id-ID')})
                  </li>
                `).join('')}
              </ul>
              
              <div class="flex gap-2 justify-end">
                <button 
                  onclick="verifyPayment(${tx.ID_TRANSAKSI}, false)" 
                  class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition"
                >
                  Tolak Pembayaran
                </button>
                <button 
                  onclick="verifyPayment(${tx.ID_TRANSAKSI}, true)" 
                  class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
                >
                  Terima Pembayaran
                </button>
              </div>
            </div>
          </div>
        </div>
      `).join('');
      
    } catch (error) {
      console.error('Gagal memuat data verifikasi:', error);
      container.innerHTML = `
        <div class="bg-white rounded shadow p-8 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-red-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-red-500">Gagal memuat data verifikasi. Silakan coba lagi nanti.</p>
        </div>
      `;
    }
  }
  
  // Attach functions to window to make them globally accessible
  window.verifyPayment = async function(id, isValid) {
    const action = isValid ? 'terima' : 'tolak';
    if (!confirm(`Yakin ingin ${action} pembayaran ini?`)) return;
    
    try {
      await axios.post(`http://localhost:8000/api/cs/transaksi/${id}/verify`, 
        { is_valid: isValid },
        { headers: { Authorization: `Bearer ${verificationToken}` } }
      );
      
      alert(`Pembayaran berhasil di${action}`);
      loadVerifications();
    } catch (error) {
      console.error('Gagal verifikasi pembayaran:', error);
      alert(`Gagal ${action} pembayaran. Silakan coba lagi.`);
    }
  };
  
  window.openPreviewModal = function(imageUrl) {
    document.getElementById('bukti-preview').src = imageUrl;
    const modal = document.getElementById('modal-preview');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  };
  
  window.closePreviewModal = function() {
    const modal = document.getElementById('modal-preview');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  };
  
  // Load verifications on page load
  loadVerifications();
})(); // Tutup IIFE di sini
</script>
@endsection