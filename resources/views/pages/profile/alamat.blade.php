@extends('layouts.app')

@section('title', 'Alamat Pembeli')

@section('content')
<div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-3xl mx-auto bg-white p-6 shadow-md rounded relative">
        <div class="max-w-3xl mx-auto mb-4">
            <a href="{{ url('/pembeli/profile') }}" class="text-green-600 hover:underline text-sm">
                ← Kembali ke Profil
            </a>
        </div>
        
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Daftar Alamat</h2>
            <button id="btnTambah"
                class="bg-green-600 text-white px-4 py-2 rounded">
                Tambah Alamat Baru
            </button>
        </div>

        <input id="inputCari"
            type="text"
            placeholder="Cari alamat..."
            class="border px-3 py-2 rounded w-full mb-4" />

        <ul id="alamatList" class="space-y-2"></ul>
    </div>
</div>

<div id="modalAlamat"
    class="fixed inset-0 hidden bg-black/40 z-50 place-content-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Tambah Alamat Baru</h3>

        <form id="formAlamat" class="space-y-3">
            <div>
                <label class="block text-sm mb-1">Nama Alamat</label>
                <input name="nama_alamat"
                    class="w-full px-3 py-2 border rounded"
                    required />
            </div>
            <div>
                <label class="block text-sm mb-1">Alamat Lengkap</label>
                <textarea name="alamat_lengkap"
                    class="w-full px-3 py-2 border rounded"
                    required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm mb-1">Kecamatan</label>
                    <input name="kecamatan"
                        class="w-full px-3 py-2 border rounded"
                        required />
                </div>
                <div>
                    <label class="block text-sm mb-1">Kota</label>
                    <input name="kota"
                        class="w-full px-3 py-2 border rounded"
                        required />
                </div>
            </div>
            <div>
                <label class="block text-sm mb-1">Kode Pos</label>
                <input name="kode_pos"
                    class="w-full px-3 py-2 border rounded"
                    required />
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button" id="btnBatal"
                    class="px-4 py-2 border rounded">
                    Batal
                </button>
                <button type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const listEl = document.getElementById('alamatList');
        const searchEl = document.getElementById('inputCari');
        const modalEl = document.getElementById('modalAlamat');
        const formEl = document.getElementById('formAlamat');
        const btnTambah = document.getElementById('btnTambah');
        const btnBatal = document.getElementById('btnBatal');

        const token = localStorage.getItem('token') || '';
        const api = axios.create({
            baseURL: 'http://localhost:8000/api/pembeli',
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });

        function showModal() {
            modalEl.classList.remove('hidden');
        }

        function hideModal() {
            modalEl.classList.add('hidden');
            formEl.reset();
        }

        btnTambah.addEventListener('click', showModal);
        btnBatal.addEventListener('click', hideModal);
        searchEl.addEventListener('input', e => loadAlamat(e.target.value.toLowerCase()));
        formEl.addEventListener('submit', submitAlamat);

        async function loadAlamat(keyword = '') {
            try {
                const res = await api.get('/alamat/show');
                const raw = res.data.data ?? res.data;
                const list = Array.isArray(raw) ? raw : [];

                const mapped = list.map(r => ({
                    id: r.ID_ALAMAT ?? r.id,
                    nama_alamat: r.NAMA_ALAMAT ?? r.nama_alamat,
                    alamat_lengkap: r.ALAMAT_LENGKAP ?? r.alamat_lengkap,
                    kecamatan: r.KECAMATAN ?? r.kecamatan,
                    kota: r.KOTA ?? r.kota,
                    kode_pos: r.KODE_POS ?? r.kode_pos,
                }));

                const filtered = keyword ?
                    mapped.filter(a =>
                        (a.nama_alamat || '').toLowerCase().includes(keyword) ||
                        (a.alamat_lengkap || '').toLowerCase().includes(keyword)
                    ) :
                    mapped;

                listEl.innerHTML = filtered.length ?
                    filtered.map(a => `
                        <li class="border px-4 py-3 rounded hover:bg-gray-50 cursor-pointer">
                        <div onclick="window.location.href = '/pembeli/profile/alamat/${a.id}/edit'">
                            <p class="font-semibold">${a.nama_alamat || '(Tanpa Nama)'}</p>
                            <p class="text-sm text-gray-600">${a.alamat_lengkap ?? '-'}</p>
                            <p class="text-xs text-gray-500">
                            ${a.kecamatan ?? '-'}, ${a.kota ?? '-'}, ${a.kode_pos ?? '-'}
                            </p>
                        </div>
                        </li>
                    `).join('') :
                    `<p class="text-sm text-gray-500">Belum ada alamat.</p>`;

            } catch (err) {
                console.error(err);
                listEl.innerHTML =
                    `<p class="text-sm text-red-500">Gagal memuat alamat.</p>`;
            }
        }

        async function submitAlamat(e) {
            e.preventDefault();
            try {
                const data = Object.fromEntries(new FormData(formEl));
                await api.post('/alamat', data);
                alert('Alamat berhasil ditambahkan!');
                hideModal();
                loadAlamat();
            } catch (err) {
                console.error(err);
                alert('Gagal menambahkan alamat.');
            }
        }

        loadAlamat();
    });
</script>
@endsection
