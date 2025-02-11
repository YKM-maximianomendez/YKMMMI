<!-- Modal -->
<div x-data="showOTFalla" x-bind="listeners" class="modal fade" id="modal-ordenfalla" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-ordenfallaLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h1 class="modal-title fs-6" id="modal-ordenfallaLabel" x-text="modal.title"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre x-text="JSON.stringify(orden_falla, null, 2)"></pre>
                
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        document.addEventListener('alpine:init', () => {
            const modal = new Modal(document.getElementById('modal-ordenfalla'))

            Alpine.data('showOTFalla', () => ({
                modal: {
                    title: null
                },
                orden_falla: {},
                consultar_falla(id) {
                    axios.get(route('mantenimiento.ordenesreparacion-falla.show', id))
                    .then(response => {
                        const {
                            falla,
                            falla_evidencias,
                            falla_actividades_reparacion
                        } = response.data;

                        console.log(response.data)

                        this.orden_falla = falla;
                        modal.show();
                    })
                    .catch(e => {
                        console.log(e)
                    })
                    .finally(() => {

                    })
                },
                listeners: {
                    ['@ver-ordenfalla.window']({ detail }) {
                        this.modal.title = "Falla: " + detail.no_falla;
                        this.consultar_falla(detail.id_orden_falla);
                    }
                }
            }))
        })
    </script>
@endpush
