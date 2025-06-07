@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-6 text-dark">Chỉnh sửa Đơn hàng</h1>

    <form action="{{ route('admin.orders.update', $order['id']) }}" method="POST"
        class="bg-white rounded-xl shadow p-6 space-y-6">
        @csrf
        @method('PATCH')

        {{-- Hiển thị Mã đơn (disabled) --}}
        <div>
            <label class="block mb-1 font-medium">Mã Đơn</label>
            <input type="text" value="{{ $order['id'] }}"
                class="w-32 border px-4 py-2 rounded bg-gray-100 cursor-not-allowed" disabled>
        </div>

        {{-- Hiển thị Tên khách hàng (disabled) --}}
        <div>
            <label class="block mb-1 font-medium">Khách hàng</label>
            <input type="text" value="{{ $order['customer'] }}"
                class="w-1/2 border px-4 py-2 rounded bg-gray-100 cursor-not-allowed" disabled>
        </div>

        {{-- Trạng thái Thanh toán --}}
        <div>
            <label class="block mb-1 font-medium">Trạng thái Thanh toán</label>
            <select name="payment_status"
                class="w-1/3 border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary" required>
                <option value="paid" {{ $order['payment_status'] === 'paid' ? 'selected' : '' }}>
                    Đã thanh toán
                </option>
                <option value="unpaid" {{ $order['payment_status'] === 'unpaid' ? 'selected' : '' }}>
                    Chưa thanh toán
                </option>
            </select>
            @error('payment_status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Trạng thái Đơn hàng --}}
        <div>
            <label class="block mb-1 font-medium">Trạng thái Đơn hàng</label>
            <select name="order_status"
                class="w-1/3 border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary" required>
                <option value="pending" {{ $order['order_status'] === 'pending' ? 'selected' : '' }}>
                    Chờ xác nhận
                </option>
                <option value="processing" {{ $order['order_status'] === 'processing' ? 'selected' : '' }}>
                    Đang xử lý
                </option>
                <option value="completed" {{ $order['order_status'] === 'completed' ? 'selected' : '' }}>
                    Hoàn thành
                </option>
                <option value="canceled" {{ $order['order_status'] === 'canceled' ? 'selected' : '' }}>
                    Đã hủy
                </option>
            </select>
            @error('order_status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ngày tạo đơn (disabled) --}}
        <div>
            <label class="block mb-1 font-medium">Ngày tạo</label>
            <input type="date" value="{{ \Carbon\Carbon::parse($order['created_at'])->format('Y-m-d') }}"
                class="w-1/3 border px-4 py-2 rounded bg-gray-100 cursor-not-allowed" disabled>
        </div>

        {{-- Ghi chú (disabled) --}}
        <div>
            <label class="block mb-1 font-medium">Ghi chú</label>
            <textarea class="w-full border px-4 py-2 rounded bg-gray-100 cursor-not-allowed" rows="3"
                disabled>{{ $order['notes'] }}</textarea>
        </div>

        {{-- Nút Lưu & Hủy --}}
        <div class="pt-4 space-x-4">
            <button type="submit" onclick="return confirm('Xác nhận cập nhật trạng thái đơn?')"
                class="px-6 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">
                Lưu
            </button>
            <a href="{{ route('admin.orders.index') }}" class="px-6 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">
                Hủy
            </a>
        </div>
    </form>
@endsection