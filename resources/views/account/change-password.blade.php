@extends('layouts.csthongtin')

@section('title','Đổi Mật Khẩu')

@section('account.content')
<style>

  .ff-form-card {
  background-color: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 2rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  margin-bottom: 2rem;
  
}

/* Divider giữa tiêu đề và body */
.ff-form-divider {
  height: 1px;
  background-color: #e5e7eb;
  margin: 1rem 0 1.5rem;
}

/* Nhóm input */
.ff-form-group {
  margin-bottom: 1.5rem;
}
.ff-form-group label {
  display: block;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}
.ff-form-group input[type="password"] {
  width: 25%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.25rem;
  font-size: 1rem;
  color: #1f2937;
  transition: border-color .2s, box-shadow .2s;
}
.ff-form-group input[type="password"]:focus {
  outline: none;
  border-color: #ef4444;
  box-shadow: 0 0 0 1px rgba(239,68,68,0.5);
}

/* Lỗi */
.ff-error {
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #ef4444;
}

/* Nút hành động */
.ff-form-action {
  margin-top: 2rem;
}
.ff-save-btn {
  background-color: #ef4444;
  color: #ffffff;
  padding: 0.75rem 2rem;
  font-size: 1rem;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  transition: background-color .2s;
}
.ff-save-btn:hover {
  background-color: #dc2626;
}

/* Responsive: trên mobile thu nhỏ padding */
@media (max-width: 640px) {
  .ff-form-card {
    padding: 1.5rem;
  }
}
</style>

<div class="ff-form-card">
  <h2>Đổi Mật Khẩu</h2>
  <div class="ff-form-divider"></div>

  @if(session('success'))
    <div class="mb-4 text-green-600">{{ session('success') }}</div>
  @endif

  <form action="{{ route('profile.update_password') }}" method="POST">
    @csrf @method('PATCH')

    {{-- Mật khẩu mới --}}
    <div class="ff-form-group">
      <label for="new_password">Mật khẩu mới</label>
      <input id="new_password"
             name="new_password"
             type="password"
             class="@error('new_password') ff-input-error @enderror">
      @error('new_password')
        <div class="ff-error">{{ $message }}</div>
      @enderror
    </div>

    {{-- Xác nhận mật khẩu mới --}}
    <div class="ff-form-group">
      <label for="new_password_confirmation">Nhập lại mật khẩu</label>
      <input id="new_password_confirmation"
             name="new_password_confirmation"
             type="password">
    </div>

    <div class="ff-form-action">
      <button type="submit" class="ff-save-btn">
        Cập Nhật Mật Khẩu
      </button>
    </div>
  </form>
</div>
@endsection
