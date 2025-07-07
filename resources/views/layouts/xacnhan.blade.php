@extends('home.viewdangnhap')
@section('title','Xác nhận mã OTP')
@section('content')
<link rel="stylesheet" href="{{ asset('css/xacnhan.css') }}">

<div class="xn-ch-container">
  <h1 class="xn-ch-title"><i class="fas fa-key"></i> Xác nhận mã OTP</h1>
  <hr class="xn-ch-hr">

  @if (session('status'))
    <div class="xn-ch-status">{{ session('status') }}</div>
  @endif

  @if ($errors->any())
    <div class="xn-ch-alert-danger">
      <ul>
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('dangky.confirm') }}" class="xn-ch-form">
    @csrf

    <div class="xn-ch-group">
      <label for="otp">Nhập mã OTP</label>
      <input id="otp" name="otp" type="text" required>
    </div>

    <button type="submit" class="xn-ch-btn">
      <i class="fas fa-check-circle"></i> Xác nhận và tạo tài khoản
    </button>
  </form>
</div>
@endsection
