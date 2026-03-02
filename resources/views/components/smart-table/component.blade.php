@props([
    'id' => null, // ID для таблицы, если не передан, будет использован id_table
])

<div class="">
    <div class="">
        {{ $controlPanel ?? '' }}
    </div>
    <div id="{{ $id }}" class=""></div>
</div>

@push('styles')
    <style>
       
    </style>
@endpush
