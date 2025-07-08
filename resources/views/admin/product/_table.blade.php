<table class="w-full table-auto">
  <thead>
    <tr class="bg-gray-200 text-gray-700 text-center">
      <th class="px-4 py-2">ID SP</th>
      <th class="px-4 py-2">Tên SP</th>
      <th class="px-4 py-2">Ảnh</th>
      <th class="px-4 py-2">Size</th>
      <th class="px-4 py-2">Màu</th>
      <th class="px-4 py-2">Số lượng</th>
      <th class="px-4 py-2">Trạng thái</th>
      <th class="px-4 py-2">Ngày tạo</th>
      <th class="px-4 py-2">Hành động</th>
    </tr>
  </thead>
  <tbody>
    @forelse($variants as $v)
      <tr class="border-b text-center">
        <td>{{ $v->product->id }}</td>
        <td class="text-left">{{ $v->product->ten }}</td>
        <td>
          @if($v->product->images->isNotEmpty())
            <img src="{{ asset('images/' . $v->product->images->first()->image_path) }}"
                 class="h-12 w-12 object-cover mx-auto rounded" />
          @else
            <span class="text-gray-400">Không có ảnh</span>
          @endif
        </td>
        <td>{{ $v->kichCo->size }}</td>
        <td>{{ $v->mauSac->mausac }}</td>
        <td>{{ $v->sl }}</td>
        <td>
          @if($v->trang_thai == '1')
            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Hiển thị</span>
          @else
            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Ẩn</span>
          @endif
        </td>
        <td>{{ \Carbon\Carbon::parse($v->created_at)->format('d/m/Y') }}</td>
        
        <td class="px-4 py-2 space-x-2">
          <a href="{{ route('admin.variant.edit', $v->id) }}"
             class="px-3 py-1 bg-yellow-400 text-white rounded">Sửa</a>
          <form action="{{ route('admin.variant.destroy', $v->id) }}" method="POST" class="inline-block"
                onsubmit="return confirm('Xác nhận xóa biến thể này?')">
            @csrf @method('DELETE')
            <button class="px-3 py-1 bg-red-500 text-white rounded">Xóa</button>
          </form>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="9" class="py-6 text-center text-gray-500">Chưa có biến thể nào</td>
      </tr>
    @endforelse
  </tbody>
</table>

@if(method_exists($variants, 'links'))
  <div class="mt-4">{{ $variants->links() }}</div>
@endif
