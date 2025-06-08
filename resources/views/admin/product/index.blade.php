@extends('layouts.admin')

@section('content1')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Quản lý sản phẩm</h1>

  <div class="overflow-auto bg-light p-4 h-[500px] rounded-xl shadow border border-gray-300">
    <table class="min-w-full table-auto bg-white">
    <thead>
      <tr class="bg-gray-200 text-gray-700">
      <th class="px-4 py-2">ID</th>
      <th class="px-4 py-2">Tên</th>
      <th class="px-4 py-2">Hình ảnh</th>
      <th class="px-4 py-2">Thời gian</th>
      <th class="px-4 py-2">Số lượng</th>
      <th class="px-4 py-2">Hành động</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $item)
      <tr class="text-center border-b">
      <td class="px-4 py-2">{{ $item->id }}</td>
      <td class="px-4 py-2">{{ $item->name }}</td>
      <td class="px-4 py-2">
      <img src="{{ asset($item->image = 'images/a.png') }}" alt="{{ $item->name }}"
      class="h-12 mx-auto object-contain" />
      </td>
      <td class="px-4 py-2">{{ $item->time }}</td>
      <td class="px-4 py-2">{{ $item->qty }}</td>
      <td class="px-4 py-2 space-x-2">
      <a href="{{ route('admin.product.edit', $item->id) }}"
      class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">
      Sửa
      </a>
      <form action="#" method="POST" class="inline">
      @csrf @method('DELETE')
      <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
        onclick="return confirm('Bạn có chắc muốn xóa?')">
        Xóa
      </button>
      </form>
      </td>
      </tr>
    @endforeach
    </tbody>
    </table>

    <!-- Nút Thêm ở dưới góc phải -->
    <div class="mt-4 text-right">
    <a href="{{ route('admin.product.create') }}"
      class="absolute bottom-4 right-4 px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
      Thêm
    </a>
    </div>
  </div>
@endsection