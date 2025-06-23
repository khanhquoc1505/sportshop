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
      <th class="px-4 py-2">Kích cỡ</th>
      <th class="px-4 py-2">Màu sắc</th>
      <th class="px-4 py-2">Trạng thái</th>
      <th class="px-4 py-2">Thời gian</th>
      <th class="px-4 py-2">Số lượng</th>
      <th class="px-4 py-2">Hành động</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $item)
      <tr class="text-center border-b">
      <td class="px-4 py-2">{{ $item->id }}</td>
      <td class="px-4 py-2">{{ $item->ten }}</td>
      <td class="px-4 py-2">
      @php
      $firstImage = $item->images->first()->image_path ?? null;
    @endphp
      @if ($firstImage)
      <img src="{{ asset($firstImage) }}" alt="{{ $item->ten }}" class="h-12 mx-auto object-contain" />
      @else
      <span class="text-gray-400 italic">Không có ảnh</span>
      @endif
      </td>
      <td class="px-4 py-2">
     @foreach($item->kichCos() as $kc)
    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full mr-1">
        {{ $kc->size }}
    </span>
@endforeach
      </td>

      <td class="px-4 py-2">
     @foreach($item->mauSacs() as $ms)
    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full mr-1">
        {{ $ms->mausac }}
    </span>
@endforeach
      </td>

     <td class="px-4 py-2">
    @if($item->trang_thai == '1')
        <span class="px-2 py-1 bg-green-100 text-green-800 text-sm rounded-full">Hiển thị</span>
    @else
        <span class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded-full">Ẩn</span>
    @endif
</td>
      <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->ngay_nhap)->format('d/m/Y') }}</td>
      <td class="px-4 py-2">{{ $item->tong_so_luong }}</td>
      <td class="px-4 py-2">
  <div class="flex justify-center gap-x-3">
    <a href="{{ route('admin.product.edit', $item->id) }}"
       class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">
       Sửa
    </a>
    <form action="{{ route('admin.product.destroy', $item->id) }}" method="POST">
      @csrf @method('DELETE')
      <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
        onclick="return confirm('Bạn có chắc muốn xóa?')">
        Xóa
      </button>
    </form>
  </div>
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