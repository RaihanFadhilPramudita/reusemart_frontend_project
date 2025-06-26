@extends('layouts.app')

@section('title', 'Data Penitipan Saya')

@section('content')
<div class="flex bg-gray-100 min-h-screen">
    <!-- Sidebar Component -->
    @include('components.penitip-sidebar')

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Daftar Barang Titipan</h2>

            <!-- Filter dan Search Section -->
            <div class="mb-6 space-y-4">
                <!-- Filter Status -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">Filter Status Barang</label>
                        <select id="statusFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                            <option value="">Semua Status</option>
                            <option value="laku">Barang Sudah Laku</option>
                            <option value="belum_laku">Barang Belum Laku</option>
                            <option value="didonasikan">Barang Didonasikan</option>
                        </select>
                    </div>
                </div>

                <!-- Search Bar -->
                <div>
                    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Cari Barang</label>
                    <input type="text" id="searchInput"
                        placeholder="Cari nama barang atau deskripsi..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                </div>
            </div>

            <!-- Summary Cards -->
            <div id="summaryCards" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Summary cards will be populated by JavaScript -->
            </div>

            <!-- Barang List -->
            <div id="barangList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>

            <!-- Loading State -->
            <div id="loading" class="text-center py-10">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-green-600"></div>
                <p class="mt-2 text-gray-600">Memuat data...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Barang -->
<div id="modalDetail" class="fixed inset-0 hidden bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto m-4">
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold">Detail Barang</h3>
            <button onclick="tutupModal()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
        </div>
        <div id="detailContent" class="p-6"></div>
    </div>
</div>

<!-- Modal Masa Penitipan -->
<div id="modalMasaTitip" class="fixed inset-0 hidden bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md m-4 p-6">
        <h3 class="text-xl font-bold mb-4 border-b pb-2">Masa Penitipan</h3>

        <div class="space-y-3 my-4">
            <p><strong>Tanggal Titip:</strong> <span id="tanggalTitip" class="font-medium"></span></p>
            <p><strong>Tanggal Berakhir:</strong> <span id="tanggalAkhir" class="font-medium"></span></p>
        </div>

        <div class="flex justify-end mt-6">
            <button onclick="document.getElementById('modalMasaTitip').classList.add('hidden')"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Perpanjangan -->
