@extends('layouts.app')

@section('title', 'Edit Alamat')

@section('content')
<div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-md rounded p-6">
        <h2 class="text-xl font-bold mb-6">Edit Alamat</h2>

        <form id="editAlamatForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium">Nama Alamat</label>
                <input type="text" id="nama_alamat" name="nama_alamat" class="w-full border px-3 py-2 rounded" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Alamat Lengkap</label>
                <input type="text" id="alamat_lengkap" name="alamat_lengkap" class="w-full border px-3 py-2 rounded" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Kecamatan</label>
                <input type="text" id="kecamatan" name="kecamatan" class="w-full border px-3 py-2 rounded" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Kota</label>
                <input type="text" id="kota" name="kota" class="w-full border px-3 py-2 rounded" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Kode Pos</label>
                <input type="text" id="kode_pos" name="kode_pos" class="w-full border px-3 py-2 rounded" required>
            </div>

            <div class="flex justify-between pt-4">
                <button type="button" id="hapusAlamat" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Hapus</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection


@section('scripts')
<script>
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    const pathParts = window.location.pathname.split('/').filter(Boolean);
    const id = pathParts[pathParts.length - 2]; 

    axios
        .get(`http://localhost:8000/api/pembeli/alamat/${id}`)
        .then(res => {
            const d = res.data.data;
            nama_alamat.value = d.NAMA_ALAMAT ?? '';
            alamat_lengkap.value = d.ALAMAT ?? '';
            kecamatan.value = d.KECAMATAN ?? '';
            kota.value = d.KOTA ?? '';
            kode_pos.value = d.KODE_POS ?? '';
        })
        .catch(() => alert('Gagal memuat data alamat'));

    editAlamatForm.addEventListener('submit', e => {
        e.preventDefault();

        const payload = {
            nama_alamat: nama_alamat.value.trim(),
            alamat_lengkap: alamat_lengkap.value.trim(),
            kecamatan: kecamatan.value.trim(),
            kota: kota.value.trim(),
            kode_pos: kode_pos.value.trim(),

            NAMA_ALAMAT: nama_alamat.value.trim(),
            ALAMAT: alamat_lengkap.value.trim(),
            KECAMATAN: kecamatan.value.trim(),
            KOTA: kota.value.trim(),
            KODE_POS: kode_pos.value.trim()
        };

        axios.put(`http://localhost:8000/api/pembeli/alamat/${id}`, payload)
            .then(() => {
                alert('Alamat berhasil diperbarui');
                window.location.href = '/pembeli/profile/alamat';
            })
            .catch(err => {
                console.error('Server response:', err.response?.data);
                const errors = err.response?.data?.errors;
                if (errors) {
                    alert(Object.values(errors).flat().join('\n'));
                } else {
                    alert(err.response?.data?.message || 'Gagal memperbarui alamat');
                }
            });
    });

    hapusAlamat.addEventListener('click', () => {
        if (!confirm('Yakin ingin menghapus alamat ini?')) return;

        axios.delete(`http://localhost:8000/api/pembeli/alamat/${id}`)
            .then(() => {
                alert('Alamat berhasil dihapus');
                window.location.href = '/pembeli/profile/alamat';
            })
            .catch(() => alert('Gagal menghapus alamat'));
    });
</script>
@endsection