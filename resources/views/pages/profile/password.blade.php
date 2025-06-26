@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-md mx-auto bg-white p-6 shadow-md rounded">
        <h2 class="text-xl font-bold mb-6">Ubah Password</h2>

        <form id="password-form" class="space-y-4">
            <div>
                <label class="block text-sm font-medium">Password Lama</label>
                <input type="password" id="current_password" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Password Baru</label>
                <input type="password" id="new_password" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Konfirmasi Password</label>
                <input type="password" id="confirm_password" class="w-full border rounded px-3 py-2" required>
                <p id="password-mismatch" class="text-red-500 text-xs hidden">Password tidak sama</p>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('confirm_password').addEventListener('input', function() {
        const newPass = document.getElementById('new_password').value;
        document.getElementById('password-mismatch').classList.toggle('hidden', this.value === newPass);
    });

    document.getElementById('password-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const token = localStorage.getItem('token');
        axios.defaults.headers.common['Authorization'] = Bearer `${token}`;

        try {
            await axios.post('http://localhost:8000/api/pembeli/change-password', {
                current_password: document.getElementById('current_password').value,
                new_password: document.getElementById('new_password').value,
                confirm_password: document.getElementById('confirm_password').value
            });
            alert('Password berhasil diubah');
        } catch (err) {
            alert('Gagal mengubah password');
        }
    });
</script>
@endsection