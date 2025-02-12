@extends('layouts.app')
@section('styles')
<style>
    table.dataTable td,
    th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:after {
        content: "" !important;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row g-3 justify-content-center justify-content-center">
        <div class="col-md-12">
            <div class="alert alert-light text-primary border-secondary d-flex justify-content-between py-1 pe-1 align-items-center" role="alert">
                <div>Reporte de Ordenes de reparación</div>
                <div>
                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop" aria-controls="staticBackdrop">
                        <i class="fas fa-filter"></i> Filtros de búsqueda
                    </button>
                </div>
            </div>

            @include('mantenimiento.reportes.ordenesreparacion.offcanvas-filtros')
        </div>
        <div class="col-md-12">
            <p class="text-center" id="legend"></p>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered w-100 border-secondary" id="example">
                    <thead class="border-secondary">
                        <tr>
                            <th scope="col" class="text-center">No. Orden</th>
                            <th scope="col" class="text-center">Fecha Emisión</th>
                            <th scope="col" class="text-center">Tipo Atención</th>
                            <th scope="col" class="text-center">Usuario Emite</th>
                            <th scope="col" class="text-center">Prensa</th>
                            <th scope="col" class="text-center">Número de Parte & Op</th>
                            <th scope="col" class="text-center">Pzas Terminadas</th>
                            <th scope="col" class="text-center">Pzas Requeridas</th>
                            <th scope="col" class="text-center">Fecha Requiere Produccion</th>
                            <th scope="col" class="text-center">Falla General</th>
                            <th scope="col" class="text-center">Fallas</th>
                            <th scope="col" class="text-center">Fecha Cierre Mantenimiento</th>
                            <th scope="col" class="text-center">Fecha Cierre Prensas</th>
                            <th scope="col" class="text-center">Estatus</th>
                            <th scope="col" class="text-center">Turno</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@include('mantenimiento.ordenesreparacion.formulario-show')
@endsection
@section('scripts')
<script type="text/javascript">
    let fields = [{
            id: 'id_estacion',
            label: 'Estación'
        },
        {
            id: 'id_numeroparte',
            label: 'Número de Parte'
        },
        {
            id: 'id_tipoatencion',
            label: 'Tipo de Atención'
        },
        {
            id: 'id_usuario_capturo',
            label: 'Usuario Capturó'
        },
        {
            id: 'id_turno',
            label: 'Turno'
        },
        {
            id: 'fecha_emision_inicio',
            label: 'Fecha Emisión Inicio'
        },
        {
            id: 'fecha_emision_fin',
            label: 'Fecha Emisión Fin'
        }
    ];

    function resetFilters() {
        fields.filter(e => e.id !== 'fecha_emision_fin' && e.id !== 'fecha_emision_inicio').forEach(field => {
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

    const reload_datatable = (dt) => {
        dt.ajax.url(route('mantenimiento.reportes.ordenesreparacion.index')).load();
    }

    const now = new Date();

    document.addEventListener('DOMContentLoaded', () => {
        const datatable = $('#example').DataTable({
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            autoFilter: true,
                            title: 'Reporte-OrdenesReparacion-' + now.toLocaleDateString()
                        }, 
                        'colvis'
                    ]
                }
            },
            responsive: true,
            scrollX: true,
            processing: true,
            ajax: {
                url: '',
                data: function(d) {
                    d.orden_id_estacion = $('#id_estacion').val();
                    d.orden_id_numeroparte = null;
                    d.orden_id_tipoatencion = $('#id_tipoatencion').val();
                    d.orden_id_usuario_capturo = null;
                    d.orden_id_turno = $('#id_turno').val();
                    d.orden_fecha_emision_inicio = $('#fecha_emision_inicio').val();
                    d.orden_fecha_emision_fin = $('#fecha_emision_fin').val();
                },
                dataSrc: 'data'
            },
            columns: [{
                    data: 'no_orden',
                    render: (data, type, row, meta) => {
                        return `
                                <span role="button" class="text-primary fw-bolder ver-ot" id="${row.id_orden}">${data}</span>
                            `
                    }
                },
                {
                    data: 'orden_fecha_emision'
                },
                {
                    data: 'orden_tipoatencion'
                },
                {
                    data: 'falla_usuario_registro'
                },
                {
                    data: 'orden_estacion'
                },
                {
                    data: 'orden_numeroparte',
                    render: (data, type, row, meta) => `<strong>${data}</strong> ( ${row.orden_operacion} )`
                },
                {
                    data: 'orden_pzas_terminadas'
                },
                {
                    data: 'orden_pzas_requeridas'
                },
                {
                    data: 'orden_fecha_requiere_prod'
                },
                {
                    data: 'falla_falla',
                    render: (data, type, row, meta) => `${row.falla_codigo_falla} - ${data}`
                },
                {
                    data: 'orden_num_fallas'
                },
                {
                    data: 'orden_fecha_cierre_mtto'
                },
                {
                    data: 'orden_fecha_cierre_prensas'
                },
                {
                    data: 'orden_estatus'
                },
                {
                    data: 'orden_turno'
                },
            ],
            drawCallback: function() {
                var api = this.api();
                $('#legend').text(generateLegend())
            },
            columnDefs: [{
                targets: '_all',
                className: 'text-center'
            }]
        })

        $(document).on('click', '.ver-ot', ({ target: span }) => {
            window.dispatchEvent(new CustomEvent('ver-orden', {
                detail: {
                    id_orden: span.id,
                    no_orden: span.textContent
                }
            }))
        })

        $(document).on('click', '.reset', () => {
            resetFilters();
            datatable.clear().draw();
        })

        $(document).on('submit', '#filtros', (e) => {
            e.preventDefault();
            reload_datatable(datatable);
        })
    })
</script>
@endsection