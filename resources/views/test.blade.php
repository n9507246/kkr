@extends('layouts.app')

@section('content')
    <div class='mt-2' style="overflow-x:auto;">
        <div id="test-smart-table"></div>
    </div>
@endsection

@push('scripts')
<script  type="module">
     import { create_smart_table, eeeeeeeee } from '{{ Vite::asset('resources/js/app.js') }}';
    document.addEventListener('DOMContentLoaded', function() {
 
            create_smart_table({
                debug: true,
                id: 'test-smart-table',
                columns: [
                    { title: 'ID', field: 'id' },
                    { title: 'Название', field: 'name' },
                    { title: 'Описание', field: 'description' },
                ]
            });
    });
</script>
@endpush