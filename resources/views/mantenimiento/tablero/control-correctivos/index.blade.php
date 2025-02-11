@extends('layouts.guest')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="d-flex justify-content-between align-items-center">
                <h1 class="text-uppercase text-center fw-bold">
                    <strong>Tablero de control: correctivos</strong>
                </h1>

                <div>
                    <img class="mb-2" src="{{ asset('ykm.png') }}" alt="" width="200">
                </div>
            </div>

            <div class="card border-secondary">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-2">
                            <div class="card-group">
                                <div class="card border-secondary">
                                    <div class="card-body border-secondary">
                                        <h1 class="fw-bolder mb-0 text-center" id="ordenes-abiertas">0</h1>
                                    </div>
                                    <div class="card-footer border-secondary text-center fw-bolder text-body-secondary">
                                        Ordenes Abiertas
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-sm table-bordered border-secondary" id="example" style="table-layout: fixed">
                        <thead class="border-secondary">
                            <tr>
                                <th scope="col" class="text-center">No. Orden Falla</th>
                                <th scope="col" class="text-center">Prensa</th>
                                <th scope="col" class="text-center">Número de Parte & Op</th>
                                <th scope="col" class="text-center">Falla General</th>
                                <th scope="col" class="text-center">Técnico Responsable</th>
                                <th scope="col" class="text-center">Hrs Estimadas de Rep</th>
                                <th scope="col" class="text-center">Hrs Realed de Rep</th>
                                <th scope="col" class="text-center">Estado</th>
                                <th scope="col" class="text-center">Fecha Requiere Prod</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const print_contador = (c) => {
            $('#ordenes-abiertas').text(c.ordenes_abiertas);
        }

        const datatable = $('#example').DataTable({
            processing: true,
            ajax: {
                url: route('mantenimiento.tablero.control-correctivos.index'),
                dataSrc: function(json) {
                    print_contador(json.contador);
                    return json.data;
                }
            },
            columns: [{
                    data: 'no_falla',
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('fw-bold fs-6 text-decoration-underline border-secondary')

                        const nivel_prioridad = rowData.falla_prioridad_nivel;

                        if (nivel_prioridad == 5) {
                            $(td).addClass('table-success text-success')
                        }

                        if (nivel_prioridad == 3) {
                            $(td).addClass('table-warning text-dark')
                        }

                        if (nivel_prioridad == 1) {
                            $(td).addClass('table-danger text-danger')
                        }
                    }
                },
                {
                    data: 'orden_estacion'
                },
                {
                    data: 'orden_numeroparte',
                    render: (data, type, row, meta) => `<strong>${data}</strong> ( ${row.orden_operacion} )`
                },
                {
                    data: 'falla_falla',
                    render: (data, type, row, meta) => `${row.falla_codigo_falla} - ${data}`
                },
                {
                    data: 'falla_tecnico_responsable',
                    render: (data) => data === null ? `<span class="text-secondary">S / A</span>` : data
                },
                {
                    data: 'falla_tiempo_hh_estimado',
                    render: (data, type, row, meta) => `${parseFloat(data).toFixed(2)} hr(s)`
                },
                {
                    data: 'falla_tiempo_hh_real_total',
                    render: (data, type, row, meta) => row.falla_tiempo_hh_real == null ? '' : `${parseFloat(data).toFixed(2)} hr(s)`
                },
                {
                    data: 'orden_estatus',
                    render: (data, type, row, meta) => `
                    <span class="badge text-bg-success fs-6">${data}</span>
                    <br>
                    <span class="fw-bold">${row.falla_estatus}</span>
                `
                },
                {
                    data: 'orden_fecha_requiere_prod'
                },
            ],
            searching: false,
            ordering: false,
            columnDefs: [{
                targets: '_all',
                className: 'text-center'
            }]
        })
    })
</script>
@endpush