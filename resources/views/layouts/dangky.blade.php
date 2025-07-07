@extends('home.viewdangnhap')
@section('title','Đăng ký')
@section('content')
<link rel="stylesheet" href="{{ asset('css/dangky.css') }}">

<div class="dk-ch-container">
  

  @if ($errors->any())
    <div class="dk-ch-alert-danger">
      <ul>
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('dangky.process') }}" class="dk-ch-form">
    @csrf

    <div class="dk-ch-group">
      <label for="ten_nguoi_dung">Họ và tên *</label>
      <input id="ten_nguoi_dung" type="text" name="ten_nguoi_dung"
             value="{{ old('ten_nguoi_dung') }}" required>
    </div>

    <div class="dk-ch-group">
      <label for="email">Email *</label>
      <input id="email" type="email" name="email"
             value="{{ old('email') }}" required>
    </div>

    <div class="dk-ch-group">
      <label for="sdt">Số điện thoại *</label>
      <input id="sdt" type="text" name="sdt"
             value="{{ old('sdt') }}" required>
    </div>

    <div class="dk-ch-group">
      <label for="dia_chi">Địa chỉ</label>
      <input id="dia_chi" type="text" name="dia_chi"
             value="{{ old('dia_chi') }}">
    </div>

    <div class="dk-ch-group dk-password-wrapper">
      <label for="mat_khau">Mật khẩu *</label>
      <input id="mat_khau" type="password" name="mat_khau" required>
      <i class="fas fa-eye" onclick="togglePassword('mat_khau')"></i>
    </div>

    <div class="dk-ch-group dk-password-wrapper">
      <label for="mat_khau_confirmation">Xác nhận mật khẩu *</label>
      <input id="mat_khau_confirmation" type="password"
             name="mat_khau_confirmation" required>
      <i class="fas fa-eye" onclick="togglePassword('mat_khau_confirmation')"></i>
    </div>

    <small class="dk-ch-note">
      Lưu ý: Nhập email chính xác để nhận mã kích hoạt.
    </small>

    <button type="submit" class="dk-ch-btn">
      <i class="fas fa-paper-plane"></i> Đăng ký
    </button>
  </form>
</div>

<script>
  function togglePassword(id) {
    const f = document.getElementById(id);
    f.type = f.type === 'password' ? 'text' : 'password';
  }
</script>
@endsection
