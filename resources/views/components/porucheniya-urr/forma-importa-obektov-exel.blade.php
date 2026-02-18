@props(['order' => null])

<div class="text-center py-4">
    <i class="bi bi-file-earmark-excel fs-1 text-success"></i>
    <h5 class="mt-3">Импорт кадастровых номеров из Excel</h5>
    <p class="text-muted">Загрузите файл Excel со списком кадастровых номеров</p>


    {{-- <form action="{{ route('porucheniya-urr.import') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        --}}
    <form action="" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order['id'] ?? '' }}" id="orderIdForImport">

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="mb-3">
                    <input type="file"
                        class="form-control"
                        name="file"
                        accept=".xlsx,.xls,.csv"
                        required>
                </div>

                <div class="d-flex justify-content-center gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload"></i> Загрузить
                    </button>
                    <a href="" class="btn btn-outline-secondary">
                        <i class="bi bi-download"></i> Скачать шаблон
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
