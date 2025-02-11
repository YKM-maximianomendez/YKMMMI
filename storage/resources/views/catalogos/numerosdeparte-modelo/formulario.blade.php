<div x-data="numeropartemodeloForm" x-bind="listeners" class="modal fade" id="modal-numeropartemodelo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-numeropartemodeloLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form @submit.prevent="submit($el)">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h1 class="modal-title fs-6" id="modal-numeropartemodeloLabel" x-text="modal.title"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <template x-if="error">
                        <p class="text-danger fw-bold" x-text="error"></p>
                    </template>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="codigo" class="form-label fw-bold">Código:</label>
                            <input
                                type="text"
                                class="form-control"
                                :class="!errors.modelo || 'is-invalid'"
                                :readonly="request.isEdit"
                                id="modelo"
                                name="modelo"
                                x-model="formulario.modelo"
                            >
                            <template x-if="errors.modelo">
                                <div class="invalid-feedback fw-bolder" x-text="errors.modelo[0]"></div>
                            </template>
                        </div>
                        <div class="col-md-6">
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
        const modal = new Modal(document.getElementById('modal-numeropartemodelo'), {});

        // EVENT: Resetear form
        modal._element.addEventListener('hidden.bs.modal', () => {
            window.dispatchEvent(new CustomEvent('numeropartemodelo-clear'))
        })

        Alpine.data('numeropartemodeloForm', () => ({
            modal: {
                title: null
            },
            request: {
                isLoading: false,
                isEdit: false,
                url: null
            },
            formulario: {
                modelo: null,
                descripcion: null,
                estatus: true
            },
            errors: [],
            error: null,
            submit(form) {
                this.resetErrors();

                const formData = new FormData(form);

                if (this.request.isEdit) formData.append('_method', 'PUT')

                this.request.isLoading = true;

                axios.post(this.request.url, formData)
                    .then(response => {
                        modal.hide();
                        this.$dispatch('numeropartemodelo-success', response.data.message ?? "Operación realizada con éxito.")
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
            prepareForm(title, url, isEdit) {
                this.modal.title = title;
                this.request.url = url;
                this.request.isEdit = isEdit;
            },
            fill(data) {
                const { modelo, descripcion, estatus } = data;

                this.formulario = {
                    modelo: modelo,
                    descripcion: descripcion,
                    estatus: estatus
                };
            },
            reset() {
                this.formulario = {
                    modelo: null,
                    descripcion: null,
                    estatus: true
                }
            },
            resetErrors() {
                this.errors = [];
                this.error = null;
            },
            listeners: {
                ['@numeropartemodelo-create.window']() {
                    this.prepareForm("Nuevo Modelo", route('catalogos.numeroparte-modelo.store'), false);
                    modal.show();
                },
                ['@numeropartemodelo-edit.window']({ detail }) {
                    const { data } = detail;

                    this.prepareForm(`Editar Modelo: ${data.modelo}`, route('catalogos.numeroparte-modelo.update', [data.id_modelo]), true);
                    this.fill(data);
                    modal.show();
                },
                ['@numeropartemodelo-clear.window']() {
                    this.resetErrors();
                    this.reset();
                }
            }
        }))
    })
</script>
@endpush
