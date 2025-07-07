@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Thêm người dùng mới</h1>

  <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white p-6 rounded-xl shadow space-y-4">
    @csrf

    <div>
      <label class="block mb-1">Tên người dùng*</label>
      <input type="text" name="ten_nguoi_dung" value="{{ old('ten_nguoi_dung') }}"
             class="w-full border px-3 py-2 rounded" required>
             @error('ten_nguoi_dung')
  <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
@enderror
    </div>

    <div>
      <label class="block mb-1">Email*</label>
      <input type="email" name="email" value="{{ old('email') }}"
             class="w-full border px-3 py-2 rounded" required>
             @error('email')
  <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
@enderror
    </div>

    <div>
      <label class="block mb-1">Số điện thoại*</label>
      <input type="text" name="sdt" value="{{ old('sdt') }}"
             class="w-full border px-3 py-2 rounded" required>
    </div>

    <div>
      <label class="block mb-1">Địa chỉ*</label>
      <input type="text" name="dia_chi" value="{{ old('dia_chi') }}"
             class="w-full border px-3 py-2 rounded" required>
    </div>

    <div>
      <label class="block mb-1">Mật khẩu*</label>
      <input type="text" name="mat_khau" value="{{ old('mat_khau') }}"
             class="w-full border px-3 py-2 rounded" required>
    </div>

    <div>
      <label class="block mb-1">Vai trò*</label>
      <select name="vai_tro" class="w-full border px-3 py-2 rounded {{ $errors->has('vai_tro') ? 'input-error' : '' }}" id="vai_tro">
        <option value="admin" {{ old('vai_tro') === 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="customer" {{ old('vai_tro') === 'customer' ? 'selected' : '' }}>Customer</option>
      </select>
    </div>

    <div class="flex justify-between">
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
      ← Quay lại
    </a>
    <button type="submit" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
      Thêm
    </button>
    </div>
  </form>
@endsection
