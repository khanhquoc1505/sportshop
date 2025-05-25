@extends('home.viewdangnhap')
@section('title', 'Đăng ký')
@section('content')
<form class="login-form">
  <label for="name">Họ và tên</label>
  <input type="text" id="name" placeholder="Nhập họ tên">

  <label for="email">Email</label>
  <input type="email" id="email" placeholder="Nhập email">

  <label for="password">Mật khẩu</label>
  <input type="password" id="password" placeholder="Tạo mật khẩu">

  <label for="confirm">Xác nhận mật khẩu</label>
  <input type="password" id="confirm" placeholder="Nhập lại mật khẩu">

  <button type="submit" class="login-submit">Đăng ký</button>
</form>
@endsection
