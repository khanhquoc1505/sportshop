@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6">Thêm Voucher</h1>

  <div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.vouchers.store') }}" method="POST" class="space-y-6">
    @csrf
    {{-- Loại, Giá trị, Số lượng, Ngày bắt đầu và Ngày kết thúc vẫn giữ như trước --}}
    <div class="grid grid-cols-2 gap-6">
      {{-- Loại --}}
      <div>
      <label for="loai" class="block text-sm font-medium text-gray-700">Loại</label>
      <select name="loai" id="loai" class="mt-1 block w-full px-4 py-2 border rounded focus:outline-none focus:ring">
        <option value="">-- Chọn loại --</option>
        <option value="fixed" {{ old('loai') === 'fixed' ? 'selected' : '' }}>Giá tiền cố định</option>
        <option value="percent" {{ old('loai') === 'percent' ? 'selected' : '' }}>Phần trăm</option>
      </select>
      @error('loai')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      {{-- Giá trị --}}
      <div>
      <label for="giatri" class="block text-sm font-medium text-gray-700">Giá trị</label>
      <input type="number" name="soluong_value" id="giatri" step="0.01" value="{{ old('soluong_value') }}"
        class="mt-1 block w-full px-4 py-2 border rounded focus:outline-none focus:ring"
        placeholder="Ví dụ: 50000 cho Fixed, 10 cho %" />
      @error('soluong_value')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      {{-- Số lượng --}}
      <div>
      <label for="soluong" class="block text-sm font-medium text-gray-700">Số lượng</label>
      <input type="number" name="soluong" id="soluong" value="{{ old('soluong') }}"
        class="mt-1 block w-full px-4 py-2 border rounded focus:outline-none focus:ring"
        placeholder="Tổng số mã được phát hành" />
      @error('soluong')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      {{-- Ngày bắt đầu --}}
      <div>
      <label for="ngay_bat_dau" class="block text-sm font-medium text-gray-700">
        Ngày bắt đầu
      </label>
      <input type="datetime-local" name="ngay_bat_dau" id="ngay_bat_dau" step="60" {{-- cập nhật phút; dùng step="1"
        nếu muốn có giây --}}
        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring" />
      @error('ngay_bat_dau')
      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
      </div>

      {{-- Ngày kết thúc --}}
      <div>
      <label for="ngay_ket_thuc" class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
      <input type="date" name="ngay_ket_thuc" id="ngay_ket_thuc" value="{{ old('ngay_ket_thuc') }}"
        class="mt-1 block w-full px-4 py-2 border rounded focus:outline-none focus:ring" />
      @error('ngay_ket_thuc')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- Chọn sản phẩm --}}
    <div>
      <label for="sanphams" class="block text-sm font-medium text-gray-700">Sản phẩm (SKU)</label>
      <select name="sanphams[]" id="sanphams" multiple size="5"
      class="mt-1 block w-full px-4 py-2 border rounded focus:outline-none focus:ring">
      @foreach($sanphams as $sp)
      <option value="{{ $sp->id }}" {{ in_array($sp->id, old('sanphams', [])) ? 'selected' : '' }}>
      {{ $sp->masanpham }} – {{ $sp->tensp }}
      </option>
    @endforeach
      </select>
      @error('sanphams')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Nút lưu/hủy --}}
    <div class="flex justify-between">
    <a href="{{ route('admin.vouchers.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
      ← Quay lại
    </a>
    <button type="submit" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
      Lưu
    </button>
    </div>
    </form>
  </div>
@endsection
@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
    const dtInput = document.getElementById('ngay_bat_dau');

    function pad(n) {
      return n < 10 ? '0' + n : n;
    }

    function syncClock() {
      const now = new Date();
      const local = now.getFullYear() + '-' +
      pad(now.getMonth() + 1) + '-' +
      pad(now.getDate()) + 'T' +
      pad(now.getHours()) + ':' +
      pad(now.getMinutes());
      dtInput.value = local;
    }

    // Khởi tạo ngay khi load
    syncClock();
    // Cập nhật mỗi 1 phút
    setInterval(syncClock, 60_000);
    });
  </script>
@endpush