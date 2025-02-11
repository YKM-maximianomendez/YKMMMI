@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9" x-data="ordentrabajoForm" x-bind="listeners">
            <div class="row justify-content-between align-items-end mb-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text border-secondary" id="basic-addon1">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control border-secondary" @keydown.enter="buscar_folio($el.value)" @reset.window="$($el).val(null)" placeholder="Buscar por folio..." aria-label="Buscar por folio..." aria-describedby="basic-addon1">
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit($el)" :action="request.url" x-ref="form">
                <div class="card border-secondary">
                    <div class="card-header border-secondary pe-1 py-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <span x-text="title"></span>
                            <div>
                                <button type="submit" style="width: 90px;" :disabled="request.isProcessing" class="btn btn-success" title="Guardar">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                                <button type="button" style="width: 90px;" :disabled="request.isProcessing" class="btn btn-secondary" @click="resetAction" title="Resetear">
                                    <i class="fas fa-redo-alt"></i> Resetear
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <template x-if="error">
                            <p class="text-danger fw-bold" x-text="error"></p>
                        </template>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="text-center">
                                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                        <input value="1" type="radio" class="btn-check" name="id_tipoatencion" id="at-prensa" autocomplete="off">
                                        <label class="btn btn-outline-primary fw-bold" for="at-prensa" style="width: 125px">Prensa</label>

                                        <input value="2" type="radio" class="btn-check" name="id_tipoatencion" id="at-taller" autocomplete="off">
                                        <label class="btn btn-outline-primary fw-bold" for="at-taller" style="width: 125px">Taller</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-0">
                                <hr>
                            </div>
                        </div>
                        <div class="row g-3 justify-content-center">
                            <div class="col-md-3">
                                <label for="fecha_requiere_prod" class="form-label fw-bold">Fecha Requiere Prod:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input
                                        type="date"
                                        class="form-control"
                                        id="fecha_requiere_prod"
                                        name="fecha_requiere_prod"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="id_estacion" class="form-label fw-bold">Estación:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-train"></i></span>
                                    <select
                                        id="id_estacion"
                                        name="id_estacion"
                                        class="form-select"
                                        x-data="{
                                                init() {
                                                    const select = $($el).select2({
                                                        theme: 'bootstrap-5',
                                                        width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                                                        placeholder: $( this ).data( 'placeholder' ),
                                                    })
                                                }
                                            }"
                                        @reset.window="$($el).val(null).trigger('change')"
                                        required
                                    >
                                        <option selected value="">Selecciona una estación</option>
                                        @foreach($estaciones as $id => $estacion)
                                        <option value="{{ $id }}">{{ $estacion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="id_numeroparte" class="form-label fw-bold">Número de Parte:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-cogs"></i></span>
                                    <select
                                        id="id_numeroparte"
                                        name="id_numeroparte"
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
                                        required>
                                        <option selected value="">Selecciona un número de parte</option>
                                        @foreach($numerosdeparte as $id => $numeroparte)
                                        <option value="{{ $id }}">{{ $numeroparte }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 justify-content-center mt-3">
                            <div class="col-md-3">
                                <label for="operacion" class="form-label fw-bold">Operación:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tasks"></i></span>
                                    <input
                                        type="text"
                                        class="form-control text-center"
                                        id="operacion"
                                        name="operacion"
                                        required
                                        x-data="{
                                            init() {
                                                IMask(
                                                    $el,
                                                    {
                                                        mask: '0/0',
                                                        lazy: false,
                                                        overwrite: 'shift',
                                                    }
                                                )
                                            }
                                        }">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="piezas_terminadas" class="form-label fw-bold">Piezas Terminadas:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                                    <input
                                        type="number"
                                        min="0"
                                        class="form-control"
                                        id="piezas_terminadas"
                                        name="piezas_terminadas">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="piezas_requeridas" class="form-label fw-bold">Piezas Requeridas:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-sort-numeric-down"></i></span>
                                    <input
                                        type="number"
                                        min="0"
                                        class="form-control"
                                        id="piezas_requeridas"
                                        name="piezas_requeridas">
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 justify-content-center">
                            <div class="col-md-12">
                                <hr class="custom-hr mb-0">
                            </div>
                            <div class="col-md-5">
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
                                        @reset.window="$($el).val(null).trigger('change')"
                                        required
                                    >
                                        <option selected value="">Selecciona una falla</option>
                                        @foreach($fallas as $falla)
                                        <option value="{{ $falla['id_falla'] }}">{{ $falla['codigo'] . ' - ' . $falla['falla'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
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
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-12">
                                <hr class="custom-hr mb-0">
                            </div>
                            <div class="col-md-12">
                                <label for="observaciones" class="form-label fw-bold">Observaciones:</label>
                                <textarea class="form-control" id="observaciones" rows="3" name="observaciones" @reset.window="$($el).val(null)"></textarea>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-md-12 text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" style="width: 150px;" :disabled="request.isProcessing || request.isEdit" class="btn btn-warning" @click="abrir_modal_evidencias">
                                        <i class="fas fa-upload"></i> Subir Evidencias
                                    </button>
                                </div>

                                <p class="pt-1 mb-0 text-primary text-decoration-underline" x-text="archivos_adjuntos.length + ' archivo(s) adjuntados.'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('mantenimiento.ordenesreparacion.formulario-evidencias')
@endsection
@section('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const text_initial = "Nueva orden de reparación";

        Alpine.data('ordentrabajoForm', () => ({
            request: {
                isProcessing: false,
                url: route('mantenimiento.ordenesreparacion.store'),
                isEdit: false
            },
            title: text_initial,
            archivos_adjuntos: [],
            error: null,
            errors: [],
            reset() {
                if (this.request.isEdit) {
                    this.request.isEdit = false;
                    this.request.url = route('mantenimiento.ordenesreparacion.store');
                    this.title = text_initial;
                }

                this.archivos_adjuntos = [];
                this.$refs.form.reset();
                this.resetErrors();

                this.$dispatch('reset');
            },
            resetErrors() {
                this.error = null;
                this.errors = [];
            },
            resetAction() {
                Swal.fire({
                    title: "Resetear formulario",
                    text: "¿Deseas resetar el formulario?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si",
                    cancelButtonText: "No, Cancelar",
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.reset();
                    }
                });
            },
            abrir_modal_evidencias() {
                this.$dispatch('ordenevidencias-upload', this.archivos_adjuntos)
            },
            submit(form) {
                this.resetErrors()

                const formData = new FormData(form);

                if (this.request.isEdit == true) formData.append('_method', 'PUT')

                if (this.archivos_adjuntos.length > 0) {
                    this.archivos_adjuntos.forEach(file => {
                        formData.append('evidencias[]', file.file)
                    });
                }

                this.request.isProcessing = true;

                axios.post(form.action, formData)
                    .then(response => {
                        Swal.fire({
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500,
                            text: response.data.message || "Operación realizada con éxito",
                            didClose: () => {
                                this.reset();
                            }
                        })
                    })
                    .catch(e => {
                        const {
                            errorMessage,
                            validationErrors
                        } = handleErrors(e)
                        this.error = errorMessage;
                        this.errors = validationErrors;
                    })
                    .finally(() => {
                        this.request.isProcessing = false
                    })
            },
            set_orden(data) {
                const orden = data.orden;

                this.request.url = route('mantenimiento.ordenesreparacion.update', orden.id_orden)
                this.request.isEdit = true;
                this.title = "Actualizar orden de reparación: " + orden.no_orden;

                this.$refs.form.elements['id_tipoatencion'].forEach(opt => {
                    if (opt.value === orden.orden_id_tipoatencion) {
                        opt.checked = true;
                    }
                })

                $(this.$refs.form.elements['id_falla']).val(orden.falla_id_falla).trigger('change');
                $(this.$refs.form.elements['operacion']).val(orden.orden_operacion);
                $(this.$refs.form.elements['piezas_requeridas']).val(orden.orden_pzas_requeridas);
                $(this.$refs.form.elements['piezas_terminadas']).val(orden.orden_pzas_terminadas);
                $(this.$refs.form.elements['id_causa']).val(orden.falla_id_causa).trigger('change');
                $(this.$refs.form.elements['observaciones']).val(orden.falla_observaciones);
                $(this.$refs.form.elements['id_numeroparte']).val(orden.orden_id_numeroparte).trigger('change');
                $(this.$refs.form.elements['id_estacion']).val(orden.orden_id_estacion).trigger('change');
                $(this.$refs.form.elements['fecha_requiere_prod']).val(orden.orden_fecha_requiere_prod);
                $(this.$refs.form.elements['id_tipoatencion']).val(orden.orden_id_tipoatencion).trigger('change');
            },
            buscar_folio(folio) {
                this.resetErrors();

                if (folio === null || folio === '' || folio === undefined) {
                    return false;
                }

                this.request.isProcessing = true;

                axios.get(route('mantenimiento.ordenesreparacion.edit', folio))
                    .then(response => {
                        this.set_orden(response.data)
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
                        this.request.isProcessing = false;
                    })
            },
            listeners: {
                ['@ordenevidencias-set.window']({ detail }) {
                    this.archivos_adjuntos = detail;
                }
            }
        }))
    })
</script>
@endsection