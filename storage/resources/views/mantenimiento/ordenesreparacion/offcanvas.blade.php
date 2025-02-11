<div x-data="ordenFallasDatatable" x-bind="listeners" class="offcanvas offcanvas-top offcanvas-xxl" tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasTopLabel">Offcanvas top</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Launch demo modal
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-sm table-bordered w-100" id="example-1" style="table-layout: fixed">
            <thead class="table-secondary">
                <tr>
                    <th scope="col" class="text-center">No. Falla</th>
                    <th scope="col" class="text-center">Falla</th>
                    <th scope="col" class="text-center">Lider Prensas</th>
                    <th scope="col" class="text-center">Lider ToolRoom</th>
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

@push('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const bsOffcanvas = new Offcanvas('#offcanvasTop');

        let tbl = $('#example-1');
        let datatable = null

        Alpine.data('ordenFallasDatatable', () => ({
            id_orden: null,
            create_datatable() {
                if ($.fn.DataTable.isDataTable(tbl)) tbl.DataTable().destroy();

                datatable = tbl.DataTable({

                })
            },
            listeners: {
                ['@OT-verfallas.window']({
                    detail
                }) {
                    this.id_orden = detail.id_orden;
                    this.create_datatable();
                    bsOffcanvas.show();
                },
            }
        }))
    })
</script>
@endpush