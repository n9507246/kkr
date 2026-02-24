@props([
    'id',
    'ajaxUrl',
    'columns' => [],
    'height' => '500px',
    'method' => 'GET',
    'editUrl' => null,
    'deleteUrl' => null,
    'debug' => false
])

<div style="overflow-x:auto;">
    <div id="{{ $id }}"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const defaultMinWidth = 200;
    const defaultMaxWidth = 300;

    let preparedColumns = @json($columns).map(col => ({
        minWidth: defaultMinWidth,
        maxWidth: defaultMaxWidth,
        ...col,
    }));


    const table = new Tabulator("#{{ $id }}", {
        layout: "fitDataStretch",
        height: "{{ $height }}",

        ajaxURL: "{{ $ajaxUrl }}",
        ajaxConfig: "{{ $method }}",

        pagination: true,
        paginationMode: "remote",
        sortMode: "remote",
        filterMode: "remote",

        paginationSize: 10,

        ajaxResponse: function(url, params, response) {
            @if($debug)
                console.log('ajaxResponse ==========> ', {
                data: response.data,
                last_page: response.last_page,
            })
            @endif
            return {
                data: response.data,
                last_page: response.last_page,
            };
        },

        columns: [
            ...preparedColumns,

            @if($editUrl || $deleteUrl)
            {
                title: "Действия",
                field: "actions",
                headerSort: false,
                hozAlign: "center",
                width: 160,
                formatter: function(cell){
                    const row = cell.getRow().getData();
                    let buttons = '';

                    @if($editUrl)
                        buttons += `
                            <a href="{{ $editUrl }}/${row.id}"
                               class="btn btn-outline-warning btn-sm py-0 px-1 me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                        `;
                    @endif

                    @if($deleteUrl)
                        buttons += `
                            <button class="btn btn-outline-danger btn-sm py-0 px-1 delete-btn"
                                    data-id="${row.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        `;
                    @endif

                    return buttons;
                }
            }
            @endif
        ],
    });

    @if($deleteUrl)
    document.getElementById("{{ $id }}").addEventListener("click", function(e){
        const btn = e.target.closest(".delete-btn");
        if(!btn) return;

        const id = btn.dataset.id;

        if(confirm("Удалить запись?")){
            fetch("{{ $deleteUrl }}/" + id, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                },
            })
            .then(res => res.json())
            .then(res => {
                if(res.success){
                    table.replaceData();
                }
            });
        }
    });
    @endif

});
</script>
@endpush
