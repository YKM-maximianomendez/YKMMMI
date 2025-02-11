<!-- Modal -->
<div x-data="showOTFalla" x-bind="listeners" class="modal fade" id="modal-ordenfalla" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-ordenfallaLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h1 class="modal-title fs-6" id="modal-ordenfallaLabel" x-text="modal.title"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" x-html="body"></div>
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
                body: null,
                id_orden_falla: null,
                listeners: {
                    ['@ver-ordenfalla.window']({ detail }) {
                        this.id_orden_falla = detail.id_orden_falla;
                        this.modal.title = "Falla: " + detail.no_falla;
                        modal.show();
                    }
                }
            }))
        })
    </script>
@endpush
