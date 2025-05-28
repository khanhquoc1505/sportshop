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
        @foreach(array_keys($reportData[0] ?? []) as $col)
          <th>{{ $col }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach($reportData as $row)
        <tr>
          @foreach($row as $val)
            <td>{{ $val }}</td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
