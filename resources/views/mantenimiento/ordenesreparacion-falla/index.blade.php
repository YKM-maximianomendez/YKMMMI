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


                    <table class="table table-sm table-striped table-bordered w-100 border-secondary" id="example" style="table-layout: fixed">
                        <thead class="border-secondary">
                            <tr>
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
                                <th scope="col" class="text-center"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('mantenimiento.ordenesreparacion-falla.formulario-show')
    @include('mantenimiento.ordenesreparacion-falla.formulario-programarfalla')
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
        const filtro_selected = () => $('input[name="filtro"]:checked').val()

        const print_actions = (row) => {
            const estatus_falla = row.falla_id_estatus;
            const { id_orden, id_orden_falla, no_falla } = row;

            let action = '';
            
            switch (parseInt(estatus_falla)) {
                case 1:
                    action = `
                        <div>
                            <button type="button" class="btn btn-sm fw-bolder btn-primary programar-OT">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stopwatch-fill" viewBox="0 0 16 16">
                                    <path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07A7.001 7.001 0 0 0 8 16a7 7 0 0 0 5.29-11.584l.013-.012.354-.354.353.354a.5.5 0 1 0 .707-.707l-1.414-1.415a.5.5 0 1 0-.707.707l.354.354-.354.354-.012.012A6.97 6.97 0 0 0 9 2.071V1h.5a.5.5 0 0 0 0-1zm2 5.6V9a.5.5 0 0 1-.5.5H4.5a.5.5 0 0 1 0-1h3V5.6a.5.5 0 1 1 1 0"/>
                                </svg>
                            </button>
                        </div>
                    `
                    break;
                case 2:
                case 3:
                    action = `
                        <button type="button" class="btn fw-bold btn-sm btn-warning cerrar-falla" data-orden="${id_orden}" data-num-falla="${no_falla}" data-falla="${id_orden_falla}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                        </button>
                    `
                default:
                    break;
            }

            return action;
        }

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
                    data: null,
                    width: '4%',
                    render: (data, type, row, meta) => {
                        if (row.es_lider_prensas) return '';
                        return print_actions(row)
                    }
                },
            ],
            columnDefs: [{
                targets: '_all',
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

        $(window).on('programar-OT-success', (e) => {
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