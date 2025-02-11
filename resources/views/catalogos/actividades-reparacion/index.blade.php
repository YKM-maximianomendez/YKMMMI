@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h3 class="fw-bolder">
                            <strong>Actividades de Reparación</strong>
                        </h3>

                        <div>
                            <button type="button" class="btn btn-outline-primary fw-bold me-1" id="actividadreparacion-create">
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

                    <table class="table table-hover table-sm table-striped border-secondary table-bordered w-100" id="example" style="table-layout: fixed">
                        <thead class="border-secondary">
                            <tr>
                                <th scope="col" class="text-center">Codigo</th>
                                <th scope="col" class="text-center">Descripción</th>
                                <th scope="col" class="text-center">Fecha Registro</th>
                                <th scope="col" class="text-center">Estatus</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('catalogos.actividades-reparacion.formulario')
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {
        const datatable = $('#example').DataTable({
            ajax: {
                url: route('catalogos.actividad-reparacion.index')
            },
            processing: true,
            columns: [
                {
                    data: 'codigo',
                    render: (data) => `<span class="text-primary fw-bolder">${data}</span>`,
                    width: '15%'
                },
                {
                    data: 'actividad_reparacion'
                },
                {
                    data: 'fecha_registro'
                },
                {
                    data: 'estatus_desc',
                    width: '15%'
                },
                {
                    data: null,
                    orderable: false,
                    width: '15%',
                    render: () => {
                        return `
                            <div>
                                <span role="button" class="actividadreparacion-edit me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square text-warning" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                    </svg>
                                </span>
                                <span role="button" class="actividadreparacion-destroy">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg text-danger" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </span>
                            </div>
                        `
                    }
                },
            ],
            columnDefs: [{
                targets: [0, 2, 3, 4],
                className: 'text-center'
            }]
        })

        $(document).on('click', '#actividadreparacion-create', () => {
            this.dispatchEvent(new CustomEvent('actividadreparacion-create'))
        })

        $(document).on('click', '.actividadreparacion-edit', (e) => {
            this.dispatchEvent(new CustomEvent('actividadreparacion-edit', {
                detail: {
                    data: datatable.row(e.target.closest('tr')).data()
                }
            }))
        })

        $(window).on('actividadreparacion-success', (e) => {
            Swal.fire({
                icon: 'success',
                showConfirmButton: false,
                timer: 1500,
                text: e.detail.message || "Operación realizada con éxito.",
                didClose: () => datatable.ajax.reload()
            })
        })

        $(document).on('click', '#reload', () => datatable.ajax.reload())
    })
</script>
@endsection