@extends('layouts.app')

@section('content')
<div>
    <x-tabulator-table
        id="users-table"
        :rows="$users"
        :columns="[
            ['title' => 'Имя', 'field' => 'name'],
            ['title' => 'Эл. почта', 'field' => 'email'],
        ]"
        :auto-columns="true"
        :show-actions="true"
        {{-- delete-url="{{ route('users.destroy', ['id' => '']) }}" --}}
    />
</div>
@endsection
