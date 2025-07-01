@extends('home.viewdangnhap')
@section('title', 'Quên mật khẩu')
@section('content')

<div class="fg-container">
  <h1 class="fg-title">Quên mật khẩu</h1>
  <hr class="fg-hr">

  @if ($errors->any())
    <div class="fg-alert">
      <ul>
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('password.email') }}" class="fg-form">
    @csrf
    <div class="fg-group">
      <input
        type="text"              {{-- cho phép nhập email hoặc số điện thoại --}}
        name="login"
        class="fg-input"
        placeholder="Email hoặc Số điện thoại"
        value="{{ old('login') }}"
        required>
    </div>
    <button type="submit" class="fg-btn">LẤY LẠI MẬT KHẨU</button>
  </form>
</div>

@endsection


