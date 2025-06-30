@extends('home.trangchu')
@section('title','Thanh toán thất bại')
@section('content')
  <div class="alert alert-danger">
    Thanh toán thất bại (mã {{ $code }}). Vui lòng thử lại.
  </div>
  <a href="{{ route('cart.checkout') }}">Quay lại trang thanh toán</a>
@endsection
