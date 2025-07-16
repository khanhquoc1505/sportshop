<!DOCTYPE html>
<html>
<head>
  <title>Đơn hàng #{{ $order['id'] }}</title>
  <style>
    /* Tối giản style in: bảng, font, spacing... */
    body { font-family: sans-serif; margin: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #333; padding: 8px; text-align: left; }
  </style>
</head>
<body onload="window.print()">
  <h1>Đơn hàng #{{ $order['id'] }}</h1>
  <p>Khách: {{ $order['customer_name'] }}</p>
  <p>Ngày đặt: {{ \Carbon\Carbon::parse($order['created_at'])->format('d/m/Y H:i') }}</p>
  <table>
    <thead>
      <tr>
        <th>Ảnh</th><th>Tên</th><th>SKU</th><th>SL</th><th>Đơn giá</th><th>Thành tiền</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order['items'] as $i)
        <tr>
          <td><img src="{{ $i['image_url'] }}" width="50"></td>
          <td>{{ $i['name'] }}</td>
          <td>{{ $i['sku'] }}</td>
          <td>{{ $i['quantity'] }}</td>
          <td>{{ number_format($i['price'],0,',','.') }}₫</td>
          <td>{{ number_format($i['subtotal'],0,',','.') }}₫</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <p>Tổng hàng: {{ number_format($order['sum_items'],0,',','.') }}₫</p>
  <p>Giảm giá: {{ number_format($order['discount'],0,',','.') }}₫</p>
  <p>Phí ship: {{ number_format($order['shipping_fee'],0,',','.') }}₫</p>
  <p><strong>Tổng đơn: {{ number_format($order['total_order_value'],0,',','.') }}₫</strong></p>
</body>
</html>
