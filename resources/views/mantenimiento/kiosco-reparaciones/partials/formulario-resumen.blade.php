<!-- Modal -->
<div class="modal fade" x-data="reparacionesResumen" x-bind="listeners" id="modal-resumen" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-resumenLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h1 class="modal-title fs-5" id="modal-resumenLabel" x-text="modal.title"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" x-model="id_orden_falla" id="id_orden_falla">
                <template x-if="error">
                    <p class="text-danger fw-bold" x-text="error"></p>
                </template>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="1-tab" style="width: 200px;" data-bs-toggle="tab" data-bs-target="#1" type="button" role="tab" aria-controls="1" aria-selected="true">Actividades de Reparación</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="2-tab" style="width: 200px;" data-bs-toggle="tab" data-bs-target="#2" type="button" role="tab" aria-controls="2" aria-selected="false">Evidencias</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="3-tab" style="width: 200px;" data-bs-toggle="tab" data-bs-target="#3" type="button" role="tab" aria-controls="3" aria-selected="false">Materiales Utilizados</button>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade" id="1" role="tabpanel" aria-labelledby="1-tab">
                        <table class="table table-sm table-striped table-bordered w-100" id="tbl-1" style="table-layout: fixed">
                            <thead class="table-secondary">
                                <tr>
                                    <th scope="col" class="text-center">Actividad</th>
                                    <th scope="col" class="text-center">Tiempo</th>
                                    <th scope="col" class="text-center">Fecha</th>
                                    <th scope="col" class="text-center">Estatus</th>
                                    <th scope="col" class="text-center">Fecha Captura</th>
                                    <th scope="col" class="text-center">Tecnico Reparador</th>
                                    <th scope="col" class="text-center">Observaciones</th>
                                    <th scope="col" class="text-center">Turno</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="2" role="tabpanel" aria-labelledby="2-tab">
                        <table class="table table-sm table-striped table-bordered w-100" id="tbl-2" style="table-layout: fixed">
                            <thead class="table-secondary">
                                <tr>
                                    <th scope="col" class="text-center">Nombre Archivo</th>
                                    <th scope="col" class="text-center">Tipo</th>
                                    <th scope="col" class="text-center">Fecha Capturado</th>
                                    <th scope="col" class="text-center">Usuario Capturó</th>
                                    <th scope="col" class="text-center">Turno</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="3" role="tabpanel" aria-labelledby="3-tab">
                        <table class="table table-sm table-striped table-bordered w-100" id="tbl-3" style="table-layout: fixed">
                            <thead class="table-secondary">
                                <tr>
                                    <th scope="col" class="text-center">ID</th>
                                    <th scope="col" class="text-center">Insumo</th>
                                    <th scope="col" class="text-center">Cantidad</th>
                                    <th scope="col" class="text-center">Unidad</th>
                                    <th scope="col" class="text-center">Fecha Capturado</th>
                                    <th scope="col" class="text-center">Usuario Capturó</th>
                                    <th scope="col" class="text-center">Turno</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const modal = new Modal(document.getElementById('modal-resumen'), {});

        let tbl_1 = $('#tbl-1');
        let tbl_2 = $('#tbl-2');
        let tbl_3 = $('#tbl-3');

        modal._element.addEventListener('hidden.bs.modal', () => {
            $('#myTab button').removeClass('active');
            $('#myTabContent .tab-pane').removeClass('show active');
        })

        $('#myTab button').on('shown.bs.tab', function(event) {
            var activeTabId = $(event.target).attr('id');
            var id = $('#id_orden_falla').val();

            if (activeTabId === '1-tab') {
                if ($.fn.DataTable.isDataTable(tbl_1)) tbl_1.DataTable().destroy();

                tbl_1.DataTable({
                    ajax: {
                        url: route('mantenimiento.kiosco-reparaciones.resumen-reparacion', id),
                        data: function(d) {
                            d.tipo = 1
                        }
                    },
                    processing: true,
                    columns: [
                        { data: 'codigo', render: (data, type, row, meta) => `${data} - ${row.actividad_reparacion}` },
                        { data: 'tiempo_hh', width: '10%' },
                        { data: 'fecha', width: '10%' }, 
                        { data: 'terminada', render: (data) => data == true ? 'Terminada' : 'Pendiente', width: '10%' },
                        { data: 'fecha_captura', width: '10%' },
                        { data: 'tecnico_reparador' },
                        { data: 'observaciones' },
                        { data: 'turno_captura', width: '10%' },
                    ],
                    columnDefs: [{
                        targets:'_all',
                        className: 'text-center'
                    }]
                })
            }

            if (activeTabId === '2-tab') {
                if ($.fn.DataTable.isDataTable(tbl_2)) tbl_2.DataTable().destroy();

                tbl_2.DataTable({
                    ajax: {
                        url: route('mantenimiento.kiosco-reparaciones.resumen-reparacion', id),
                        data: function(d) {
                            d.tipo = 2
                        }
                    },
                    processing: true,
                    columns: [
                        { data: 'nombre', render: (data, type, row, meta) => `<a href="${route('download-evidencia', row.id_orden_evidencia)}" target="_blank">${data}</a>` },
                        { data: 'tipo_evidencia' },
                        { data: 'fecha_captura' },
                        { data: 'usuario_captura' }, 
                        { data: 'turno_captura' }, 
                    ],
                    columnDefs: [{
                        targets: '_all',
                        className: 'text-center'
                    }]
                })
            }

            if (activeTabId === '3-tab') {
                if ($.fn.DataTable.isDataTable(tbl_3)) tbl_3.DataTable().destroy();

                tbl_3.DataTable({
                    ajax: {
                        url: route('mantenimiento.kiosco-reparaciones.resumen-reparacion', id),
                        data: function(d) {
                            d.tipo = 3
                        }
                    },
                    processing: true,
                    columns: [
                        { data: 'id_orden_materialutilizado', width: '9%' },
                        { data: 'insumo_refaccion' },
                        { data: 'cantidad', width: '10%' },
                        { data: 'unidad_medida', width: '10%' }, 
                        { data: 'fecha_captura', width: '10%' }, 
                        { data: 'usuario_captura' }, 
                        { data: 'turno_captura', width: '10%' }, 
                    ],
                    columnDefs: [{
                        targets: '_all',
                        className: 'text-center'
                    }]
                })
            }
        });

        Alpine.data('reparacionesResumen', () => ({
            request: {
                isLoading: false,
                url: null
            },
            modal: {
                title: null
            },
            id_orden_falla: null,
            init() {

            },
            error: null,
            listeners: {
                ['@kioscoreparaciones-resumen.window']({
                    detail
                }) {
                    this.modal.title = "Resumen de reparaciones - Falla: " + detail.no_falla;
                    this.id_orden_falla = detail.id_orden_falla;
                    modal.show();
                }
            }
        }))
    })
</script>
@endpush