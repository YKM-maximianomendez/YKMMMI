@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-body">
                    <div class="text-center">
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                            <input type="radio" value="1" class="btn-check" name="filtro" id="btnradioE" autocomplete="off" checked>
                            <label class="btn btn-outline-info" style="width: 150px;" for="btnradioE">
                                Abiertas
                                <br>
                                <h2 id="ordenes-abiertas" class="fw-bold">0</h2>
                            </label>
                            <input type="radio" value="2" class="btn-check" name="filtro" id="btnradioP" autocomplete="off">
                            <label class="btn btn-outline-info" style="width: 150px;" for="btnradioP">
                                Cerradas (MTTO)
                                <br>
                                <h2 id="ordenes-cerradas-mtto" class="fw-bold">0</h2>
                            </label>
                            <input type="radio" value="3" class="btn-check" name="filtro" id="btnradioT" autocomplete="off">
                            <label class="btn btn-outline-info" style="width: 150px;" for="btnradioT">
                                Cerradas (Prensas)
                                <br>
                                <h2 id="ordenes-cerradas-prensas" class="fw-bold">0</h2>
                            </label>
                        </div>
                    </div>

                    <table class="table table-sm table-striped table-bordered w-100 border-secondary" id="example" style="table-layout: fixed">
                        <thead class="border-secondary">
                            <tr>
                                <th scope="col" class="text-center">No. Orden</th>
                                <th scope="col" class="text-center">Lider Prensas</th>
                                <th scope="col" class="text-center">Prensa</th>
                                <th scope="col" class="text-center">Número de Parte & Op</th>
                                <th scope="col" class="text-center">Falla Principal</th>
                                <th scope="col" class="text-center">Fecha Emisión</th>
                                <th scope="col" class="text-center">Seguimienti</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('mantenimiento.ordenesreparacion.formulario-show')
    @include('mantenimiento.ordenesreparacion.formulario-cerrarOT')
    @include('mantenimiento.ordenesreparacion.offcanvas')
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {
        const asignar_valores_contador = (contador) => {
            $('#ordenes-abiertas').text(contador.ordenes_abiertas);
            $('#ordenes-cerradas-mtto').text(contador.ordenes_cerradas_mtto);
            $('#ordenes-cerradas-prensas').text(contador.ordenes_cerradas_prensas);
        }

        const filtro_selected = () => $('input[name="filtro"]:checked').val();

        const datatable = $('#example').DataTable({
            ajax: {
                url: route('mantenimiento.ordenesreparacion.index'),
                data: function(d) {
                    d.id_estatus_orden = filtro_selected
                },
                dataSrc: function(json) {
                    asignar_valores_contador(json.contador)
                    return json.data;
                }
            },
            columns: [
                {
                    data: 'no_orden',
                    render: (data, type, row, meta) => {
                        return `
                                <span role="button" class="text-primary fw-bolder ver-ot" id="${row.id_orden}">${data}</span>
                            `
                    },
                    width: '9%'
                },
                {
                    data: 'falla_usuario_registro'
                },
                {
                    data: 'orden_estacion',
                    width: '9%',
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
                    data: 'orden_fecha_emision',
                    width: '9%'
                },
                {
                    data: 'orden_fecha_requiere_prod',
                    width: '9%'
                },
                {
                    data: 'acciones',
                    orderable: false,
                    width: '9%'
                },
            ],
            columnDefs: [{
                targets: [0, 2, 3, 5, 6, 7],
                className: 'text-center'
            }],
            processing: true,
            ordering: false
        })

        $(document).on('click', '.ver-ot', ({target: span}) => {
            window.dispatchEvent(new CustomEvent('ver-orden', {
                detail: {
                    id_orden: span.id,
                    no_orden: span.textContent
                }
            }))
        })
        
        $(document).on('click', '.cerrar-OT', (e) => {
            const orden = datatable.row(e.target.closest('tr')).data();

            this.dispatchEvent(new CustomEvent('cerrar-OT', {
                detail: {
                    orden: orden
                }
            }))
        })

        $(document).on('click', '.confirmar-cierre-OT', (e) => {
            const orden = datatable.row(e.target.closest('tr')).data();

            Swal.fire({
                icon: 'question',
                title: 'Confirmar Cierre',
                text: `¿Marcar como terminada la orden ${orden.no_orden}?`,
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar",
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    return axios.post(route('mantenimiento.ordenreparacion.confirmar-cierre', orden.id_orden), {
                        '_method': 'PUT'
                    })
                        .then(response => response.data)
                        .catch(error => {
                            const { errorMessage } = handleErrors(error);
                            Swal.showValidationMessage(`Error: ${errorMessage}`);
                        })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if(result.isConfirmed){
                    Swal.fire({
                        icon: "success",
                        text: "Operación realizada con éxito.",
                        showConfirmButton: false,
                        timer: 1500,
                        willClose: datatable.ajax.reload()
                    }).then()
                }
            })
        })

        $(window).on('ordenfalla-success cerrar-ot-success', function(e) {
            console.log(e.detail)
            Swal.fire({
                icon: 'success',
                showConfirmButton: false,
                timer: 1500,
                text: e.detail.message || "Operación realizada con éxito.",
                didClose: () => datatable.ajax.reload(function() {
                    if (e.type === "cerrar-ot-success") {
                        const file = e.detail.file;
                        if (file.content !== null) {
                            const contentType = file.mime_type; // Cambia esto según el tipo de tu archivo

                            // Decodifica el contenido base64
                            const byteCharacters = atob(file.content);
                            const byteNumbers = new Array(byteCharacters.length);
                            for (let i = 0; i < byteCharacters.length; i++) {
                                byteNumbers[i] = byteCharacters.charCodeAt(i);
                            }
                            const byteArray = new Uint8Array(byteNumbers);

                            // Crea un Blob con el contenido del archivo
                            const blob = new Blob([byteArray], {
                                type: contentType
                            });

                            // Crea un enlace de descarga
                            const link = document.createElement('a');
                            link.href = URL.createObjectURL(blob);
                            link.download = file.name; // Cambia esto por el nombre que quieras darle al archivo
                            link.click();

                            link.remove();
                        }
                    }
                })
            })
        })

        $('input[name="filtro"]').on('change', function() {
            datatable.ajax.reload();
        })
    })
</script>
@endsection