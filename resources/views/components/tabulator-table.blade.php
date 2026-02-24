<!-- TODO: добавить скрол а то поля не видно -->
@props([
    'id',
    'rows' => [],
    'columns' => [],
    'autoColumns' => true,
    'showActions' => false,
    'deleteUrl' => null,
    'editUrl' => null,
])

<div id="{{ $id }}"></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const tableConfig = {
        data: @json($rows),
        layout: "fitColumns",
        pagination: "local",
        paginationSize: 10,
    };

    @if($autoColumns)
        tableConfig.autoColumns = true;
    @endif

    @if(!empty($columns))
        tableConfig.columns = @json($columns);
    @endif

    const table = new Tabulator("#{{ $id }}", tableConfig);

    @if($showActions)
        table.on("tableBuilt", function () {

            // Добавляем колонку Actions один раз
            const actionsColumnExists = table.getColumnDefinitions().some(col => col.field === 'actions');
            if(!actionsColumnExists){
                table.addColumn({
                    title: 'Действия',
                    field: 'actions',
                    hozAlign: "center",
                    width: 160,
                    frozen: true
                });
            }

            // ======= предупреждения один раз =======
            @if(!$deleteUrl)
                console.warn("deleteUrl не указан, кнопка удаления не создана.");
            @endif
            @if(!$editUrl)
                console.warn("editUrl не указан, кнопка редактирования не создана.");
            @endif

            // ======= создаём кнопки для каждой строки =======
            table.getRows().forEach(row => {
                const cell = row.getCell("actions");
                if(cell){
                    cell.getElement().innerHTML = "";

                    // кнопка Редактировать
                    @if($editUrl)
                        const btnEdit = document.createElement("a");
                        btnEdit.href = "{{ $editUrl }}/" + row.getData().id;
                        btnEdit.className = "btn btn-outline-warning btn-sm py-0 px-1";
                        btnEdit.title = "Редактировать";
                        btnEdit.innerHTML = '<i class="bi bi-pencil"></i>';
                        cell.getElement().appendChild(btnEdit);
                    @endif

                    // кнопка Удалить
                    @if($deleteUrl)
                        const btnDelete = document.createElement("button");
                        btnDelete.className = "btn btn-outline-danger btn-sm py-0 px-1";
                        btnDelete.title = "Удалить";
                        btnDelete.innerHTML = '<i class="bi bi-trash"></i>';
                        btnDelete.addEventListener("click", function() {
                            if(confirm("Удалить эту запись?")){
                                fetch("{{ $deleteUrl }}/" + row.getData().id, {
                                    method: "DELETE",
                                    headers: {
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                        "Accept": "application/json",
                                    },
                                })
                                .then(res => res.json())
                                .then(res => {
                                    if(res.success){
                                        table.deleteRow(row);
                                    } else {
                                        alert("Ошибка при удалении");
                                    }
                                });
                            }
                        });
                        cell.getElement().appendChild(btnDelete);
                    @endif
                }
            });

        });
    @endif

});
</script>
@endpush
