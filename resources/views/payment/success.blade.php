{{-- resources/views/vnpay/success.blade.php --}}
@extends('home.trangchu')
@section('title','Thanh toán thành công')
@section('content')
  <div class="alert alert-success">
    Thanh toán đơn <strong>{{ $order->madon }}</strong> thành công!
  </div>
  <a href="{{ route('donhang.show',$order->id) }}">Xem chi tiết đơn hàng</a>
@endsection
