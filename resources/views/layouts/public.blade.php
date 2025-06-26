<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>@yield('title', 'ReuseMart')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  </head>
  <body class="bg-gray-50">
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

    <header class="bg-green-600 text-white shadow">
        <div class="flex justify-between items-center text-sm px-6 py-1 border-b border-green-500 bg-green-700">
            <div class="flex space-x-4">
            <span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 13l4 4L19 7" /></svg> Terpercaya, Aman & Amanah</span>
            <span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 13l4 4L19 7" /></svg> Garansi Uang Kembali 100%</span>
            <span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 13l4 4L19 7" /></svg> Gratis Ongkir</span>
            </div>
            <div class="space-x-4">
            <a href="#" class="hover:underline">Belum Punya Toko?</a>
            <a href="#" class="hover:underline">Buka Toko</a>
            </div>
        </div>

        <div class="flex flex-wrap justify-between items-center px-6 py-4">
            <div class="text-2xl font-bold">Reuse Mart</div>
            <div class="flex-1 mx-6 max-w-xl w-full">
            <div class="flex bg-white rounded-full overflow-hidden shadow-sm">
                <<input type="text" id="searchInput" class="flex-1 px-4 py-2 text-black focus:outline-none" placeholder="Situs Jual Beli Online Termurah">
                <button id="searchButton" class="px-4 bg-green-500 hover:bg-green-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </button>
            </div>
            </div>
            <div class="flex items-center space-x-3">
        <a href="/cart" class="relative" title="Keranjang Belanja">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 rounded-full bg-white p-1 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
              </a>
              <a href="/login">
                  <img src="https://www.svgrepo.com/show/382106/account-avatar-profile-user-11.svg" class="w-8 h-8 rounded-full bg-white p-1" alt="User" />
              </a>
          </div>
        </div>
    </header>

    <main class="p-6">
      @yield('content')
    </main>

    @yield('scripts')
  </body>
</html>
