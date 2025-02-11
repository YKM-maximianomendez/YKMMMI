<div x-data="estacionNumerosParteList" x-bind="listeners" class="modal fade" id="modal-estacion-partesasignadas" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-estacion-partesasignadasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h1 class="modal-title fs-6" id="modal-estacion-partesasignadasLabel" x-text="modal.title"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered table-striped" id="partes-asignadas" style="table-layout: fixed">
                    <thead class="table-secondary">
                        <tr>
                            <th scope="col" class="text-center">Número de Parte</th>
                            <th scope="col" class="text-center">Modelo(s)</th>
                            <th scope="col" class="text-center">Estatus</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const modal = new Modal(document.getElementById('modal-estacion-partesasignadas'), {});
        let table = $('#partes-asignadas')

        modal._element.addEventListener('hidden.bs.modal', () => {
            if ($.fn.DataTable.isDataTable(table)) {
                table.DataTable().destroy();
            }
        })

        Alpine.data('estacionNumerosParteList', () => ({
            modal: {
                title: ""
            },
            request: {
                isLoading: false,
                url: null
            },
            createDatatable(id) {
                table.DataTable({
                    ajax: {
                        url: route('catalogos.estacion.show', id)
                    },
                    processing: true,
                    columns: [
                        { data: 'numeroparte' },
                        { data: 'modelos' },
                        { data: 'estatus_desc' },
                    ],
                    columnDefs: [
                        { targets: '_all', className: 'text-center' }
                    ],
                })
            },
            error: null,
            listeners: {
                ['@estacionpartesasignadas-show.window']({detail}) {
                    this.modal.title = "Partes asignadas: " + detail.estacion.estacion;
                    this.createDatatable(detail.estacion.id_estacion);
                    modal.show();
                },
            }
        }))
    })
</script>
@endpush