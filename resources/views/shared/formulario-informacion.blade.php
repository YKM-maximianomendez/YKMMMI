<!-- Modal -->
<div class="modal fade" x-data="systemInformation" x-bind="listeners" id="sysInformationModal" tabindex="-1" aria-labelledby="sysInformationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h1 class="modal-title fs-6" id="sysInformationModalLabel">Información del Sistema</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center fw-bolder fs-6 mb-0" x-text="data.system_name"></h4>
                    </div>
                    <div class="card-body text-center">
                        <p class="card-text"><strong><i class="fas fa-code-branch"></i> Versión del Sistema: </strong><span x-text="data.version + ' (' + data.version_date + ')'"></span></p>
                        <p class="card-text"><strong><i class="fas fa-book"></i> Manual de Usuario: </strong> <a href="link_al_manual"><i class="fas fa-link"></i> Link al Manual de Usuario</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        const modal = new Modal(document.getElementById('sysInformationModal'), {});

        Alpine.data('systemInformation', () => ({
            data: {},
            about_system() {
                axios.get(route('about-system'))
                    .then(response => {
                        this.data = response.data.information;
                        modal.show();
                    })
                    .catch(e => {
                        const { validationErrors, errorMessage } = handleErrors(e);
                        alert(errorMessage);
                    })
                    .finally(() => {
                        
                    })
            },
            listeners: {
                ['@information-system.window'] () {
                   this.about_system();
                }
            }
        }))
    })
</script>
@endpush
