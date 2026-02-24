@extends('layouts.app')

@section('content')
<div>
    <x-tabulator-table
        id="users-table"
        ajax-url="{{ route('test.index') }}"
        :columns="[
            ['title' => 'Кададстровый номер', 'field' => 'kadastroviy_nomer'],
            ['title' => 'Исполнитель', 'field' => 'ispolnitel'],
            ['title' => 'Дата оканчания работ','field' => 'data_okonchaniya_rabot'],
            ['title' => 'Вид работ','field' => 'vidi_rabot.nazvanie'],
            ['title' => 'Вид работ','field' => 'kommentariy'],
        ]"
        :debug=true
        {{-- edit-url="{{ route('users.edit', '') }}"
        delete-url="{{ route('users.destroy', '') }}" --}}
    />
</div>
@endsection
