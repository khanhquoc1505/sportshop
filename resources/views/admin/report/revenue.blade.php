@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-6 text-dark text-center">BÁO CÁO DOANH THU</h1>

    <div class="overflow-auto bg-light p-6 rounded-xl shadow w-full shadow border border-gray-300">
        {{-- Tab chính --}}
        <div class="flex space-x-4 mb-6">
            <button class="px-4 py-2 bg-white rounded-lg shadow text-dark">Thời gian</button>
            <button class="px-4 py-2 bg-white rounded-lg shadow text-dark">Sản phẩm</button>
        </div>

        {{-- Phần lọc theo Thời gian --}}
        <div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                {{-- Loại thời gian --}}
                <div>
                    <label class="block mb-1 font-medium">Loại thời gian</label>
                    <select class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        <option>Báo cáo theo ngày</option>
                        <option>Báo cáo theo tháng</option>
                        <option>Báo cáo theo năm</option>
                    </select>
                </div>

                {{-- Lọc tất cả --}}
                <div>
                    <label class="block mb-1 font-medium">Lọc tất cả</label>
                    <select class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        <option>Tất cả</option>
                        <option>Sản phẩm A</option>
                        <option>Sản phẩm B</option>
                    </select>
                </div>

                {{-- Ngày bắt đầu --}}
                <div>
                    <label class="block mb-1 font-medium">Ngày bắt đầu</label>
                    <input type="date"
                        class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary" />
                </div>

                {{-- Ngày kết thúc --}}
                <div>
                    <label class="block mb-1 font-medium">Ngày kết thúc</label>
                    <input type="date"
                        class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary" />
                </div>
            </div>

            {{-- Search + Export --}}
            <div class="flex flex-col sm:flex-row items-center sm:space-x-4 space-y-4 sm:space-y-0 mb-8">
                <input type="text" placeholder="Tìm kiếm"
                    class="flex-1 border px-4 py-2 rounded-full focus:outline-none focus:ring-2 focus:ring-primary" />
                <div class="space-x-2">
                    <a href="{{ route('admin.revenue.export', request()->query()) }}"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">
                        Xuất Excel
                    </a>
                    <a href="{{ route('admin.revenue.print', request()->query()) }}" target="_blank"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">
                        In báo cáo
                    </a>
                </div>
            </div>

            {{-- Tab con Tổng quan / Chi tiết --}}
            <div class="flex space-x-6 border-b mb-4">
                <button class="pb-2 border-b-2 border-primary text-primary">Tổng quan</button>
                <button class="pb-2 text-dark">Chi tiết</button>
            </div>

            {{-- Khu vực hiển thị báo cáo --}}
            <div class="h-64 bg-white rounded-lg shadow flex items-center justify-center text-gray-400">
                Nội dung báo cáo ở đây
            </div>
        </div>
    </div>
@endsection