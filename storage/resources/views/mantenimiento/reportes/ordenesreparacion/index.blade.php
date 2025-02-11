@extends('layouts.app')
@section('styles')
<style>
    table.dataTable td {
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
                <div>Reporte de Ordenes de reparación</div>
                <div>
                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop" aria-controls="staticBackdrop">
                        <i class="fas fa-filter"></i> Filtros avanzados
                    </button>
                </div>
            </div>

            @include('mantenimiento.reportes.ordenesreparacion.offcanvas-filtros')
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered w-100 border-secondary" id="example">
                    <thead class="border-secondary">
                        <tr>
                            <th scope="col" class="text-center">No. Orden</th>
                            <th scope="col" class="text-center">Fecha Emisión</th>
                            <th scope="col" class="text-center">Prensa</th>
                            <th scope="col" class="text-center">Número de Parte & Op</th>
                            <th scope="col" class="text-center">Falla General</th>
                            <th scope="col" class="text-center">Turno Reporte</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    let fields = [
        {
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

    document.addEventListener('DOMContentLoaded', () => {
        const datatable = $('#example').DataTable({
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
            columns: [
                {
                    data: 'no_orden'
                },
                {
                    data: 'orden_fecha_emision'
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
                    data: 'orden_turno'
                },
            ],
            drawCallback: function() {
                var api = this.api();
                api.caption(generateLegend())
            },
            columnDefs: [{
                targets: '_all',
                className: 'text-center'
            }]
        })

        $(document).on('click', '.reset', () => {
            resetFilters();
            datatable.clear().draw();
        })

        $(document).on('click', '.search', () => {
            reload_datatable(datatable);
        })
    })
</script>
@endsection