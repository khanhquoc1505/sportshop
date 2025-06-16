@extends('home.viewdangnhap')
@section('title', 'Đăng nhập')
@section('content')

  <form class="login-form" method="POST" action="{{ route('login') }}">
  @csrf
  <label for="login">Email hoặc Số điện thoại</label>
  <input type="text" name="login" placeholder="Nhập email hoặc số điện thoại">

  <label for="password">Mật khẩu</label>
  <input type="password" name="mat_khau" required>

  <input type="checkbox" name="remember"> Ghi nhớ
  <button type="submit">Đăng nhập</button>
</form>


<!-- popup quên mật khẩu -->
<div class="popup-overlay" id="popup-overlay"></div>

<!-- Popup 1: Nhập email hoặc SĐT -->
<div class="popup" id="popup-step1">
  <h3>Quên mật khẩu</h3>
  <label for="email-step1">Email hoặc số điện thoại</label>
  <input type="text" id="email-step1" placeholder="Value" required>
  <div class="popup-actions">
    <button id="cancel-popup1">Cancel</button>
    <button id="next-step">Reset Password</button>
  </div>
</div>

<!-- Popup 2: Nhập mật khẩu mới -->
<div class="popup" id="popup-step2">
  <h3>Nhập mật khẩu mới</h3>
  <label for="new-password">Mật khẩu mới</label>
  <input type="password" id="new-password" placeholder="Value" required>
  <label for="confirm-password">Nhập lại mật khẩu</label>
  <input type="password" id="confirm-password" placeholder="Value" required>
  <label for="OTP">Nhập OTP</label>
  <input type="OTP" id="OTP" placeholder="Value" required>
  <div class="popup-actions">
    <button id="cancel-popup2">Cancel</button>
    <button id="submit-reset">Xác nhận</button>
  </div>
</div>

@endsection
