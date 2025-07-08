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
        {{-- Kênh Bán hàng --}}
        <div>
          <p class="text-sm text-gray-600">Kênh Bán hàng</p>
          <p class="text-lg font-medium">POS</p>
        </div>
      </div>

      <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
       {{-- Trạng thái Giao hàng --}}
  <div>
    <p class="text-sm text-gray-600">Trạng thái Giao hàng</p>
    <form method="POST"
          action="{{ route('admin.orders.updateDeliveryStatus', $order['id']) }}"
          class="mt-1 flex items-center">
      @csrf
      @method('PATCH')
      <select name="delivery_status" id="delivery_status" class="border rounded px-2 py-1 text-sm">
        <option value="pending"        @selected($order['delivery_status']=='pending')>Chờ giao hàng</option>
          <option value="waiting_pickup" @selected($order['delivery_status']=='waiting_pickup')>Chờ lấy hàng</option>
          <option value="shipping"       @selected($order['delivery_status']=='shipping')>Đang giao hàng</option>
          <option value="delivered"      @selected($order['delivery_status']=='delivered')>Đã giao hàng</option>
          <option value="returned"       @selected($order['delivery_status']=='returned')>Trả hàng</option>
          <option value="canceled"       @selected($order['delivery_status']=='canceled')>Hủy giao hàng</option>
          <option value="incomplete"     @selected($order['delivery_status']=='incomplete')>Chưa hoàn thành</option>
      </select>
      <button type="submit"
              class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
        Cập nhật
      </button>
    </form>
  </div>

        {{-- Trạng thái Thanh toán --}}
        <div>
          <p class="text-sm text-gray-600">Trạng thái Thanh toán</p>
          <div class="mt-1">
            @switch($order['trangthai'])
   @case(2)
                  <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Chưa thanh toán</span>
                  @break
                @case(3)
                  <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Đã thanh toán</span>
                  @break
                @case(4)
                  <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Đang hoàn tiền</span>
                  @break
                @case(5)
                  <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Đã hoàn tiền</span>
                  @break
                @default
                  <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Không xác định</span>
      <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Không xác định</span>
  @endswitch
          </div>
        </div>

        {{-- Hình thức Giao hàng --}}
        <div>
          <p class="text-sm text-gray-600">Hình thức Giao hàng</p>
          <p class="text-lg font-medium">{{ $order['shipping_method_label'] }}</p>
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
            @forelse($order['items'] as $item)
              <tr class="border-b text-center">
                <td class="px-4 py-2">
                  <img src="{{ $item['image_url'] }}"
                       alt=""
                       class="mx-auto h-12 w-12 object-cover rounded-md">
                </td>
                <td class="px-4 py-2 text-left">{{ $item['name'] }}</td>
                <td class="px-4 py-2">{{ $item['sku'] }}</td>
                <td class="px-4 py-2">{{ $item['quantity'] }}</td>
                <td class="px-4 py-2">{{ number_format($item['price'],0,',','.') }}₫</td>
                <td class="px-4 py-2">
                  {{ number_format($item['price'] * $item['quantity'],0,',','.') }}₫
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-6 text-center text-gray-500">
                  Chưa có sản phẩm nào
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- 5. Ghi chú & Tổng quan Giá trị --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow p-6 grid md:grid-cols-2 gap-6">
      {{-- 5.1. Ghi chú --}}
      <div>
        <h3 class="text-lg font-semibold mb-2">Ghi chú Đơn hàng</h3>
        <form method="POST" action="{{ route('admin.orders.updateNotes', $order['id']) }}">
          @csrf
          @method('PATCH')
          <textarea name="notes"
                    rows="3"
                    class="w-full px-3 py-2 border rounded focus:ring-primary"
                    placeholder="Nhập ghi chú...">{{ old('notes', $order['notes'] ?? '') }}</textarea>
          <button type="submit"
                  class="mt-2 px-4 py-2 bg-blue-600 text-white rounded">
            Cập nhật
          </button>
        </form>
      </div>

     {{-- 3. Tổng quan Giá trị --}}
  <div class="bg-white border border-gray-200 rounded-lg shadow p-6 mt-6">
    <h2 class="text-xl font-semibold mb-4">Tổng quan Giá trị</h2>
    <div class="grid grid-cols-2 gap-4 text-gray-700">
      <div class="flex justify-between">
        <span>Tổng tiền hàng:</span>
        <span class="font-medium">{{ number_format($order['sum_items'],0,',','.') }}₫</span>
      </div>
      <div class="flex justify-between">
        <span>Giảm giá:</span>
        <span class="font-medium">{{ number_format($order['discount'],0,',','.') }}₫</span>
      </div>
      <div class="flex justify-between">
        <span>Phí vận chuyển:</span>
        <span class="font-medium">{{ number_format($order['shipping_fee'],0,',','.') }}₫</span>
      </div>

      <div class="col-span-2 border-t pt-2 mt-2 font-semibold flex justify-between">
        <span>Tổng giá trị đơn hàng:</span>
        <span>{{ number_format($order['total_order_value'],0,',','.') }}₫</span>
      </div>

      <div class="flex justify-between">
        <span>Đã thanh toán:</span>
        <span class="font-medium">{{ number_format($order['paid_amount'],0,',','.') }}₫</span>
      </div>
      <div class="flex justify-between">
        <span>Đã hoàn trả:</span>
        <span class="font-medium">{{ number_format($order['refunded_amount'],0,',','.') }}₫</span>
      </div>
      <div class="flex justify-between">
        <span>Thực nhận:</span>
        <span class="font-semibold">{{ number_format($order['received_amount'],0,',','.') }}₫</span>
      </div>
    </div>
  </div>

    {{-- 6. Thông tin Người mua & Kho bán (giữ nguyên) --}}
    {{-- … --}}

    {{-- 7. Nút In --}}
    <div class="flex justify-end pt-4">
      <button onclick="window.print()"
              class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
        In đơn hàng
      </button>
    </div>
    <div>
       {{-- 7. Nút Hoàn tiền (nếu đủ điều kiện) --}}
    @if(
      in_array($order['trangthai'], ['4']) 
      && $order['delivery_status'] !== 'delivered'
    )
      <form method="POST"
            action="{{ route('admin.orders.refund', ['order' => $order['id'] ]) }}"
            onsubmit="return confirm('Bạn có chắc muốn hoàn tiền cho đơn này?');">
        @csrf
        @method('PATCH')
        <button type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
          Hoàn tiền
        </button>
      </form>
    @endif
    </div>
  </div>
  
@endsection
