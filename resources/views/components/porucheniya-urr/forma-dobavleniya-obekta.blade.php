@props(['order' => null])

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Список кадастровых номеров</h5>
        <button type="button" class="btn btn-success btn-sm" id="addCadastralRow">
            <i class="bi bi-plus-circle"></i> Добавить строку
        </button>
    </div>

    {{-- <form id="cadastralForm" method="POST" action="{{ $order ? route('obekty-nedvizhimosti.update', $order->id) : route('obekty-nedvizhimosti.store') }}"> --}}
    <form id="cadastralForm" method="POST" action="">
        @csrf
        @if($order)
            @method('PUT')
        @endif

        <div class="table-responsive">
            <table class="table table-bordered" id="cadastralTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 30%">Кадастровый номер</th>
                        <th style="width: 20%">Тип объекта</th>
                        <th style="width: 20%">Тип работ</th>
                        <th style="width: 15%">Исполнитель</th>
                        <th style="width: 5%">Площадь</th>
                        <th style="width: 5%"></th>
                    </tr>
                </thead>
                <tbody id="cadastralRows">
                    @if($order && $order->cadastralItems && count($order->cadastralItems) > 0)
                        @foreach($order->cadastralItems as $index => $item)
                            <tr class="cadastral-row" data-id="{{ $item->id ?? '' }}">
                                <td class="row-number">{{ $loop->iteration }}</td>
                                <td>
                                    <input type="text"
                                           name="cadastral_items[{{ $index }}][cadastral_number]"
                                           class="form-control form-control-sm"
                                           value="{{ $item->cadastral_number ?? '' }}"
                                           placeholder="Введите кадастровый номер"
                                           required>
                                    <input type="hidden" name="cadastral_items[{{ $index }}][id]" value="{{ $item->id ?? '' }}">
                                </td>
                                <td>
                                    <select name="cadastral_items[{{ $index }}][object_type]" class="form-select form-select-sm" required>
                                        <option value="">Выберите тип</option>
                                        <option value="земельный участок" {{ isset($item->object_type) && $item->object_type == 'земельный участок' ? 'selected' : '' }}>Земельный участок</option>
                                        <option value="здание" {{ isset($item->object_type) && $item->object_type == 'здание' ? 'selected' : '' }}>Здание</option>
                                        <option value="сооружение" {{ isset($item->object_type) && $item->object_type == 'сооружение' ? 'selected' : '' }}>Сооружение</option>
                                        <option value="помещение" {{ isset($item->object_type) && $item->object_type == 'помещение' ? 'selected' : '' }}>Помещение</option>
                                        <option value="машино-место" {{ isset($item->object_type) && $item->object_type == 'машино-место' ? 'selected' : '' }}>Машино-место</option>
                                        <option value="единый недвижимый комплекс" {{ isset($item->object_type) && $item->object_type == 'единый недвижимый комплекс' ? 'selected' : '' }}>Единый недвижимый комплекс</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="cadastral_items[{{ $index }}][work_type]" class="form-select form-select-sm" required>
                                        <option value="">Выберите тип работ</option>
                                        <option value="межевание" {{ isset($item->work_type) && $item->work_type == 'межевание' ? 'selected' : '' }}>Межевание</option>
                                        <option value="технический план" {{ isset($item->work_type) && $item->work_type == 'технический план' ? 'selected' : '' }}>Технический план</option>
                                        <option value="акт обследования" {{ isset($item->work_type) && $item->work_type == 'акт обследования' ? 'selected' : '' }}>Акт обследования</option>
                                        <option value="карта-план" {{ isset($item->work_type) && $item->work_type == 'карта-план' ? 'selected' : '' }}>Карта-план</option>
                                        <option value="комплексные работы" {{ isset($item->work_type) && $item->work_type == 'комплексные работы' ? 'selected' : '' }}>Комплексные работы</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text"
                                           name="cadastral_items[{{ $index }}][executor]"
                                           class="form-control form-control-sm"
                                           value="{{ $item->executor ?? '' }}"
                                           placeholder="ФИО исполнителя">
                                </td>
                                <td>
                                    <input type="number"
                                           name="cadastral_items[{{ $index }}][area]"
                                           class="form-control form-control-sm"
                                           value="{{ $item->area ?? '' }}"
                                           placeholder="кв.м"
                                           step="0.01"
                                           min="0">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-row" title="Удалить">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </form>

    <div class="d-flex justify-content-between mt-3">
        <button type="button" class="btn btn-secondary" onclick="document.querySelector('#main-tab').click()">
            <i class="bi bi-arrow-left"></i> Назад
        </button>
        <button type="button" class="btn btn-success" id="saveCadastralItems">
            <i class="bi bi-save"></i> Сохранить кадастровые номера
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cadastralRows = document.getElementById('cadastralRows');
    const addButton = document.getElementById('addCadastralRow');
    const saveButton = document.getElementById('saveCadastralItems');
    const itemsCountSpan = document.getElementById('itemsCount');

    // Функция для переиндексации всех строк
    function reindexRows() {
        const rows = cadastralRows.querySelectorAll('tr');

        rows.forEach((row, index) => {
            // Обновляем номер строки
            const rowNumber = row.querySelector('.row-number');
            if (rowNumber) {
                rowNumber.textContent = index + 1;
            }

            // Обновляем индексы в name атрибутах всех полей ввода и select
            const inputs = row.querySelectorAll('input, select');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/cadastral_items\[\d+\]/, `cadastral_items[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });

        updateItemsCount();
    }

    // Функция для обновления счетчика
    function updateItemsCount() {
        if (itemsCountSpan) {
            const count = cadastralRows.querySelectorAll('tr').length;
            itemsCountSpan.textContent = count;
        }
    }

    // Функция для создания новой строки
    function createNewRow() {
        const rowCount = cadastralRows.querySelectorAll('tr').length;

        // Создаем элементы через DOM API для надежности
        const tr = document.createElement('tr');
        tr.className = 'cadastral-row';

        // Номер строки
        const tdNumber = document.createElement('td');
        tdNumber.className = 'row-number';
        tdNumber.textContent = rowCount + 1;
        tr.appendChild(tdNumber);

        // Кадастровый номер
        const tdCadastral = document.createElement('td');
        const inputCadastral = document.createElement('input');
        inputCadastral.type = 'text';
        inputCadastral.name = `cadastral_items[${rowCount}][cadastral_number]`;
        inputCadastral.className = 'form-control form-control-sm';
        inputCadastral.placeholder = 'Введите кадастровый номер';
        inputCadastral.required = true;
        tdCadastral.appendChild(inputCadastral);

        // Скрытое поле для ID
        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = `cadastral_items[${rowCount}][id]`;
        inputId.value = '';
        tdCadastral.appendChild(inputId);

        tr.appendChild(tdCadastral);

        // Тип объекта
        const tdObjectType = document.createElement('td');
        const selectObjectType = document.createElement('select');
        selectObjectType.name = `cadastral_items[${rowCount}][object_type]`;
        selectObjectType.className = 'form-select form-select-sm';
        selectObjectType.required = true;

        const options = [
            { value: '', text: 'Выберите тип' },
            { value: 'земельный участок', text: 'Земельный участок' },
            { value: 'здание', text: 'Здание' },
            { value: 'сооружение', text: 'Сооружение' },
            { value: 'помещение', text: 'Помещение' },
            { value: 'машино-место', text: 'Машино-место' },
            { value: 'единый недвижимый комплекс', text: 'Единый недвижимый комплекс' }
        ];

        options.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.text;
            selectObjectType.appendChild(option);
        });

        tdObjectType.appendChild(selectObjectType);
        tr.appendChild(tdObjectType);

        // Тип работ
        const tdWorkType = document.createElement('td');
        const selectWorkType = document.createElement('select');
        selectWorkType.name = `cadastral_items[${rowCount}][work_type]`;
        selectWorkType.className = 'form-select form-select-sm';
        selectWorkType.required = true;

        const workOptions = [
            { value: '', text: 'Выберите тип работ' },
            { value: 'межевание', text: 'Межевание' },
            { value: 'технический план', text: 'Технический план' },
            { value: 'акт обследования', text: 'Акт обследования' },
            { value: 'карта-план', text: 'Карта-план' },
            { value: 'комплексные работы', text: 'Комплексные работы' }
        ];

        workOptions.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.text;
            selectWorkType.appendChild(option);
        });

        tdWorkType.appendChild(selectWorkType);
        tr.appendChild(tdWorkType);

        // Исполнитель
        const tdExecutor = document.createElement('td');
        const inputExecutor = document.createElement('input');
        inputExecutor.type = 'text';
        inputExecutor.name = `cadastral_items[${rowCount}][executor]`;
        inputExecutor.className = 'form-control form-control-sm';
        inputExecutor.placeholder = 'ФИО исполнителя';
        tdExecutor.appendChild(inputExecutor);
        tr.appendChild(tdExecutor);

        // Площадь
        const tdArea = document.createElement('td');
        const inputArea = document.createElement('input');
        inputArea.type = 'number';
        inputArea.name = `cadastral_items[${rowCount}][area]`;
        inputArea.className = 'form-control form-control-sm';
        inputArea.placeholder = 'кв.м';
        inputArea.step = '0.01';
        inputArea.min = '0';
        tdArea.appendChild(inputArea);
        tr.appendChild(tdArea);

        // Кнопка удаления
        const tdAction = document.createElement('td');
        tdAction.className = 'text-center';
        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'btn btn-danger btn-sm remove-row';
        deleteButton.title = 'Удалить';
        deleteButton.innerHTML = '<i class="bi bi-trash"></i>';
        tdAction.appendChild(deleteButton);
        tr.appendChild(tdAction);

        return tr;
    }

    // Добавление новой строки
    addButton.addEventListener('click', function() {
        const newRow = createNewRow();
        cadastralRows.appendChild(newRow);
        updateItemsCount();
    });

    // Удаление строки (делегирование событий)
    cadastralRows.addEventListener('click', function(e) {
        const deleteButton = e.target.closest('.remove-row');
        if (deleteButton) {
            const row = deleteButton.closest('tr');
            const rowCount = cadastralRows.querySelectorAll('tr').length;

            if (rowCount > 1) {
                row.remove();
                reindexRows();
            } else {
                if (confirm('Вы уверены, что хотите удалить последнюю строку?')) {
                    row.remove();
                    reindexRows();
                }
            }
        }
    });

    // Сохранение формы
    saveButton.addEventListener('click', function() {
        // Валидация
        let isValid = true;
        const requiredFields = cadastralRows.querySelectorAll('input[required], select[required]');

        requiredFields.forEach(field => {
            if (!field.value) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            alert('Пожалуйста, заполните все обязательные поля');
            return;
        }

        // Подготовка данных для отправки
        const form = document.getElementById('cadastralForm');
        const formData = new FormData(form);

        // Отправка через fetch
        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Данные успешно сохранены');
                updateItemsCount();
            }
        })
        .catch(error => {
            alert('Ошибка при сохранении: ' + error.message);
        });
    });

    // Инициализация счетчика
    updateItemsCount();

    // Добавление первой строки, если таблица пуста
    if (cadastralRows.querySelectorAll('tr').length === 0) {
        addButton.click();
    }
});
</script>
@endpush
