@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
  <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-green-700 mb-6">Reset Password</h2>
    
    <div id="success-message" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 hidden">
      Link reset password telah dikirim ke email Anda. Silakan cek kotak masuk atau folder spam.
    </div>

    <form id="forgotPasswordForm" class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Reset Password Untuk</label>
        <select name="role" class="w-full px-3 py-2 border rounded" required>
          <option value="pembeli">Pembeli</option>
          <option value="penitip">Penitip</option>
          <option value="organisasi">Organisasi</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" class="w-full px-3 py-2 border rounded" required>
      </div>
      
      <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">
        Kirim Link Reset Password
      </button>
    </form>

    <div class="mt-4 text-center">
      <a href="{{ url('/login') }}" class="text-green-600 hover:underline">Kembali ke halaman login</a>
    </div>
    
    <p id="error" class="text-red-600 text-sm mt-4 text-center hidden"></p>
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.getElementById('forgotPasswordForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const role = document.querySelector('[name=role]').value;
  const email = document.querySelector('[name=email]').value;
  const errorEl = document.getElementById('error');
  const successEl = document.getElementById('success-message');
  const submitBtn = e.target.querySelector('button[type="submit"]');
  
  // Reset UI state
  errorEl.classList.add('hidden');
  successEl.classList.add('hidden');
  
  // Disable button and show loading state
  submitBtn.disabled = true;
  submitBtn.innerHTML = 'Mengirim...';
  
  try {
   const response = await axios.post(`http://localhost:8000/api/auth/${role}/forgot-password`, { 
    email,
    type: role
  });

    
    if (response.data.success) {
      successEl.classList.remove('hidden');
      document.querySelector('[name=email]').value = '';
    } else {
      errorEl.textContent = response.data.message || 'Terjadi kesalahan. Silakan coba lagi nanti.';
      errorEl.classList.remove('hidden');
    }
  } catch (err) {
    console.error('Password reset error:', err);
    
    if (err.response && err.response.data && err.response.data.message) {
      errorEl.textContent = err.response.data.message;
    } else {
      errorEl.textContent = 'Terjadi kesalahan saat mengirim permintaan reset password. Silakan coba lagi nanti.';
    }
    
    errorEl.classList.remove('hidden');
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Kirim Link Reset Password';
  }
});
</script>
@endsection