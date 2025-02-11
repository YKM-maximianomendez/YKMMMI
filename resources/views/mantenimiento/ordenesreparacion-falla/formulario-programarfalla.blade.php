<div x-data="programarOT" x-bind="listeners" class="modal fade" id="modal-programarOT" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-programarOTLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form @submit.prevent="submit($el)">
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h1 class="modal-title fs-6" id="modal-programarOTLabel" x-text="modal.title"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" :disabled="request.isLoading"></button>
                </div>
                <div class="modal-body">
                    <template x-if="error">
                        <p class="text-danger fw-bold" x-text="error"></p>
                    </template>

                    <div class="row g-3 mb-3">
                        <div class="col-md-12 text-center">
                            <div class="btn-group btn-group w-100" role="group" aria-label="Basic radio toggle button group">
                                @foreach($prioridades as $id => $prioridad)
                                    <input value="{{ $id }}" x-model="formulario.id_prioridad" type="radio" class="btn-check" name="id_prioridad" id="prioridad-{{ $id }}" autocomplete="off">
                                    <label class="btn w-100 btn-outline-primary" for="prioridad-{{ $id }}">{{ $prioridad }}</label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="horas" class="form-label fw-bold">Hrs:</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-clock"></i></div>
                                <input 
                                    type="number"
                                    min="0"
                                    name="horas"
                                    class="form-control text-center"
                                    required
                                    placeholder="Horas"
                                    :class="!errors.horas || 'is-invalid'"
                                    x-model="formulario.horas"
                                >
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="fraccion" class="form-label fw-bold">Fracción:</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-clock"></i></div>
                                <select
                                    class="form-select"
                                    id="fraccion"
                                    :class="!errors.fraccion || 'is-invalid'"
                                    name="fraccion"
                                    x-model="formulario.fraccion"
                                    required
                                >
                                    @foreach ($fracciones as $id => $minutos)
                                    <option value="{{ $id }}" @selected($minutos===0)>{{ $minutos }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="id_tecnico_responsable" class="form-label fw-bold">Técnico Responsable:</label>
                            <select 
                                class="form-select"
                                :class="!errors.id_tecnico_responsable || 'is-invalid'"
                                id="id_tecnico_responsable"
                                name="id_tecnico_responsable"
                                x-model="formulario.id_tecnico_responsable"
                            >
                                <option value="" selected>Selecciona...</option>
                                @foreach($tecnicos_reparadores as $id => $nombre)
                                    <option value="{{ $id }}">{{ $nombre }}</option>
                                @endforeach
                            </select>
                            <template x-if="errors.id_tecnico_responsable">
                                <div class="invalid-feedback fw-bolder" x-text="errors.id_tecnico_responsable[0]"></div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-1 justify-content-center">
                    <button style="width: 100px;" type="submit" class="btn btn-success fw-bold" :disabled="request.isLoading">
                        <i class="fas fa-calendar-check"></i> Programar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        document.addEventListener('alpine:init', () => {
            const modal = new Modal(document.getElementById('modal-programarOT'), {});

            modal._element.addEventListener('hidden.bs.modal', () => {
                this.dispatchEvent(new CustomEvent('programar-OT-clear'))
            })

            Alpine.data('programarOT', () => ({
                modal: {
                    title: null
                },
                formulario: {
                    id_prioridad: null,
                    horas: null,
                    fraccion: '0.00',
                    id_tecnico_responsable: null
                },
                request: {
                    isLoading: false,
                    url: null
                },
                error: null,
                errors: [],
                reset() {
                    this.formulario = {
                        id_prioridad: null,
                        horas: null,
                        fraccion: '0.00',
                        id_tecnico_responsable: null
                    }
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
                            this.$dispatch('programar-OT-success', response.data.message ?? "Operación realizada con éxito.")
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
                    ['@programar-OT.window'] ({ detail }) {
                        const { orden } = detail;

                        this.modal.title = `Programar Orden Falla: ${orden.no_falla}`;

                        this.request.url = route('mantenimiento.ordenreparacion-falla.programar', [
                            orden.id_orden,
                            orden.id_orden_falla
                        ])
                        modal.show();
                    },
                    ['@programar-OT-clear.window'] () {
                        this.reset();
                        this.resetErrors();
                    }
                }
            }))
        })
    </script>
@endpush
