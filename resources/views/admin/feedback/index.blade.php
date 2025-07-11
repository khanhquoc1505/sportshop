@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Quản lý Đánh giá & Phản hồi</h1>

{{-- 1) Search + Filter --}}
<div class="flex flex-wrap gap-3 items-center mb-6">
  <input id="feedbackSearch" type="text" placeholder="Nhập mã sản phẩm, tên khách hàng" class="px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary w-64" />
  <select id="feedbackRepliedFilter" class="px-4 py-2 border rounded focus:ring-2 focus:ring-primary">
    <option value="">-- Trạng thái trả lời --</option>
    <option value="1">Đã trả lời</option>
    <option value="0">Chưa trả lời</option>
  </select>
</div>

{{-- 2) Bảng feedback --}}
<div class="bg-white rounded shadow overflow-auto">
  <table id="feedbackTable" class="min-w-full table-fixed">
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
        <tr data-id="{{ $f['id'] }}" class="border-b hover:bg-gray-50">
          <td class="px-4 py-2 text-center feedback-id">{{ $f['id'] }}</td>
          <td class="px-4 py-2 feedback-product">{{ $f['product'] }}</td>
          <td class="px-4 py-2 feedback-customer">{{ $f['customer'] }}</td>
          <td class="px-4 py-2 text-center">{{ $f['rating'] }}★</td>
          <td class="px-4 py-2">
            {{ $f['comment'] }}
            @if($f['reply'])
              <div class="mt-1 px-2 py-1 border-l-4 border-blue-400 bg-blue-50 text-blue-800 text-sm rounded reply-box">
                <span class="font-semibold">Phản hồi:</span> <span class="reply-content">{{ $f['reply'] }}</span>
              </div>
            @endif
          </td>
          <td class="px-4 py-2">{{ \Carbon\Carbon::parse($f['created_at'])->format('d/m/Y H:i') }}</td>
          <td class="px-4 py-2 text-center feedback-status" data-replied="{{ $f['is_replied'] ? '1' : '0' }}">
            @if($f['is_replied'])
              <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Đã trả lời</span>
            @else
              <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Chưa trả lời</span>
            @endif
          </td>
          <td class="px-4 py-2 text-center space-x-2">
            @if($f['is_replied'])
              <button onclick="openReplyModal('{{ $f['id'] }}', '{{ addslashes($f['reply']) }}')" class="px-2 py-1 bg-yellow-400 text-white rounded text-sm">Sửa phản hồi</button>
            @else
              <button onclick="openReplyModal('{{ $f['id'] }}', '')" class="px-2 py-1 bg-blue-500 text-white rounded text-sm">Trả lời</button>
            @endif
            <form action="{{ route('admin.feedback.destroy', $f['id']) }}" method="POST" class="inline-block" onsubmit="return confirm('Xóa feedback này?')">
              @csrf @method('DELETE')
              <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded text-sm">Xóa</button>
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

<div class="mt-4">
  {{ $feedbacks->links() }}
</div>

{{-- Modal trả lời/sửa phản hồi --}}
<div id="replyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
  <div class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded shadow-lg w-96">
      <h3 class="text-lg font-semibold mb-4" id="replyModalTitle">Trả lời Feedback</h3>
      <textarea id="replyTextarea" rows="4" class="w-full border rounded px-3 py-2 mb-4 focus:outline-none" placeholder="Viết lời phản hồi..."></textarea>
      <div class="flex justify-end space-x-3">
        <button type="button" onclick="closeReplyModal()" class="px-4 py-2 bg-gray-300 rounded">Hủy</button>
        <button id="replySendBtn" class="px-4 py-2 bg-blue-600 text-white rounded">Gửi</button>
      </div>
    </div>
  </div>
</div>
@endsection

{{-- SweetAlert2 cho session flash message --}}
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

