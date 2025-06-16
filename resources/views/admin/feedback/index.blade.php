@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-4">Quản lý Đánh giá & Phản hồi</h1>

  {{-- 1) Search + Filter – bỏ form submit, gán id để JS bắt --}}
  <div class="flex flex-wrap gap-3 items-center mb-6">
    <input id="feedbackSearch"
           type="text"
           placeholder="Tìm kiếm ID, sản phẩm, khách..."
           class="px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary w-64"/>

    <select id="feedbackRepliedFilter"
            class="px-4 py-2 border rounded focus:ring-2 focus:ring-primary">
      <option value="">-- Trạng thái trả lời --</option>
      <option value="1">Đã trả lời</option>
      <option value="0">Chưa trả lời</option>
    </select>
  </div>

  {{-- 2) Bảng feedback --}}
  <div class="bg-white rounded shadow overflow-auto">
    <table id="feedbackTable" class="w-full table-auto">
      <thead class="bg-gray-100 text-gray-700">
        <tr>
          <th class="px-4 py-2">ID</th>
          <th class="px-4 py-2">Sản phẩm</th>
          <th class="px-4 py-2">Khách hàng</th>
          <th class="px-4 py-2">Đánh giá</th>
          <th class="px-4 py-2">Nội dung</th>
          <th class="px-4 py-2">Ngày tạo</th>
          <th class="px-4 py-2">Trạng thái</th>
          <th class="px-4 py-2">Hành động</th>
        </tr>
      </thead>
      <tbody>
        @forelse($feedbacks as $f)
          <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-2 text-center feedback-id">{{ $f['id'] }}</td>
            <td class="px-4 py-2 feedback-product">{{ $f['product'] }}</td>
            <td class="px-4 py-2 feedback-customer">{{ $f['customer'] }}</td>
            <td class="px-4 py-2 text-center">{{ $f['rating'] }}★</td>
            <td class="px-4 py-2">{{ $f['comment'] }}</td>
            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($f['created_at'])->format('d/m/Y H:i') }}</td>
            <td class="px-4 py-2 text-center feedback-status"
                data-replied="{{ $f['is_replied'] ? '1' : '0' }}">
              @if($f['is_replied'])
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Đã trả lời</span>
              @else
                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Chưa trả lời</span>
              @endif
            </td>
            <td class="px-4 py-2 text-center space-x-2">
              @if(!$f['is_replied'])
                <button onclick="openReplyModal('{{ $f['id'] }}')"
                        class="px-2 py-1 bg-blue-500 text-white rounded text-sm">
                  Trả lời
                </button>
              @endif
              <form action="{{ route('admin.feedback.destroy', $f['id']) }}"
                    method="POST" class="inline-block"
                    onsubmit="return confirm('Xóa feedback này?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="px-2 py-1 bg-red-500 text-white rounded text-sm">
                  Xóa
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="py-6 text-center text-gray-500">
              Chưa có feedback nào
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Phân trang (vẫn giữ server‐side) --}}
  <div class="mt-4">
    {{ $feedbacks->links() }}
  </div>

  {{-- Modal trả lời (giữ nguyên) --}}
  <div id="replyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <form id="replyForm" method="POST" class="bg-white p-6 rounded shadow-lg w-96">
      @csrf
      <h3 class="mb-4 text-lg font-semibold">Trả lời Feedback</h3>
      <textarea name="reply" rows="4"
                class="w-full border rounded px-3 py-2 focus:outline-none"></textarea>
      <div class="mt-4 text-right">
        <button type="button"
                onclick="closeReplyModal()"
                class="px-4 py-2 mr-2 bg-gray-300 rounded">Hủy</button>
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded">Gửi</button>
      </div>
    </form>
  </div>

@endsection

@push('scripts')
<script>
  (() => {
    const searchInput    = document.getElementById('feedbackSearch');
    const repliedSelect  = document.getElementById('feedbackRepliedFilter');
    const rows           = document.querySelectorAll('#feedbackTable tbody tr');

    function filterRows() {
      const q   = searchInput.value.trim().toLowerCase();
      const rep = repliedSelect.value;
      rows.forEach(row => {
        const id       = row.querySelector('.feedback-id').textContent.toLowerCase();
        const prod     = row.querySelector('.feedback-product').textContent.toLowerCase();
        const cust     = row.querySelector('.feedback-customer').textContent.toLowerCase();
        const status   = row.querySelector('.feedback-status').getAttribute('data-replied');

        const matchText   = id.includes(q) || prod.includes(q) || cust.includes(q);
        const matchStatus = !rep || status === rep;

        row.style.display = (matchText && matchStatus) ? '' : 'none';
      });
    }

    searchInput.addEventListener('input', filterRows);
    repliedSelect.addEventListener('change', filterRows);
  })();

  function openReplyModal(id) {
    const modal = document.getElementById('replyModal');
    const form  = document.getElementById('replyForm');
    form.action = `/admin/feedback/${id}/reply`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }
  function closeReplyModal() {
    document.getElementById('replyModal')
            .classList.replace('flex','hidden');
  }
</script>
@endpush