<div id="modalPerpanjang" class="fixed inset-0 hidden bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md m-4 p-6">
        <h3 class="text-xl font-bold mb-4 border-b pb-2">Perpanjang Masa Penitipan</h3>

        <div class="space-y-4 my-4">
            <p class="text-gray-700">Perpanjangan akan menambahkan 30 hari dari tanggal berakhir saat ini.</p>

            <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg">
                <p class="text-sm text-yellow-800"><strong>Catatan:</strong> Perpanjangan masa penitipan akan menambah komisi menjadi 30% dari harga jual.</p>
            </div>

            <div class="flex items-center space-x-3">
                <span>Tanggal berakhir saat ini:</span>
                <span id="currentExpiry" class="font-medium"></span>
            </div>

            <div class="flex items-center space-x-3">
                <span>Tanggal berakhir baru:</span>
                <span id="newExpiry" class="font-medium text-green-600"></span>
            </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
            <button onclick="document.getElementById('modalPerpanjang').classList.add('hidden')"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">
                Batal
            </button>
            <button id="btnConfirmPerpanjang"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">
                Konfirmasi Perpanjangan
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const API_URL = "http://localhost:8000/api/penitip/tampil-barang";
    let currentItemId = null;
    let currentPenitipanId = null;
    let allBarangData = []; // Store all data for filtering

    function getImageUrl(path) {
        // If path is null/undefined/empty, return a default image
        if (!path) return '/img/default.jpg';
        return `http://localhost:8000/storage/${path}`;
    }

    function formatDate(dateString) {
        if (!dateString || dateString === '-') return '-';
        const date = new Date(dateString);
        if (isNaN(date)) return dateString;
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    function addDays(dateString, days) {
        if (!dateString || dateString === '-') return '-';
        const date = new Date(dateString);
        if (isNaN(date)) return dateString;
        date.setDate(date.getDate() + days);
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    function getStatusCategory(status) {
        if (!status) return 'belum_laku';
        const statusLower = status.toLowerCase();
        
        if (statusLower.includes('sold out') || statusLower.includes('terjual') || statusLower.includes('laku') || statusLower.includes('disiapkan')) {
            return 'laku';
        } else if (statusLower.includes('donasi') || statusLower.includes('didonasikan')) {
            return 'didonasikan';
        } else {
            return 'belum_laku';
        }
    }

    function filterBarang(barang, statusFilter, searchQuery) {
        return barang.filter(item => {
            // Filter by status
            const statusMatch = !statusFilter || getStatusCategory(item.STATUS) === statusFilter;
            
            // Filter by search query
            const searchMatch = !searchQuery || 
                item.NAMA_BARANG.toLowerCase().includes(searchQuery.toLowerCase()) ||
                (item.DESKRIPSI && item.DESKRIPSI.toLowerCase().includes(searchQuery.toLowerCase()));
            
            return statusMatch && searchMatch;
        });
    }

    function updateSummaryCards(barang) {
        const totalBarang = barang.length;
        const sudahLaku = barang.filter(b => getStatusCategory(b.STATUS) === 'laku').length;
        const belumLaku = barang.filter(b => getStatusCategory(b.STATUS) === 'belum_laku').length;
        const didonasikan = barang.filter(b => getStatusCategory(b.STATUS) === 'didonasikan').length;

        const summaryHTML = `
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Barang</p>
                        <p class="text-2xl font-bold text-gray-900">${totalBarang}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Sudah Laku</p>
                        <p class="text-2xl font-bold text-green-600">${sudahLaku}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Belum Laku</p>
                        <p class="text-2xl font-bold text-yellow-600">${belumLaku}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Didonasikan</p>
                        <p class="text-2xl font-bold text-purple-600">${didonasikan}</p>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById("summaryCards").innerHTML = summaryHTML;
    }

    function renderBarang(barang) {
        const container = document.getElementById("barangList");
        container.innerHTML = "";

        if (!barang.length) {
            container.innerHTML = '<div class="text-center text-gray-500 col-span-full py-10">Tidak ada barang ditemukan.</div>';
            return;
        }

        barang.forEach(b => {
            const card = document.createElement("div");
            card.className = "bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden";

            // Format the date for display
            let formattedDate = formatDate(b.TANGGAL_AKHIR);

            // Check status and categorize
            const statusCategory = getStatusCategory(b.STATUS);
            
            let statusClass = "bg-gray-100 text-gray-800";
            let statusText = b.STATUS || 'Tersedia';
            let dateText = formattedDate;

            if (statusCategory === 'laku') {
                statusClass = "bg-green-100 text-green-800";
                dateText = "Sudah Terjual";
            } else if (statusCategory === 'didonasikan') {
                statusClass = "bg-purple-100 text-purple-800";
                dateText = "Sudah Didonasikan";
            } else if (statusCategory === 'belum_laku') {
                statusClass = "bg-yellow-100 text-yellow-800";
                dateText = `Exp: ${formattedDate}`;
            }

            card.innerHTML = `
            <div class="h-48 overflow-hidden">
                <img src="${getImageUrl(b.FOTO_1)}" alt="Foto ${b.NAMA_BARANG}" 
                    class="w-full h-full object-cover">
            </div>
            <div class="p-4">
                <h4 class="text-lg font-bold mb-1 truncate">${b.NAMA_BARANG}</h4>
                <p class="text-gray-600 mb-2 text-sm line-clamp-2">${b.DESKRIPSI || 'Tidak ada deskripsi'}</p>
                <div class="flex justify-between items-center mt-4 text-sm">
                    <span class="px-2 py-1 rounded-full ${statusClass}">${statusText}</span>
                    <span class="text-gray-600">${dateText}</span>
                </div>
                <button onclick="lihatDetail(${b.ID_BARANG})" 
                    class="w-full mt-4 text-center bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
                    Lihat Detail
                </button>
            </div>
        `;
            container.appendChild(card);
        });
    }

    function applyFilters() {
        const statusFilter = document.getElementById("statusFilter").value;
        const searchQuery = document.getElementById("searchInput").value;
        
        const filteredBarang = filterBarang(allBarangData, statusFilter, searchQuery);
        renderBarang(filteredBarang);
        updateSummaryCards(allBarangData); // Always show summary for all data
    }

    function lihatDetail(id) {
        axios.get(`${API_URL}/${id}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        }).then(res => {
            const data = res.data.data;
            currentItemId = id;

            const penitipanId = data.id_penitipan || data.ID_PENITIPAN;

            // Check status category
            const statusCategory = getStatusCategory(data.STATUS);
            const isSoldOut = statusCategory === 'laku';
            const isDonated = statusCategory === 'didonasikan';

            // Calculate days until expiry (only if not sold out or donated)
            let selisihHari = 0;
            let bisaPerpanjang = !isSoldOut && !isDonated;
            let sudahBerakhir = false;

            if (!isSoldOut && !isDonated) {
                const akhir = new Date(data.TANGGAL_AKHIR);
                const sekarang = new Date();
                selisihHari = Math.ceil((akhir - sekarang) / (1000 * 60 * 60 * 24));
                sudahBerakhir = selisihHari <= 0;
            }

            // Format dates
            const formattedTitip = formatDate(data.TANGGAL_TITIP);
            let formattedAkhir = formatDate(data.TANGGAL_AKHIR);
            
            if (isSoldOut) {
                formattedAkhir = "Sudah Terjual";
            } else if (isDonated) {
                formattedAkhir = "Sudah Didonasikan";
            }

            // Create status badge with color
            let statusBadge = data.STATUS || "Tersedia";
            let statusClass = "bg-yellow-100 text-yellow-800";
            
            if (isSoldOut) {
                statusClass = "bg-green-100 text-green-800";
            } else if (isDonated) {
                statusClass = "bg-purple-100 text-purple-800";
            }

            const html = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <div>
                    <div class="rounded-lg overflow-hidden mb-2">
                        <img src="${getImageUrl(data.FOTO_1)}" class="w-full h-64 object-cover">
                    </div>
                    <div class="rounded-lg overflow-hidden">
                        <img src="${getImageUrl(data.FOTO_2)}" class="w-full h-48 object-cover">
                    </div>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-2">${data.NAMA_BARANG}</h3>
                    <p class="text-green-600 text-2xl font-bold mb-4">Rp${parseInt(data.HARGA || 0).toLocaleString()}</p>
                    
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex justify-between">
                                <span>Status:</span>
                                <span class="font-medium px-2 py-1 rounded-full ${statusClass}">${statusBadge}</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Tanggal Titip:</span>
                                <span class="font-medium">${formattedTitip}</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Berlaku Hingga:</span>
                                <span class="font-medium">${formattedAkhir}</span>
                            </li>
                            ${isSoldOut || isDonated ? '' : `
                            <li class="flex justify-between">
                                <span>Sisa Waktu:</span>
                                <span class="font-medium ${selisihHari < 0 ? 'text-red-600' : selisihHari <= 3 ? 'text-yellow-600' : 'text-green-600'}">
                                    ${selisihHari < 0 ? `Lewat ${Math.abs(selisihHari)} hari` : `${selisihHari} hari lagi`}
                                </span>
                            </li>`}
                        </ul>
                    </div>
                    
                    <div class="flex gap-2 flex-wrap">
                        <button onclick="lihatMasaTitip('${formattedTitip}', '${formattedAkhir}')" 
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Masa Penitipan
                        </button>
                        
                        ${!isSoldOut && !isDonated ? `
                        <button onclick="showPerpanjangModal('${data.TANGGAL_AKHIR}', ${id}, ${penitipanId || 0})" 
                            class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Perpanjang
                        </button>
                        ` : ''}
                                        
                        ${sudahBerakhir && !isSoldOut && !isDonated ? `
                        <button onclick="handleAmbilBarang(${data.ID_BARANG})" 
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            Ambil Barang
                        </button>` : ''}
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="font-bold mb-2">Deskripsi</h4>
                        <p class="text-gray-700">${data.DESKRIPSI || 'Tidak ada deskripsi'}</p>
                    </div>
                </div>
            </div>
        `;
            document.getElementById("detailContent").innerHTML = html;
            document.getElementById("modalDetail").classList.remove("hidden");
        }).catch(error => {
            console.error("Error fetching item details:", error);
            alert("Gagal memuat detail barang. Silakan coba lagi nanti.");
        });
    }

    function tutupModal() {
        document.getElementById("modalDetail").classList.add("hidden");
    }

    function lihatMasaTitip(tanggalTitip, tanggalAkhir) {
        document.getElementById('tanggalTitip').textContent = tanggalTitip;
        document.getElementById('tanggalAkhir').textContent = tanggalAkhir;
        document.getElementById('modalMasaTitip').classList.remove('hidden');
    }

    function showPerpanjangModal(expiryDate, barangId, penitipanId) {
        currentItemId = barangId;
        currentPenitipanId = penitipanId || 0;

        const currentExpiry = formatDate(expiryDate);
        const newExpiry = addDays(expiryDate, 30);

        document.getElementById('currentExpiry').textContent = currentExpiry;
        document.getElementById('newExpiry').textContent = newExpiry;

        const confirmBtn = document.getElementById('btnConfirmPerpanjang');
        confirmBtn.onclick = () => handlePerpanjang();

        document.getElementById('modalPerpanjang').classList.remove('hidden');
    }

    function handlePerpanjang() {
        if (!currentItemId) {
            alert("Terjadi kesalahan. Silakan coba lagi.");
            return;
        }

        const confirmBtn = document.getElementById('btnConfirmPerpanjang');
        const originalText = confirmBtn.innerHTML;

        confirmBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `;
        confirmBtn.disabled = true;

        let endpoint;
        if (!currentPenitipanId || currentPenitipanId === 0) {
            endpoint = `http://localhost:8000/api/penitip/barang/${currentItemId}/extend`;
        } else {
            endpoint = `http://localhost:8000/api/penitip/penitipan/${currentPenitipanId}/extend`;
        }

        axios.post(endpoint, {}, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
            .then(response => {
                console.log("Success response:", response);
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;

                document.getElementById('modalPerpanjang').classList.add('hidden');
                document.getElementById("modalDetail").classList.add("hidden");

                alert("Masa penitipan berhasil diperpanjang untuk 30 hari ke depan.");
                loadBarang();
            })
            .catch(error => {
                console.error("Perpanjangan gagal:", error);
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;

                const errorMsg = error.response?.data?.message || "Gagal memperpanjang masa penitipan. Silakan coba lagi nanti.";
                alert(errorMsg);
            })
            .finally(() => {
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            });
    }

    function handleAmbilBarang(id) {
        if (!confirm("Yakin ingin mengambil barang ini?")) return;

        axios.post(`http://localhost:8000/api/penitip/barang/${id}/ajukan-ambil`, {}, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        }).then(() => {
            alert("Permintaan pengambilan barang berhasil diajukan. Menunggu konfirmasi dari pihak gudang.");
            loadBarang();
            tutupModal();
        }).catch(error => {
            console.error("Error ajukan ambil:", error);
            alert("Gagal memproses permintaan ambil barang. Silakan coba lagi nanti.");
        });
    }

    function loadBarang() {
        document.getElementById("loading").classList.remove("hidden");
        document.getElementById("barangList").innerHTML = "";

        axios.get(API_URL, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        }).then(res => {
            allBarangData = res.data.data;
            updateSummaryCards(allBarangData);
            applyFilters();
            document.getElementById("loading").classList.add("hidden");
        }).catch(error => {
            console.error("Error loading data:", error);
            document.getElementById("loading").classList.add("hidden");
            document.getElementById("barangList").innerHTML =
                `<div class="text-center text-red-500 col-span-full py-10">
                Terjadi kesalahan saat memuat data. Silakan coba lagi nanti.
            </div>`;
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        loadBarang();
        
        // Add event listeners for filters
        document.getElementById("statusFilter").addEventListener("change", applyFilters);
        document.getElementById("searchInput").addEventListener("input", applyFilters);
    });
</script>
@endsection