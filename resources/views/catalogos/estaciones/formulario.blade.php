<div x-data="estacionForm" x-bind="listeners" class="modal fade" id="modal-estacion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-estacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form @submit.prevent="submit($el)">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h1 class="modal-title fs-6" id="modal-estacionLabel" x-text="modal.title"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <template x-if="error">
                        <p class="text-danger fw-bolder" x-text="error"></p>
                    </template>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="codigo" class="form-label fw-bold">Estación:</label>
                            <input
                                type="text"
                                class="form-control fw-bold text-center text-primary"
                                :class="!errors.estacion || 'is-invalid'"
                                :readonly="request.isEdit"
                                id="estacion"
                                name="estacion"
                                x-model="formulario.estacion"
                            >
                            <template x-if="errors.estacion">
                                <div class="invalid-feedback fw-bolder" x-text="errors.estacion[0]"></div>
                            </template>
                        </div>
                        <div class="col-md-6">
                            <label for="descripcion" class="form-label fw-bold">Nombre:</label>
                            <input
                                type="text"
                                class="form-control"
                                :class="!errors.descripcion || 'is-invalid'"
                                id="descripcion"
                                name="descripcion"
                                x-model="formulario.descripcion"
                            >
                            <template x-if="errors.descripcion">
                                <div class="invalid-feedback fw-bolder" x-text="errors.descripcion[0]"></div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-1 justify-content-center">
                    <button type="submit" class="btn btn-success fw-bold" :disabled="request.isLoading">
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
            const modal = new Modal(document.getElementById('modal-estacion'), {});
            
            modal._element.addEventListener('hidden.bs.modal', () => {
                window.dispatchEvent(new CustomEvent('estacion-clear'))
            })

            Alpine.data('estacionForm', () => ({
                modal: {
                    title: ""
                },
                request: {
                    isLoading: false,
                    isEdit: false,
                    url: null
                },
                formulario: {
                    estacion: null,
                    descripcion: null
                },
                errors: [],
                error: null,
                prepareForm(title, url, isEdit) {
                    this.modal.title = title;
                    this.request.isEdit = isEdit;
                    this.request.url = url;
                },
                submit(form) {
                    this.resetErrors();

                    const formData = new FormData(form);

                    if (this.request.isEdit) formData.append('_method', 'PUT')

                    this.request.isLoading = true;

                    axios.post(this.request.url, formData)
                        .then(response => {
                            modal.hide();

                            window.dispatchEvent(new CustomEvent('estacion-success', {
                                detail: {
                                    message: response.data.message ?? "Operación realizada con éxito.",
                                    open_md_partes: !this.request.isEdit,
                                    estacion: response.data.hasOwnProperty('estacion')
                                        ? response.data.estacion
                                        : null
                                }
                            }))
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
                fill(data) {
                    const { estacion, descripcion, estatus } = data;

                    this.formulario = {
                        estacion: estacion,
                        descripcion: descripcion,
                        estatus: estatus
                    };
                },
                reset() {
                    this.formulario = {
                        estacion: null,
                        descripcion: null
                    }
                },
                resetErrors() {
                    this.errors = [];
                    this.error = null;
                },
                listeners: {
                    ['@estacion-create.window'] () {
                        this.prepareForm("Nueva Estación", route('catalogos.estacion.store'), false);
                        
                        modal.show();
                    },
                    ['@estacion-edit.window'] ({ detail }) {
                        const { data } = detail;

                        this.prepareForm( `Editar estación: ${data.estacion}`, route('catalogos.estacion.update', [data.id_estacion]), true);
                        this.fill(data);

                        modal.show();
                    },
                    ['@estacion-clear.window']() {
                        this.resetErrors();
                        this.reset();
                    }
                }
            }))
        })
    </script>
@endpush
