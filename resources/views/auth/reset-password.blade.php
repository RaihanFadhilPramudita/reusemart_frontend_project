@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
  <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-green-700 mb-6">Reset Password</h2>
    
    <div id="success-message" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 hidden">
      Password berhasil diperbarui. Anda akan dialihkan ke halaman login.
    </div>

    <form id="resetPasswordForm" class="space-y-4">
      <input type="hidden" id="token" value="{{ $token ?? '' }}">
      <input type="hidden" id="email" value="{{ $email ?? '' }}">
      <input type="hidden" id="type" value="{{ $type ?? 'pembeli' }}">
      
      <div>
        <label class="block text-sm font-medium">Password Baru</label>
        <input type="password" id="password" class="w-full px-3 py-2 border rounded" required minlength="6">
      </div>
      
      <div>
        <label class="block text-sm font-medium">Konfirmasi Password</label>
        <input type="password" id="password_confirmation" class="w-full px-3 py-2 border rounded" required minlength="6">
      </div>
      
      <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">
        Reset Password
      </button>
    </form>
    
    <p id="error" class="text-red-600 text-sm mt-4 text-center hidden"></p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('resetPasswordForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const token = document.getElementById('token').value;
      const email = document.getElementById('email').value;
      const type = document.getElementById('type').value;
      const password = document.getElementById('password').value;
      const password_confirmation = document.getElementById('password_confirmation').value;
      
      const errorEl = document.getElementById('error');
      const successEl = document.getElementById('success-message');
      
      errorEl.classList.add('hidden');
      successEl.classList.add('hidden');
      
      if (password !== password_confirmation) {
        errorEl.textContent = 'Konfirmasi password tidak cocok';
        errorEl.classList.remove('hidden');
        return;
      }
      
      try {
        const response = await axios.post(`http://localhost:8000/api/auth/${type}/reset-password`, {
          token,
          email,
          password,
          password_confirmation
        });
        
        successEl.classList.remove('hidden');
        
        // Redirect to login page after 3 seconds
        setTimeout(() => {
          window.location.href = '/login';
        }, 3000);
        
      } catch (err) {
        console.error(err);
        
        if (err.response && err.response.data && err.response.data.message) {
          errorEl.textContent = err.response.data.message;
        } else {
          errorEl.textContent = 'Terjadi kesalahan saat reset password. Silakan coba lagi nanti.';
        }
        
        errorEl.classList.remove('hidden');
      }
    });
  });
</script>
@endsection