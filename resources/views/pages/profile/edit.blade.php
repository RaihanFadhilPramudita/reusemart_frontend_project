@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="min-h-screen bg-gray-100 py-10">
    <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Edit Profile</h2>

        <form id="editProfileForm" enctype="multipart/form-data" class="space-y-5">
            <!-- Foto -->
            <div class="flex flex-col items-center">
                <div class="relative w-28 h-28 rounded-full bg-gray-200 overflow-hidden shadow mb-2">
                    <img id="foto-preview" class="w-full h-full object-cover hidden" />
                    <label for="foto"
                        class="absolute bottom-1 right-1 bg-green-600 hover:bg-green-700 p-[6px] rounded-full cursor-pointer shadow-md">
                        <svg class="w-[14px] h-[14px] text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536M4 13v7h7l10-10-7-7L4 13z" />
                        </svg>
                    </label>
                </div>
                <input type="file" name="foto" id="foto" class="hidden" accept="image/*">
            </div>

            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap</label>
                <input type="text" name="nama_pembeli" id="nama_pembeli"
                    class="w-full border rounded-lg px-4 py-2 focus:ring focus:outline-none focus:border-green-500"
                    required>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Alamat Email</label>
                <input type="email" name="email" id="email"
                    class="w-full border rounded-lg px-4 py-2 focus:ring focus:outline-none focus:border-green-500"
                    required>
            </div>

            <!-- Telepon -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Nomor WhatsApp</label>
                <input type="text" name="no_telepon" id="no_telepon"
                    class="w-full border rounded-lg px-4 py-2 focus:ring focus:outline-none focus:border-green-500"
                    required>
            </div>

            <!-- Link ubah password -->
            <div class="text-right">
                <a href="/profile/password" class="text-green-600 text-sm hover:underline">Ubah Password</a>
            </div>

            <!-- Tombol Simpan -->
            <div>
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('token');
    axios.defaults.headers.common['Authorization'] = Bearer `${token}`;

    axios.get('http://localhost:8000/api/pembeli/profile').then(res => {
        const d = res.data.data;
        document.getElementById('nama_pembeli').value = d.NAMA_PEMBELI;
        document.getElementById('email').value = d.EMAIL;
        document.getElementById('no_telepon').value = d.NO_TELEPON;

        if (d.FOTO_PROFIL) {
            const preview = document.getElementById('foto-preview');
            preview.src = '/storage/foto_pembeli/' + d.FOTO_PROFIL;
            preview.classList.remove('hidden');
        }
    });

    document.getElementById('foto').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('foto-preview');
                img.src = e.target.result;
                img.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('editProfileForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            await axios.post('http://localhost:8000/api/pembeli/profile', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            alert('Profil berhasil diperbarui!');
        } catch (err) {
            alert('Gagal menyimpan perubahan.');
        }
    });
</script>
@endsection