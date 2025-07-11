{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title', 'Admin Dashboard')</title>
  <!-- Tailwind + FontAwesome -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#5089AB',
            dark: '#2E2E2E',
            light: '#E5E5E5',   /* xám nhạt */
            sidebar: '#1F2937'
          }
        }
      }
    }
  </script>
  <!-- 2️⃣ import Tailwind + FontAwesome -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="flex flex-col h-screen bg-light">

  {{-- HEADER --}}
  <header class="flex items-center justify-between bg-primary px-6 py-3 shadow-lg">
    <div class="flex items-center space-x-6">
      <a href="/" class="h-10">
        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="h-10">
      </a>
      <!-- <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="h-10" /> -->
      <i class="fa-solid fa-globe text-2xl"></i>
    </div>
    <div class="flex items-center space-x-4">
      <i class="fa-solid fa-question-circle cursor-pointer"></i>
    </div>
  </header>

  <div class="flex flex-1 overflow-hidden">
    {{-- SIDEBAR --}}
    <aside class="w-64 bg-dark text-light flex-shrink-0 overflow-y-auto">
      <nav class="mt-6">
        <ul class="space-y-1">
          {{-- Users --}}
          <li>
            <a href="{{ route('admin.users.index') }}" @class(['flex items-center px-4 py-2 hover:bg-gray-700', 'bg-gray-700 border-l-4 border-primary font-medium' => request()->routeIs('admin.users.*')])>
              <i class="fa-solid fa-users mr-3 w-5"></i>
              Quản lý người dùng
            </a>
          </li>
          {{-- Revenue --}}
          <li>
            <a href="{{ route('admin.report.revenue') }}" @class(['flex items-center px-4 py-2 hover:bg-gray-700', 'bg-gray-700 border-l-4 border-primary font-medium' => request()->routeIs('admin.report.*')])>
              <i class="fa-solid fa-chart-line mr-3 w-5"></i>
              Thống kê doanh thu
            </a>
          </li>

          {{-- Sản phẩm with submenu --}}
          <li
            x-data="{ open: {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.inventory.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
              class="w-full flex items-center px-4 py-2 hover:bg-gray-700 focus:outline-none">
              <i class="fa-solid fa-box mr-3 w-5"></i>
              <span class="flex-1 text-left">Sản phẩm</span>
              <i :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fa-solid"></i>
            </button>
            <ul x-show="open" class="bg-sidebar/50">
              <li>
                <a href="{{ route('admin.product.index') }}" @class([
            'block px-10 py-2 hover:bg-gray-700',
            'bg-gray-700 border-l-4 border-primary font-medium' => request()->routeIs('admin.products.*')
        ])>
                  <i class="fa-solid fa-list mr-2"></i>
                  Danh sách sản phẩm
                </a>
              </li>
              <li>
                <a href="{{ route('admin.inventory.index') }}" @class([
            'block px-10 py-2 hover:bg-gray-700',
            'bg-gray-700 border-l-4 border-primary font-medium' => request()->routeIs('admin.inventory.*')
        ])>
                  <i class="fa-solid fa-warehouse mr-2"></i>
                  Quản lý kho
                </a>
              </li>
            </ul>
          </li>

          {{-- Categories --}}
          <li>
            <a href="{{ route('admin.categories.index') }}" @class([
          'flex items-center px-4 py-2 hover:bg-gray-700',
          'bg-gray-700 border-l-4 border-primary font-medium' => request()->routeIs('admin.categories.*')
      ])>
              <i class="fa-solid fa-tags mr-3 w-5"></i>
              Quản lý danh mục
            </a>
          </li>

          {{-- Members --}}
          <li>
            <a href="{{ route('admin.members.index') }}" @class([
          'flex items-center px-4 py-2 hover:bg-gray-700',
          'bg-gray-700 border-l-4 border-primary font-medium' => request()->routeIs('admin.members.*')
      ])>
              <i class="fa-solid fa-id-card mr-3 w-5"></i>
              Quản lý thành viên
            </a>
          </li>

          {{-- Orders --}}
          <li>
            <a href="{{ route('admin.orders.index') }}" @class([
          'flex items-center px-4 py-2 hover:bg-gray-700',
          'bg-gray-700 border-l-4 border-primary font-medium' => request()->routeIs('admin.orders.*')
      ])>
              <i class="fa-solid fa-receipt mr-3 w-5"></i>
              Quản lý đơn hàng
            </a>
          </li>

          {{-- Feedback --}}
          <li>
            <a href="{{ route('admin.feedback.index') }}" @class(['flex items-center px-4 py-2 hover:bg-gray-700', 'bg-gray-700 border-l-4 border-primary font-medium' => request()->routeIs('admin.feedback.*')])>
              <i class="fa-solid fa-comments mr-3 w-5"></i>
              Đánh giá & phản hồi
            </a>
          </li>
          <li>
            <a href="{{ route('admin.vouchers.index') }}" @class([
          'flex items-center px-4 py-2 hover:bg-gray-700',
          'bg-gray-700 border-l-4 border-primary font-medium' => request()->routeIs('admin.vouchers.*')
      ])>
    <i class="fa fa-ticket-alt mr-3 w-5"></i>
              Voucher
            </a>
          </li>
        </ul>
      </nav>
    </aside>

    {{-- MAIN --}}
    <main class="flex-1 overflow-auto p-6 bg-gray-100">
      @yield('content')
      @yield('content1')
    </main>
  </div>

  {{-- AlpineJS (nếu dùng x-data) --}}
  <script src="//unpkg.com/alpinejs" defer></script>

  @stack('scripts')
</body>

</html>