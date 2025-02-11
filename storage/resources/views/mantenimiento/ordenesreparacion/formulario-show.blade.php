<!-- Modal -->
<div x-data="showOT" x-bind="listeners" class="modal fade" id="modal-orden" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-ordenLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h1 class="modal-title fs-6" id="modal-ordenLabel" x-text="modal.title"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="row mb-1">
                            <label for="orden_tipoatencion" class="col-md-6 col-form-label text-end">Tipo de Atención:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="orden_tipoatencion" x-model="orden.orden_tipoatencion" placeholder="Tipo de atención" disabled>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <label for="orden_estacion" class="col-md-6 col-form-label text-end">Estación:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="orden_estacion" x-model="orden.orden_estacion" placeholder="Estación" disabled>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <label for="orden_numeroparte" class="col-md-6 col-form-label text-end">Número de Parte:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="orden_numeroparte" x-model="orden.orden_numeroparte" placeholder="Número de Parte" disabled>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <label for="orden_operacion" class="col-md-6 col-form-label text-end">Operación:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="orden_operacion" x-model="orden.orden_operacion" placeholder="Operación" disabled>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <label for="orden_tipomantenimiento" class="col-md-6 col-form-label text-end">Tipo de Mantenimiento:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="orden_tipomantenimiento" x-model="orden.orden_tipomantenimiento" placeholder="Tipo de Mantenimiento" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row mb-1">
                            <label for="orden_pzas_requeridas" class="col-md-6 col-form-label text-end">Piezas Requeridas:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="orden_pzas_requeridas" x-model="orden.orden_pzas_requeridas" placeholder="Pzas Requeridas" disabled>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <label for="orden_pzas_terminadas" class="col-md-6 col-form-label text-end">Piezas Terminadas:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="orden_pzas_terminadas" x-model="orden.orden_pzas_terminadas" placeholder="Pzas Terminadas" disabled>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <label for="orden_fecha_requiere_prod" class="col-md-6 col-form-label text-end">Fecha Requiere Producción:</label>
                            <div class="col-md-6">
                                <input type="date" class="form-control" id="orden_fecha_requiere_prod" x-model="orden.orden_fecha_requiere_prod" placeholder="Fecha Requiere Producción" disabled>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <label for="orden_fecha_emision" class="col-md-6 col-form-label text-end">Fecha de Emisión:</label>
                            <div class="col-md-6">
                                <input type="datetime-local" class="form-control" id="orden_fecha_emision" x-model="orden.orden_fecha_emision" placeholder="Fecha de Emisión" disabled>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <label for="orden_turno" class="col-md-6 col-form-label text-end">Turno:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="orden_turno" x-model="orden.orden_turno" placeholder="Turno" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row mb-1">
                            <label for="orden_usuario_registro" class="col-md-6 col-form-label text-end">Lider Prensas Emite:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="orden_usuario_registro" x-model="orden.orden_usuario_registro" placeholder="Pzas Requeridas" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-fallas-tab" data-bs-toggle="tab" data-bs-target="#nav-fallas" type="button" role="tab" aria-controls="nav-fallas" aria-selected="true">Fallas</button>
                        <button class="nav-link" id="nav-evidencias-tab" data-bs-toggle="tab" data-bs-target="#nav-evidencias" type="button" role="tab" aria-controls="nav-evidencias" aria-selected="false">Evidencias</button>
                        <button class="nav-link" id="nav-materialesutilizados-tab" data-bs-toggle="tab" data-bs-target="#nav-materialesutilizados" type="button" role="tab" aria-controls="nav-materialesutilizados" aria-selected="false">Materiales Utilizados</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-fallas" role="tabpanel" aria-labelledby="nav-fallas-tab" tabindex="0">
                        <table class="table table-sm table-bordered border-secondary w-100" id="tbl-1" style="table-layout: fixed">
                            <thead class="border-secondary">
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
                    <div class="tab-pane fade" id="nav-evidencias" role="tabpanel" aria-labelledby="nav-evidencias-tab" tabindex="0">
                        <table class="table table-sm border-secondary table-bordered w-100" id="tbl-2" style="table-layout: fixed">
                            <thead class="border-secondary">
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
                    <div class="tab-pane fade" id="nav-materialesutilizados" role="tabpanel" aria-labelledby="nav-materialesutilizados-tab" tabindex="0">
                        <table class="table table-sm table-bordered border-secondary w-100" id="tbl-3" style="table-layout: fixed">
                            <thead class="border-secondary">
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
        const modal = new Modal(document.getElementById('modal-orden'))

        const tbl_1 = $('#tbl-1').DataTable({
            columnDefs: [{
                targets: '_all',
                className: 'text-center'
            }]
        })

        const tbl_2 = $('#tbl-2').DataTable({
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

        const tbl_3 = $('#tbl-3').DataTable({
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

        Alpine.data('showOT', () => ({
            modal: {
                title: null
            },
            orden: {},
            add_data_to_datatable(dt, data) {
                dt.clear();
                dt.rows.add(data).draw();
            },
            id_orden: null,
            consultar_OT() {
                axios.get(route('mantenimiento.ordenesreparacion.show', this.id_orden))
                    .then(response => {
                        const {
                            orden,
                            orden_evidencias,
                            orden_fallas,
                            orden_material_utilizado
                        } = response.data;

                        this.orden = orden;
                        this.add_data_to_datatable(tbl_2, orden_evidencias);
                        this.add_data_to_datatable(tbl_3, orden_material_utilizado);
                        modal.show();
                    })
                    .catch(e => {
                        console.log(e)
                    })
                    .finally(() => {

                    })
            },
            listeners: {
                ['@ver-orden.window']({
                    detail
                }) {
                    this.id_orden = detail.id_orden;
                    this.modal.title = "Orden: " + detail.no_orden;

                    this.consultar_OT();
                }
            }
        }))
    })
</script>
@endpush