{{-- resources/views/admin/orders/show.blade.php --}}
@extends('layouts.admin')

@section('content')
  {{-- 1. Breadcrumb --}}
  <nav class="text-sm text-gray-500 mb-2">
    <a href="{{ route('admin.orders.index') }}" class="hover:underline">Đơn hàng</a>
    <span class="px-1">/</span>
    <span class="font-semibold text-gray-700">{{ $order['id'] }}</span>
  </nav>

  {{-- 2. Tiêu đề & nút Quay lại --}}
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-black">
      Chi tiết Đơn hàng: <span class="text-primary">{{ $order['id'] }}</span>
    </h1>
    <a href="{{ route('admin.orders.index') }}"
       class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
      ← Quay lại
    </a>
  </div>

  <div class="space-y-6">

    {{-- 3. Header Thông tin chung --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Mã Đơn --}}
        <div>
          <p class="text-sm text-gray-600">Mã Đơn</p>
          <p class="text-lg font-medium">{{ $order['id'] }}</p>
        </div>

        {{-- Ngày Tạo --}}
        <div>
          <p class="text-sm text-gray-600">Ngày Tạo</p>
          <p class="text-lg font-medium">
            {{ \Carbon\Carbon::parse($order['created_at'])->format('d/m/Y H:i') }}
          </p>
        </div>

        {{-- Kênh bán hàng (có thể hardcode tạm) --}}
        <div>
          <p class="text-sm text-gray-600">Kênh Bán hàng</p>
          <p class="text-lg font-medium">POS</p>
        </div>
      </div>

      <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Trạng thái Giao hàng --}}
        <div>
          <p class="text-sm text-gray-600">Trạng thái Giao hàng</p>
          <div class="mt-1">
            @if($order['delivery_status'] === 'pending')
              <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Chờ giao</span>
            @elseif($order['delivery_status'] === 'shipping')
              <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Đang giao</span>
            @elseif($order['delivery_status'] === 'delivered')
              <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Đã giao</span>
            @else
              <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">Đã trả về</span>
            @endif
          </div>
        </div>

        {{-- Trạng thái Thanh toán --}}
        <div>
          <p class="text-sm text-gray-600">Trạng thái Thanh toán</p>
          <div class="mt-1">
            @if($order['payment_status'] === 'paid')
              <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Đã thanh toán</span>
            @else
              <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">Chưa thanh toán</span>
            @endif
          </div>
        </div>

        {{-- Hình thức Giao hàng --}}
        <div>
          <p class="text-sm text-gray-600">Hình thức Giao hàng</p>
          <p class="text-lg font-medium">{{ $order['shipping_method'] }}</p>
        </div>
      </div>
    </div>

    {{-- 4. Danh sách Sản phẩm trong Đơn --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-4">Sản phẩm trong đơn</h2>
      <div class="overflow-x-auto">
        <table class="w-full table-auto">
          <thead>
            <tr class="bg-gray-100 text-gray-700 text-center">
              <th class="px-4 py-2">Ảnh</th>
              <th class="px-4 py-2">Tên sản phẩm</th>
              <th class="px-4 py-2">SKU</th>
              <th class="px-4 py-2">Số lượng</th>
              <th class="px-4 py-2">Giá (VNĐ)</th>
              <th class="px-4 py-2">Thành tiền (VNĐ)</th>
            </tr>
          </thead>
          <tbody>
            @if(count($order['items']) === 0)
              <tr>
                <td colspan="6" class="py-6 text-center text-gray-500">
                  Chưa có sản phẩm nào
                </td>
              </tr>
            @else
              @foreach($order['items'] as $item)
                <tr class="border-b text-center">
                  {{-- Ảnh sản phẩm --}}
                  <td class="px-4 py-2">
                    <img src="{{ $item['image_url'] ?? 'https://via.placeholder.com/50' }}"
                         alt="Ảnh sản phẩm"
                         class="mx-auto h-12 w-12 object-cover rounded-md">
                  </td>

                  {{-- Tên sản phẩm --}}
                  <td class="px-4 py-2 text-left">
                    <p class="font-medium text-gray-800">{{ $item['name'] }}</p>
                  </td>

                  {{-- SKU --}}
                  <td class="px-4 py-2">{{ $item['sku'] }}</td>

                  {{-- Số lượng --}}
                  <td class="px-4 py-2">{{ $item['quantity'] }}</td>

                  {{-- Giá --}}
                  <td class="px-4 py-2">
                    {{ number_format($item['price'], 0, ',', '.') }} ₫
                  </td>

                  {{-- Thành tiền --}}
                  <td class="px-4 py-2">
                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} ₫
                  </td>
                </tr>
              @endforeach
            @endif
          </tbody>
        </table>
      </div>
    </div>

    {{-- 5. Ghi chú Đơn hàng & Tổng quan Giá trị --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- 5.1. Ghi chú --}}
      <div>
        <h3 class="text-lg font-semibold mb-2">Ghi chú Đơn hàng</h3>
        <form method="POST" action="{{ route('admin.orders.updateNotes', $order['id']) }}">
          @csrf
          <textarea name="notes"
                    rows="3"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    placeholder="Nhập ghi chú...">{{ $order['notes'] }}</textarea>
          <button type="submit"
                  class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            Cập nhật
          </button>
        </form>
      </div>

      {{-- 5.2. Tổng quan Giá trị --}}
      <div class="space-y-2">
        <p class="text-lg font-semibold">Tổng quan Giá trị</p>
        <div class="grid grid-cols-2 text-gray-700 gap-2">
          {{-- Tổng tiền hàng --}}
          <span>Tổng tiền hàng:</span>
          @php
            $sumItems = collect($order['items'])->sum(fn($i)=> $i['price'] * $i['quantity']);
          @endphp
          <span class="font-medium">{{ number_format($sumItems, 0, ',', '.') }} ₫</span>

          {{-- Giảm giá --}}
          <span>Giảm giá:</span>
          <span class="font-medium">{{ number_format($order['discount'], 0, ',', '.') }} ₫</span>

          {{-- Phí vận chuyển --}}
          <span>Phí vận chuyển:</span>
          <span class="font-medium">{{ number_format($order['shipping_fee'], 0, ',', '.') }} ₫</span>

          {{-- Tổng giá trị đơn hàng --}}
          <span class="text-gray-800">Tổng giá trị đơn hàng:</span>
          <span class="font-semibold">{{ number_format($order['total_amount'], 0, ',', '.') }} ₫</span>

          {{-- Đã thanh toán --}}
          <span>Đã thanh toán:</span>
          <span class="font-medium">{{ number_format($order['paid_amount'], 0, ',', '.') }} ₫</span>

          {{-- Đã hoàn trả --}}
          <span>Đã hoàn trả:</span>
          <span class="font-medium">{{ number_format($order['refunded_amount'], 0, ',', '.') }} ₫</span>

          {{-- Thực nhận --}}
          <span>Thực nhận:</span>
          <span class="font-medium">{{ number_format($order['received_amount'], 0, ',', '.') }} ₫</span>
        </div>
      </div>
    </div>

    {{-- 6. Thông tin người mua & kho bán--}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- 6.1. Thông tin Người Mua --}}
      <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Thông Tin Người Mua</h3>
        <div class="space-y-2 text-gray-700">
          <p><span class="font-medium">Tên:</span> {{ $order['customer'] }}</p>
          <p><span class="font-medium">Số điện thoại:</span> 0909000000</p>
          <p><span class="font-medium">Email:</span> example@gmail.com</p>
          <p><span class="font-medium">Địa chỉ:</span> 182 Lê Đại Hành, Q.1, TP.HCM</p>
        </div>
      </div>

      {{-- 6.2. Kho bán --}}
      <div>
        <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold mb-4">Kho bán</h3>
          <p class="text-gray-800">Kho Chính</p>
        </div>
      </div>
    </div>

    {{-- 7. Nút In --}}
    <div class="flex justify-end pt-4">
      <button onclick="window.print()"
              class="flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded transition">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-5 w-5 mr-2 text-gray-700"
             viewBox="0 0 20 20"
             fill="currentColor">
          <path d="M6 2a2 2 0 00-2 2v4h12V4a2 2 0 00-2-2H6z" />
          <path fill-rule="evenodd"
                d="M4 9v5a2 2 0 002 2h1v2h6v-2h1a2 2 0 002-2V9H4zm2 3h8v4H6v-4z"
                clip-rule="evenodd" />
        </svg>
        In đơn hàng
      </button>
    </div>

  </div>
@endsection
