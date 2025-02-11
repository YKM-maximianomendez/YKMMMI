<div x-data="clasificacionfallaForm" x-bind="listeners" class="modal fade" id="modal-clasificacionfalla" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-clasificacionfallaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form @submit.prevent="submit($el)">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h1 class="modal-title fs-6" id="modal-clasificacionfallaLabel" x-text="modal.title"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <template x-if="error">
                        <p class="text-danger fw-bold" x-text="error"></p>
                    </template>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="descripcion" class="form-label fw-bold">Descripción:</label>
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
            const modal = new Modal(document.getElementById('modal-clasificacionfalla'))
            // EVENT: Resetear form
            modal._element.addEventListener('hidden.bs.modal', () => {
                this.dispatchEvent(new CustomEvent('clasificacionfalla-clear'))
            })

            Alpine.data('clasificacionfallaForm', () => ({
                modal: {
                  title: null
                },
                request: {
                    isLoading: false,
                    isEdit: false,
                    url: null
                },
                formulario: {
                    codigo: null,
                    descripcion: null,
                    estatus: true
                },
                errors: [],
                error: null,
                prepareForm(title, url, isEdit) {
                    this.modal.title = title;
                    this.request.url = url;
                    this.request.isEdit = isEdit;
                },
                fill(data) {
                    const { descripcion, estatus } = data;

                    this.formulario = {
                        descripcion: descripcion,
                        estatus: estatus
                    };
                },
                submit(form) {
                    this.resetErrors();

                    const formData = new FormData(form);

                    if (this.request.isEdit) formData.append('_method', 'PUT')

                    this.request.isLoading = true;

                    axios.post(this.request.url, formData)
                        .then(response => {
                            modal.hide();
                            this.$dispatch('clasificacionfalla-success', response.data.message ?? "Operación realizada con éxito.");
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
                reset() {
                    this.formulario = {
                        descripcion: null,
                        estatus: true
                    }
                },
                resetErrors() {
                    this.errors = [];
                    this.error = null;
                },
                listeners: {
                    ['@clasificacionfalla-create.window'] () {
                        this.prepareForm("Nueva Clasificación", route('catalogos.clasificacion-falla.store'), false);
                        modal.show();
                    },
                    ['@clasificacionfalla-edit.window'] ({ detail }) {
                        const { data } = detail;

                        this.prepareForm(`Editar Clasificacion: ${data.descripcion}`, route('catalogos.clasificacion-falla.update', [ data.id_clasificacion ]), true);
                        this.fill(data);

                        modal.show();
                    },
                    ['@clasificacionfalla-clear.window']() {
                        this.resetErrors();
                        this.reset();
                    }
                }
            }))
        })
    </script>
@endpush
