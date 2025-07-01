@extends('home.viewdangnhap')
@section('title', 'Đặt lại mật khẩu')
@section('content')

<div class="rs-container">
  <h2 class="rs-title">Đặt lại mật khẩu.</h2>
  <p class="rs-desc">Nhập mật khẩu mới cho tài khoản.</p>
  <hr class="rs-hr">

  @if ($errors->any())
    <div class="rs-alert-danger">
      <ul>
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  @if (session('status'))
    <div class="rs-alert-success">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('password.update') }}" class="rs-form">
  @csrf

  {{-- GIỮ LOGIN ẨN --}}
  <input type="hidden" name="login" value="{{ $login }}">

  <div class="rs-group">
    <label>Mật khẩu mới: <span class="rs-required">*</span></label>
    <input type="password" name="new_password" class="rs-input" required>
  </div>

  <div class="rs-group">
    <label>Nhập lại mật khẩu: <span class="rs-required">*</span></label>
    <input type="password" name="new_password_confirmation" class="rs-input" required>
  </div>

  <div class="rs-captcha-wrapper">
    <div class="rs-captcha-box">{{ session('plain_captcha') }}</div>
    <span class="rs-refresh" onclick="location.reload()">&#x21bb;</span>
  </div>

  <div class="rs-group">
    <label>Nhập mã captcha: <span class="rs-required">*</span></label>
    <input type="text" name="code" class="rs-input" required maxlength="6" minlength="6">
  </div>

  <button type="submit" class="rs-btn">Gửi</button>
</form>

</div>

@endsection


