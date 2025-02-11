<!-- Modal -->
<div class="modal fade" x-data="actividadesForm" x-bind="listeners" id="modal-actividades" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-actividadesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h1 class="modal-title fs-5" id="modal-actividadesLabel" x-text="modal.title"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <template x-if="error">
                    <p class="text-danger fw-bold" x-text="error"></p>
                </template>

                <form class="row g-2 mb-3" @submit.prevent="agregarActividad($el)" x-ref="form">
                    <div class="col-5">
                        <label for="actividad" class="visually-hidden">Actividad de Reparación</label>
                        <div class="input-group">
                            <div class="input-group-text"><i class="fas fa-tools"></i></div>
                            <select
                                id="id_act_reparacion"
                                name="id_act_reparacion"
                                required
                                x-data="{
                                    init() {
                                        select = $($el).select2({
                                            theme: 'bootstrap-5',
                                            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                                            placeholder: $( this ).data( 'placeholder' ),
                                            dropdownParent: $('#modal-actividades'),
                                        })
                                    }
                                }"
                            >
                                <option value="" selected>Selecciona...</option>
                                @foreach($actividades_reparacion as $actividad)
                                <option value="{{ $actividad->id_act_reparacion }}">{{ $actividad->codigo . ' - ' . $actividad->actividad_reparacion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <label for="horas" class="visually-hidden">Hora</label>
                        <div class="input-group">
                            <div class="input-group-text"><i class="fas fa-clock"></i></div>
                            <input 
                                type="number"
                                min="0"
                                name="horas"
                                class="form-control text-center"
                                required
                                placeholder="Horas"
                            >
                        </div>
                    </div>
                    <div class="col-2">
                        <label for="feaccion" class="visually-hidden">Fraccion</label>
                        <div class="input-group">
                            <div class="input-group-text"><i class="fas fa-clock"></i></div>
                            <select
                                class="form-select"
                                name="fraccion"
                                id="fraccion"
                                required
                            >
                                @foreach ($fracciones as $id => $minutos)
                                <option value="{{ $id }}" @selected($minutos===0)>{{ $minutos }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <label for="fecha" class="visually-hidden">Fecha</label>
                        <div class="input-group">
                            <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                            <input type="date" class="form-control text-center" id="fecha" name="fecha" value="{{ now()->toDateString() }}">
                        </div>
                    </div>
                    <div class="col-1">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm table-bordered" id="actividades">
                            <thead class="table-secondary">
                                <tr>
                                    <th scope="col" class="text-center">Actividad Reparación</th>
                                    <th scope="col" class="text-center">Tiempo HH</th>
                                    <th scope="col" class="text-center">Fecha</th>
                                    <th scope="col" class="text-center">Observaciones</th>
                                    <th scope="col" class="text-center">Completada</th>
                                    <th scope="col" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-1 justify-content-center">
                <button type="button" class="btn btn-success fw-bold" @click="submit" :disabled="request.isLoading">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const modal = new Modal(document.getElementById('modal-actividades'), {});

        modal._element.addEventListener('hidden.bs.modal', () => {
            this.dispatchEvent(new CustomEvent('kioscoreparaciones-actividades-clear'))
        })

        const datatable = $('#actividades').DataTable({
            columns: [{
                    data: 'actividad.actividad'
                },
                {
                    data: 'tiempo_hh',
                    width: '10%'
                },
                {
                    data: 'fecha',
                    width: '10%'
                },
                {
                    data: null,
                    render: () => `
                        <textarea
                            cols="30"
                            rows="2"
                            class="form-control observaciones"
                        ></textarea>
                    `,
                    orderable: false
                },
                {
                    data: null,
                    render: () => `
                        <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input act-terminada" type="checkbox" value="" id="flexCheckDefault" checked style="width: 1.5rem; height: 1.5rem;">
                        </div>
                    `,
                    orderable: false,
                    width: '10%'
                },
                {
                    data: null,
                    orderable: false,
                    render: () => `
                        <span role="button" class="actividad-delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                            </svg>
                        </span>
                    `,
                    width: '10%'
                },
            ],
            columnDefs: [{
                targets: '_all',
                className: 'text-center'
            }]
        })

        $('#actividades tbody').on('click', '.actividad-delete', function() {
            datatable.row($(this).parents('tr')).remove().draw();
        });

        Alpine.data('actividadesForm', () => ({
            request: {
                isLoading: false
            },
            modal: {
                title: null
            },
            errors: [],
            error: null,
            resetErrors() {
                this.errors = [];
                this.error = null;
            },
            id_orden_falla: null,
            prepareForm(title) {
                this.modal.title = title;
            },
            agregarActividad(form) {
                const actividad = $('#id_act_reparacion').select2('data');

                const obj = {
                    actividad: {
                        id_act_reparacion: actividad[0].id,
                        actividad: actividad[0].text
                    },
                    tiempo_hh: form.elements['horas'].value + form.elements['fraccion'].value,
                    fecha: form.elements['fecha'].value
                }

                datatable.rows.add([obj]).draw();

                form.reset();
                $('#id_act_reparacion').val(null).trigger('change');
            },
            get actividades_reparacion() {
                let actividades = [];

                datatable.rows().every(function() {
                    var data = this.data();

                    actividades.push({
                        id_act_reparacion: parseInt(data.actividad.id_act_reparacion),
                        tiempo_hh: data.tiempo_hh,
                        fecha: data.fecha,
                        observaciones: $(this.node()).find('.observaciones').val(),
                        terminada: $(this.node()).find('.act-terminada').is(':checked')
                    })
                })

                return actividades;
            },
            submit() {
                const data = {
                    actividades: this.actividades_reparacion,
                    id_orden_falla: parseInt(this.id_orden_falla)
                }

                this.resetErrors();

                this.request.isLoading = true;

                axios.post(route("mantenimiento.kiosco-reparaciones.store-actividades"), data)
                    .then(response => {
                        modal.hide();
                        this.$dispatch('kioscoreparaciones-success', {
                            message: response.data.message
                        });
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
            listeners: {
                ['@kioscoreparaciones-createactividades.window']({ detail }) {
                    this.id_orden_falla = detail.id_orden_falla;

                    this.prepareForm(
                        "Reporte de Reparaciones: " + detail.no_falla
                    );
                    modal.show();
                },
                ['@kioscoreparaciones-actividades-clear.window']() {
                    datatable.clear().draw();
                    this.$refs.form.reset();
                    this.resetErrors();
                    $('#id_act_reparacion').val(null).trigger('change');
                }
            }
        }))
    })
</script>
@endpush