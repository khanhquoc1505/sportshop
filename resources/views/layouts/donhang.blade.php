@extends('home.trangchu')
@section('title', 'Đơn hàng của tôi')
@section('content')
<div class="dh-container dh-mt-4">
  <h3 class="dh-mb-4">Đơn hàng của tôi</h3>

  @forelse($orders as $order)
    @php
      $subtotal   = $order->chiTiet->sum(fn($i)=> $i->dongia * $i->soluong);
      $discount   = $order->chiTiet->sum(fn($i)=> $i->item_discount ?? 0);
      $total      = $subtotal - $discount;

      // Thay vì gọi thẳng ->first()->sanPham, chúng ta kiểm tra trước
      $firstDetail = $order->chiTiet->first();
      $firstItem   = $firstDetail ? $firstDetail->sanPham : null;
      $otherCount  = max(0, $order->chiTiet->count() - 1);
    @endphp

    <div class="dh-card">
      <div class="dh-card-body">
        <h5 class="dh-card-title">
          {{ $loop->iteration }}. {{ $order->madon }}
          ngày {{ $order->created_at->format('d/m/Y') }}
          [ -{{ $subtotal > 0 ? round($discount / $subtotal * 100) : 0 }}% ]
        </h5>

        @if($firstItem)
          <p class="dh-text-muted dh-mb-3">
            {{ $firstItem->ten }} / {{ $firstItem->masanpham }}
            @if($otherCount>0) … và {{ $otherCount }} sản phẩm khác @endif
          </p>
        @else
          <p class="dh-text-muted dh-mb-3">Không có sản phẩm</p>
        @endif

        <hr>
        <p class="dh-mb-1">
          Tổng hóa đơn: {{ number_format($subtotal,0,',','.') }}₫ 
          / Thành tiền: {{ number_format($total,0,',','.') }}₫
        </p>
        <p class="dh-mb-3">
          Tình trạng đơn hàng:
          <span class="dh-badge dh-bg-success">Hoàn thành</span>
        </p>
        <a href="{{ route('donhang.show', $order->id) }}" class="dh-btn dh-btn-info">
          Xem chi tiết
        </a>
      </div>
    </div>
@empty
    <div class="alert alert-secondary">Bạn chưa có đơn hàng nào.</div>
@endforelse
</div>
@endsection
