@extends('layouts.app')
@section('styles')
<style>
    .intermitente {
        animation: intermitente 2s infinite;
    }

    @keyframes intermitente {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0;
        }
    }

    .icon-thick {
        stroke-width: 0.7;
        /* Ajusta el valor según el grosor deseado */
        stroke: currentColor;
        /* Usa el color actual del elemento */
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12" x-data="ordenesProgramadasList" x-bind="listeners">
            <div class="alert alert-light text-primary border-secondary d-flex justify-content-between py-1 pe-1 align-items-center" role="alert">
                <div>Kiosco de Reparaciones</div>
                <div>
                    <button type="button" class="btn btn-outline-secondary" id="reload"  @click="consultar_ordenes">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
                        </svg>
                    </button>
                </div>
            </div>

            <template x-if="error">
                <p class="text-danger fw-bold" x-text="error"></p>
            </template>

            <template x-if="ordenes.length == 0">
                <p class="text-center text-secondary">No hay datos...</p>
            </template>

            <template x-if="ordenes.length>0 && !error">
                <div class="mt-2">
                    <div class="row justify-content-center row-cols-1 row-cols-md-3 g-4">
                        <template x-for="(orden, index) in ordenes">
                            <div class="col">
                                <div
                                    class="card"
                                    :class="{
                                    'border-danger': orden.falla_prioridad === 'ALTA',
                                    'border-warning': orden.falla_prioridad === 'MEDIA',
                                    'border-primary': orden.falla_prioridad === 'BAJA',
                                    'border-secondary': orden.falla_prioridad === 'SIN ASIGNAR'
                                }">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between" style="height: 22px;">
                                            <div>
                                                <button class="btn btn-light" @click="$dispatch('kioscoreparaciones-resumen', orden)">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                            <div>
                                                <template x-if="orden.falla_num_reparaciones_pendientes > 0">
                                                    <button class="btn btn-outline-danger intermitente" @click="$dispatch('kioscoreparaciones-reparacionespendientes', orden)">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>

                                        <h5 class="card-title text-center mb-0">
                                            <strong x-text="orden.no_falla"></strong>
                                        </h5>

                                        <p x-text="orden.falla_general" class="mb-0 text-center"></p>

                                        <div class="d-flex justify-content-between">
                                            <span class="text-center w-100">
                                                <small>Tiempo total reparaciones:</small>
                                                <br>
                                                <small class="fw-bold text-primary" x-text="parseFloat(orden.falla_tiempo_hh_real_total).toFixed(2) + ' hr(s)'"></small>
                                            </span>
                                            <span class="text-center w-100">
                                                <small>Reparaciones realizadas:</small>
                                                <br>
                                                <small class="fw-bold text-primary" x-text="orden.falla_num_reparaciones"></small>
                                            </span>
                                        </div>
                                        <hr class="my-1">
                                        <p class="mb-0 text-center fs-6" x-text="orden.falla_tecnico_responsable"></p>
                                    </div>
                                    <div
                                        class="card-footer p-1"
                                        :class="{
                                        'border-danger': orden.falla_prioridad === 'ALTA',
                                        'border-warning': orden.falla_prioridad === 'MEDIA',
                                        'border-primary': orden.falla_prioridad === 'BAJA',
                                        'border-secondary': orden.falla_prioridad === 'SIN ASIGNAR'
                                    }">
                                        <div class="btn-group d-flex mb-0" role="group" aria-label="Basic example">
                                            <button
                                                type="button"
                                                class="w-100 btn"
                                                :class="{
                                                'btn-danger': orden.falla_prioridad === 'ALTA',
                                                'btn-warning': orden.falla_prioridad === 'MEDIA',
                                                'btn-primary': orden.falla_prioridad === 'BAJA',
                                                'btn-secondary': orden.falla_prioridad === 'SIN ASIGNAR'
                                            }"
                                                title="Reportar Actividades"
                                                @click="$dispatch('kioscoreparaciones-createactividades', orden)">
                                                <i class="fas fa-tasks"></i> Reparaciones
                                            </button>
                                            <button
                                                type="button"
                                                class="w-100 btn"
                                                :class="{
                                                'btn-danger': orden.falla_prioridad === 'ALTA',
                                                'btn-warning': orden.falla_prioridad === 'MEDIA',
                                                'btn-primary': orden.falla_prioridad === 'BAJA',
                                                'btn-secondary': orden.falla_prioridad === 'SIN ASIGNAR'
                                            }"
                                                title="Reportar Evidencias"
                                                @click="$dispatch('kioscoreparaciones-createevidencias', orden)">
                                                <i class="fas fa-paperclip"></i> Evidencias
                                            </button>
                                            <button
                                                type="button"
                                                class="w-100 btn"
                                                :class="{
                                                'btn-danger': orden.falla_prioridad === 'ALTA',
                                                'btn-warning': orden.falla_prioridad === 'MEDIA',
                                                'btn-primary': orden.falla_prioridad === 'BAJA',
                                                'btn-secondary': orden.falla_prioridad === 'SIN ASIGNAR'
                                            }"
                                                title="Reportar Material Utilizado"
                                                @click="$dispatch('kioscoreparaciones-createMaterialutilizado', orden)">
                                                <i class="fas fa-tools"></i> Materiales U
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@include('mantenimiento.kiosco-reparaciones.partials.formulario-actividadesreparacion')
@include('mantenimiento.kiosco-reparaciones.partials.formulario-materialutilizado')
@include('mantenimiento.kiosco-reparaciones.partials.formulario-evidencias')
@include('mantenimiento.kiosco-reparaciones.partials.formulario-reparacionespendientes')
@include('mantenimiento.kiosco-reparaciones.partials.formulario-resumen')
@endsection
@section('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        Alpine.data('ordenesProgramadasList', () => ({
            ordenes: [],
            querying_ordenesfallas: false,
            error: null,
            consultar_ordenes() {
                this.querying_ordenesfallas = true;

                Swal.fire({
                    title: 'Cargando...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                axios.get(route('mantenimiento.ordenesprogramacion-fallas.programadas'))
                    .then(response => {
                        Swal.close();
                        this.ordenes = response.data;
                    })
                    .catch(e => {
                        Swal.close();
                        const { errorMessage, validationErrors } = handleErrors(e);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Ocurrió un error',
                            text: errorMessage
                        });
                    })
                    .finally(() => {
                        this.querying_ordenesfallas = false;
                    })
            },
            init() {
                this.consultar_ordenes();
            },
            listeners: {
                ['@kioscoreparaciones-success.window']({detail}) {
                    Swal.fire({
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500,
                        text: detail.message || "Operación realizada con éxito",
                        didClose: () => {
                            this.consultar_ordenes();
                        }
                    });
                }
            }
        }))
    })
</script>
@endsection