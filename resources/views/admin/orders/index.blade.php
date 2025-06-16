@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Quản lý Đơn hàng</h1>

  {{-- -------------------- Search & Filter -------------------- --}}
  <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    {{-- Tìm kiếm --}}
    <div class="col-span-1 md:col-span-2">
      <input
        type="text"
        name="search"
        value="{{ old('search', $search) }}"
        placeholder="Tìm kiếm mã đơn hoặc tên khách"
        class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-primary"
      />
    </div>
  </form>

{{-- -------------------- Bảng Đơn hàng -------------------- --}}
  <div class="bg-white rounded-xl shadow p-6 w-full overflow-auto border border-gray-300">
    <table id="ordersTable" class="w-full table-auto">
      <thead>
        <tr class="bg-gray-200 text-gray-700 text-center">
          <th class="px-4 py-2">Mã Đơn</th>
          <th class="px-4 py-2">Ngày Tạo</th>
          <th class="px-4 py-2">Khách hàng</th>
          <th class="px-4 py-2">Thanh toán</th>
          <th class="px-4 py-2">Trạng thái Đơn</th>
          <th class="px-4 py-2">Hình thức Giao hàng</th>
          <th class="px-4 py-2">Trạng thái Giao</th>
          <th class="px-4 py-2">Tổng tiền (VNĐ)</th>
          <th class="px-4 py-2">Xóa</th>
        </tr>
      </thead>
      <tbody>
        @foreach($orders as $o)
          <tr class="border-b text-center">
            {{-- Mã Đơn (click vào sẽ chuyển trang chi tiết) --}}
            <td class="px-4 py-2 text-left">
              <a href="{{ route('admin.orders.show', $o['id']) }}"
                 class="text-blue-600 hover:underline">
                {{ $o['id'] }}
              </a>
            </td>

            {{-- Ngày Tạo --}}
            <td class="px-4 py-2">
              {{ \Carbon\Carbon::parse($o['created_at'])->format('d/m/Y') }}
            </td>

            {{-- Khách hàng --}}
            <td class="px-4 py-2">{{ $o['customer'] }}</td>

            {{-- Thanh toán --}}
            <td class="px-4 py-2">
              @if($o['payment_status'] === 'paid')
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Đã thanh toán</span>
              @else
                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Chưa thanh toán</span>
              @endif
            </td>

            {{-- Trạng thái Đơn --}}
            <td class="px-4 py-2">
              @if($o['order_status'] === 'pending')
                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Chờ xác nhận</span>
              @elseif($o['order_status'] === 'processing')
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Đang xử lý</span>
              @elseif($o['order_status'] === 'completed')
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Hoàn thành</span>
              @else
                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Đã hủy</span>
              @endif
            </td>

            {{-- Hình thức Giao hàng --}}
            <td class="px-4 py-2">{{ $o['shipping_method'] }}</td>

            {{-- Trạng thái Giao --}}
            <td class="px-4 py-2">
              @switch($o['delivery_status'])
                {{-- Chờ giao --}}
                @case('pending')
                  <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">
                    Chờ giao hàng
                  </span>
                  @break

                {{-- Chờ lấy hàng --}}
                @case('waiting_pickup')
                  <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                    Chờ lấy hàng
                  </span>
                  @break

                {{-- Đang giao --}}
                @case('shipping')
                  <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                    Đang giao hàng
                  </span>
                  @break

                {{-- Đã giao --}}
                @case('delivered')
                  <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    Đã giao hàng
                  </span>
                  @break

                {{-- Trả hàng --}}
                @case('returned')
                  <span class="px-2 py-1 bg-pink-100 text-pink-800 rounded-full text-sm">
                    Trả hàng
                  </span>
                  @break

                {{-- Hủy giao hàng --}}
                @case('canceled')
                  <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                    Hủy giao hàng
                  </span>
                  @break

                {{-- Chưa hoàn thành --}}
                @case('incomplete')
                  <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                    Chưa hoàn thành
                  </span>
                  @break

                {{-- Mặc định --}}
                @default
                  <span class="px-2 py-1 bg-gray-200 text-gray-600 rounded-full text-sm">
                    Không xác định
                  </span>
              @endswitch
            </td>
            {{-- Tổng tiền (VNĐ) --}}
            <td class="px-4 py-2">
              {{ number_format($o['total_amount'], 0, ',', '.') }}
            </td>

            {{-- Xóa --}}
            <td class="px-4 py-2">
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
            </td>
          </tr>
        @endforeach

        @if($orders->isEmpty())
          <tr>
            <td colspan="9" class="py-6 text-center text-gray-500">Chưa có đơn hàng nào</td>
          </tr>
        @endif
      </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-4">
      {{ $orders->links() }}
    </div>
  </div>
@endsection

@push('scripts')
<script>
  // Tìm kiếm live: so khớp mã đơn hoặc tên khách
  (function() {
    const input = document.querySelector('input[name="search"]');
    if (!input) return;
    const rows = document.querySelectorAll('#ordersTable tbody tr');

    function stripDiacritics(str) {
      return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    }

    function filterRows() {
      const qNorm = stripDiacritics(input.value.trim());
      rows.forEach(tr => {
        // cột 0 = Mã Đơn, cột 2 = Khách hàng (vì cột 1 là Ngày Tạo)
        const idText   = tr.cells[0].textContent.trim();
        const nameText = tr.cells[2].textContent.trim();
        const textNorm = stripDiacritics(idText + ' ' + nameText);
        tr.style.display = (!qNorm || textNorm.includes(qNorm)) ? '' : 'none';
      });
    }

    input.addEventListener('input', filterRows);
    filterRows();
  })();
</script>
@endpush
