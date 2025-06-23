@extends('home.viewdangnhap')
@section('title', 'Đăng ký')
@section('content')
@if ($errors->any())
  <div class="alert alert-danger" style="color: red;">
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
  <form class="login-form" method="POST" action="{{ route('dangky') }}">
  @csrf
  <label for="name">Họ và tên</label>
  <input type="text" name="ten_nguoi_dung" required>

  <label for="email">Email</label>
  <input type="email" name="email">

  <label for="sdt">Số điện thoại</label>
  <input type="text" name="sdt">

  <label for="dia_chi">Địa chỉ</label>
  <input type="text" name="dia_chi">

  <label for="password">Mật khẩu</label>
  <input type="password" name="mat_khau" required>

  <label for="confirm">Xác nhận mật khẩu</label>
  <input type="password" name="mat_khau_confirmation" required>

  <button type="submit">Đăng ký</button>
</form>

@endsection
