{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6">Dashboard</h1>

  {{-- Filter form --}}
  <form method="GET" action="{{ route('admin.dashboard') }}"
        class="mb-8 flex flex-wrap gap-4 items-end">
    <div>
      <label class="block text-sm">Từ ngày</label>
      <input type="date" name="start_date" value="{{ $start }}"
             class="border rounded p-2" />
    </div>
    <div>
      <label class="block text-sm">Đến ngày</label>
      <input type="date" name="end_date" value="{{ $end }}"
             class="border rounded p-2" />
    </div>
    <div>
      <label class="block text-sm">Độ phân giải</label>
      <select name="period" class="border rounded p-2">
        <option value="day"   @selected($period=='day')>Theo ngày</option>
        <option value="month" @selected($period=='month')>Theo tháng</option>
        <option value="year"  @selected($period=='year')>Theo năm</option>
      </select>
    </div>
    <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded">
      Lọc
    </button>
  </form>

  {{-- Pie charts: Qty & Revenue --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl shadow p-2">
      <h2 class="font-medium mb-2 text-sm">Top 5 SP theo số lượng</h2>
      <canvas id="qtyChart" class="w-full h-28"></canvas>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
      <h2 class="font-medium mb-2 text-sm">Top 5 SP theo doanh thu</h2>
      <canvas id="revChart" class="w-full h-28"></canvas>
    </div>
  </div>

  {{-- Bar + Line side by side --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
    {{-- 1) Bar chart: Tổng số đơn hoàn thành --}}
    <div class="bg-white rounded-xl shadow p-4">
      <h2 class="font-medium mb-2 text-sm">
        Tổng đơn đã hoàn thành ({{ ucfirst($period) }})
      </h2>
      <canvas id="ordersChart" class="w-full h-32"></canvas>
    </div>
    {{-- 2) Line chart: Lượt truy cập --}}
    <div class="bg-white rounded-xl shadow p-4">
      <h2 class="font-medium mb-2 text-sm">
        Lượt truy cập ({{ ucfirst($period) }})
      </h2>
      <canvas id="visitsChart" class="w-full h-32"></canvas>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Pie: số lượng
      new Chart(
        document.getElementById('qtyChart'),
        {
          type: 'pie',
          data: {
            labels: @json($labels),
            datasets: [{ data: @json($qtyData), borderWidth: 1 }]
          }
        }
      );

      // Pie: doanh thu
      new Chart(
        document.getElementById('revChart'),
        {
          type: 'pie',
          data: {
            labels: @json($labels),
            datasets: [{ data: @json($revenueData), borderWidth: 1 }]
          },
          options: {
            plugins: {
              tooltip: {
                callbacks: {
                  label: ctx => '₫ ' + new Intl.NumberFormat('vi-VN').format(ctx.parsed)
                }
              }
            }
          }
        }
      );

      // 3) Bar: tổng đơn hoàn thành (thu nhỏ cột)
      new Chart(
        document.getElementById('ordersChart'),
        {
          type: 'bar',
          data: {
            labels: @json($orderLabels),
            datasets: [{
              label: 'Số đơn',
              data: @json($orderData),
              backgroundColor: 'rgba(59, 130, 246, 0.5)',
              borderColor:     'rgba(59, 130, 246, 1)',
              borderWidth: 1,
              // ---- Thêm phần này để thu nhỏ cột ----
              barPercentage:      0.4,  // 40% width so với category
              categoryPercentage: 0.6,  // 60% width mỗi category
              maxBarThickness:   20     // tối đa 20px
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true,
                ticks: { precision: 0 }
              }
            },
            plugins: { legend: { display: false } }
          }
        }
      );

      // Line: lượt truy cập
      new Chart(
        document.getElementById('visitsChart'),
        {
          type: 'line',
          data: {
            labels: @json($visitLabels),
            datasets: [{
              label: 'Lượt truy cập',
              data: @json($visitData),
              fill: false,
              tension: 0.3,
              borderWidth: 2
            }]
          },
          options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
          }
        }
      );
    });
  </script>
@endpush
