@extends('layouts.app')

@section('content')
    
    <form to-smart-table='test-smart-table' role='fiters_table' class="mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="kadastroviy_nomer" class="form-control form-control-sm" placeholder="Кадастровый номер">
            </div>
            <div class="col-md-4">
                <input type="text" name="ispolnitel" class="form-control form-control-sm" placeholder="ФИО исполнителя">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-sm w-100">Применить фильтр</button>
            </div>
        </div>
    </form>

    <div class="d-flex gap-2 mb-3">
        <div class="dropdown" to-smart-table='test-smart-table' role='controll_column_visiable'>
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="columnDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                <i class="bi bi-layout-three-columns me-1"></i> Колонки
            </button>
            <div class="spisok-polonok dropdown-menu shadow border-0" aria-labelledby="columnDropdown" >
                <div class="fw-bold small mb-2 border-bottom px-3 py-2">Отображение полей:</div>
                <div id="columnCheckboxes" to-smart-table='test-smart-table' role='controll_column_visiable_list'></div>
                <div class="dropdown-divider"></div>
                <button type="button" class="btn btn-link btn-sm text-decoration-none w-100 text-start" id="resetColumnState">
                    <i class="bi bi-arrow-counterclockwise"></i> Сбросить вид
                </button>
            </div>
        </div>
    </div>

    <div class='mt-2'>
        <div id="test-smart-table"  style='overflow: hidden; width: 100%;'>
        </div>
    </div>
@endsection

@push('scripts')
<script  type="module">
    import { create_smart_table} from '{{ Vite::asset('resources/js/app.js') }}';
    document.addEventListener('DOMContentLoaded', function() {
 
            create_smart_table({
                debug: true,
                height: '80vh',
                id: 'test-smart-table',
                ajaxURL: "{{ route('test') }}",
                columns: [
                    { 
                        title: 'кадастровый номер', 
                        field: 'kadastroviy_nomer', 
                        formatter: (cell) => `<a href="/obekti-nedvizhimosti/${cell.getData().id}/redaktirovat-obekt" class="text-primary fw-bold text-decoration-none">${cell.getData().kadastroviy_nomer || '-'}</a>`
                    },
                    { title: 'ФИО исполнителя', field: 'ispolnitel' },
                    { title: 'Вид работ', field: 'vidi_rabot.nazvanie' },
                ],
                controll_column_visiable: true,
                apply_filters: true,
            });
    });
</script>
@endpush