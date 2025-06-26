@extends('layouts.app')

@section('content')
<div class="flex">
  <x-admin-sidebar />
  <main class="flex-1 p-6">
    <h2 class="text-xl font-bold mb-4">Tambah Pegawai</h2>

    <form method="POST" action="{{ route('pegawai.store') }}" class="space-y-4">
      @csrf

      <div>
        <label class="block text-sm font-medium">Nama Pegawai</label>
        <input type="text" name="nama" class="w-full px-3 py-2 border rounded" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Username</label>
        <input type="text" name="username" class="w-full px-3 py-2 border rounded" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" class="w-full px-3 py-2 border rounded" required>
      </div>

      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
    </form>
  </main>
</div>

<script>
document.querySelector('form').addEventListener('submit', async e => {
  e.preventDefault();

  const data = {
    nama: document.querySelector('[name=nama]').value,
    username: document.querySelector('[name=username]').value,
    email: document.querySelector('[name=email]').value
  };

  try {
    await axios.post('/pegawai', data);
    window.location.href = '/admin/pegawai';
  } catch (err) {
    alert('Gagal menambahkan pegawai');
    console.error(err);
  }
});
</script>

@endsection
