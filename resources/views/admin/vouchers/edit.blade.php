@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6">Sửa Voucher</h1>

  <div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST" class="space-y-6">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-2 gap-6">
        {{-- Mã Voucher (readonly) --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Mã Voucher</label>
          <input
            type="text"
            value="{{ $voucher->ma_voucher }}"
            disabled
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded bg-gray-100"
          />
        </div>

        {{-- Loại --}}
        <div>
          <label for="loai" class="block text-sm font-medium text-gray-700">Loại</label>
          <select
            name="loai"
            id="loai"
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring"
          >
            <option value="fixed"  {{ old('loai', $voucher->loai)==='fixed'   ? 'selected' : '' }}>
              Giá tiền cố định
            </option>
            <option value="percent"{{ old('loai', $voucher->loai)==='percent' ? 'selected' : '' }}>
              Phần trăm
            </option>
          </select>
          @error('loai')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Giá trị --}}
        <div>
          <label for="soluong_value" class="block text-sm font-medium text-gray-700">Giá trị</label>
          <input
            type="number"
            name="soluong_value"
            id="soluong_value"
            step="0.01"
            value="{{ old('soluong_value', $voucher->soluong) }}"
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring"
            placeholder="Ví dụ: 50000 cho Fixed, 10 cho %"
          />
          @error('soluong_value')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Số lượng --}}
        <div>
          <label for="soluong" class="block text-sm font-medium text-gray-700">Số lượng</label>
          <input
            type="number"
            name="soluong"
            id="soluong"
            value="{{ old('soluong', $voucher->quantity ?? $voucher->soluong) }}"
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring"
            placeholder="Tổng số mã được phát hành"
          />
          @error('soluong')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Ngày bắt đầu --}}
        <div>
          <label for="ngay_bat_dau" class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
          <input
            type="datetime-local"
            name="ngay_bat_dau"
            id="ngay_bat_dau"
            value="{{ old('ngay_bat_dau', $voucher->ngay_bat_dau->format('Y-m-d\TH:i')) }}"
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring"
            step="60"
          />
          @error('ngay_bat_dau')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Ngày kết thúc --}}
        <div>
          <label for="ngay_ket_thuc" class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
          <input
            type="datetime-local"
            name="ngay_ket_thuc"
            id="ngay_ket_thuc"
            value="{{ old('ngay_ket_thuc', $voucher->ngay_ket_thuc->format('Y-m-d\TH:i')) }}"
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring"
            step="60"
          />
          @error('ngay_ket_thuc')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
      </div>

      {{-- Chọn sản phẩm --}}
      <div>
        <label for="sanphams" class="block text-sm font-medium text-gray-700">Sản phẩm (SKU)</label>
        <select
          name="sanphams[]"
          id="sanphams"
          multiple
          size="5"
          class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring"
        >
          @foreach($sanphams as $sp)
            <option
              value="{{ $sp->id }}"
              {{ in_array($sp->id, old('sanphams', $voucher->sanphams->pluck('id')->all())) ? 'selected' : '' }}
            >
              {{ $sp->masanpham }} – {{ $sp->ten ?? $sp->tensp ?? '' }}
            </option>
          @endforeach
        </select>
        @error('sanphams')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      {{-- Nút lưu/hủy --}}
      <div class="flex space-x-4">
        <button
          type="submit"
          class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
          Cập nhật Voucher
        </button>
        <a
          href="{{ route('admin.vouchers.index') }}"
          class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
        >
          Hủy
        </a>
      </div>
    </form>
  </div>
@endsection
