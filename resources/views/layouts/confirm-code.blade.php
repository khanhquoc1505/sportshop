@extends('home.viewdangnhap')
@section('title', 'Xác nhận mã OTP')
@section('content')

<div class="cf-container">
  <h1 class="cf-title"><i class="fas fa-sync-alt"></i> Nhập mã xác nhận</h1>
  <hr class="cf-hr">

  @if(session('status'))
    <div class="cf-alert-success">{{ session('status') }}</div>
  @endif

  @if($errors->has('code'))
    <div class="cf-alert-danger">{{ $errors->first('code') }}</div>
  @endif

  <form method="POST" action="{{ route('password.confirm') }}" class="cf-form">
    @csrf
    <div class="cf-group">
      <input
        type="text"
        name="code"
        class="cf-input"
        placeholder="Mã OTP (6 chữ số)"
        value="{{ old('code') }}"
        minlength="6"
        maxlength="6"
        required
      >
    </div>
    <button type="submit" class="cf-btn">
      <i class="fas fa-check"></i> Xác nhận
    </button>
  </form>
</div>

@endsection
