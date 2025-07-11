@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Quản lý Đơn hàng</h1>

  {{-- -------------------- Search & Filter -------------------- --}}
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <input
      id="orderSearch"
      type="text"
      value="{{ old('search', $search ?? '') }}"
      placeholder="Nhập mã đơn hoặc tên khách"
      class="px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-primary"
    />
  </div>

  {{-- -------------------- Bảng Đơn hàng -------------------- --}}
  <div class="bg-white rounded-xl shadow p-6 w-full overflow-auto border border-gray-300">
    <table id="ordersTable" class="w-full table-auto">
      <thead>
        <tr class="bg-gray-200 text-gray-700 text-center">
          <th class="px-4 py-2">Mã Đơn</th>
          <th class="px-4 py-2">Ngày Tạo</th>
          <th class="px-4 py-2">Khách hàng</th>
          <th class="px-4 py-2">Thanh toán</th>
          <!-- <th class="px-4 py-2">Trạng thái Đơn</th> -->
          <th class="px-4 py-2">Hình thức Giao hàng</th>
          <th class="px-4 py-2">Trạng thái Giao</th>
          <th class="px-4 py-2">Tổng tiền (VNĐ)</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $o)
          <tr class="border-b text-center">
            {{-- Mã Đơn --}}
            <td class="px-4 py-2 text-left">
              <a href="{{ route('admin.orders.show', $o['id']) }}"
                 class="text-blue-600 hover:underline">
                {{ $o['madon'] }}
              </a>
            </td>

            {{-- Ngày Tạo --}}
            <td class="px-4 py-2">
              {{ \Carbon\Carbon::parse($o['created_at'])->format('d/m/Y') }}
            </td>

            {{-- Khách hàng --}}
            <td class="px-4 py-2">{{ $o->user->ten_nguoi_dung }}</td>

            {{-- Thanh toán --}}
            <td class="px-4 py-2">
              @switch($o['trangthai'])
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
                  <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Hủy đặt hàng</span>
              @endswitch
            </td>

<!--             {{-- Trạng thái Đơn --}}
            <td class="px-4 py-2">
              @switch($o->trangthaidonhang)
                @case('dadathang')
                  <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Đã đặt hàng</span>
                  @break
                @case('daduyet')
                  <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Đang xử lý</span>
                  @break
                @case('hoanthanh')
                  <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Hoàn thành</span>
                  @break
                @case('dahuy')
                  <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Đã hủy</span>
                  @break
                @default
                  <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Không xác định</span>
              @endswitch
            </td> -->

            {{-- Hình thức Giao hàng --}}
            <td class="px-4 py-2 text-capitalize">
              {{ $o['shipping_method_label'] }}
            </td>

            {{-- Trạng thái Giao --}}
            <td class="px-4 py-2">
              @switch($o['delivery_status'])
                @case('pending')
                  <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Chờ giao hàng</span>
                  @break
                @case('waiting_pickup')
                  <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">Chờ lấy hàng</span>
                  @break
                @case('shipping')
                  <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Đang giao hàng</span>
                  @break
                @case('delivered')
                  <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Đã giao hàng</span>
                  @break
                @case('returned')
                  <span class="px-2 py-1 bg-pink-100 text-pink-800 rounded-full text-sm">Trả hàng</span>
                  @break
                @case('canceled')
                  <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Hủy giao hàng</span>
                  @break
                @case('incomplete')
                  <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Chưa hoàn thành</span>
                  @break
                @default
                  <span class="px-2 py-1 bg-gray-200 text-gray-600 rounded-full text-sm">Không xác định</span>
              @endswitch
            </td>

            {{-- Tổng tiền --}}
            <td class="px-4 py-2">  
              {{ number_format($o->tongtien, 0, ',', '.') }}₫
            </td>

            {{-- Xóa --}}
           <!--  <td class="px-4 py-2 text-center">
              @php
                $canDelete = $o['order_status'] === 'huy' && in_array($o['trangthai'], [2]);
              @endphp

              @if($canDelete)
                <form action="{{ route('admin.orders.destroy', $o['id']) }}"
                      method="POST"
                      onsubmit="return confirm('Xác nhận xóa đơn hàng này?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                    Xóa
                  </button>
                </form>
              @else
                <button type="button"
                        disabled
                        class="px-3 py-1 bg-gray-300 text-gray-500 rounded cursor-not-allowed">
                  Xóa
                </button>
              @endif
            </td> -->
          </tr>
        @empty
          <tr>
            <td colspan="9" class="py-6 text-center text-gray-500">Chưa có đơn hàng nào</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Phân trang --}}
    <div class="mt-4">
      {{ $orders->links() }}
    </div>
  </div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('orderSearch');
    const rows  = document.querySelectorAll('#ordersTable tbody tr');

    function stripDiacritics(str) {
      return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    }

    input.addEventListener('input', () => {
      const q = stripDiacritics(input.value.trim());
      rows.forEach(tr => {
        const idText   = tr.cells[0].textContent.trim();
        const nameText = tr.cells[2].textContent.trim();
        tr.style.display = (!q || stripDiacritics(idText + ' ' + nameText).includes(q))
                            ? '' : 'none';
      });
    });

    if (input.value) {
      input.dispatchEvent(new Event('input'));
    }
  });
</script>
@endpush
