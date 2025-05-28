@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Chỉnh sửa user</h1>

  <form action="{{ route('admin.users.update', $user['id']) }}" method="POST"
    class="bg-white p-6 rounded-xl shadow space-y-4">
    @csrf @method('PATCH')

    <div>
    <label class="block mb-1">Tên</label>
    <input type="text" name="name" value="{{ old('name', $user['name']) }}" class="w-full border px-3 py-2 rounded"
      required>
    </div>

    <div>
    <label class="block mb-1">Email</label>
    <input type="email" name="email" value="{{ old('email', $user['email']) }}" class="w-full border px-3 py-2 rounded"
      required>
    </div>

    <div>
    <label class="block mb-1">SĐT</label>
    <input type="text" name="phone" value="{{ old('phone', $user['phone']) }}" class="w-full border px-3 py-2 rounded">
    </div>

    <div>
    <label class="block mb-1">Role</label>
    <select name="role" class="w-full border px-3 py-2 rounded">
      <option value="admin" {{ $user['role'] == 'admin' ? 'selected' : '' }}>Admin</option>
      <option value="customer" {{ $user['role'] == 'customer' ? 'selected' : '' }}>Customer</option>
    </select>
    </div>

    <div>
    <label class="block mb-1">Trạng thái</label>
    <select name="is_active" class="w-full border px-3 py-2 rounded">
      <option value="1" {{ $user['is_active'] ? 'selected' : '' }}>Active</option>
      <option value="0" {{ !$user['is_active'] ? 'selected' : '' }}>Inactive</option>
    </select>
    </div>

    <div class="flex space-x-2 pt-4">
    <button type="submit" onclick="return confirm('Xác nhận cập nhật user?')"
      class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
      Lưu
    </button>
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
      Hủy
    </a>
    </div>
  </form>
@endsection