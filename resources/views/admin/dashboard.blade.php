{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Dashboard</h1>

  <div class="bg-white rounded-xl shadow p-6 border border-gray-300">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- BIỂU ĐỒ SỐ LƯỢNG --}}
      <div>
        <h2 class="text-lg font-medium mb-4">Số lượng bán ra (Top 5 SP)</h2>
        <canvas id="chartQty" class="w-full h-60"></canvas>
      </div>

      {{-- BIỂU ĐỒ DOANH THU --}}
      <div>
        <h2 class="text-lg font-medium mb-4">Doanh thu theo sản phẩm (Top 5 SP)</h2>
        <canvas id="chartRev" class="w-full h-60"></canvas>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  {{-- Chart.js từ CDN --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const labels       = @json($labels);
      const qtyData      = @json($qtyData);
      const revenueData  = @json($revenueData);

      // Màu nền (có thể thay đổi tuỳ ý)
      const colorsQty = ['#4F46E5','#10B981','#F59E0B','#EF4444','#3B82F6'];
      const colorsRev = ['#6366F1','#34D399','#FBBF24','#F87171','#60A5FA'];

      // Chart số lượng
      new Chart(document.getElementById('chartQty'), {
        type: 'pie',
        data: {
          labels,
          datasets: [{
            data: qtyData,
            backgroundColor: colorsQty,
          }]
        },
        options: {
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });

      // Chart doanh thu
      new Chart(document.getElementById('chartRev'), {
        type: 'pie',
        data: {
          labels,
          datasets: [{
            data: revenueData,
            backgroundColor: colorsRev,
          }]
        },
        options: {
          plugins: {
            legend: { position: 'bottom' },
            tooltip: {
              callbacks: {
                label(ctx) {
                  const v = ctx.parsed;
                  return ctx.label + ': ' +
                    new Intl.NumberFormat('vi-VN', {
                      style: 'currency', currency: 'VND', maximumFractionDigits: 0
                    }).format(v);
                }
              }
            }
          }
        }
      });
    });
  </script>
@endpush
