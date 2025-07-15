{{-- layouts/csthongtin.blade.php --}}
@extends('home.trangchu')

@section('title', $title ?? 'Tài Khoản')

@section('content')
<div class="container ff-profile-container mt-4">
  <div class="ff-profile-row">
    {{-- SIDEBAR --}}
    <aside class="ff-profile-sidebar">
      <ul class="ff-menu-list">
        <li class="ff-menu-item {{ request()->routeIs('profile.index') ? 'active' : '' }}">
          <i class="fas fa-user ff-menu-icon"></i>
          <a href="{{ route('profile.index') }}">Tài Khoản Của Tôi</a>
        </li>
        <li class="ff-menu-item {{ request()->routeIs('favorites.index') ? 'active' : '' }}">
          <i class="fas fa-heart ff-menu-icon"></i>
          <a href="{{ route('favorites.index') }}">Yêu Thích</a>
        </li>
        <li class="ff-menu-item {{ request()->routeIs('profile.change_password') ? 'active' : '' }}">
          <i class="fas fa-lock ff-menu-icon"></i>
          <a href="{{ route('profile.change_password') }}">Đổi Mật Khẩu</a>
        </li>
      </ul>
    </aside>

    {{-- NỘI DUNG --}}
    <div class="ff-profile-main">
      @yield('account.content')
    </div>
  </div>
</div>
@endsection
