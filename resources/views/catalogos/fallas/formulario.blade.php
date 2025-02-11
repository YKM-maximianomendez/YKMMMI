<div x-data="fallaForm" x-bind="listeners" class="modal fade" id="modal-falla" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-fallaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form @submit.prevent="submit($el)">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h1 class="modal-title fs-6" id="modal-fallaLabel" x-text="modal.title"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <template x-if="error">
                        <p class="text-danger fw-bold" x-text="error"></p>
                    </template>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="id_clasificacion" class="form-label fw-bold">Clasificación de Falla:</label>
                            <select 
                                id="id_clasificacion" 
                                class="form-select"
                                :class="!errors.id_clasificacion || 'is-invalid'"
                                name="id_clasificacion"
                                x-model="formulario.id_clasificacion"
                            >
                                <option value="" selected>Selecciona...</option>
                                @foreach($clasificaciones_falla as $clasificacion => $id)
                                    <option value="{{ $clasificacion }}">{{ $id }}</option>
                                @endforeach
                            </select>
                            <template x-if="errors.id_clasificacion">
                                <div class="invalid-feedback fw-bolder" x-text="errors.id_clasificacion[0]"></div>
                            </template>
                        </div>
                        <div class="col-md-12">
                            <label for="descripcion" class="form-label fw-bolder">Descripción:</label>
                            <textarea 
                                class="form-control" 
                                :class="!errors.descripcion || 'is-invalid'"
                                id="descripcion"
                                name="descripcion"
                                rows="3"
                                x-model="formulario.descripcion"
                            >
                            </textarea>
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
            const modal = new Modal(document.getElementById('modal-falla'))

            modal._element.addEventListener('hidden.bs.modal', () => {
                this.dispatchEvent(new CustomEvent('falla-clear'))
            })

            Alpine.data('fallaForm', () => ({
                modal: {
                    title: null
                },
                request: {
                    isLoading: false,
                    isEdit: false,
                    url: null
                },
                formulario: {
                    descripcion: null,
                    id_clasificacion: null,
                    estatus: true
                },
                errors: [],
                error: null,
                prepareForm(title, url, isEdit) {
                    this.modal.title = title;
                    this.request.url = url;
                    this.request.isEdit = isEdit;
                },
                submit(form) {
                    this.resetErrors();

                    const formData = new FormData(form);

                    if (this.request.isEdit) formData.append('_method', 'PUT')

                    this.request.isLoading = true;

                    axios.post(this.request.url, formData)
                        .then(response => {
                            modal.hide();
                            this.$dispatch('falla-success', response.data.message ?? "Operación realizada con éxito.")
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
                fill(data) {
                    const { falla, estatus, id_clasificacion } = data;

                    this.formulario = {
                        descripcion: falla,
                        id_clasificacion: id_clasificacion,
                        estatus: estatus
                    }
                },
                reset() {
                    this.formulario = {
                        descripcion: null,
                        id_clasificacion: null,
                        estatus: true
                    }
                },
                resetErrors() {
                    this.errors = [];
                    this.error = null;
                },
                listeners: {
                    ['@falla-create.window']() {
                        this.prepareForm("Nueva Falla", route("catalogos.falla.store"), false);                        
                        modal.show();
                    },
                    ['@falla-edit.window']({ detail }) {
                        const { data } = detail;

                        this.prepareForm("Editar Falla: " + data.codigo, route('catalogos.falla.update', [data.id_falla]), true);
                        this.fill(data);

                        modal.show();
                    },
                    ['@falla-clear.window']() {
                        this.resetErrors();
                        this.reset();
                    }
                }
            }))
        })
    </script>
@endpush
