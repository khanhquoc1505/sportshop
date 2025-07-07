@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Chỉnh sửa người dùng</h1>

  <form action="{{ route('admin.users.update', $user['id']) }}" method="POST"
    class="bg-white p-6 rounded-xl shadow space-y-4">
    @csrf @method('PATCH')

    <div>
    <label class="block mb-1">Tên người dùng</label>
    <input type="text" name="ten_nguoi_dung" value="{{ old('ten_nguoi_dung', $user['ten_nguoi_dung']) }}"
      class="w-full border px-3 py-2 rounded" required>
    </div>

    <div>
    <label class="block mb-1">Email</label>
    <input type="email" name="email" value="{{ old('email', $user['email']) }}" class="w-full border px-3 py-2 rounded"
      required>
    </div>

    <div>
    <label class="block mb-1">Mật khẩu (mới)</label>
    <input type="password" name="mat_khau" class="w-full border px-3 py-2 rounded" placeholder="Để trống nếu không đổi">
    </div>

    <div>
    <label class="block mb-1">Số điện thoại</label>
    <input type="text" name="sdt" value="{{ old('sdt', $user['sdt']) }}" class="w-full border px-3 py-2 rounded">
    </div>

    <div>
    <label class="block mb-1">Địa chỉ</label>
    <input type="text" name="dia_chi" value="{{ old('dia_chi', $user['dia_chi']) }}"
      class="w-full border px-3 py-2 rounded">
    </div>

    <div>
    <label class="block mb-1">Vai trò</label>
    <select name="vai_tro" class="w-full border px-3 py-2 rounded">
      <option value="admin" {{ $user['vai_tro'] == 'admin' ? 'selected' : '' }}>Admin</option>
      <option value="customer" {{ $user['vai_tro'] == 'customer' ? 'selected' : '' }}>Customer</option>
    </select>
    </div>
    <div class="flex justify-between">
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
      ← Quay lại
    </a>
    <button type="submit" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
      Lưu
    </button>
    </div>
  </form>
@endsection