@push('scripts')
<script>
  let currentId = null;
  const modal = document.getElementById('replyModal');
  const textarea = document.getElementById('replyTextarea');
  const sendBtn = document.getElementById('replySendBtn');
  const modalTitle = document.getElementById('replyModalTitle');

  // Search/filter table rows
  (() => {
    const searchInput = document.getElementById('feedbackSearch');
    const repliedSelect = document.getElementById('feedbackRepliedFilter');
    const rows = document.querySelectorAll('#feedbackTable tbody tr');

    function filterRows() {
      const q = searchInput.value.trim().toLowerCase();
      const rep = repliedSelect.value;
      rows.forEach(row => {
        const id = row.querySelector('.feedback-id').textContent.toLowerCase();
        const prod = row.querySelector('.feedback-product').textContent.toLowerCase();
        const cust = row.querySelector('.feedback-customer').textContent.toLowerCase();
        const status = row.querySelector('.feedback-status').getAttribute('data-replied');
        const matchText = id.includes(q) || prod.includes(q) || cust.includes(q);
        const matchStatus = !rep || status === rep;
        row.style.display = (matchText && matchStatus) ? '' : 'none';
      });
    }
    searchInput.addEventListener('input', filterRows);
    repliedSelect.addEventListener('change', filterRows);
  })();

  // Modal functions
  function openReplyModal(id, reply = '') {
    currentId = id;
    textarea.value = reply ? decodeURIComponent(reply.replace(/\+/g, ' ')) : '';
    modalTitle.textContent = reply ? 'Sửa phản hồi' : 'Trả lời Feedback';
    modal.classList.remove('hidden');
  }
  function closeReplyModal() {
    modal.classList.add('hidden');
  }

  // Gửi reply/sửa reply qua AJAX
  sendBtn.onclick = async function (e) {
    e.preventDefault();
    const reply = textarea.value.trim();
    if (!reply) {
      textarea.focus();
      return;
    }
    sendBtn.disabled = true;
    sendBtn.textContent = 'Đang gửi...';
    try {
      const res = await fetch(`/admin/feedback/${currentId}/reply`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({ reply })
      });
      const data = await res.json();
      if (data.success) {
        // Cập nhật lại dòng table (reply và trạng thái)
        const row = document.querySelector(`#feedbackTable tr[data-id="${currentId}"]`);
        if (row) {
          // Cập nhật reply
          let replyBox = row.querySelector('.reply-box');
          if (!replyBox) {
            // Nếu chưa có reply box thì thêm vào
            const commentTd = row.querySelector('td:nth-child(5)');
            replyBox = document.createElement('div');
            replyBox.className = "mt-1 px-2 py-1 border-l-4 border-blue-400 bg-blue-50 text-blue-800 text-sm rounded reply-box";
            replyBox.innerHTML = `<span class="font-semibold">Phản hồi:</span> <span class="reply-content"></span>`;
            commentTd.appendChild(replyBox);
          }
          replyBox.querySelector('.reply-content').textContent = data.reply;

          // Cập nhật trạng thái
          const statusTd = row.querySelector('.feedback-status');
          statusTd.innerHTML = `<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Đã trả lời</span>`;
          statusTd.setAttribute('data-replied', '1');

          // Chuyển nút "Trả lời" thành "Sửa phản hồi" (hoặc đổi màu/nội dung nếu muốn)
          let btn = row.querySelector('button[onclick^="openReplyModal"]');
          if (btn) {
            btn.textContent = "Sửa phản hồi";
            btn.className = "px-2 py-1 bg-yellow-400 text-white rounded text-sm";
            btn.onclick = function() { openReplyModal(currentId, data.reply); }
            btn.style.display = '';
          }
        }
        closeReplyModal();

        // Hiện thông báo SweetAlert2
        Swal.fire({
          icon: 'success',
          title: data.updated ? 'Cập nhật lại phản hồi thành công!' : 'Đã trả lời phản hồi!',
          showConfirmButton: false,
          timer: 2000,
          toast: true,
          position: 'top-end'
        });
      } else {
        alert('Có lỗi khi gửi phản hồi');
      }
    } catch (err) {
      alert('Gửi thất bại. Kiểm tra lại kết nối!');
    }
    sendBtn.disabled = false;
    sendBtn.textContent = 'Gửi';
  };
</script>
@endpush
