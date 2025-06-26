@extends('layouts.app')

@section('title', 'Profil Pembeli')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>
    <div class="container mx-auto px-4 py-10">
        <div class="bg-white rounded-lg shadow-md flex max-w-6xl mx-auto overflow-hidden">

            <aside class="w-1/4 bg-gray-50 p-6 border-r">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 mx-auto rounded-full bg-gray-300 overflow-hidden">
                        <img id="foto-profile" class="w-full h-full object-cover hidden" />
                    </div>
                    <h3 class="font-semibold mt-2" id="nama-pengguna">Nama Pengguna</h3>
                    <p class="text-sm text-gray-500" id="email-pengguna">email@example.com</p>
                    <p class="text-xs text-green-600 font-semibold">Poin: <span id="poin">0</span></p>
                    <a onclick='logout()' class="text-green-600 text-sm hover:underline mt-2 block">Logout</a>
                </div>

                <nav class="text-sm space-y-3">
                    <a href="/pembeli/profile/alamat" class="block hover:text-green-600">Alamat</a>
                    <a href="/pembeli/profile/edit" class="block hover:text-green-600">Edit Profil</a>
                    <a href="/pembeli/profile/pembatalan-transaksi" class="block hover:text-green-600 font-medium">Pembatalan Transaksi</a>
                    <a href="/pembeli/profile/bantuan" class="block hover:text-green-600">Bantuan</a>
                </nav>
            </aside>

            <main class="w-3/4 p-6">
                <h2 class="text-xl font-bold mb-4">Transaksi Saya</h2>

                <div class="flex border-b text-sm font-medium text-gray-600 space-x-6 mb-4">
                    <button class="tab-btn pb-2" data-tab="Diproses">Diproses</button>
                    <button class="tab-btn pb-2" data-tab="Dikirim">Dikirim</button>
                    <button class="tab-btn pb-2" data-tab="Selesai">Selesai</button>
                    <button class="tab-btn pb-2" data-tab="Pembayaran Ditolak">Pembayaran Ditolak</button>
                    <button class="tab-btn pb-2" data-tab="Dibatalkan">Dibatalkan</button>
                </div>

                <div id="tab-content" class="text-center py-10 text-gray-500">
                    <img src="/assets/empty-order.png" class="mx-auto mb-4 w-20" alt="Empty">
                    <p class="text-sm">Belum ada pesanan</p>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    axios.get('http://localhost:8000/api/pembeli/profile')
        .then(res => {
            const user = res.data.data;
            document.getElementById('nama-pengguna').textContent = user.NAMA_PEMBELI || 'Pengguna';
            document.getElementById('email-pengguna').textContent = user.EMAIL || '-';
            document.getElementById('poin').textContent = user.POIN || 0;
        })
        .catch(() => window.location.href = '/login');

    let transaksiData = [];

    axios.get('http://localhost:8000/api/pembeli/transaksi')
        .then(res => {
            transaksiData = res.data.data;
            initTabs();
        })
        .catch(err => {
            console.error("Gagal memuat transaksi:", err);
            document.getElementById('tab-content').innerHTML = `<p class="text-red-500">Gagal memuat transaksi.</p>`;
        });

    function initTabs() {
        const tabs = document.querySelectorAll('.tab-btn');
        const tabContent = document.getElementById('tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(btn => {
                    btn.classList.remove('text-green-600', 'border-b-2', 'border-green-600');
                });
                tab.classList.add('text-green-600', 'border-b-2', 'border-green-600');

                const status = tab.dataset.tab.toLowerCase();
                let filtered = [];
                
                // Custom filtering based on tab name
                if (status === 'diproses') {
                    filtered = transaksiData.filter(t => 
                        t.STATUS_TRANSAKSI.toLowerCase() === 'dikemas' || 
                        t.STATUS_TRANSAKSI.toLowerCase() === 'diproses'
                    );
                } else if (status === 'pembayaran ditolak') {
                    filtered = transaksiData.filter(t => 
                        t.STATUS_TRANSAKSI.toLowerCase() === 'pembayaran ditolak' ||
                        t.STATUS_TRANSAKSI.toLowerCase().includes('ditolak')
                    );
                } else if (status === 'dibatalkan') {
                    filtered = transaksiData.filter(t => 
                        t.STATUS_TRANSAKSI.toLowerCase() === 'dibatalkan' ||
                        t.STATUS_TRANSAKSI.toLowerCase() === 'dibatalkan pembeli'
                    );
                } else {
                    filtered = transaksiData.filter(t => 
                        t.STATUS_TRANSAKSI.toLowerCase() === status
                    );
                }

                // Check if there are no transactions to display
                if (filtered.length === 0) {
                    tabContent.innerHTML = `
                        <img src="/assets/empty-order.png" class="mx-auto mb-4 w-20" alt="Empty">
                        <p class="text-sm">Belum ada pesanan pada tab "<strong>${tab.dataset.tab}</strong>"</p>
                    `;
                    return; // Exit early
                }

                // Special handling for "selesai" tab
                if (status === 'selesai') {
                    tabContent.innerHTML = filtered.map(trx => {
                        // Check if rating exists already for this transaction
                        const hasRated = trx.RATING !== undefined && trx.RATING !== null;
                        
                        // Get the penitip ID from the transaction details
                        // Adjust this path based on your actual API response structure
                        const penitipId = trx.detail_transaksi?.[0]?.barang?.ID_PENITIP || '';
                        
                        return `
                            <div class="bg-white border rounded p-4 mb-4 text-left shadow-sm">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold">Transaksi #${trx.ID_TRANSAKSI}</p>
                                        <p class="text-sm text-gray-500">${new Date(trx.WAKTU_PESAN).toLocaleDateString('id-ID')}</p>
                                    </div>
                                    <span class="text-sm px-3 py-1 rounded-full bg-gray-100">${trx.STATUS_TRANSAKSI}</span>
                                </div>
                                <p class="mt-2">Total: <strong>Rp${Number(trx.TOTAL_AKHIR).toLocaleString('id-ID')}</strong></p>
                                
                                <!-- Rating Section -->
                                <div class="mt-3 pt-3 border-t">
                                    ${hasRated ? 
                                        `<div class="flex items-center">
                                            <p class="text-sm mr-2">Penilaian Anda:</p>
                                            <div class="flex text-yellow-400">
                                                ${generateStars(trx.RATING)}
                                            </div>
                                        </div>` 
                                        : 
                                        `<div class="rating-container" data-transaction-id="${trx.ID_TRANSAKSI}" data-penitip-id="${penitipId}">
                                            <p class="text-sm mb-1">Beri Penilaian:</p>
                                            <div class="flex items-center">
                                                <div class="star-rating flex">
                                                    <span class="star cursor-pointer text-xl" data-value="1">☆</span>
                                                    <span class="star cursor-pointer text-xl" data-value="2">☆</span>
                                                    <span class="star cursor-pointer text-xl" data-value="3">☆</span>
                                                    <span class="star cursor-pointer text-xl" data-value="4">☆</span>
                                                    <span class="star cursor-pointer text-xl" data-value="5">☆</span>
                                                </div>
                                                <button class="submit-rating ml-3 px-3 py-1 bg-green-600 text-white text-sm rounded disabled:bg-gray-300" disabled>Kirim</button>
                                            </div>
                                        </div>`
                                    }
                                </div>
                            </div>
                        `;
                    }).join('');
                    
                    // Initialize star rating functionality
                    initializeRatingSystem();
                } else {
                    // Regular format for other tabs
                    tabContent.innerHTML = filtered.map(trx => `
                        <div class="bg-white border rounded p-4 mb-4 text-left shadow-sm">
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
                }
            });
        });

        tabs[0].click();
    }
});

async function logout() {
    const token = localStorage.getItem('token');
    if (!token) return window.location.href = "/login";

    try {
        await axios.post('http://localhost:8000/api/pembeli/logout', {}, {
            headers: { Authorization: `Bearer ${token}` }
        });
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = "/login";
    } catch (error) {
        alert("Logout gagal.");
    }
}

// Generate stars based on rating value
function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<span class="text-xl">★</span>'; // Filled star
        } else {
            stars += '<span class="text-xl">☆</span>'; // Empty star
        }
    }
    return stars;
}

// Initialize rating system
function initializeRatingSystem() {
    // Select all star elements
    const stars = document.querySelectorAll('.star');
    const submitButtons = document.querySelectorAll('.submit-rating');
    
    stars.forEach(star => {
        // Mouse over
        star.addEventListener('mouseover', function() {
            const ratingValue = parseInt(this.getAttribute('data-value'));
            const container = this.closest('.star-rating');
            const allStars = container.querySelectorAll('.star');
            
            allStars.forEach(s => {
                const starValue = parseInt(s.getAttribute('data-value'));
                if (starValue <= ratingValue) {
                    s.textContent = '★'; // Filled star
                    s.classList.add('text-yellow-400');
                } else {
                    s.textContent = '☆'; // Empty star
                    s.classList.remove('text-yellow-400');
                }
            });
            
            // Enable submit button
            const submitButton = this.closest('.rating-container').querySelector('.submit-rating');
            submitButton.disabled = false;
            
            // Store selected rating
            container.setAttribute('data-selected-rating', ratingValue);
        });
        
        // Mouse out (if not clicked)
        star.addEventListener('mouseout', function() {
            const container = this.closest('.star-rating');
            if (!container.classList.contains('rating-selected')) {
                const allStars = container.querySelectorAll('.star');
                allStars.forEach(s => {
                    s.textContent = '☆'; // Reset to empty star
                    s.classList.remove('text-yellow-400');
                });
                
                // Disable submit button
                const submitButton = this.closest('.rating-container').querySelector('.submit-rating');
                submitButton.disabled = true;
            }
        });
        
        // Click to select
        star.addEventListener('click', function() {
            const ratingValue = parseInt(this.getAttribute('data-value'));
            const container = this.closest('.star-rating');
            
            // Mark as selected
            container.classList.add('rating-selected');
            container.setAttribute('data-selected-rating', ratingValue);
            
            // Lock in the stars
            const allStars = container.querySelectorAll('.star');
            allStars.forEach(s => {
                const starValue = parseInt(s.getAttribute('data-value'));
                if (starValue <= ratingValue) {
                    s.textContent = '★'; // Filled star
                    s.classList.add('text-yellow-400');
                } else {
                    s.textContent = '☆'; // Empty star
                    s.classList.remove('text-yellow-400');
                }
            });
            
            // Enable submit button
            const submitButton = this.closest('.rating-container').querySelector('.submit-rating');
            submitButton.disabled = false;
        });
    });
    
    // Handle submit button
    submitButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const container = this.closest('.rating-container');
            const starRating = container.querySelector('.star-rating');
            const rating = parseInt(starRating.getAttribute('data-selected-rating'));
            const transactionId = container.getAttribute('data-transaction-id');
            const penitipId = container.getAttribute('data-penitip-id');
            
            if (!rating || !transactionId) {
                alert('Silakan pilih rating terlebih dahulu');
                return;
            }
            
            // Disable the button during submission
            this.disabled = true;
            this.textContent = 'Mengirim...';
            
            try {
                // Submit rating to the server
                await submitRating(transactionId, rating, penitipId);
                
                // Replace rating UI with static stars
                const ratingHTML = `
                    <div class="flex items-center">
                        <p class="text-sm mr-2">Penilaian Anda:</p>
                        <div class="flex text-yellow-400">
                            ${generateStars(rating)}
                        </div>
                    </div>
                `;
                container.innerHTML = ratingHTML;
                
                // Success message
                showToast('Terima kasih atas penilaian Anda!', 'success');
            } catch (error) {
                console.error('Error submitting rating:', error);
                // Re-enable the button
                this.disabled = false;
                this.textContent = 'Kirim';
                showToast('Gagal mengirim penilaian. Silakan coba lagi.', 'error');
            }
        });
    });
}

// Submit rating to the server
async function submitRating(transactionId, rating, penitipId) {
    const token = localStorage.getItem('token');
    if (!token) {
        throw new Error('Tidak ada token autentikasi');
    }
    
    try {
        const response = await axios.post('http://localhost:8000/api/pembeli/rating', {
            id_transaksi: transactionId,
            rating: rating,
            id_penitip: penitipId
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        
        return response.data;
    } catch (error) {
        console.error('Error in submitRating:', error);
        throw error;
    }
}

// Toast notification
function showToast(message, type = 'success') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `mb-3 p-4 rounded shadow-lg flex items-center ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    
    // Add icon based on type
    const icon = type === 'success' ? '✅' : '❌';
    
    // Set content
    toast.innerHTML = `
        <span class="mr-2">${icon}</span>
        <span>${message}</span>
    `;
    
    // Add to container
    toastContainer.appendChild(toast);
    
    // Remove after 3 seconds with fade effect
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 500ms';
        setTimeout(() => {
            toast.remove();
        }, 500);
    }, 3000);
}
</script>
@endsection