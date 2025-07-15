@extends('layouts.csthongtin')

@section('title','Sản Phẩm Yêu Thích')

@section('account.content')
<style>
  /* Container card */
  .ff-form-card {
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    padding: 20px;
    margin-bottom: 20px;
  }

  /* Divider line */
  .ff-form-divider {
    height: 1px;
    background-color: #e5e7eb;
    margin: 16px 0;
  }

  /* Full-width table */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
  }

  /* Table header */
  thead tr {
    background-color: #f3f4f6;
  }
  th {
    padding: 12px 8px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
  }

  /* Table body cells */
  td {
    padding: 12px 8px;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
  }

  /* Row hover */
  tbody tr:hover {
    background-color: #fafafa;
  }

  /* Thumbnail image */
  img {
    display: block;
    width: 64px;
    height: 64px;
    object-fit: cover;
    border-radius: 4px;
  }

  /* Title link */
  a.text-purple-600 {
    color: #7c3aed;
    text-decoration: none;
  }
  a.text-purple-600:hover {
    text-decoration: underline;
  }

  /* Add to cart button */
  button.bg-purple-600 {
    background-color: #7c3aed;
    color: #ffffff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
  }
  button.bg-purple-600:hover {
    opacity: 0.9;
  }

  /* Remove button icon */
  button.text-gray-600 {
    background: none;
    border: none;
    color: #4b5563;
    font-size: 1.2rem;
    cursor: pointer;
  }
  button.text-gray-600:hover {
    color: #e53e3e;
  }
</style>
<div class="ff-form-card">
  <h2>Sản Phẩm Yêu Thích</h2>
  <div class="ff-form-divider"></div>

  @if($favorites->isEmpty())
    <p>Chưa có sản phẩm yêu thích nào.</p>
  @else
    <table>
      <thead>
        <tr>
          <th>Hình ảnh</th>
          <th>Tên sản phẩm</th>
          <th>Giá</th>
          <th>Remove</th>
        </tr>
      </thead>
      <tbody>
        @foreach($favorites as $product)
          <tr>
            <td>
              @if($img = $product->avatarImage)
                <img src="{{ asset('images/' . $img->image_path) }}" alt="{{ $product->ten }}">
              @else
                <div class="bg-gray-200 rounded" style="width:64px;height:64px;"></div>
              @endif
            </td>
            <td>
              <a href="{{ route('product.show', $product->id) }}" class="text-purple-600">
                {{ $product->ten }}
              </a>
            </td>
            <td>
              <span class="font-semibold">
                {{ number_format($product->gia_ban, 0, '.', ',') }}₫
              </span>
            </td>
            <td class="text-center">
              <form action="{{ route('favorites.remove', $product->id) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="text-gray-600">
                  <i class="fas fa-times"></i>
                </button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection
