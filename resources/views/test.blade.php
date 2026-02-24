@extends('layouts.app')

@section('content')
<div>
    <x-tabulator-table
        id="users-table"
        ajax-url="{{ route('test') }}"
        edit-url="{{ route('test') }}"
        delete-url="{{ route('test') }}"
        :columns="[
            ['title' => 'Кададстровый номер', 'field' => 'kadastroviy_nomer', formatter: (cell) => {
                    const d = cell.getData();
                    return `<a href="/obekti-nedvizhimosti/${d.id}/redaktirovat-obekt" class="text-primary fw-bold text-decoration-none">${d.kadastroviy_nomer || '-'}</a>`;
                }],
            ['title' => 'Исполнитель', 'field' => 'ispolnitel'],
            ['title' => 'Дата оканчания работ','field' => 'data_okonchaniya_rabot'],
            ['title' => 'Вид работ','field' => 'vidi_rabot.nazvanie'],
            ['title' => 'Вид работ','field' => 'kommentariy'],
        ]"
        {{-- :debug=true --}}
    />
</div>
@endsection
