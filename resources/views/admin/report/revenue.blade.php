@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark text-center">BÁO CÁO DOANH THU</h1>

  <div class="bg-light p-6 rounded-xl shadow border border-gray-300">
    <form action="{{ route('admin.report.revenue') }}" method="GET" class="space-y-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Loại thời gian --}}
        <div>
          <label class="block mb-1 font-medium">Loại thời gian</label>
          <select name="period" class="w-full border px-3 py-2 rounded">
            <option value="day"   {{ $period=='day'   ? 'selected' : '' }}>Báo cáo theo ngày</option>
            <option value="month" {{ $period=='month' ? 'selected' : '' }}>Báo cáo theo tháng</option>
            <option value="year"  {{ $period=='year'  ? 'selected' : '' }}>Báo cáo theo năm</option>
          </select>
        </div>

        {{-- Lọc sản phẩm --}}
        <div>
          <label class="block mb-1 font-medium">Lọc sản phẩm</label>
          <select name="product_id" class="w-full border px-3 py-2 rounded">
            <option value="all">Tất cả</option>
            @foreach($products as $p)
              <option value="{{ $p->id }}"
                {{ $productId == $p->id ? 'selected' : '' }}>
                {{ $p->ten }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Ngày bắt đầu --}}
        <div>
          <label class="block mb-1 font-medium">Ngày bắt đầu</label>
          <input type="date"
                 name="start_date"
                 value="{{ request('start_date') }}"
                 class="w-full border px-3 py-2 rounded" />
        </div>

        {{-- Ngày kết thúc --}}
        <div>
          <label class="block mb-1 font-medium">Ngày kết thúc</label>
          <input type="date"
                 name="end_date"
                 value="{{ request('end_date') }}"
                 class="w-full border px-3 py-2 rounded" />
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
          Lọc
        </button>

        {{-- Xuất Excel --}}
        <a href="{{ route('admin.revenue.export', request()->all()) }}"
           class="px-6 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">
          Xuất Excel
        </a>

        {{-- In báo cáo --}}
        <a href="{{ route('admin.revenue.print', request()->only([
    'type','product','start_date','end_date'
])) }}"
   class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
  In báo cáo
</a>

        {{-- Đặt lại --}}
        <a href="{{ route('admin.report.revenue') }}"
           class="px-6 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
          Đặt lại
        </a>
      </div>
    </form>

    {{-- Bảng Tổng quan --}}
    <div class="overflow-x-auto mt-6">
      <table class="min-w-full bg-white rounded-lg shadow">
        <thead class="bg-gray-100 text-gray-700 text-center">
          <tr>
            <th class="px-4 py-2">
              @if($period=='month') Tháng 
              @elseif($period=='year') Năm 
              @else Ngày 
              @endif
            </th>
            <th class="px-4 py-2">Số đơn</th>
            <th class="px-4 py-2">Tổng doanh thu</th>
          </tr>
        </thead>
        <tbody class="text-center">
          @forelse($overview as $row)
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-2">
                @if($period=='month')
                  {{ \Carbon\Carbon::createFromFormat('Y-m', $row->label)->format('m/Y') }}
                @elseif($period=='year')
                  {{ $row->label }}
                @else
                  {{ \Carbon\Carbon::parse($row->label)->format('d/m/Y') }}
                @endif
              </td>
              <td class="px-4 py-2">{{ $row->so_don }}</td>
              <td class="px-4 py-2 text-green-700 font-semibold">
                {{ number_format($row->tong_doanhthu, 0, ',', '.') }} ₫
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="py-4 text-gray-500">Chưa có dữ liệu</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  {{-- === mới: biểu đồ line === --}}
  <div class="mt-8 bg-white rounded-xl shadow p-6">
    <h2 class="text-xl font-medium mb-4">
      Số đơn hàng theo {{ ucfirst($period) }}
    </h2>
    <canvas id="ordersLineChart" class="w-full" style="height:200px;"></canvas>
  </div>
@endsection
@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const ctx = document.getElementById('ordersLineChart').getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: @json($chartLabels),
          datasets: [{
            label: 'Số đơn',
            data: @json($orderCounts),
            fill: false,
            borderColor: 'rgb(59,130,246)',
            backgroundColor: 'rgba(59,130,246,0.2)',
            tension: 0.3,
            pointRadius: 4
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: { precision: 0 }
            }
          },
          plugins: {
            legend: { display: false }
          }
        }
      });
    });
  </script>
@endpush