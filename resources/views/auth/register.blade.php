@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
  <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-green-700 mb-2">Daftar</h2>
    <p class="text-center text-sm mb-6">
      Sudah punya akun?
      <a href="{{ url('/login') }}" class="text-green-600 font-semibold hover:underline">
        Masuk Sekarang
      </a>
    </p>

    <form id="registerForm" class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Daftar Sebagai</label>
        <select name="role" class="w-full px-3 py-2 border rounded" required onchange="updateFormFields(this.value)">
          <option value="pembeli">Pembeli</option>
          <option value="organisasi">Organisasi</option>
        </select>
      </div>

      <div id="dynamicFields"></div>

      <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">Daftar</button>
    </form>

    <p id="error" class="text-red-600 text-sm mt-4 text-center hidden"></p>
    <p id="success" class="text-green-600 text-sm mt-4 text-center hidden"></p>
  </div>
</div>

<script>
const dynamic = document.getElementById('dynamicFields');

const defaultFields = {
  pembeli: `
    <div>
      <label class="block text-sm font-medium">Nama Lengkap</label>
      <input type="text" name="nama_pembeli" class="w-full px-3 py-2 border rounded" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Email</label>
      <input type="email" name="email" class="w-full px-3 py-2 border rounded" required>
    </div>
    <div>
      <label class="block text-sm font-medium">No Telepon</label>
      <input type="text" name="no_telepon" class="w-full px-3 py-2 border rounded" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Tanggal Lahir</label>
      <input type="date" name="tanggal_lahir" class="w-full px-3 py-2 border rounded" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Password</label>
      <input type="password" name="password" class="w-full px-3 py-2 border rounded" required minlength="6">
    </div>
  `,
  organisasi: `
    <div>
       <label class="block text-sm font-medium">Nama Organisasi</label>
      <input type="text" id="namaOrganisasi" name="nama_organisasi" class="w-full px-3 py-2 border rounded" required>
      <small id="pesanOrganisasi" class="text-sm mt-1 block"></small>
    </div>
    <div>
      <label class="block text-sm font-medium">Alamat</label>
      <input type="text" name="alamat" class="w-full px-3 py-2 border rounded" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Email</label>
      <input type="email" name="email" class="w-full px-3 py-2 border rounded" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Username</label>
      <input type="text" name="username" class="w-full px-3 py-2 border rounded" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Password</label>
      <input type="password" name="password" class="w-full px-3 py-2 border rounded" required minlength="6">
    </div>
    <div>
      <label class="block text-sm font-medium">No Telepon</label>
      <input type="text" name="no_telepon" class="w-full px-3 py-2 border rounded" required>
    </div>
  `
};

function updateFormFields(role) {
  dynamic.innerHTML = defaultFields[role];
}

updateFormFields('pembeli'); // default awal

document.getElementById('registerForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const role = document.querySelector('[name=role]').value;

  const errorEl = document.getElementById('error');
  const successEl = document.getElementById('success');
  errorEl.classList.add('hidden');
  successEl.classList.add('hidden');

  let data = {};
  let endpoint = '';

  if (role === 'pembeli') {
    endpoint = 'http://localhost:8000/api/auth/pembeli/register';
    data = {
      email: document.querySelector('[name="email"]').value,
      password: document.querySelector('[name="password"]').value,
      nama_pembeli: document.querySelector('[name="nama_pembeli"]').value,
      no_telepon: document.querySelector('[name="no_telepon"]').value,
      tanggal_lahir: document.querySelector('[name="tanggal_lahir"]').value,
    };
  } else if (role === 'organisasi') {
    endpoint = 'http://localhost:8000/api/auth/organisasi/register';
    data = {
      nama_organisasi: document.querySelector('[name="nama_organisasi"]').value,
      alamat: document.querySelector('[name="alamat"]').value,
      email: document.querySelector('[name="email"]').value,
      username: document.querySelector('[name="username"]').value,
      password: document.querySelector('[name="password"]').value,
      no_telepon: document.querySelector('[name="no_telepon"]').value,
    };
  }

  console.log("Data dikirim:", data);

  try {
    await axios.post(endpoint, data); // langsung kirim object biasa
    successEl.textContent = 'Pendaftaran berhasil! Silakan login.';
    successEl.classList.remove('hidden');
    e.target.reset();
    updateFormFields(role);
  } catch (err) {
    console.error("Gagal daftar:", err.response?.data || err.message || err);
    if (err.response?.data?.errors) {
      errorEl.innerHTML = Object.values(err.response.data.errors)
        .map(msgs => `<div>${msgs[0]}</div>`).join('');
    } else {
      errorEl.textContent = err.response?.data?.message || 'Registrasi gagal.';
    }
    errorEl.classList.remove('hidden');
  }
});
</script>
<script>
document.addEventListener('input', async function (e) {
  if (e.target.id !== 'namaOrganisasi') return;

  const nama = e.target.value.trim();
  const pesan = document.getElementById('pesanOrganisasi');
  if (nama.length < 3) {
    pesan.textContent = '';
    return;
  }

  try {
    const res = await axios.get(`http://localhost:8000/api/cek-organisasi?nama=${encodeURIComponent(nama)}`);
    if (res.data.exists) {
      pesan.textContent = `Nama "${nama}" sudah ada di database, masukkan nama organisasi lain. Alamat organisasi ini adalah ${res.data.alamat}.`;
      pesan.className = 'text-sm mt-1 text-red-600';
    } else {
      pesan.textContent = `Nama "${nama}" belum ditemukan di database, sehingga boleh digunakan.`;
      pesan.className = 'text-sm mt-1 text-green-600';
    }
  } catch (err) {
    pesan.textContent = '';
    console.error('Gagal cek nama organisasi:', err);
  }
});

</script>

@endsection
