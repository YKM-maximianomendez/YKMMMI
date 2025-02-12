@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-body">
                    <div class="text-center">
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                            <input type="radio" value="E" class="btn-check" name="filtro" id="btnradioE" autocomplete="off" checked>
                            <label class="btn btn-outline-info" style="width: 150px;" for="btnradioE">
                                Emitidas
                                <br>
                                <h2 id="fallas-emitidas" class="fw-bold">0</h2>
                            </label>
                            <input type="radio" value="P" class="btn-check" name="filtro" id="btnradioP" autocomplete="off">
                            <label class="btn btn-outline-info" style="width: 150px;" for="btnradioP">
                                Programadas
                                <br>
                                <h2 id="fallas-programadas" class="fw-bold">0</h2>
                            </label>
                            <input type="radio" value="T" class="btn-check" name="filtro" id="btnradioT" autocomplete="off">
                            <label class="btn btn-outline-info" style="width: 150px;" for="btnradioT">
                                Terminadas (OT Abierta)
                                <br>
                                <h2 id="fallas-terminadas" class="fw-bold">0</h2>
                            </label>
                        </div>
                    </div>

                    <hr>

                    @hasrole('Lider ToolRoom')
                    <div>
                        <button type="button" class="btn btn-success ordenfalla-create">
                            + Falla
                        </button>
                    </div>
                    @endhasrole

                    <table class="table table-sm table-striped table-bordered w-100 border-secondary" id="example" style="table-layout: fixed">
                        <thead class="border-secondary">
                            <tr>
                                <th scope="col" class="text-center">No. Orden</th>
                                <th scope="col" class="text-center">No. Falla</th>
                                <th scope="col" class="text-center">Prensa</th>
                                <th scope="col" class="text-center">Número de Parte & Op</th>
                                <th scope="col" class="text-center">Falla</th>
                                <th scope="col" class="text-center">Lider Prensas</th>
                                <th scope="col" class="text-center">Hrs. Estimadas</th>
                                <th scope="col" class="text-center">Hrs. Reales</th>
                                <th scope="col" class="text-center">Emitida</th>
                                <th scope="col" class="text-center">Programada</th>
                                <th scope="col" class="text-center">Terminada</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('mantenimiento.ordenesreparacion-falla.formulario-show')
    @include('mantenimiento.ordenesreparacion-falla.formulario-programarfalla')
    @include('mantenimiento.ordenesreparacion-falla.formulario-create')
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    const asignar_valores_contador = (contador) => {
       $('#fallas-programadas').text(contador.fallas_programadas);
       $('#fallas-emitidas').text(contador.fallas_emitidas);
       $('#fallas-terminadas').text(contador.fallas_terminadas);
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        const filtro_selected = () => $('input[name="filtro"]:checked').val();

        const datatable = $('#example').DataTable({
            ajax: {
                url: "{{ route('mantenimiento.ordenesreparacion-falla.index') }}",
                data: function(d) {
                    d.estatus_orden_falla = filtro_selected()
                },
                dataSrc: function (json) {
                    asignar_valores_contador(json.contador)
                    return json.data;
                }
            },
            processing: true,
            columns: [
                { data: 'no_orden', width: '7%' },
                {
                    data: 'no_falla',
                    width: '7%',
                    render: (data, type, row, meta) => `<span class="fw-bold text-primary ver-otfalla" role="button" id="${row.id_orden_falla}">${data}</span>`,
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).addClass('fw-bold border-secondary')

                        const nivel_prioridad = rowData.falla_prioridad_nivel;

                        if (nivel_prioridad == 5) {
                            $(td).addClass('table-success text-success')
                        }

                        if (nivel_prioridad == 3) {
                            $(td).addClass('table-warning text-warning')
                        }

                        if (nivel_prioridad == 1) {
                            $(td).addClass('table-danger text-danger')
                        }                        
                    }
                },
                {
                    data: 'orden_estacion',
                    width: '8%'
                },
                {
                    data: 'orden_numeroparte',
                    render: (data, type, row, meta) => `<strong>${data}</strong> ( ${row.orden_operacion} )`,
                },
                {
                    data: 'falla_falla',
                    render: (data, type, row, meta) => `${row.falla_codigo_falla} - ${data}`
                },
                {
                    data: 'falla_usuario_registro',
                },
                {
                    data: 'falla_tiempo_hh_estimado',
                    width: '8%'
                },
                {
                    data: 'falla_tiempo_hh_real',
                    width: '8%'
                },
                {
                    data: 'orden_fecha_emision',
                    width: '8%'
                },
                {
                    data: 'falla_fecha_programacion',
                    width: '8%'
                },
                {
                    data: 'falla_fecha_termino',
                    width: '8%'
                },
                {
                    data: 'acciones',
                    width: '8%',
                },
            ],
            columnDefs: [{
                targets: [0, 1, 2, 3, 6, 7, 8, 9, 10, 11],
                className: 'text-center'
            }],
            ordering: false,
            drawCallback: function(settings) {
                var api = this.api();

                var filtro = filtro_selected();

                if (filtro === "E") {
                    api.column(6).visible(1);
                    api.column(7).visible(0);

                    api.column(8).visible(1);
                    api.column(9).visible(0);
                    api.column(10).visible(0);
                }

                if (filtro == "P") {
                    api.column(6).visible(1);
                    api.column(7).visible(0);

                    api.column(8).visible(0);
                    api.column(9).visible(1);
                    api.column(10).visible(0);
                }

                if (filtro == "T") {
                    api.column(6).visible(0);
                    api.column(7).visible(1);

                    api.column(8).visible(0);
                    api.column(9).visible(0);
                    api.column(10).visible(1);
                }
            }
        });

        $(document).on('click', '.ordenfalla-create', (e) => {
            this.dispatchEvent(new CustomEvent('ordenfalla-create'))
        })

        $('input[name="filtro"]').on('change', (e) => {
            datatable.ajax.reload();
        });

        $(document).on('click', '.ver-otfalla', ({ target: span }) => {
            window.dispatchEvent(new CustomEvent('ver-ordenfalla', {
                detail: {
                    id_orden_falla: span.id,
                    no_falla: span.textContent
                }
            }))
        })

        $(document).on('click', '.programar-OT', (e) => {
            const orden = datatable.row(e.target.closest('tr')).data();
            this.dispatchEvent(new CustomEvent('programar-OT', {
                detail: {
                    orden: orden
                }
            }))
        })

        $(document).on('click', '.cerrar-falla', (e) => {
            const button = e.target.closest('button');

            const orden = {
                id_orden: button.dataset.orden,
                id_orden_falla: button.dataset.falla,
                no_falla: button.dataset.numFalla
            }

            swalCerrarFalla(
                orden,
                () => datatable.ajax.reload()
            )
        })

        $(window).on('programar-OT-success ordenfalla-success', (e) => {
            Swal.fire({
                icon: 'success',
                showConfirmButton: false,
                timer: 1500,
                text: e.detail.message || "Operación realizada con éxito.",
                didClose: () => datatable.ajax.reload()
            })
        })
    })
</script>
@endsection