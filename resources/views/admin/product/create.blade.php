@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-8 text-dark">Thêm sản phẩm</h1>

    <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data"
        class="bg-white p-8 rounded-xl shadow space-y-6 border border-gray-300">
        @csrf

        <div class="grid grid-cols-3 gap-6">
            {{-- Tên sản phẩm --}}
            <div>
                <label class="block text-sm font-medium mb-1">Tên sản phẩm</label>
                <input type="text" name="name" class="w-full border px-3 py-2 rounded" placeholder="Nhập tên">
            </div>
            {{-- Hình ảnh --}}
            <div>
                <label class="block text-sm font-medium mb-1">Hình ảnh</label>
                <input type="file" name="image" class="w-full border px-3 py-2 rounded">
            </div>

            {{-- Size --}}
            <div>
                <label class="block text-sm font-medium mb-1">Size</label>
                <select name="size" class="w-full border px-3 py-2 rounded">
                    <option value="">Chọn size</option>
                    <option>S</option>
                    <option>M</option>
                    <option>L</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">

            {{-- Giá bán --}}
            <div>
                <label class="block text-sm font-medium mb-1">Giá bán</label>
                <input type="number" name="price" class="w-full border px-3 py-2 rounded" placeholder="Nhập giá">
            </div>

            {{-- Màu sản phẩm --}}
            <div>
                <label class="block text-sm font-medium mb-1">Màu</label>
                <input type="text" name="color" class="w-full border px-3 py-2 rounded" placeholder="Nhập màu sản phẩm">
            </div>

            {{-- Thương hiệu --}}
            <div>
                <label class="block text-sm font-medium mb-1">Thương hiệu</label>
                <input type="text" name="brand" class="w-full border px-3 py-2 rounded" placeholder="Nhập thương hiệu">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            {{-- Loại sản phẩm --}}
            <div>
                <label class="block text-sm font-medium mb-1">Loại sản phẩm</label>
                <select name="category" class="w-full border px-3 py-2 rounded">
                    <option value="">Chọn loại</option>
                    <option>Áo</option>
                    <option>Quần</option>
                    <option>Phụ kiện</option>
                </select>
            </div>

            {{-- Ngày nhập --}}
            <div>
                <label class="block text-sm font-medium mb-1">Ngày nhập</label>
                <input type="date" name="import_date" class="w-full border px-3 py-2 rounded">
            </div>

            {{-- Số lượng --}}
            <div>
                <label class="block text-sm font-medium mb-1">Số lượng</label>
                <input type="number" name="qty" class="w-full border px-3 py-2 rounded" placeholder="Nhập số lượng">
            </div>
        </div>

        {{-- Mô tả --}}
        <div>
            <label class="block text-sm font-medium mb-1">Mô tả</label>
            <textarea name="description" class="w-full border px-3 py-2 rounded" rows="4"
                placeholder="Nhập mô tả sản phẩm"></textarea>
        </div>

        {{-- Nút Thêm --}}
        <div class="flex space-x-2 mt-6">
            <button type="submit" onclick="return confirm('Xác nhận thêm sản phẩm mới?')"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                Thêm
            </button>
            <a href="{{ route('admin.product.index') }}"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                Hủy
            </a>
        </div>
    </form>
@endsection