@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-4">Tạo Voucher mới</h1>

  <form method="POST" action="{{ route('admin.vouchers.store') }}" class="space-y-4 max-w-lg">
    @csrf

    <div>
      <label class="block mb-1 font-medium">Mã Voucher</label>
      <input type="text" name="code" value="{{ old('code') }}"
             class="w-full px-3 py-2 border rounded @error('code') border-red-500 @enderror">
      @error('code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block mb-1 font-medium">Mã sản phẩm (SKU)</label>
      <input type="text" name="product_sku" value="{{ old('product_sku') }}"
             class="w-full px-3 py-2 border rounded @error('product_sku') border-red-500 @enderror">
      @error('product_sku') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex space-x-4">
      <div class="flex-1">
        <label class="block mb-1 font-medium">Loại</label>
        <select name="type"
                class="w-full px-3 py-2 border rounded @error('type') border-red-500 @enderror">
          <option value="fixed"  {{ old('type')==='fixed'?'selected':'' }}>Tiền cố định</option>
          <option value="percent"{{ old('type')==='percent'?'selected':'' }}>Phần trăm</option>
        </select>
        @error('type') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>
      <div class="flex-1">
        <label class="block mb-1 font-medium">Giá trị</label>
        <input type="number" name="discount" value="{{ old('discount') }}"
               class="w-full px-3 py-2 border rounded @error('discount') border-red-500 @enderror">
        @error('discount') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    <div class="flex space-x-4">
      <div class="flex-1">
        <label class="block mb-1 font-medium">Hạn dùng</label>
        <input type="date" name="expiration" value="{{ old('expiration') }}"
               class="w-full px-3 py-2 border rounded @error('expiration') border-red-500 @enderror">
        @error('expiration') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>
      <div class="flex-1">
        <label class="block mb-1 font-medium">Giới hạn số lần dùng</label>
        <input type="number" name="usage_limit" value="{{ old('usage_limit',1) }}"
               class="w-full px-3 py-2 border rounded @error('usage_limit') border-red-500 @enderror">
        @error('usage_limit') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    <div class="flex items-center space-x-2">
      <label class="inline-flex items-center">
        <input type="checkbox" name="is_active" {{ old('is_active')?'checked':'' }}
               class="form-checkbox h-5 w-5 text-primary">
        <span class="ml-2">Kích hoạt ngay</span>
      </label>
    </div>

    <div class="flex space-x-2">
      <button type="submit"
              class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Lưu Voucher
      </button>
      <a href="{{ route('admin.vouchers.index') }}"
         class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
        Hủy
      </a>
    </div>
  </form>
@endsection
