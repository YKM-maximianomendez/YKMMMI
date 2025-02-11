<div x-data="ordenevidenciasForm" x-bind="listeners" class="modal fade" id="modal-evidencias" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-evidenciasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h1 class="modal-title fs-5" id="modal-evidenciasLabel" x-text="modal.title"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> Recuerda que solo se permiten subir <strong>3 archivos</strong> y que no superen cada uno los <strong>5 MB</strong> de tamaño.
                </div>
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="row">
                            <label for="evidencia" class="col-sm-5 col-form-label fw-bold">Selecciona un archivo:</label>
                            <div class="col-sm-7">
                                <input type="file" name="evidencia" id="evidencia" class="form-control" x-ref="file" @change="agregar($el)">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-sm table-bordered w-100" style="table-layout: fixed" id="tbl-evidencias">
                            <thead class="table-secondary">
                                <tr>
                                    <th scope="col">Archivo</th>
                                    <th scope="col">Tamaño</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-1 justify-content-center">
                <button type="button" class="btn btn-secondary fw-bold" @click="asignarEvidencias">
                    <i class="fas fa-arrow-down"></i> Asignar
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const modal = new Modal(document.getElementById('modal-evidencias'), {});

        const datatable = $('#tbl-evidencias').DataTable({
            columns: [
                {
                    data: 'name'
                },
                {
                    data: 'size',
                    render: (data) => `${parseFloat(data).toFixed(2)} MB`,
                    orderable: false,
                    width: '15%'
                },
                {
                    data: null,
                    render: () => `
                            <span role="button" class="evidencia-delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                                </svg>
                            </span>
                        `,
                    orderable: false,
                    width: '15%'
                },
            ],
            columnDefs: [
                { targets: [1, 2], className: 'text-center' }
            ]
        })

        $('#tbl-evidencias tbody').on('click', '.evidencia-delete', function() {
            datatable.row($(this).parents('tr')).remove().draw();
        });

        Alpine.data('ordenevidenciasForm', () => ({
            modal: {
                title: "Subir evidencias"
            },
            request: {
                isLoading: false
            },
            agregar(input) {
                if (this.evidencias.length >= 3) {
                    alert("Solo se permiten subir 3 archivos.");
                    this.resetInput();
                    return;
                }

                const maxSize = 5 * 1024 * 1024;

                const archivo = input.files[0];

                if (archivo.size > maxSize) {
                    alert("El archivo supera los 5 MB. Por favor, selecciona un archivo más pequeño.");
                    this.resetInput();
                    return;
                }

                datatable.rows.add([{
                    file: archivo,
                    size: ((archivo.size / 1024) / 1024),
                    name: archivo.name
                }]).draw();

                this.resetInput();
            },
            resetInput() {
                this.$refs.file.value = "";
            },
            resetDatatables(dt) {
                dt.clear().draw();
            },
            get evidencias() {
                return datatable.rows().data().toArray();
            },
            asignarEvidencias() {
                modal.hide();
                this.$dispatch('ordenevidencias-set', this.evidencias);
                this.resetInput();
            },
            listeners: {
                ['@ordenevidencias-upload.window']({detail}) {
                    this.evidencias = detail;
                    modal.show();
                },
                ['@reset.window']() {
                    if ($.fn.dataTable.isDataTable('#tbl-evidencias')) {
                        this.resetDatatables(datatable)
                    }
                }
            }
        }))
    })
</script>
@endpush