@extends('layouts.app')

@section('title', 'Proses Pesanan')

@section('content')
<div class="flex">
  <x-gudang-sidebar />

  <main class="flex-1 p-6">
    <h2 class="text-xl font-bold mb-6">Kelola Pesanan Diproses</h2>

    <div class="mb-4">
      <input type="text" id="searchInput" placeholder="Cari nomor transaksi atau nama pembeli..."
        class="w-full md:w-1/2 px-4 py-2 border rounded">
    </div>

    <!-- Filter Tabs -->
    <div class="flex space-x-4 mb-6 border-b">
      <button id="btnDiproses" class="tab-btn px-4 py-2 text-green-600 border-b-2 border-green-600">
        Diproses
      </button>
      <button id="btnSelesai" class="tab-btn px-4 py-2">
        Selesai
      </button>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="text-center py-10">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-green-600"></div>
      <p class="mt-2 text-gray-600">Memuat data pesanan...</p>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden text-center py-12 bg-white rounded shadow">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pesanan</h3>
      <p class="mt-1 text-sm text-gray-500" id="emptyStateMessage">
        Belum ada pesanan yang diproses.
      </p>
    </div>

    <!-- Order List -->
    <div id="orderList" class="hidden space-y-4"></div>

    <!-- Order Detail Modal -->
    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white">
          <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Detail Pesanan #123</h3>
          <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal()">
            <span class="text-2xl">&times;</span>
          </button>
        </div>
        <div class="p-6" id="modalContent">
          <!-- Content will be loaded dynamically -->
        </div>
        <div class="px-6 py-4 border-t bg-gray-50 sticky bottom-0">
          <div class="flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100"
              onclick="closeModal()">Tutup</button>
            <button type="button" id="btnProsesOrder"
              class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
              Proses Pesanan
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const searchInput = document.getElementById('searchInput');
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    const emptyStateMessage = document.getElementById('emptyStateMessage');
    const orderList = document.getElementById('orderList');
    const tabDiproses = document.getElementById('btnDiproses');
    const tabSelesai = document.getElementById('btnSelesai');
    const btnProsesOrder = document.getElementById('btnProsesOrder');

    // State
    let currentTab = 'diproses';
    let orders = [];
    let currentOrderId = null;

    // Init
    loadOrders();

    // Load orders from API
    async function loadOrders(search = '') {
      try {
        // Show loading state
        loadingState.classList.remove('hidden');
        orderList.classList.add('hidden');
        emptyState.classList.add('hidden');

        // Get token - ensure it's available
        const token = localStorage.getItem('token');
        if (!token) {
          console.error('No authentication token found');
          emptyState.classList.remove('hidden');
          loadingState.classList.add('hidden');
          emptyStateMessage.textContent = 'Sesi telah berakhir. Silakan login kembali.';
          return;
        }

        // Check user role to ensure they're a CS
        const userStr = localStorage.getItem('user');
        if (userStr) {
          const user = JSON.parse(userStr);
          const isGudang = user?.jabatan?.NAMA_JABATAN === "Gudang";
          if (!isGudang) {
            console.error('User is not a Gudang');
            emptyState.classList.remove('hidden');
            loadingState.classList.add('hidden');
            emptyStateMessage.textContent = 'Anda tidak memiliki akses untuk melihat halaman ini.';
            return;
          }
        }

        // Fetch orders
        // Fix bagian ini
        const statusFilter = currentTab === 'selesai' ? 'Selesai' : 'Diproses';

        const url = search ?
          `http://localhost:8000/api/gudang/pesanan?status=${statusFilter}&search=${encodeURIComponent(search)}` :
          `http://localhost:8000/api/gudang/pesanan?status=${statusFilter}`;


        // // For debugging
        // console.log('Requesting URL:', url);
        // console.log('With token:', token);

        const response = await axios.get(url, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        });

        orders = (response.data.data || []).filter(order => {
          return (
            order.WAKTU_KIRIM !== null ||
            order.WAKTU_AMBIL !== null
          );
        });

        // Hide loading state
        loadingState.classList.add('hidden');

        // Show empty state if no orders
        if (!orders.length) {
          emptyState.classList.remove('hidden');
          emptyStateMessage.textContent = currentTab === 'diproses' ?
            'Belum ada pesanan yang diproses.' :
            'Belum ada pesanan yang selesai.';
          return;
        }

        // Render orders
        renderOrders();
        orderList.classList.remove('hidden');
      } catch (error) {
        console.error('Error loading orders:', error);
        loadingState.classList.add('hidden');
        emptyState.classList.remove('hidden');

        // Provide more specific error messages
        if (error.response?.status === 401 || error.response?.status === 403) {
          emptyStateMessage.textContent = 'Sesi telah berakhir atau Anda tidak memiliki akses. Silakan login kembali.';
          // Optionally redirect to login
          // window.location.href = '/login';
        } else {
          emptyStateMessage.textContent = 'Terjadi kesalahan saat memuat data pesanan.';
        }
      }
    }

    // Render orders list
    function renderOrders() {
      orderList.innerHTML = orders.map(order => `
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="p-4 border-b">
            <div class="flex justify-between items-center">
              <div>
                <span class="font-semibold">Pesanan #${order.ID_TRANSAKSI}</span>
                <span class="text-sm text-gray-500 ml-2">${formatDate(order.WAKTU_PESAN)}</span>
              </div>
              <span class="px-3 py-1 bg-${getStatusColor(order.STATUS_TRANSAKSI)}-100 
                text-${getStatusColor(order.STATUS_TRANSAKSI)}-800 rounded-full text-xs">
                ${order.STATUS_TRANSAKSI}
              </span>
            </div>
          </div>
          
          <div class="p-4">
            <div class="grid md:grid-cols-3 gap-4 text-sm">
              <div>
                <p class="text-gray-500">Pembeli</p>
                <p class="font-medium">${order.pembeli?.NAMA_PEMBELI || 'N/A'}</p>
              </div>
              <div>
                <p class="text-gray-500">Metode Pengiriman</p>
                <p class="font-medium">${order.JENIS_DELIVERY || 'N/A'}</p>
              </div>
              <div>
                <p class="text-gray-500">Total</p>
                <p class="font-bold text-green-600">Rp${formatPrice(order.TOTAL_AKHIR)}</p>
              </div>
            </div>
            
            <div class="mt-4 flex justify-end">
              <button type="button" onclick="showOrderDetail(${order.ID_TRANSAKSI})" 
                class="px-4 py-2 border border-gray-300 rounded text-sm font-medium hover:bg-gray-50">
                Lihat Detail
              </button>
              ${currentTab === 'diproses' ? `
                <button type="button" onclick="processOrder(${order.ID_TRANSAKSI})" 
                  class="ml-3 px-4 py-2 bg-green-600 rounded text-sm font-medium text-white hover:bg-green-700">
                  Proses Pesanan
                </button>
              ` : ''}
            </div>
          </div>
        </div>
      `).join('');
    }

    // Show order detail modal
    window.showOrderDetail = async (orderId) => {
      try {
        // Get order detail
        const token = localStorage.getItem('token');
        if (!token) {
          alert('Sesi telah berakhir. Silakan login kembali.');
          return;
        }

        const response = await axios.get(`http://localhost:8000/api/gudang/pesanan/${orderId}`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });

        console.log('Order detail response:', response.data);
        console.log('Detail structure:', JSON.stringify(response.data.data));

        const order = response.data.data;
        currentOrderId = orderId;

        // Update modal title
        document.getElementById('modalTitle').textContent = `Detail Pesanan #${orderId}`;

        // Show/hide process button based on status
        if (currentTab === 'diproses') {
          btnProsesOrder.classList.remove('hidden');
        } else {
          btnProsesOrder.classList.add('hidden');
        }

        // Render order detail
        const modalContent = document.getElementById('modalContent');
        modalContent.innerHTML = `
          <div class="space-y-6">
            <div>
              <h4 class="font-semibold text-gray-900 mb-2">Informasi Pesanan</h4>
              <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div>
                  <p class="text-gray-500">Tanggal Pesanan</p>
                  <p class="font-medium">${formatDate(order.WAKTU_PESAN)}</p>
                </div>
                <div>
                  <p class="text-gray-500">Status</p>
                  <p class="font-medium">${order.STATUS_TRANSAKSI}</p>
                </div>
                <div>
                  <p class="text-gray-500">Metode Pengiriman</p>
                  <p class="font-medium">${order.JENIS_DELIVERY || 'N/A'}</p>
                </div>
                <div>
                  <p class="text-gray-500">Total</p>
                  <p class="font-bold text-green-600">Rp${formatPrice(order.TOTAL_AKHIR)}</p>
                </div>
              </div>
            </div>
            
            <div>
              <h4 class="font-semibold text-gray-900 mb-2">Informasi Pembeli</h4>
              <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div>
                  <p class="text-gray-500">Nama</p>
                  <p class="font-medium">${order.pembeli?.NAMA_PEMBELI || 'N/A'}</p>
                </div>
                <div>
                  <p class="text-gray-500">Email</p>
                  <p class="font-medium">${order.pembeli?.EMAIL || 'N/A'}</p>
                </div>
                <div>
                  <p class="text-gray-500">No. Telepon</p>
                  <p class="font-medium">${order.pembeli?.NO_TELEPON || 'N/A'}</p>
                </div>
              </div>
            </div>
            
            <div>
              <h4 class="font-semibold text-gray-900 mb-2">Alamat Pengiriman</h4>
              <p class="text-sm">${order.alamat?.ALAMAT_LENGKAP || 'N/A'}</p>
              <p class="text-sm">${order.alamat?.KECAMATAN || ''} ${order.alamat?.KOTA || ''} ${order.alamat?.KODE_POS || ''}</p>
            </div>
            
            <div>
              <h4 class="font-semibold text-gray-900 mb-2">Detail Produk</h4>
              <div class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    ${(order.detail_transaksi || []).map(item => `
                      <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                              <img class="h-10 w-10 rounded object-cover" 
                                src="${item.barang?.GAMBAR_URL || '/img/default.jpg'}" alt="">
                            </div>
                            <div class="ml-4">
                              <div class="text-sm font-medium text-gray-900">${item.barang?.NAMA_BARANG || 'N/A'}</div>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          Rp${formatPrice(item.barang?.HARGA)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          ${item.JUMLAH}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          Rp${formatPrice(item.HARGA_SUBTOTAL)}
                        </td>
                      </tr>
                    `).join('')}
                  </tbody>
                </table>
              </div>
            </div>
            
            ${order.bukti_transfer ? `
              <div>
                <h4 class="font-semibold text-gray-900 mb-2">Bukti Pembayaran</h4>
                <div class="border p-4 rounded-lg">
                  <a href="${order.bukti_transfer}" target="_blank" class="text-green-600 hover:underline">
                    <img src="${order.bukti_transfer}" alt="Bukti Pembayaran" class="max-h-64 mx-auto">
                    <p class="text-center mt-2 text-sm">Klik untuk melihat gambar lengkap</p>
                  </a>
                </div>
              </div>
            ` : ''}
          </div>
        `;

        // Show modal
        document.getElementById('detailModal').classList.add('flex');
        document.getElementById('detailModal').classList.remove('hidden');
      } catch (error) {
        console.error('Error loading order detail:', error);

        if (error.response?.status === 401 || error.response?.status === 403) {
          alert('Sesi telah berakhir atau Anda tidak memiliki akses. Silakan login kembali.');
        } else {
          alert('Terjadi kesalahan saat memuat detail pesanan.');
        }
      }
    };

    // Close modal
    window.closeModal = () => {
      document.getElementById('detailModal').classList.add('hidden');
      document.getElementById('detailModal').classList.remove('flex');
      currentOrderId = null;
    };

    // Process order
    window.processOrder = async (orderId) => {
      if (!confirm('Apakah Anda yakin ingin memproses pesanan ini ke status Selesai?')) {
        return;
      }

      try {
        const token = localStorage.getItem('token');
        if (!token) {
          alert('Sesi telah berakhir. Silakan login kembali.');
          return;
        }

        await axios.put(`http://localhost:8000/api/gudang/pesanan/${orderId}/selesai`, {
          status: 'Selesai'
        }, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        });


        // Close modal if open
        if (currentOrderId === orderId) {
          closeModal();
        }

        // Reload orders
        await loadOrders(searchInput.value);

        // Show success message
        alert('Pesanan berhasil diproses!');
      } catch (error) {
        console.error('Error processing order:', error);

        if (error.response?.status === 401 || error.response?.status === 403) {
          alert('Sesi telah berakhir atau Anda tidak memiliki akses. Silakan login kembali.');
        } else {
          alert('Terjadi kesalahan saat memproses pesanan.');
        }
      }
    };

    // Event Listeners
    tabDiproses.addEventListener('click', () => {
      if (currentTab !== 'diproses') {
        currentTab = 'diproses';
        tabDiproses.classList.add('text-green-600', 'border-b-2', 'border-green-600');
        tabSelesai.classList.remove('text-green-600', 'border-b-2', 'border-green-600');
        loadOrders(searchInput.value);
      }
    });

    tabSelesai.addEventListener('click', () => {
      if (currentTab !== 'selesai') {
        currentTab = 'selesai';
        tabSelesai.classList.add('text-green-600', 'border-b-2', 'border-green-600');
        tabDiproses.classList.remove('text-green-600', 'border-b-2', 'border-green-600');
        loadOrders(searchInput.value);
      }
    });

    searchInput.addEventListener('input', () => {
      loadOrders(searchInput.value);
    });

    btnProsesOrder.addEventListener('click', () => {
      if (currentOrderId) {
        processOrder(currentOrderId);
      }
    });

    // Helper functions
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

    function formatPrice(price) {
      if (!price) return '0';
      return parseInt(price).toLocaleString('id-ID');
    }

    function getStatusColor(status) {
      if (!status) return 'gray';

      status = status.toLowerCase();

      if (status === 'selesai') return 'green';
      if (status === 'diproses' || status === 'dikemas') return 'blue';
      if (status === 'dikirim') return 'indigo';
      if (status.includes('ditolak') || status === 'dibatalkan') return 'red';

      return 'gray';
    }
  });
</script>
@endsection