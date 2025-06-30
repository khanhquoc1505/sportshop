{{-- resources/views/components/keep.blade.php --}}
@props(['data' => []])

@php
$fields = [
    'product_id','size','color_id',
    'image_path','color_name',
    'quantity','order_voucher','mode',
];
@endphp

@foreach ($fields as $f)
    @php $v = $data[$f] ?? request($f); @endphp
    @isset($v)
        <input type="hidden" name="{{ $f }}" value="{{ $v }}">
    @endisset
@endforeach
