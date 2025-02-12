@extends('layouts.app')
@section('styles')
<style>
        .optional-field::before {
            content: "* ";
            color:rgb(252, 0, 0);
            font-size: 0.875em;
        }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h3 class="fw-bolder">
                            <strong>Usuarios</strong>
                        </h3>

                        <div>
                            <button type="button" class="btn btn-outline-primary me-1" id="usuario-create">
                                <i class="fas fa-plus"></i> Agregar Registro
                            </button>

                            <button type="button" class="btn btn-outline-secondary" id="reload">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <hr>

                    <table class="table table-sm table-striped border-secondary table-bordered w-100" id="example" style="table-layout: fixed">
                        <thead class="border-secondary">
                            <tr>
                                <th scope="col" class="text-center">Núm. Nómina</th>
                                <th scope="col" class="text-center">Nombre Completo</th>
                                <th scope="col" class="text-center">Correo Electrónico</th>
                                <th scope="col" class="text-center">Tripulacion</th>
                                <th scope="col" class="text-center">Rol</th>
                                <th scope="col" class="text-center">Estatus</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('seguridad.usuarios.formulario')
@endsection
@section('scripts')
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {
        const datatable = $('#example').DataTable({
            ajax: {
                url: route('seguridad.usuarios.index')
            },
            processing: true,
            columns: [{
                    data: 'numero_nomina',
                    render: (data) => `<span class="text-primary fw-bolder">${data}</span>`,
                    width: '10%'
                },
                {
                    data: 'nombre'
                },
                {
                    data: 'correo_electronico'
                },
                {
                    data: 'tripulacion',
                    width: '10%'
                },
                {
                    data: 'rol',
                    width: '15%'
                },
                {
                    data: 'estatus_desc',
                    width: '10%'
                },
                {
                    data: null,
                    orderable: false,
                    render: (data, type, row, meta) => {
                        return `
                                <div>
                                    <span role="button" class="usuario-edit me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square text-warning" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                        </svg>
                                    </span>
                                    ${row.restablecer_password}
                                </div>
                            `
                    },
                    width: '7%'
                },
            ],
            columnDefs: [{
                targets: [0, 3, 4, 5, 6],
                className: 'text-center'
            }]
        })

        $(document).on('click', '#usuario-create', () => {
            this.dispatchEvent(new CustomEvent('usuario-create'))
        })

        $(document).on('click', '.usuario-edit', (e) => {
            this.dispatchEvent(new CustomEvent('usuario-edit', {
                detail: {
                    data: datatable.row(e.target.closest('tr')).data()
                }
            }))
        })

        $(window).on('usuario-success', (e) => {
            const { message } = e.detail;

            Swal.fire({
                icon: 'success',
                showConfirmButton: false,
                timer: 1500,
                text: message || "Operación realizada con éxito",
                didClose: () => {
                    datatable.ajax.reload()
                }
            })
        })

        $(document).on('click', '#reload', () => datatable.ajax.reload())
    })
</script>
@endsection