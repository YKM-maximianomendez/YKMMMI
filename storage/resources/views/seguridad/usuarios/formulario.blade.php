<div x-data="usuarioForm" x-bind="listeners" class="modal fade" id="modal-usuario" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-usuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form @submit.prevent="submit($el)">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h1 class="modal-title fs-6" id="modal-usuarioLabel" x-text="modal.title"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <template x-if="error">
                        <p class="text-danger fw-bold" x-text="error"></p>
                    </template>

                    <div class="row mb-3">
                        <label for="numero_nomina" class="col-sm-6 col-form-label fw-bold">Número de Nómina:</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                                <input
                                    type="text"
                                    class="form-control text-center fw-bold"
                                    :class="!errors.numero_nomina || 'is-invalid'"
                                    id="numero_nomina"
                                    name="numero_nomina"
                                    x-model="formulario.numero_nomina"
                                    :readonly="request.isEdit"
                                    x-data="{
                                    init() {
                                        IMask($el, {
                                            mask: '00000'
                                        });
                                    }
                                }">
                            </div>
                            <template x-if="errors.numero_nomina">
                                <div class="invalid-feedback fw-bolder" x-text="errors.numero_nomina[0]"></div>
                            </template>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nombre" class="form-label fw-bold">Nombre Completo:</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-user"></i></div>
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="!errors.nombre || 'is-invalid'"
                                    id="nombre"
                                    name="nombre"
                                    x-model="formulario.nombre"
                                >
                            </div>
                            <template x-if="errors.nombre">
                                <div class="invalid-feedback fw-bolder" x-text="errors.nombre[0]"></div>
                            </template>
                        </div>
                        <div class="col-12">
                            <label for="correo_electronico" class="form-label fw-bold">Correo Electrónico:</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-envelope"></i></div>
                                <input
                                    type="email"
                                    class="form-control"
                                    :class="!errors.correo_electronico || 'is-invalid'"
                                    id="correo_electronico"
                                    name="correo_electronico"
                                    x-model="formulario.correo_electronico"
                                >
                            </div>
                            <template x-if="errors.correo_electronico">
                                <div class="invalid-feedback fw-bolder" x-text="errors.correo_electronico[0]"></div>
                            </template>
                        </div>
                        <div class="col-6">
                            <label for="password" class="form-label fw-bold">Contraseña:</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-lock"></i></div>
                                <input
                                    type="password"
                                    class="form-control"
                                    :class="!errors.password || 'is-invalid'"
                                    id="password"
                                    name="password"
                                    x-model="formulario.password"
                                >
                            </div>
                            <template x-if="errors.password">
                                <div class="invalid-feedback fw-bolder" x-text="errors.password[0]"></div>
                            </template>
                        </div>
                        <div class="col-6">
                        <label for="password" class="form-label fw-bold">Contraseña (confirmar):</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-lock"></i></div>
                                <input
                                    type="password"
                                    class="form-control"
                                    :class="!errors.password_confirmation || 'is-invalid'"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    x-model="formulario.password_confirmation"
                                >
                            </div>
                            <template x-if="errors.password_confirmation">
                                <div class="invalid-feedback fw-bolder" x-text="errors.password_confirmation[0]"></div>
                            </template>
                        </div>
                        <div class="col-8">
                            <label for="id_rol" class="form-label fw-bold">Rol:</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-user-tag"></i></div>
                                <select
                                    id="id_rol"
                                    name="id_rol"
                                    class="form-select"
                                    :class="!errors.id_rol || 'is-invalid'"
                                    x-model="formulario.id_rol"
                                >
                                    <option value="" selected>Seleccionar...</option>
                                    @foreach ($roles as $id => $rol)
                                    <option value="{{ $id }}">{{ $rol }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <template x-if="errors.id_rol">
                                <div class="invalid-feedback fw-bolder" x-text="errors.id_rol[0]"></div>
                            </template>
                        </div>
                        <div class="col-4">
                            <label for="tripulacion" class="form-label fw-bold">Tripulación:</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-users"></i></div>
                                <select
                                    id="tripulacion"
                                    name="tripulacion"
                                    class="form-select"
                                    :class="!errors.tripulacion || 'is-invalid'"
                                    x-model="formulario.tripulacion"
                                >
                                    <option value="" selected>No Aplica</option>
                                    @foreach ($tripulaciones as $id => $tripulacion)
                                    <option value="{{ $tripulacion }}">{{ $tripulacion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <template x-if="errors.tripulacion">
                                <div class="invalid-feedback fw-bolder" x-text="errors.tripulacion[0]"></div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-1 justify-content-center">
                    <button type="submit" class="btn btn-success" :disabled="request.isLoading">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    const successEvent = (message) => new CustomEvent('usuario-success', {
        detail: {
            message: message
        }
    })

    document.addEventListener('alpine:init', () => {
        const modal = new Modal(document.getElementById('modal-usuario'))

        modal._element.addEventListener('hidden.bs.modal', () => {
            this.dispatchEvent(new CustomEvent('usuario-clear'))
        })

        Alpine.data('usuarioForm', () => ({
            modal: {
                title: null
            },
            request: {
                isLoading: false,
                isEdit: false,
                url: null
            },
            formulario: {
                numero_nomina: null,
                nombre: null,
                correo_electronico: null,
                id_rol: null,
                tripulacion: null,
                password: null,
                password_confirmation: null
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

                        window.dispatchEvent(successEvent(response.data.message ?? "Operación realizada con éxito."))
                    })
                    .catch(e => {
                        const {
                            errorMessage,
                            validationErrors
                        } = handleErrors(e);
                        
                        this.error = errorMessage;
                        this.errors = validationErrors;
                    })
                    .finally(() => {
                        this.request.isLoading = false;
                    })
            },
            fill(data) {
                for (let campo in data) {
                    if (this.formulario.hasOwnProperty(campo)) {
                        this.formulario[campo] = data[campo];
                    }
                }
            },
            reset() {
                for (let campo in this.formulario) {
                    if (this.formulario.hasOwnProperty(campo)) {
                        this.formulario[campo] = null;
                    }
                }
            },
            resetErrors() {
                this.errors = [];
                this.error = null;
            },
            listeners: {
                ['@usuario-create.window']() {
                    this.prepareForm("Nuevo Usuario", route("seguridad.usuarios.store"), false);
                    modal.show();
                },
                ['@usuario-edit.window']({
                    detail
                }) {
                    const {
                        data
                    } = detail;

                    this.prepareForm("Editar Usuario: " + data.numero_nomina, route('seguridad.usuarios.update', [data.id]), true);
                    this.fill(data);

                    modal.show();
                },
                ['@usuario-clear.window']() {
                    this.resetErrors();
                    this.reset();
                }
            }
        }))
    })
</script>
@endpush