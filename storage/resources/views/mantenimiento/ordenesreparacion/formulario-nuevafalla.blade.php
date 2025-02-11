<div x-data="ordenfallaForm" x-bind="listeners" class="modal fade" id="modal-ordenfalla" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-ordenfallaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="submit($el)">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h1 class="modal-title fs-5" id="modal-ordenfallaLabel" x-text="modal.title"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="falla-tab" data-bs-toggle="tab" data-bs-target="#falla-tab-pane" type="button" role="tab" aria-controls="falla-tab-pane" aria-selected="true">Nueva Falla</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="evidencias-tab" data-bs-toggle="tab" data-bs-target="#evidencias-tab-pane" type="button" role="tab" aria-controls="evidencias-tab-pane" aria-selected="false">Adjuntar Evidencias</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="myTabContent">
                        <div class="tab-pane fade show active" id="falla-tab-pane" role="tabpanel" aria-labelledby="falla-tab" tabindex="0">
                            <div class="row g-3">
                                <div class="col-md-6">
                                <label for="id_falla" class="form-label fw-bold">Falla:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-exclamation-triangle"></i></span>
                                    <select
                                        id="id_falla"
                                        name="id_falla"
                                        class="form-select"
                                        x-data="{
                                            init() {
                                                const select = $($el).select2({
                                                    theme: 'bootstrap-5',
                                                    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                                                    placeholder: $( this ).data( 'placeholder' ),
                                                })

                                                select.on('select2:select', (event) => {

                                                });
                                            }
                                        }"
                                        required
                                    >
                                        <option selected value="">Selecciona una falla</option>
                                        @foreach($fallas as $falla)
                                        <option value="{{ $falla['id_falla'] }}">{{ $falla['codigo'] . ' - ' . $falla['falla'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="id_causa" class="form-label fw-bold">Causa Falla:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-tools"></i></span>
                                        <select
                                            id="id_causa"
                                            name="id_causa"
                                            class="form-select"
                                            x-data="{
                                                init() {
                                                    const select = $($el).select2({
                                                        theme: 'bootstrap-5',
                                                        width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                                                        placeholder: $( this ).data( 'placeholder' ),
                                                    })

                                                    select.on('select2:select', (event) => {
                                                        
                                                    });
                                                }
                                            }"
                                            @reset.window="$($el).val(null).trigger('change')"
                                            required
                                        >
                                            <option selected value="">Selecciona una causa de falla</option>
                                            @foreach($causas_fallas as $causa)
                                            <option value="{{ $causa['id_causa'] }}">{{ $causa['codigo'] . ' - ' . $causa['causa'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="obsservaciones" class="form-label fw-bold">Observaciones:</label>
                                    <textarea
                                        name="observaciones"
                                        id="obsservaciones"
                                        rows="3"
                                        class="form-control"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="evidencias-tab-pane" role="tabpanel" aria-labelledby="evidencias-tab" tabindex="0">
                            <div class="row g-3">
                                
                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i> Recuerda que solo se permiten subir <strong>3 archivos</strong> y que no superen cada uno los <strong>5 MB</strong> de tamaño.
                                </div>

                                <div class="row mt-3">
                                    <label for="evidencia" class="col-sm-5 col-form-label fw-bold">Selecciona un archivo:</label>
                                    <div class="col-sm-7">
                                        <input type="file" name="evidencia" id="evidencia" class="form-control" x-ref="file" @change="agregar($el)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-sm table-bordered" id="tbl-evidencias">
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
                </div>
                <div class="modal-footer p-1 justify-content-center">
                    <button type="submit" class="btn btn-success fw-bold" :disabled="request.isProcessing">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const modal = new Modal(document.getElementById('modal-ordenfalla'));

        modal._element.addEventListener('hidden.bs.modal', () => {
            this.dispatchEvent(new CustomEvent('ordenfalla-clear'))
        })

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
            ordering: false,
            columnDefs: [
                { targets: [1, 2], className: 'text-center' }
            ]
        })

        $('#tbl-evidencias tbody').on('click', '.evidencia-delete', function() {
            datatable.row($(this).parents('tr')).remove().draw();
        });

        Alpine.data('ordenfallaForm', () => ({
            modal: {
                title: null
            },
            request: {
                isProcessing: false,
                url: null
            },
            formulario: {
                observaciones: null,
                id_falla: null,
                id_causa: null,
                archivos: []
            },
            id_orden: null,
            error: null,
            errors: [],
            reset() {
                // reset form...
                this.resetInput();
            },
            resetErrors() {
                this.error = null;
                this.errors = [];
            },
            resetInput() {
                this.$refs.file.value = "";
            },
            resetEvidencias() {
                this.formulario.archivos = [];
            },
            agregar(input) {
                if (datatable.data().count() >= 3) {
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
            get evidencias() {
                return datatable.rows().data().toArray();
            },
            submit(form) {
                this.resetErrors();

                const formData = new FormData(form);
                formData.append('id_orden', this.id_orden)
                
                if (this.evidencias.length > 0) {
                    this.evidencias.forEach(file => {
                        formData.append('evidencias[]', file.file)
                    });
                }

                this.request.isProcessing = true;

                axios.post(this.request.url, formData)
                    .then(response => {
                        modal.hide();
                        this.$dispatch('ordenfalla-success')
                    })
                    .catch(e => {
                        const { errorMessage, validationErrors } = handleErrors(e)
                        this.error = errorMessage;
                        this.errors = validationErrors;
                    })
                    .finally(() => {
                        this.request.isProcessing = false;
                    })
            },
            listeners: {
                ['@ordenfalla-create.window']({ detail }) {
                    this.id_orden = detail.id_orden;
                    this.modal.title = `${ detail.no_orden } -  Nueva falla`
                    this.request.url = route('mantenimiento.ordenesreparacion-falla.store')

                    modal.show();
                },
                ['@ordenfalla-clear.window']() {
                    this.reset();
                    this.resetErrors();
                    datatable.clear().draw();
                }
            }
        }))
    })
</script>
@endpush