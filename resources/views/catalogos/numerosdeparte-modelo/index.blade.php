@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-body">
                    <table class="table border-secondary table-sm table-striped table-bordered w-100" id="example" style="table-layout: fixed">
                        <thead class="border-secondary">
                            <tr>
                                <th scope="col" class="text-center">Modelo</th>
                                <th scope="col" class="text-center">Descripción</th>
                                <th scope="col" class="text-center">Estatus</th>
                                <th scope="col" class="text-center">Fecha Registro</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('catalogos.numerosdeparte-modelo.formulario')
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {
        const now = new Date();

        const datatable = $('#example').DataTable({
            layout: {
                topStart: {
                    buttons: [
                        'pageLength',
                        {
                            extend: 'excelHtml5',
                            autoFilter: true,
                            title: 'Catalogo-NumerosdeParteModelos-' + now.toLocaleDateString()
                        }
                    ]
                }
            },
            ajax: {
                url: route('catalogos.numeroparte-modelo.index')
            },
            processing: true,
            columns: [
                {
                    data: 'modelo',
                    render: (data) => `<span class="text-primary fw-bolder">${data}</span>`,
                },
                {
                    data: 'descripcion'
                },
                {
                    data: 'estatus_desc'
                },
                {
                    data: 'fecha_registro'
                },
                {
                    data: null,
                    orderable: false,
                    render: () => {
                        return `
                            <div>
                                <span role="button" class="numeropartemodelo-edit me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square text-warning" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                    </svg>
                                </span>
                                <span role="button" class="numeropartemodelo-destroy">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg text-danger" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </span>
                            </div>
                        `
                    }
                }
            ],
            columnDefs: [{
                targets: '_all',
                className: 'text-center'
            }],
        })

        // 1. Abrir modal para registro
        $(document).on('click', '#numeropartemodelo-create', () => {
            this.dispatchEvent(new CustomEvent('numeropartemodelo-create'))
        })

        // 2. Abrir modal para edición
        $(document).on('click', '.numeropartemodelo-edit', (e) => {
            this.dispatchEvent(new CustomEvent('numeropartemodelo-edit', {
                detail: {
                    data: datatable.row(e.target.closest('tr')).data()
                }
            }))
        })

        $(window).on('numeropartemodelo-success', (e) => {
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