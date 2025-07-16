@extends('home.viewdangnhap')
@section('title', 'Đăng nhập')
@section('content')

<form class="login-form" method="POST" action="{{ route('login.post') }}">
  @csrf

  {{-- Nếu muốn show tất cả lỗi chung --}}
  <label for="login">Email hoặc Số điện thoại</label>
  <input
    id="login"
    type="text"
    name="login"
    placeholder="Nhập email hoặc số điện thoại"
    value="{{ old('login') }}"
    required
  >
  @error('login')
    <div class="field-error">{{ $message }}</div>
  @enderror

  <label for="mat_khau">Mật khẩu</label>
  <input
    id="mat_khau"
    type="password"
    name="mat_khau"
    required
  >
  @error('mat_khau')
    <div class="field-error">{{ $message }}</div>
  @enderror

  <div class="form-footer">
    <button type="submit">Đăng nhập</button>
    <div class="fg-forgot-link">
      <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
    </div>
  </div>
</form>

@endsection
