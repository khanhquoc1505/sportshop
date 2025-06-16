@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-6">Thông tin tài khoản</h1>

  @if(session('success'))
    <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.account.update') }}" class="max-w-lg space-y-4 bg-white p-6 rounded shadow">
    @csrf

    {{-- Tên --}}
    <div>
      <label class="block mb-1 text-gray-700">Họ & tên</label>
      <input name="name"
             value="{{ old('name', $admin->name) }}"
             class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror">
      @error('name')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Email --}}
    <div>
      <label class="block mb-1 text-gray-700">Email</label>
      <input name="email"
             type="email"
             value="{{ old('email', $admin->email) }}"
             class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror">
      @error('email')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Mật khẩu mới --}}
    <div>
      <label class="block mb-1 text-gray-700">Mật khẩu mới (không bắt buộc)</label>
      <input name="password"
             type="password"
             class="w-full border rounded px-3 py-2 @error('password') border-red-500 @enderror"
             placeholder="Để trống nếu không đổi">
      @error('password')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Xác nhận mật khẩu --}}
    <div>
      <label class="block mb-1 text-gray-700">Xác nhận mật khẩu</label>
      <input name="password_confirmation"
             type="password"
             class="w-full border rounded px-3 py-2"
             placeholder="Nhập lại mật khẩu mới">
    </div>

    <button type="submit"
            class="mt-4 px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
      Cập nhật thông tin
    </button>
  </form>
@endsection
