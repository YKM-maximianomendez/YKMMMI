<div x-data="cerrarOTForm" x-bind="listeners" class="modal fade" id="modal-cerrarOT" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-cerrarOTLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form @submit.prevent="submit($el)">
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h1 class="modal-title fs-5" id="modal-cerrarOTLabel" x-text="modal.title"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <template x-if="error">
                        <div class="alert alert-danger" role="alert" x-text="error"></div>
                    </template>

                    <div class="row g-2">
                        <div class="col-6">
                            <label for="fechaInicio" class="form-label fw-bold">Fecha Reparación Inicial</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                                <input
                                    type="datetime-local" 
                                    class="form-control" 
                                    id="fecha_inicio_rep"
                                    name="fecha_inicio_rep"
                                    x-model="formulario.fecha_inicio_rep"
                                    required
                                >
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="fechaFin" class="form-label fw-bold">Fecha Reparación Fin</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                                <input 
                                    type="datetime-local" 
                                    class="form-control" 
                                    id="fecha_fin_rep"
                                    name="fecha_fin_rep"
                                    x-model="formulario.fecha_fin_rep"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-1 justify-content-center">
                    <button type="submit" class="btn btn-success fw-bold" :disabled="request.isLoading">
                        <i class="fas fa-check-circle"></i> Cerrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
    <script type="text/javascript">
        document.addEventListener('alpine:init', () => {
            const modal = new Modal(document.getElementById('modal-cerrarOT'), {});

            modal._element.addEventListener('hidden.bs.modal', () => {
                this.dispatchEvent(new CustomEvent('cerrar-OT-clear'))
            })

            Alpine.data('cerrarOTForm', () => ({
                modal: {
                    title: null
                },
                request: {
                    isLoading: false
                },
                error: null,
                errors: [],
                id_orden: null,
                formulario: {
                    fecha_inicio_rep: null,
                    fecha_fin_rep: null
                },
                prepareForm(title, url, isEdit) {
                    this.modal.title = title;
                    this.request.url = url;
                },
                fill(data) {
                    // this.formulario = {
                    //     fecha_inicio_rep: data.f_inicio_reparacion,
                    //     fecha_fin_rep: data.f_fin_reparacion
                    // }
                },
                reset() {
                    this.formulario = {
                        fecha_inicio_rep: null,
                        fecha_fin_rep: null
                    },
                    this.id_orden = null;
                },
                resetErrors() {
                    this.errors = [];
                    this.error = null;
                },
                submit(form) {
                    this.resetErrors();

                    const formData = new FormData(form);

                    this.request.isLoading = true;

                    axios.post(this.request.url, formData)
                        .then(response => {
                            modal.hide();

                            this.$dispatch('cerrar-ot-success', {
                                message: "Orden de trabajo cerrada exitosamente.",
                                file: response.data.file
                            })
                        })
                        .catch(e => {
                            const { errorMessage, validationErrors } = handleErrors(e)
                            this.error = errorMessage;
                            this.errors = validationErrors;
                        })
                        .finally(() => {
                            this.request.isLoading = false;
                        })
                },
                listeners: {
                    ['@cerrar-OT.window']({ detail }) {
                        const { orden } = detail;

                        this.id_orden = orden.id_orden;
                        this.prepareForm(
                            "Cierre de Orden: " + orden.no_orden,
                            route('mantenimiento.ordenreparacion.cerrar', [orden.id_orden])
                        );
                        this.fill(orden);
                        
                        modal.show();
                    },
                    ['@cerrar-OT-clear.window']() {
                        this.resetErrors();
                        this.reset();
                    }
                }
            }))
        })
    </script>
@endpush