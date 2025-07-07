@extends('home.viewdangnhap')
@section('title','Nhập mật khẩu mới')
@section('content')
<div class="ch-container">
  <h1 class="ch-title"><i class="fas fa-key"></i> Nhập mật khẩu mới</h1>
  <hr class="ch-hr">

  @if ($errors->any())
    <div class="ch-alert-danger">
      <ul>
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('password.sendCode') }}" class="ch-form">
    @csrf
    <div class="ch-group">
      <input
        type="password"
        name="new_password"
        class="ch-input"
        placeholder="Mật khẩu mới"
        required>
    </div>
    <div class="ch-group">
      <input
        type="password"
        name="new_password_confirmation"
        class="ch-input"
        placeholder="Nhập lại mật khẩu"
        required>
    </div>
    <button type="submit" class="ch-btn">
      <i class="fas fa-envelope-open-text"></i> Gửi mã xác nhận
    </button>
  </form>
</div>
@endsection
