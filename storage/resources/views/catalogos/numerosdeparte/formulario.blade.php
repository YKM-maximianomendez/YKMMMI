<!-- Modal -->
<div x-data="numeroparteForm" x-bind="listeners" class="modal fade" id="modal-numeroparte" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-numeroparteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form @submit.prevent="submit($el)">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h1 class="modal-title fs-6" id="modal-numeroparteLabel" x-text="modal.title"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <template x-if="error">
                        <p class="text-danger fw-bolder" x-text="error"></p>
                    </template>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="numeroparte" class="form-label fw-bold">Número de Parte:</label>
                            <input
                                type="text"
                                class="form-control text-center fw-bold text-primary"
                                :class="!errors.numeroparte || 'is-invalid'"
                                :readonly="request.isEdit"
                                id="numeroparte"
                                name="numeroparte"
                                x-model="formulario.numeroparte"
                            >
                            <template x-if="errors.numeroparte">
                                <div class="invalid-feedback fw-bolder" x-text="errors.numeroparte[0]"></div>
                            </template>
                        </div>
                        <div class="col-md-6">
                            <label for="nombre" class="form-label fw-bold">Descripción:</label>
                            <input
                                type="text"
                                class="form-control"
                                :class="!errors.nombre || 'is-invalid'"
                                id="nombre"
                                name="nombre"
                                x-model="formulario.nombre"
                            >
                            <template x-if="errors.nombre">
                                <div class="invalid-feedback fw-bolder" x-text="errors.nombre[0]"></div>
                            </template>
                        </div>
                        <div class="col-md-12">
                            <label for="id_estacion" class="form-label fw-bold">Estación (Prensa):</label>
                            <select
                                id="id_estacion"
                                name="id_estacion"
                                class="w-100 form-select"
                                :class="!errors.id_estacion || 'is-invalid'"
                                style="width: 100%"
                            >
                                <option value="">Sin asignar</option>
                                @foreach($estaciones as $id => $estacion)
                                    <option value="{{ $id }}">{{ $estacion }}</option>
                                @endforeach
                            </select>

                            <template x-if="errors.id_estacion">
                                <div class="invalid-feedback fw-bolder" x-text="errors.id_estacion[0]"></div>
                            </template>
                        </div>
                        <div class="col-md-12">
                            <label for="id_modelo" class="form-label fw-bold">Modelo(s):</label>
                            <select
                                id="id_modelo"
                                name="id_modelo[]"
                                class="w-100"
                                :class="!errors.id_modelo || 'is-invalid'"
                                multiple="multiple"
                                style="width: 100%"
                                x-data="{
                                    init() {
                                        $($el).select2({
                                            theme: 'bootstrap-5',
                                            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                                            dropdownParent: $('#modal-numeroparte')
                                        })
                                    }
                                }"
                            >
                                @foreach($modelos as $id => $modelo)
                                    <option value="{{ $id }}">{{ $modelo }}</option>
                                @endforeach
                            </select>

                            <template x-if="errors.id_modelo">
                                <div class="invalid-feedback fw-bolder" x-text="errors.id_modelo[0]"></div>
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
        const modal = new Modal(document.getElementById('modal-numeroparte'), {});

        modal._element.addEventListener('hidden.bs.modal', () => this.dispatchEvent(new CustomEvent('numeroparte-clear')))

        Alpine.data('numeroparteForm', () => ({
            modal: {
                title: null
            },
            request: {
                isLoading: false,
                isEdit: false,
                url: null
            },
            formulario: {
                numeroparte: null,
                nombre: null,
                estatus: true
            },
            errors: [],
            error: null,
            async submit(form) {
                this.resetErrors();

                const formdata = new FormData(form);

                if (this.request.isEdit) formdata.append('_method', 'PUT')

                this.request.isLoading = true;

                axios.post(this.request.url, formdata)
                    .then(response => {
                        modal.hide();
                        this.$dispatch('numeroparte-success', response.data.message);
                    })
                    .catch(e => {
                        const { validationErrors, errorMessage } = handleErrors(e);
                        this.errors = validationErrors;
                        this.error = errorMessage;
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
                const { id_modelos } = data;
                const opt_modelos = id_modelos === null ? [] : id_modelos.split(',').map(op => op.trim());

                this.formulario = {
                    numeroparte: data.numeroparte,
                    nombre: data.nombre,
                    estatus: data.estatus
                }

                $('[name="id_modelo[]"]').val(opt_modelos).trigger('change');;
            },
            reset() {
                this.formulario = {
                    numeroparte: null,
                    nombre: null,
                    estatus: true
                }
            },
            resetErrors() {
                this.errors = [];
                this.error = null
            },
            resetSelections() {
                $('#id_modelo').val([]).trigger('change');
            },
            listeners: {
                ['@numeroparte-create.window'] ({ detail }) {
                    this.prepareForm("Nuevo Número de Parte", route('catalogos.numeroparte.store'), false);
                    modal.show();
                },
                ['@numeroparte-edit.window'] ({ detail }) {
                    const { data } = detail;

                    this.prepareForm(`Editar Número de Parte: ${data.numeroparte}`, route('catalogos.numeroparte.update', [data.id_numeroparte]), true);
                    this.fill(data);
                    modal.show();
                },
                ['@numeroparte-clear.window']() {
                    this.resetErrors();
                    this.reset();
                    this.resetSelections();
                }
            }
        }))
    })
</script>
@endpush
