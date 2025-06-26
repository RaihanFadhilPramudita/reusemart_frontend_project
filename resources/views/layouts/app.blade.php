<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  </head>
  <body class="bg-gray-100">

    <div class="flex h-screen overflow-hidden">
      <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
          <h1 class="text-2xl font-bold text-green-700">@yield('title', 'Reuse Mart')</h1>
          <div class="relative">
            <button class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center">
              <span class="text-lg font-bold">A</span>
            </button>
          </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
          @yield('content')
        </main>
      </div>
    </div>

    <script>
      const token = localStorage.getItem('token');
      const user = JSON.parse(localStorage.getItem('user'));
      const userRole = user?.jabatan?.NAMA_JABATAN;

      if (token && userRole) {
        switch (userRole) {
          case "Admin":
            axios.defaults.baseURL = 'http://localhost:8000/api/admin';
            break;
          case "Customer Service":
            axios.defaults.baseURL = 'http://localhost:8000/api/cs';
            break;
          case "Gudang":
            axios.defaults.baseURL = 'http://localhost:8000/api/gudang';
            break;
          case "Kurir":
            axios.defaults.baseURL = 'http://localhost:8000/api/kurir';
            break;
          case "Owner":
            axios.defaults.baseURL = 'http://localhost:8000/api/owner';
            break;
          default:
            axios.defaults.baseURL = 'http://localhost:8000/api/auth/pegawai';
        }
      } else {
        axios.defaults.baseURL = 'http://localhost:8000/api/auth/pegawai';
      }

      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      }
      axios.defaults.headers.common['Accept'] = 'application/json';

      const currentPath = window.location.pathname;
      const isAdminPage = currentPath.startsWith('/admin');
      const isCSPage = currentPath.startsWith('/cs');
      const isGudangPage = currentPath.startsWith('/gudang');
      const isKurirPage = currentPath.startsWith('/kurir');
      const isOwnerPage = currentPath.startsWith('/owner');
      const isHunterPage = currentPath.startsWith('/hunter');
      const isLoginPage = currentPath === '/login';

      if (!token && (isAdminPage || isCSPage || isGudangPage || isKurirPage || isOwnerPage || isHunterPage)) {
        window.location.href = '/login';
      }

      if (token && userRole) {
        if (
          (isAdminPage && userRole !== "Admin") ||
          (isCSPage && userRole !== "Customer Service") ||
          (isGudangPage && userRole !== "Gudang") ||
          (isKurirPage && userRole !== "Kurir") ||
          (isOwnerPage && userRole !== "Owner") ||
          (isHunterPage && userRole !== "Hunter")
        ) {
          alert(`Anda tidak memiliki akses sebagai ${userRole === 'Admin' ? 'admin' : userRole.toLowerCase()}.`);
          window.location.href = '/login';
        }
}

      function logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
      }
    </script>
  @yield('scripts')
  </body>
</html>
