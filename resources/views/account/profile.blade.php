@extends('layouts.csthongtin')
@section('title','Hồ Sơ Của Tôi')
@section('account.content')
 <div class="ff-form-card">
        <h2>Hồ Sơ Của Tôi</h2>
        
        <div class="ff-form-divider"></div>

        <form action="{{ route('profile.update') }}"
              method="POST"
              enctype="multipart/form-data">
          @csrf @method('PATCH')

          <div class="ff-form-inner">
            {{-- CỘT TRÁI: các trường text --}}
            <div class="ff-form-left">
              <div class="ff-form-group">
                <label for="ten_nguoi_dung">Tên đăng nhập</label>
                <input id="ten_nguoi_dung"
                       name="ten_nguoi_dung"
                       type="text"
                       value="{{ old('ten_nguoi_dung',$user->ten_nguoi_dung) }}"
                       class="@error('ten_nguoi_dung') ff-input-error @enderror">
                <small class="ff-form-help">Tên đăng nhập chỉ có thể thay đổi một lần.</small>
                @error('ten_nguoi_dung')
                  <div class="ff-error">{{ $message }}</div>
                @enderror
              </div>

              <div class="ff-form-group">
                <label for="email">Email</label>
                <input id="email"
                       name="email"
                       type="email"
                       value="{{ old('email',$user->email) }}"
                       class="@error('email') ff-input-error @enderror">
                @error('email')
                  <div class="ff-error">{{ $message }}</div>
                @enderror
              </div>

              <div class="ff-form-group">
                <label for="sdt">Số điện thoại</label>
                <input id="sdt"
                       name="sdt"
                       type="text"
                       value="{{ old('sdt',$user->sdt) }}"
                       class="@error('sdt') ff-input-error @enderror">
                @error('sdt')
                  <div class="ff-error">{{ $message }}</div>
                @enderror
              </div>

              <div class="ff-form-group">
                <label for="dia_chi">Địa chỉ</label>
                <input id="dia_chi"
                       name="dia_chi"
                       type="text"
                       value="{{ old('dia_chi',$user->dia_chi) }}"
                       class="@error('dia_chi') ff-input-error @enderror">
                @error('dia_chi')
                  <div class="ff-error">{{ $message }}</div>
                @enderror
              </div>
            </div>

            {{-- CỘT PHẢI: Avatar --}}
            {{-- RIGHT COLUMN: avatar upload --}}
            <div class="ff-form-right">
              <div class="ff-form-group ff-avatar-group text-center">
                @if ($user->avatar)
  <img src="{{ $user->avatar_url }}"
       class="ff-avatar-preview mb-2">
@else
  <div class="ff-avatar-placeholder mb-2">
    <i class="fas fa-user"></i>
  </div>
@endif

<label class="ff-avatar-btn">
  Chọn Ảnh
  <input type="file" name="avatar" accept="image/*" hidden>
</label>

                <small class="ff-form-help block mt-1">
                  Dung lượng tối đa 1 MB — JPEG, PNG
                </small>
                @error('avatar')
                  <div class="ff-error">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          {{-- Nút Lưu --}}
          <div class="ff-form-action">
            <button type="submit" class="ff-save-btn">Lưu</button>
          </div>
        </form>
</div>
@endsection
