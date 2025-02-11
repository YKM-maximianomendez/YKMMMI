<!-- Modal -->
<div class="modal fade" x-data="materialUtilizadoForm" x-bind="listeners" id="modal-materialUtilizado" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-materialUtilizadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h1 class="modal-title fs-5" id="modal-materialUtilizadoLabel" x-text="modal.title"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <template x-if="error">
                    <p class="text-danger fw-bold" x-text="error"></p>
                </template>

                <form class="row g-2 mb-3" @submit.prevent="agregarInsumo($el)" x-ref="form">
                    <div class="col-6">
                        <label for="nombre" class="visually-hidden">Nombre Refacción</label>
                        <div class="input-group">
                            <div class="input-group-text"><i class="fas fa-cogs"></i></div>
                            <input type="text" class="form-control" id="refaccion" name="refaccion" required placeholder="Refacción" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-4">
                        <label for="cantidad" class="visually-hidden">Cantidad</label>
                        <div class="input-group">
                            <div class="input-group-text"><i class="fas fa-sort-numeric-up"></i></div>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" required placeholder="Cantidad" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-outline-primary fw-bold w-100">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                </form>

                <div class="row g-3" >
                    <div class="col-md-12">
                        <table class="table table-sm table-bordered" id="material-utilizado" style="table-layout: fixed">
                            <thead class="table-secondary">
                                <tr>
                                    <th scope="col" class="text-center">Refacción</th>
                                    <th scope="col" class="text-center">Cantidad</th>
                                    <th scope="col" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-1 justify-content-center">
                <button type="button" class="btn btn-success fw-bold" @click="submit" :disabled="request.isLoading">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const modal = new Modal(document.getElementById('modal-materialUtilizado'), {});
        modal._element.addEventListener('hidden.bs.modal', () => {
            this.dispatchEvent(new CustomEvent('kioscoreparaciones-createMaterialutilizado-clear'))    
        })

        const datatable = $('#material-utilizado').DataTable({
            columns: [
                { data: 'refaccion' },
                { 
                    data: 'cantidad',
                    width: '15%'
                },
                { 
                    data: null, 
                    render: () =>  `
                        <span role="button" class="materialutilizado-delete">
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
                { targets: '_all', className: 'text-center' }
            ]
        })

        $('#material-utilizado tbody').on('click', '.materialutilizado-delete', function() { 
            datatable.row($(this).parents('tr')).remove().draw(); 
        });

        Alpine.data('materialUtilizadoForm', () => ({
            request: {
                isLoading: false,
                url: null
            },
            modal: {
                title: null
            },
            formulario: {
                id_orden: null
            },
            prepareForm(title, url) {
                this.modal.title = title;
                this.request.url = url
            },
            errors: [],
            error: null,
            agregarInsumo(form) {
                datatable.rows.add([{
                    refaccion: form.elements['refaccion'].value, 
                    cantidad: form.elements['cantidad'].value, 
                }])
                .draw();

                form.reset();
            },
            get material_utilizado() {
                return datatable.rows().data().toArray();
            },
            resetErrors() {
                this.errors = [];
                this.error = null;
            },
            submit() {
                this.resetErrors();

                const data = {
                    material_utilizado: this.material_utilizado,
                    id_orden: parseInt(this.formulario.id_orden)
                }

                this.request.isLoading = true;

                axios.post(route("mantenimiento.kiosco-reparaciones.store-materialutilizado"), data)
                    .then(response => {
                        modal.hide();
                        this.$dispatch('kioscoreparaciones-success', { message: response.data.message });
                    })
                    .catch(e => {
                        const { errorMessage, validationErrors } = handleErrors(e);
                        
                        this.error = errorMessage;
                        this.errors = validationErrors;
                    })
                    .finally(() => {
                        this.request.isLoading = false;
                    })
            },
            listeners: {
                ['@kioscoreparaciones-createMaterialutilizado.window']({ detail }) {
                    this.formulario.id_orden = detail.id_orden;
                    this.prepareForm(
                        "Reporte de Materiales Utilizados: " + detail.no_falla,
                        ""
                    );
                    modal.show();
                },
                ['@kioscoreparaciones-createMaterialutilizado-clear.window']() {
                    this.resetErrors();
                    this.$refs.form.reset();
                    // Clear datatable...
                    datatable.clear().draw();
                }
            }
        }))
    })
</script>
@endpush