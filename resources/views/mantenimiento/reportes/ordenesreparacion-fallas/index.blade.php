@extends('layouts.app')
@section('styles')
<style>
    table.dataTable td, th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row g-3 justify-content-center justify-content-center">
        <div class="col-md-12">
            <div class="alert alert-light text-primary border-secondary d-flex justify-content-between py-1 pe-1 align-items-center" role="alert">
                <div>Reporte de Fallas</div>
                <div>
                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop" aria-controls="staticBackdrop">
                        <i class="fas fa-filter"></i> Filtros avanzados
                    </button>
                </div>
            </div>
            
            @include('mantenimiento.reportes.ordenesreparacion-fallas.offcanvas-filtros')
        </div>
    </div>
    <div class="col-md-12">
        <p class="text-center" id="legend"></p>
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered w-100 border-secondary" id="example">
                <thead class="table-secondary border-secondary">
                    <tr>
                        <th scope="col" class="text-center">No. Orden</th>
                        <th scope="col" class="text-center">No. Falla</th>
                        <th scope="col" class="text-center">Tipo</th>
                        <th scope="col" class="text-center">Usuario Emite</th>
                        <th scope="col" class="text-center">Fecha Emisión</th>
                        <th scope="col" class="text-center">Prensa</th>
                        <th scope="col" class="text-center">Número de Parte & Op</th>
                        <th scope="col" class="text-center">Falla</th>
                        <th scope="col" class="text-center">Causa Falla</th>
                        <th scope="col" class="text-center">Tiempo HH Estimado</th>
                        <th scope="col" class="text-center">Tiempo HH Real</th>
                        <th scope="col" class="text-center">Estatus Orden</th>
                        <th scope="col" class="text-center">Estatus</th>
                        <th scope="col" class="text-center">Fecha Recepción</th>
                        <th scope="col" class="text-center">Fecha Programación</th>
                        <th scope="col" class="text-center">Fecha Termino</th>
                        <th scope="col" class="text-center">Turno</th>
                        <th scope="col" class="text-center">Observaciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    let fields = [
        {
            id: 'id_falla',
            label: 'Falla'
        },
        {
            id: 'id_causa',
            label: 'Causa Falla'
        },
        {
            id: 'id_turno',
            label: 'Turno'
        },
        {
            id: 'id_estacion',
            label: 'Estación'
        },
        {
            id: 'id_numeroparte',
            label: 'Número de Parte'
        },
        {
            id: 'fecha_captura_inicio',
            label: 'Fecha Captura Inicio'
        },
        {
            id: 'fecha_captura_fin',
            label: 'Fecha Captura Fin'
        }
    ];

    function generateLegend() {
        let legend = "Filtro aplicado: ";
        let filters = [];

        fields.forEach(field => {
            let element = document.getElementById(field.id);

            if (element && element.value !== "") {
                let value;
                if (element.tagName === 'SELECT') {
                    value = element.options[element.selectedIndex].text;
                } else {
                    value = element.value;
                }
                filters.push(`${field.label}: ${value ?? "S/F"}`);
            }
        });

        if (filters.length > 0) {
            legend += filters.join("; ");
        } else {
            legend = "No se aplicaron filtros.";
        }

        return legend;
    }

    function resetFilters() {
        fields.filter(e => e.id !== 'fecha_captura_fin' && e.id !== 'fecha_captura_inicio').forEach(field => {
            let element = document.getElementById(field.id);
            if (element) {
                if (element.tagName === 'SELECT') {
                    $(element).val(null).trigger('change')
                } else {
                    $(element).val(null)
                }
            }
        });
    }

    const reload_datatable = (dt) => {
        dt.ajax.url(route('mantenimiento.reportes.ordenesreparacion-fallas.index')).load();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const now = new Date();

        const datatable = $('#example').DataTable({
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            autoFilter: true,
                            title: 'Reporte-Fallas-' + now.toLocaleDateString()
                        },
                        'colvis'
                    ]
                }
            },
            ajax: {
                url: '',
                dataSrc: 'data',
                data: function(d) {
                    d.falla_id_falla = $('#id_falla').val();
                    d.falla_id_causa = $('#id_causa').val();
                    d.orden_id_estacion = $('#id_estacion').val();
                    d.orden_id_numeroparte = $('#id_numeroparte').val();
                    d.falla_fecha_captura_inicio = $('#fecha_captura_inicio').val();
                    d.falla_fecha_captura_fin = $('#fecha_captura_fin').val();
                    d.falla_id_turno = $('#id_turno').val();
                }
            },
            processing: true,
            responsive: true,
            scrollX: true,
            columns: [
                { data: 'no_orden' },
                { data: 'no_falla' },
                { data: 'falla_tipo', render: (data) => data === 'U' ? 'Unica' : 'Adicional' },
                { data: 'falla_usuario_registro' },
                { data: 'falla_fecha_captura' },
                { data: 'orden_estacion' },
                {
                    data: 'orden_numeroparte',
                    render: (data, type, row, meta) => `<strong>${data}</strong> ( ${row.orden_operacion} )`
                },
                {
                    data: 'falla_falla',
                    render: (data, type, row, meta) => `${row.falla_codigo_falla} - ${data}`
                },
                {
                    data: 'falla_causa',
                    render: (data, type, row, meta) => `${row.falla_codigo_causa} - ${data}`
                },
                {
                    data: 'falla_tiempo_hh_estimado'
                },
                {
                    data: 'falla_tiempo_hh_real'
                },
                {
                    data: 'orden_estatus'
                },
                {
                    data: 'falla_estatus'
                },
                {
                    data: 'falla_fecha_recepcion'
                },
                {
                    data: 'falla_fecha_programacion'
                },
                {
                    data: 'falla_fecha_termino'
                },
                {
                    data: 'falla_turno'
                },
                {
                    data: 'falla_observaciones'
                },
            ],
            drawCallback: function() {
                var api = this.api();
                $('#legend').text(generateLegend())
            },
            columnDefs: [{
                targets: [0, 1, 2, 4, 9, 10, 11, 12, 13, 14, 15],
                className: 'text-center'
            }]
        })

        $(document).on('submit', '#filter', (e) => {
            e.preventDefault();
            reload_datatable(datatable);
        })

        $(document).on('click', '.reset', () => {
            resetFilters();
            datatable.clear().draw();
        })
    })
</script>
@endsection