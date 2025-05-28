<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin Dashboard</title>

  <!-- 1️⃣ Tailwind config custom màu phải đứng trước import CDN -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#5089AB',   /* xanh chính logo */
            dark: '#2E2E2E',   /* xám đậm/đen */
            light: '#E5E5E5',   /* xám nhạt */
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
      {{-- logo --}}
      <div class="h-14">
        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="h-full w-auto object-contain" />
      </div>

      {{-- globe --}}
      <i class="fa-solid fa-globe text-light text-2xl"></i>

      {{-- search --}}
      <div class="relative">
        <input type="text" placeholder="Value" class="w-64 pl-4 pr-10 py-2 rounded-full
                 bg-white border-2 border-light focus:border-light focus:outline-none
                 placeholder-dark text-dark" />
        <button class="absolute inset-y-0 right-3 flex items-center text-dark hover:text-primary">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>

    <div class="flex items-center space-x-4">
      <i class="fa-solid fa-user text-light text-2xl hover:text-light/70 cursor-pointer"></i>
      <i class="fa-solid fa-question-circle text-light text-2xl hover:text-light/70 cursor-pointer"></i>
    </div>
  </header>

  <div class="flex flex-1 overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-dark text-light p-6">
      <ul class="space-y-4">
        <li>
          <a href="{{ route('admin.users.index') }}"
            class="block px-3 py-2 rounded-l-full hover:bg-primary hover:text-blue-500 transition">
            Quản lý người dùng
          </a>
        </li>
        <li>
          <a href="{{ route('admin.report.revenue') }}" class="block px-3 py-2 rounded-l-full hover:bg-primary hover:text-blue-500 transition">
            Thống kê doanh thu
          </a>
        </li>
        <li>
          <a href="{{ route('admin.product.index') }}"
            class="block px-3 py-2 rounded-l-full hover:bg-primary hover:text-blue-500 transition">
            Quản lý sản phẩm
          </a>
        </li>
        <li>
          <a href="{{ route('admin.categories.index') }}"
            class="block px-3 py-2 rounded-l-full hover:bg-primary hover:text-blue-500 transition">
            Quản lý danh mục
          </a>
        </li>
        <li>
          <a href="#"
            class="block px-3 py-2 rounded-l-full hover:bg-primary hover:text-blue-500 transition">
            Quản lý kho
          </a>
        </li>
        <li>
          <a href="#"
            class="block px-3 py-2 rounded-l-full hover:bg-primary hover:text-blue-500 transition">
            Quản lý thành viên
          </a>
        </li>
        <li>
          <a href="#"
            class="block px-3 py-2 rounded-l-full hover:bg-primary hover:text-blue-500 transition">
            Quản lý đơn hàng
          </a>
        </li>
        <li>
          <a href="#"
            class="block px-3 py-2 rounded-l-full hover:bg-primary hover:text-blue-500 transition">
            Đánh giá & phản hồi
          </a>
        </li>
      </ul>
    </aside>

    {{-- MAIN --}}
    <main class="flex-1 p-6 overflow-auto bg-light">

      @yield('content')
      @yield('content1')

    </main>
  </div>
  @stack('scripts')
</body>

</html>