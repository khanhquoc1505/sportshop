<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>In Báo Cáo Doanh Thu</title>
  <style>
    /* CSS đơn giản cho in */
    body { font-family: sans-serif; padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #333; padding: 8px; text-align: center; }
    th { background: #eee; }
    @media print {
      a[href]:after { content: ""; }
    }
  </style>
</head>
<body onload="window.print()">
  <h2 style="text-align:center;">BÁO CÁO DOANH THU</h2>
  <table>
  <thead>
    <tr>
      <th>Date</th><th>Product</th><th>Quantity</th><th>Total</th>
    </tr>
  </thead>
  <tbody>
    @forelse($reportData as $row)
      <tr>
        <td>{{ $row['Date'] }}</td>
        <td>{{ $row['Product'] }}</td>
        <td>{{ $row['Quantity'] }}</td>
        <td>{{ number_format($row['Total'],0,',','.') }} đ</td>
      </tr>
    @empty
      <tr>
        <td colspan="4" class="text-center py-4">Chưa có dữ liệu</td>
      </tr>
    @endforelse
  </tbody>
</table>
</body>
</html>
