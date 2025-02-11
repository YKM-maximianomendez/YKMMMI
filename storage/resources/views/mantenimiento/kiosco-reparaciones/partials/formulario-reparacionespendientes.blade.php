<!-- Modal -->
<div class="modal fade" x-data="reparacionespendientesList" x-bind="listeners" id="modal-reparacionespendientes" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-reparacionespendientesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h1 class="modal-title fs-5" id="modal-reparacionespendientesLabel" x-text="modal.title"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <template x-if="error">
                    <p class="text-danger fw-bold" x-text="error"></p>
                </template>

                <div class="row g-3">
                    <table class="table table-sm table-bordered" id="reparaciones-pendientes">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Tecnico Reparador</th>
                                <th scope="col" class="text-center">Reparación Pendiente</th>
                                <th scope="col" class="text-center">Observaciones (Nota)</th>
                                <th scope="col" class="text-center">Fecha</th>
                                <th scope="col" class="text-center">Tiempo</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const modal = new Modal(document.getElementById('modal-reparacionespendientes'), {});
        let dtReparaciones

        modal._element.addEventListener('hidden.bs.modal', () => {
            if ($.fn.dataTable.isDataTable('#reparaciones-pendientes')) { 
                dtReparaciones.destroy();
            }
        })

        modal._element.addEventListener('shown.bs.modal', () => {
            $(document).off('focusin.modal');
        })

        Alpine.data('reparacionespendientesList', () => ({
            request: {
                isLoading: false,
                url: null
            },
            modal: {
                title: "Reparaciones Abiertas"
            },
            prepareForm(title) {
                this.modal.title = title;
            },
            errors: [],
            error: null,
            resetErrors() {
                this.errors = [];
                this.error = null;
            },
            createDatatable(id_orden_falla) {
                if (!$.fn.dataTable.isDataTable('#reparaciones-pendientes')) {
                    
                    dtReparaciones = $('#reparaciones-pendientes').DataTable({
                        ajax: {
                            url: route('mantenimiento.kiosco-reparaciones.reparaciones-pendientes', id_orden_falla)
                        },
                        processing: true,
                        columns: [
                            { data: 'id_actividad_reparacion', width: '5%' },
                            { data: 'tecnico_reparador', width: '15%' },
                            { data: 'reparacion_pendiente', width: '15%' },
                            { 
                                data: 'observaciones', 
                                render: (data) => `
                                    <textarea cols="30" rows="4"class="form-control observaciones fw-bold" disabled>${data == null ? "" : data}</textarea>
                                ` 
                            },
                            { data: 'fecha', width: '10%' },
                            { data: 'tiempo_abierta', orderable: false },
                            { 
                                data: null,
                                render: (data, type, row, meta) => `
                                    <button type"button" class="btn btn-sm btn-primary fw-bold terminar-reparacion">
                                         <i class="fas fa-check"></i> Terminar
                                    </button>
                                `,
                                orderable: false,
                                width: '10%'
                            },
                        ],
                        columnDefs: [{
                            targets: '_all',
                            className: 'text-center'
                        }],
                        searching: false,
                        info: false,
                        dom: 'rtip'
                    });

                    dtReparaciones.on('click', '.terminar-reparacion', (e) => {
                        const data = dtReparaciones.row(e.target.closest('tr')).data();

                        Swal.fire({
                            title: 'Cierre de actividad',
                            html: `
                            <div class="row">
                                <div class="col-md-12">
                                    <p>Ingresar tiempo HH adicional:</p>
                                </div>
                                <div class="col-6">
                                    <label for="horas" class="form-label fw-bold">Hrs:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="fas fa-clock"></i></div>
                                        <input 
                                            type="number"
                                            min="0"
                                            name="n-horas"
                                            id="n-horas"
                                            class="form-control text-center"
                                            placeholder="Horas"
                                            value="0"
                                        >
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="fraccion" class="form-label fw-bold">Fracción:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="fas fa-clock"></i></div>
                                        <select
                                            class="form-select"
                                            id="n-fraccion"
                                            name="n-fraccion"
                                        >
                                            @foreach ($fracciones as $id => $minutos)
                                            <option value="{{ $id }}" @selected($minutos===0)>{{ $minutos }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            `,
                            icon: 'info', 
                            showCancelButton: true,
                            confirmButtonText: 'Confirmar',
                            cancelButtonText: 'Cancelar',
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                const formdata = {
                                    tiempo_hh: parseFloat($('input[name="n-horas"]').val() + $('select[name="n-fraccion"]').val())
                                }

                                return axios.post(route('mantenimiento.kiosco-reparaciones.reparaciones-pendientes.terminar', [data.id_orden_falla, data.id_actividad_reparacion]), formdata)
                                    .then(response => {
                                        return response.data
                                    })
                                    .catch(e => {
                                        const { errorMessage, validationErrors } = handleErrors(e)
                                        Swal.showValidationMessage(errorMessage);
                                    })
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                            })
                            .then((result) => {
                                if (result.isConfirmed) {
                                    modal.hide();
                                    this.$dispatch('kioscoreparaciones-success', { message: null });
                                }
                            });
                    })
                }
            },
            listeners: {
                ['@kioscoreparaciones-reparacionespendientes.window']({ detail }) {
                    this.prepareForm("Reparaciones pendientes: " + detail.no_falla);
                    this.createDatatable(detail.id_orden_falla);
                    modal.show();
                }
            }
        }))
    })
</script>
@endpush