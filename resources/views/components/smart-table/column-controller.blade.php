@props([
    'id' => null,  // ID таблицы
    'btnControll' => null,  // слот для кнопки открытия выпадающего меню
    'dropDownMenu' => null, // слот для выпадающего меню (legacy)
    'dropdownMenu' => null, // слот для выпадающего меню
    'dropdownMenuClass' => null, // классы для дефолтного выпадающего меню
    'dropdownMenuStyle' => null, // inline-стили для дефолтного выпадающего меню
])


<div {{ $attributes->class(['dropdown']) }}>
    
    @if(!$btnControll)
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="columnDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside">
            <i class="bi bi-layout-three-columns me-1"></i> Колонки
            <span id="hiddenColumnsCount" class="hidden-count-badge" style="display:none;">0</span>
        </button>
    @else
        {{ $btnControll }}
    @endif

    @php
        $menuSlot = $dropdownMenu ?? $dropDownMenu;
    @endphp

    @if(!$menuSlot)
        <div
            @class(['spisok-kolonok', 'dropdown-menu', 'shadow', 'border-0', $dropdownMenuClass])
            @if($dropdownMenuStyle) style="{{ $dropdownMenuStyle }}" @endif
            aria-labelledby="columnDropdown"
        >
            <div class="fw-bold small mb-2 border-bottom px-3 py-2">Отображение полей:</div>
            <div role="controll_column_visiable" to-smart-table="{{ $id }}"></div>
            <div class="dropdown-divider"></div>
            <button type="button" class="btn btn-link btn-sm text-decoration-none w-100 text-start" to-smart-table="{{ $id }}"  role="reset_column_visibility">
                <i class="bi bi-arrow-counterclockwise"></i> Сбросить вид
            </button>
        </div>
    @else
        {{ $menuSlot }} 
    @endif
</div>
