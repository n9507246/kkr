@extends('layouts.app')

@section('content')
    <div class="d-flex gap-2 mb-3">
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="columnDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                <i class="bi bi-layout-three-columns me-1"></i> Колонки
            </button>
            <div class="spisok-polonok dropdown-menu shadow border-0" aria-labelledby="columnDropdown" >
                <div class="fw-bold small mb-2 border-bottom px-3 py-2">Отображение полей:</div>
                <div id="columnCheckboxes"></div>
                <div class="dropdown-divider"></div>
                <button type="button" class="btn btn-link btn-sm text-decoration-none w-100 text-start" id="resetColumnState">
                    <i class="bi bi-arrow-counterclockwise"></i> Сбросить вид
                </button>
            </div>
        </div>

    </div>
    <div class='mt-2' style="overflow-x:auto;">
        <div id="test-smart-table"></div>
    </div>
@endsection

@push('scripts')
<script  type="module">
     import { create_smart_table} from '{{ Vite::asset('resources/js/app.js') }}';
    document.addEventListener('DOMContentLoaded', function() {
 
            create_smart_table({
                debug: true,
                id: 'test-smart-table',
                ajaxURL: "{{ route('test') }}",
                columns: [
                    { 
                        title: 'кадастровый номер', 
                        field: 'kadastroviy_nomer', 
                        formatter: (cell) => {
                            const d = cell.getData();
                            return `<a href="/obekti-nedvizhimosti/${d.id}/redaktirovat-obekt" class="text-primary fw-bold text-decoration-none">${d.kadastroviy_nomer || '-'}</a>`;
                        } 
                    },
                    { title: 'ФИО исполнителя', field: 'ispolnitel' },
                    { title: 'Вид работ', field: 'vidi_rabot.nazvanie' },
                ],
                // height: '90hv'
                controll_column_visiable: true,
            });
    });
</script>
@endpush