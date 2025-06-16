@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-4">Quản lý Voucher</h1>

  @if(session('success'))
    <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  <div class="flex items-center mb-4 space-x-2">
    <a href="{{ route('admin.vouchers.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
      Thêm Voucher
    </a>
    <form method="GET" action="{{ route('admin.vouchers.index') }}" class="flex space-x-2">
      <input type="text" name="search" placeholder="Tìm mã hoặc SKU..."
             value="{{ $search }}"
             class="px-3 py-2 border rounded focus:outline-none focus:ring"
      />
      <select name="status" class="px-3 py-2 border rounded focus:outline-none focus:ring">
        <option value="">--Trạng thái--</option>
        <option value="1" {{ $status==='1'?'selected':'' }}>Active</option>
        <option value="0" {{ $status==='0'?'selected':'' }}>Inactive</option>
      </select>
      <button class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Lọc</button>
    </form>
  </div>

  <div class="overflow-auto bg-white rounded-lg shadow">
    <table class="min-w-full">
      <thead class="bg-gray-100 text-gray-700">
        <tr>
          <th class="px-4 py-2">Mã Voucher</th>
          <th class="px-4 py-2">Sản phẩm (SKU)</th>
          <th class="px-4 py-2">Loại</th>
          <th class="px-4 py-2">Giá trị</th>
          <th class="px-4 py-2">Hạn dùng</th>
          <th class="px-4 py-2">Giới hạn</th>
          <th class="px-4 py-2">Đã dùng</th>
          <th class="px-4 py-2">Trạng thái</th>
          <th class="px-4 py-2">Hành động</th>
        </tr>
      </thead>
      <tbody>
        @forelse($vouchers as $v)
          <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-3">{{ $v['code'] }}</td>
            <td class="px-4 py-3">{{ $v['product_sku'] }}</td>
            <td class="px-4 py-3 capitalize">{{ $v['type'] }}</td>
            <td class="px-4 py-3">
              @if($v['type']==='fixed')
                {{ number_format($v['discount'],0,',','.') }} ₫
              @else
                {{ $v['discount'] }} %
              @endif
            </td>
            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($v['expiration'])->format('d/m/Y') }}</td>
            <td class="px-4 py-3">{{ $v['usage_limit'] }}</td>
            <td class="px-4 py-3">{{ $v['used'] }}</td>
            <td class="px-4 py-3">
              @if($v['is_active'])
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Active</span>
              @else
                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Inactive</span>
              @endif
            </td>
            <td class="px-4 py-3 space-x-1">
              <a href="{{ route('admin.vouchers.edit',$v['code']) }}"
                 class="px-2 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm">
                Sửa
              </a>
              <form action="{{ route('admin.vouchers.destroy',$v['code']) }}"
                    method="POST" class="inline-block"
                    onsubmit="return confirm('Chắc chắn xóa?')">
                @csrf @method('DELETE')
                <button class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                  Xóa
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="py-6 text-center text-gray-500">Chưa có voucher nào</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    @if(method_exists($vouchers,'links'))
      <div class="p-4">{{ $vouchers->links() }}</div>
    @endif
  </div>
@endsection
