@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-6 text-dark">Chỉnh sửa sản phẩm</h1>

    <form method="POST" action="{{ route('admin.product.update', $item->id) }}" enctype="multipart/form-data"
        class="bg-white p-6 rounded-xl shadow space-y-4 border border-gray-300">

        @csrf
        @method('PATCH')

        <div>
            <label class="block mb-1">Tên</label>
            <input type="text" name="name" value="{{ old('name', $item->name) }}" class="w-full border px-3 py-2 rounded" />
        </div>

        <div>
            <label class="block mb-1">Hình ảnh</label>
            <input type="file" name="image" class="block" />
            @if($item->image)
                <img src="{{ asset($item->image) }}" class="h-16 mt-2 object-contain" />
            @endif
        </div>

        <div>
            <label class="block mb-1">Thời gian</label>
            <input type="date" name="time" value="{{ old('time', $item->time) }}" class="border px-3 py-2 rounded" />
        </div>

        <div>
            <label class="block mb-1">Số lượng</label>
            <input type="number" name="qty" value="{{ old('qty', $item->qty) }}" class="border px-3 py-2 rounded" />
        </div>

        <div class="flex space-x-2 mt-6">
            <button type="submit" onclick="return confirm('Bạn có chắc muốn lưu thay đổi không?')"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                Lưu
            </button>
            <a href="{{ route('admin.product.index') }}"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                Hủy
            </a>
        </div>
    </form>
@endsection