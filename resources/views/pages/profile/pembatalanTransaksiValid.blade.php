@extends('layouts.app')

@section('title', 'Pembatalan Transaksi Valid')

@section('content')
<div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-6xl mx-auto bg-white shadow-md rounded p-6">
        <div class="mb-6">
            <a href="{{ url('/pembeli/profile') }}" class="text-green-600 hover:underline text-sm">
                ← Kembali ke Profil
            </a>
        </div>
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Pembatalan Transaksi Valid</h2>
            <div class="text-sm text-gray-600">
                <span class="bg-blue-100 px-2 py-1 rounded">Dapat Dibatalkan</span>
                <span class="bg-red-100 px-2 py-1 rounded ml-2">Sudah Dibatalkan</span>
            </div>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Informasi:</strong> Hanya transaksi dengan status "Disiapkan" yang dapat dibatalkan. 
                        Transaksi yang sudah dijadwalkan pengiriman/pengambilan tidak dapat dibatalkan.
                    </p>
                </div>
            </div>
        </div>

        <div id="loadingState" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
            <p class="text-gray-600 mt-2">Memuat data transaksi...</p>
        </div>

        <div id="emptyState" class="text-center py-12 hidden">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada transaksi</h3>
            <p class="mt-2 text-gray-500">Tidak ada transaksi yang dapat dibatalkan saat ini.</p>
        </div>

        <div id="transaksiTable" class="hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No. Transaksi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Transaksi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Transaksi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status Transaksi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody id="transaksiTableBody" class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="confirmationModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Konfirmasi Pembatalan Transaksi
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="confirmationMessage">
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="confirmCancelBtn" 
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Ya, Batalkan Transaksi
                </button>
                <button type="button" id="cancelModalBtn"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Tidak, Kembali
                </button>
            </div>
        </div>
    </div>
</div>

<div id="successModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Transaksi Berhasil Dibatalkan
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="successMessage">
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="closeSuccessBtn"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    const transaksiTable = document.getElementById('transaksiTable');
    const transaksiTableBody = document.getElementById('transaksiTableBody');
    const confirmationModal = document.getElementById('confirmationModal');
    const successModal = document.getElementById('successModal');
    const confirmationMessage = document.getElementById('confirmationMessage');
    const successMessage = document.getElementById('successMessage');
    const confirmCancelBtn = document.getElementById('confirmCancelBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const closeSuccessBtn = document.getElementById('closeSuccessBtn');

    let currentTransaction = null;
    let currentUserProfile = null;

    async function loadUserProfile() {
        try {
            const response = await axios.get('http://localhost:8000/api/pembeli/profile');
            currentUserProfile = response.data.data;
        } catch (error) {
            console.error('Failed to load user profile:', error);
        }
    }

    async function loadTransactions() {
        try {
            loadingState.classList.remove('hidden');
            emptyState.classList.add('hidden');
            transaksiTable.classList.add('hidden');

            const response = await axios.get('http://localhost:8000/api/pembeli/transaksi');
            const allTransactions = response.data.data;

            const cancellableTransactions = allTransactions.filter(trx => {
                const status = trx.STATUS_TRANSAKSI.toLowerCase();
                return status === 'disiapkan' || 
                       status === 'diproses' || 
                       status === 'dibatalkan pembeli';
            });

            loadingState.classList.add('hidden');

            if (cancellableTransactions.length === 0) {
                emptyState.classList.remove('hidden');
                return;
            }

            transaksiTable.classList.remove('hidden');
            renderTransactions(cancellableTransactions);

        } catch (error) {
            console.error('Error loading transactions:', error);
            loadingState.classList.add('hidden');
            emptyState.classList.remove('hidden');
        }
    }

    function renderTransactions(transactions) {
        transaksiTableBody.innerHTML = transactions.map(trx => {
            const canCancel = trx.STATUS_TRANSAKSI.toLowerCase() !== 'dibatalkan pembeli';
            const statusClass = canCancel ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800';
            
            return `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ${trx.NO_NOTA || `#${trx.ID_TRANSAKSI}`}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${formatDate(trx.WAKTU_PESAN)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Rp ${formatCurrency(trx.TOTAL_AKHIR)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                            ${trx.STATUS_TRANSAKSI}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        ${canCancel ? 
                            `<button onclick="showCancelConfirmation(${trx.ID_TRANSAKSI}, ${trx.TOTAL_AKHIR})" 
                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded border border-red-200">
                                Batalkan Pesanan
                            </button>` :
                            `<span class="text-gray-400">Sudah Dibatalkan</span>`
                        }
                    </td>
                </tr>
            `;
        }).join('');
    }

    window.showCancelConfirmation = function(transactionId, totalAmount) {
        currentTransaction = { id: transactionId, total: totalAmount };
        
        const poinToAdd = Math.floor(totalAmount / 10000);
        const currentPoin = Number(currentUserProfile?.POIN || 0);
        const totalPoinAfter = currentPoin + poinToAdd;

        confirmationMessage.innerHTML = `
            <p class="mb-3">
                <strong>Apakah Anda yakin akan membatalkan transaksi ini?</strong>
            </p>
            <div class="bg-gray-50 p-3 rounded text-sm space-y-1">
                <p>• Total transaksi: <strong>Rp ${formatCurrency(totalAmount)}</strong></p>
                <p>• Akan dikonversi menjadi: <strong>${poinToAdd} poin reward</strong></p>
                <p>• Poin Anda saat ini: <strong>${currentPoin} poin</strong></p>
                <p>• Total poin setelah pembatalan: <strong>${totalPoinAfter} poin</strong></p>
            </div>
            <p class="mt-3 text-xs text-gray-600">
                <strong>Catatan:</strong> Transaksi yang sudah dibatalkan tidak dapat dikembalikan.
            </p>
        `;

        confirmationModal.classList.remove('hidden');
    };

    confirmCancelBtn.addEventListener('click', async () => {
        if (!currentTransaction) return;

        try {
            confirmCancelBtn.disabled = true;
            confirmCancelBtn.textContent = 'Membatalkan...';

            const response = await axios.post(`http://localhost:8000/api/pembeli/transaksi/${currentTransaction.id}/cancel-valid`);

            confirmationModal.classList.add('hidden');

            const poinAdded = Math.floor(currentTransaction.total / 10000);
            
            successMessage.innerHTML = `
                <p class="mb-2">Transaksi berhasil dibatalkan!</p>
                <div class="bg-green-50 p-3 rounded text-sm">
                    <p>✓ Total Rp ${formatCurrency(currentTransaction.total)} telah dikonversi menjadi ${poinAdded} poin reward</p>
                    <p>✓ Barang telah dikembalikan ke status tersedia</p>
                    <p>✓ Poin reward Anda telah diperbarui</p>
                </div>
            `;
            
            successModal.classList.remove('hidden');

            await loadUserProfile();
            await loadTransactions();

        } catch (error) {
            console.error('Error cancelling transaction:', error);
            alert('Gagal membatalkan transaksi. Silakan coba lagi.');
        } finally {
            confirmCancelBtn.disabled = false;
            confirmCancelBtn.textContent = 'Ya, Batalkan Transaksi';
        }
    });

    cancelModalBtn.addEventListener('click', () => {
        confirmationModal.classList.add('hidden');
        currentTransaction = null;
    });

    closeSuccessBtn.addEventListener('click', () => {
        successModal.classList.add('hidden');
    });

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID').format(amount);
    }

    async function init() {
        await loadUserProfile();
        await loadTransactions();
    }

    init();
});
</script>
@endsection