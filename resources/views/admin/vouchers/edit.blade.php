@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-4">Sửa Voucher: {{ $voucher['code'] }}</h1>

  <form method="POST" action="{{ route('admin.vouchers.update',$voucher['code']) }}" class="space-y-4 max-w-lg">
    @csrf @method('PUT')

    {{-- tương tự như create, chỉ khác value default lấy từ $voucher --}}
    <div>
      <label class="block mb-1 font-medium">Mã Voucher</label>
      <input type="text" value="{{ $voucher['code'] }}" disabled
             class="w-full px-3 py-2 border rounded bg-gray-100">
    </div>

    <div>
      <label class="block mb-1 font-medium">Mã sản phẩm (SKU)</label>
      <input type="text" name="product_sku" value="{{ old('product_sku',$voucher['product_sku']) }}"
             class="w-full px-3 py-2 border rounded @error('product_sku') border-red-500 @enderror">
      @error('product_sku') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- tiếp các field giống create… --}}

    <div class="flex space-x-2">
      <button type="submit"
              class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Cập nhật
      </button>
      <a href="{{ route('admin.vouchers.index') }}"
         class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
        Hủy
      </a>
    </div>
  </form>
@endsection
