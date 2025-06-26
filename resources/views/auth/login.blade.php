@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
  <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-green-700 mb-2">Login</h2>
    <p class="text-center text-sm mb-6">
      Belum punya akun?
      <a href="{{ url('/register') }}" class="text-green-600 font-semibold hover:underline">
        Daftar Sekarang
      </a>
    </p>

    <form id="loginForm" class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Login Sebagai</label>
        <select name="role" class="w-full px-3 py-2 border rounded" required>
          <option value="pegawai">Pegawai</option>
          <option value="pembeli">Pembeli</option>
          <option value="penitip">Penitip</option>
          <option value="organisasi">Organisasi</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium">Username</label>
        <input type="text" name="username" class="w-full px-3 py-2 border rounded" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" class="w-full px-3 py-2 border rounded" required>
      </div>
      <div class="text-sm text-right">
        <a href="#" onclick="handleForget()" class="text-gray-600 hover:text-green-600">
          Lupa Password?
        </a>
      </div>
      <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">Login</button>
    </form>

    <p id="error" class="text-red-600 text-sm mt-4 text-center hidden"></p>
  </div>

  <div id="modalReset" class="fixed inset-0 flex bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-sm">
      <h3 class="text-lg font-bold mb-4 text-center text-green-700">Reset Password Pegawai</h3>
      <input type="email" id="resetEmail" placeholder="Masukkan Email Pegawai" class="w-full border px-3 py-2 rounded mb-4" required>
      <div class="flex justify-end space-x-2">
        <button onclick="tutupResetModal()" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</button>
        <button onclick="submitReset()" class="bg-green-600 text-white px-4 py-2 rounded">Reset</button>
      </div>
      <p id="resetResult" class="text-sm text-center mt-4 text-green-700 font-medium hidden"></p>
    </div>
  </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const role = document.querySelector('[name=role]').value;
  const username = document.querySelector('[name=username]').value;
  const password = document.querySelector('[name=password]').value;
  const errorEl = document.getElementById('error');

  errorEl.classList.add('hidden');

  let endpoint = '';
  switch (role) {
    case 'pegawai':
      endpoint = 'http://localhost:8000/api/auth/pegawai/login';
      break;
    case 'pembeli':
      endpoint = 'http://localhost:8000/api/auth/pembeli/login';
      break;
    case 'penitip':
      endpoint = 'http://localhost:8000/api/auth/penitip/login';
      break;
    case 'organisasi':
      endpoint = 'http://localhost:8000/api/auth/organisasi/login';
      break;
    default:
      errorEl.textContent = 'Peran login tidak valid.';
      errorEl.classList.remove('hidden');
      return;
  }

  try {
    const loginPayload = {
      password
    };

    if (role === 'pegawai' || role === 'organisasi') {
      loginPayload.username = username;
    } else {
      loginPayload.email = username;
    }

    const res = await axios.post(endpoint, loginPayload);
    const { token, user: data, data: alt } = res.data;
    const user = data || alt;

    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    if (role === 'pegawai') {
      const jabatan = user.jabatan?.NAMA_JABATAN;
      switch (jabatan) {
        case 'Admin':
          axios.defaults.baseURL = 'http://localhost:8000/api/admin';
          window.location.href = '/admin/organisasi';
          break;
        case 'Customer Service':
          axios.defaults.baseURL = 'http://localhost:8000/api/cs';
          window.location.href = '/cs/penitip';
          break;
        case 'Gudang':
          axios.defaults.baseURL = 'http://localhost:8000/api/gudang';
          window.location.href = '/gudang/barang';
          break;
        case 'Owner':
          axios.defaults.baseURL = 'http://localhost:8000/api/owner';
          window.location.href = '/owner/request_donasi';
          break;
        default:
          errorEl.textContent = `Jabatan "${jabatan}" tidak dikenali.`;
          errorEl.classList.remove('hidden');
          return;
      }
    } else if (role === 'organisasi') {
      axios.defaults.baseURL = 'http://localhost:8000/api/organisasi';
      window.location.href = '/organisasi/request';
     } else if (role === 'pembeli') {
      axios.defaults.baseURL = 'http://localhost:8000/api/pembeli';
      window.location.href = '/';
   } else if (role === 'penitip') {
      axios.defaults.baseURL = `http://localhost:8000/api/penitip`;
      window.location.href = '/penitip';
    } else {
      window.location.href = '/';
    }

  } catch (err) {
    console.error(err);
    errorEl.textContent = 'Login gagal. Username/email atau password salah.';
    errorEl.classList.remove('hidden');
  }
});

function handleForget() {
  const role = document.querySelector('[name=role]').value;
  if (role === 'pegawai') {
    resetPassword();
  } else {
    window.location.href = '/forgot-password';
  }
}

function resetPassword() {
  document.getElementById('modalReset').classList.remove('hidden');
  document.getElementById('resetEmail').value = '';
  document.getElementById('resetResult').classList.add('hidden');
}

function tutupResetModal() {
  document.getElementById('modalReset').classList.add('hidden');
}

async function submitReset() {
  const email = document.getElementById('resetEmail').value;
  const resultEl = document.getElementById('resetResult');
  resultEl.classList.add('hidden');
  resultEl.textContent = '';

  if (!email) {
    resultEl.textContent = 'Email harus diisi.';
    resultEl.classList.remove('hidden');
    return;
  }

  try {
    const res = await axios.post('http://localhost:8000/api/auth/pegawai/reset-password', { email });
    resultEl.textContent = 'Password baru: ' + res.data.new_password;
    resultEl.classList.remove('hidden');
  } catch (err) {
    console.error(err);
    resultEl.textContent = err.response?.data?.message || 'Reset gagal. Email tidak ditemukan.';
    resultEl.classList.remove('hidden');
  }
}
</script>
@endsection
