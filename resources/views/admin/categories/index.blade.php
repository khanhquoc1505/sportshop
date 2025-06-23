@extends('layouts.admin')

@section('content')
    @if (session('success'))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 2000,
                        toast: true,
                        position: 'top-end'
                    });
                });
            </script>
        @endpush
    @endif
    <h1 class="text-3xl font-semibold mb-6 text-dark">Quản Lý Danh Mục</h1>

    {{-- Search & Filter giống User --}}
    <div class="flex items-center space-x-2 mb-4">
        {{-- ô Search --}}
        <input id="categorySearch" type="text" placeholder="Nhập tên danh mục"
            class="px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-primary w-1/3" />
        {{-- Nút Thêm --}}
        <a href="{{ route('admin.categories.create') }}"
            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
            Thêm danh mục
        </a>
    </div>

    {{-- Bảng --}}
    <div class="bg-white rounded-xl shadow p-6 w-full overflow-auto shadow border border-gray-300">
        <table id="categoriesTable" class="w-full table-auto">
            <thead>
                <tr class="bg-gray-200 text-gray-700 text-center">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Tên Danh Mục</th>
                    <th class="px-4 py-2">Trạng Thái</th>
                    <th class="px-4 py-2">Ngày Tạo</th>
                    <th class="px-4 py-2">Ngày Cập Nhật</th>
                    <th class="px-4 py-2">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $c)
                    <tr class="border-b text-center">
                        <td class="px-4 py-2 id">{{ $c['id'] }}</td>
                        <td class="px-4 py-2 name">{{ $c->loai }}</td>
                        <td class="px-4 py-2 status">
                            @if($c['status'])
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Active</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($c['created_at'])->format('Y/m/d') }}
                        </td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($c->updated_at)->format('Y/m/d') }}
                        </td>
                        <td class="px-4 py-2 space-x-2">
                            <a href="{{ route('admin.categories.edit', ['id' => $c['id']]) }}"
                                class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">Sửa</a>
                            <form action="{{ route('admin.categories.destroy', $c['id']) }}" method="POST" class="inline"
                                onsubmit="return confirm('Xác nhận xóa danh mục này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                @if($categories->isEmpty())
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">Chưa có danh mục nào</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const input = document.getElementById('categorySearch');
            const rows = document.querySelectorAll('#categoriesTable tbody tr');

            function stripDiacritics(str) {
                return str.normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase();
            }

            function filterRows() {
                const qNorm = stripDiacritics(input.value.trim());

                rows.forEach(tr => {
                    const name = tr.querySelector('.name').textContent.trim();
                    const nameNorm = stripDiacritics(name);

                    tr.style.display = (!qNorm || nameNorm.includes(qNorm))
                        ? ''
                        : 'none';
                });
            }

            input.addEventListener('input', filterRows);
            filterRows();
        })();
    </script>
@endpush