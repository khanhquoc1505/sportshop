{{-- resources/views/admin/product/create.blade.php --}}
@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-8 text-dark">Thêm sản phẩm</h1>

    <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data"
        class="bg-white p-8 rounded-xl shadow space-y-6 border border-gray-300">
        @csrf

        {{-- ---------- Tên sản phẩm (text input tự do) ---------- --}}
        <div>
            <label class="block text-sm font-medium mb-1">Tên sản phẩm</label>
            <input type="text" id="ten" name="ten" list="productNames" value="{{ old('ten') }}"
                class="w-full border px-3 py-2 rounded" placeholder="Chọn hoặc gõ tên mới" required>
            <datalist id="productNames">
                @foreach($existingNames as $name)
                    <option value="{{ $name }}">
                @endforeach
            </datalist>
        </div>

        <div class="grid grid-cols-3 gap-6">
            {{-- ---------- Hình ảnh ---------- --}}
            <div>
                <label class="block text-sm font-medium mb-1">Hình ảnh</label>
                <input type="file" name="images[]" multiple class="w-full border px-3 py-2 rounded">
            </div>

            {{-- ---------- Size (datalist) ---------- --}}
            <div>
                <label class="block text-sm font-medium mb-1">Size</label>
                <input list="sizes" name="size" value="{{ old('size') }}" class="w-full border px-3 py-2 rounded"
                    placeholder="Chọn hoặc gõ size mới">
                <datalist id="sizes">
                    @foreach($kichCoList as $kc)
                        <option value="{{ $kc->size }}">
                    @endforeach
                </datalist>
            </div>

            {{-- ---------- Màu (datalist) ---------- --}}
            <div>
                <label class="block text-sm font-medium mb-1">Màu</label>
                <input list="colors" name="color" value="{{ old('color') }}" class="w-full border px-3 py-2 rounded"
                    placeholder="Chọn hoặc gõ màu mới">
                <datalist id="colors">
                    @foreach($mausacList as $mau)
                        <option value="{{ $mau->mausac }}">
                    @endforeach
                </datalist>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            {{-- ---------- Giá nhập ---------- --}}
            <div>
                <label class="block text-sm font-medium mb-1">Giá nhập</label>
                <input type="number" name="gia_nhap" id="gia_nhap" value="{{ old('gia_nhap') }}"
                    class="w-full border px-3 py-2 rounded" placeholder="Nhập giá nhập" required>
            </div>

            {{-- ---------- Giá bán ---------- --}}
            <div>
                <label class="block text-sm font-medium mb-1">Giá bán</label>
                <input type="number" name="price" id="gia_ban" value="{{ old('gia_ban') }}"
                    class="w-full border px-3 py-2 rounded" placeholder="Nhập giá bán" required>
            </div>

            {{-- ---------- Giá bán buôn ---------- --}}
            <div>
                <label class="block text-sm font-medium mb-1">Giá bán buôn</label>
                <input type="number" name="gia_buon" id="gia_buon" value="{{ old('gia_buon') }}"
                    class="w-full border px-3 py-2 rounded" placeholder="Nhập giá buôn">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            {{-- ---------- Bộ môn (text tự do) ---------- --}}
            <div>
                <label class="block text-sm font-medium mb-1">Bộ môn</label>
                <input type="text" name="brand" value="{{ old('brand') }}" class="w-full border px-3 py-2 rounded"
                    placeholder="Nhập bộ môn">
            </div>

            {{-- ---------- Loại sản phẩm (datalist) ---------- --}}
            <div>
                <label class="block text-sm font-medium mb-1">Loại sản phẩm</label>
                <input list="categories" name="category" value="{{ old('category') }}"
                    class="w-full border px-3 py-2 rounded" placeholder="Chọn hoặc gõ loại mới">
                <datalist id="categories">
                    @foreach($dsLoai as $loai)
                        <option value="{{ $loai }}">
                    @endforeach
                </datalist>
            </div>

            {{-- ---------- Ngày nhập ---------- --}}
            <div>
                <label class="block text-sm font-medium mb-1">Ngày nhập</label>
                <input type="date" name="import_date" value="{{ old('import_date', now()->format('Y-m-d')) }}"
                    class="w-full border px-3 py-2 rounded" required>
            </div>

            {{-- ---------- Số lượng ---------- --}}
            <div>
                <label class="block text-sm font-medium mb-1">Số lượng</label>
                <input type="number" name="qty" value="{{ old('qty', 1) }}" class="w-full border px-3 py-2 rounded"
                    placeholder="Nhập số lượng" required>
            </div>
        </div>

        <div>
            <input type="hidden" name="trang_thai" value="1">
        </div>

        {{-- ---------- Mô tả ---------- --}}
        <div>
            <label class="block text-sm font-medium mb-1">Mô tả</label>
            <textarea name="description" class="w-full border px-3 py-2 rounded" rows="4"
                placeholder="Nhập mô tả">{{ old('description') }}</textarea>
        </div>

        {{-- ---------- Nút Thêm / Hủy ---------- --}}
        <div class="flex justify-between">
            <a href="{{ route('admin.product.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
                ← Quay lại
            </a>
            <button type="submit" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
                Thêm
            </button>
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('ten');
            const importInput = document.getElementById('gia_nhap');
            const sellInput = document.getElementById('gia_ban');
            const wholesaleInput = document.getElementById('gia_buon');

            if (!nameInput || !importInput || !sellInput) return;

            nameInput.addEventListener('blur', function () {
                const name = this.value.trim();

                if (!name) {
                    importInput.value = '';
                    sellInput.value = '';
                    wholesaleInput.value = '';
                    return;
                }

                fetch("{{ route('admin.product.import-price') }}?name=" + encodeURIComponent(name))
                    .then(res => res.json())
                    .then(json => {
                        importInput.value = json.importPrice ?? '';
                        sellInput.value = json.sellPrice ?? '';
                        wholesaleInput.value = json.wholesalePrice ?? '';
                    })
                    .catch(err => console.error('Fetch lỗi:', err));
            });
        });
    </script>
@endpush