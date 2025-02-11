<div class="offcanvas offcanvas-start" tabindex="-1" id="staticBackdrop" aria-labelledby="staticBackdropLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="staticBackdropLabel">Filtros de búsqueda avanzados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="id_falla" class="form-label"><i class="fas fa-exclamation-triangle"></i> Falla</label>
                    <select class="form-select" id="id_falla">
                        <option value="" selected>Todas...</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="id_causa" class="form-label"><i class="fas fa-tools"></i> Causa Falla</label>
                    <select class="form-select" id="id_causa">
                        <option value="" selected>Todas...</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="row mt-0">
                <p class="text-center text-decoration-underline">Fecha emisión de la falla:</p>
                <div class="col-md-6 mb-3">
                    <label for="fecha_captura_inicio" class="form-label"><i class="fa fa-calendar-alt"></i> Fecha de Inicio</label>
                    <input type="date" class="form-control" id="fecha_captura_inicio" value="{{ now()->startOfMonth()->toDateString() }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fecha_ecapturafin" class="form-label"><i class="fa fa-calendar-alt"></i> Fecha de Fin</label>
                    <input type="date" class="form-control" id="fecha_captura_fin" value="{{ now()->endOfMonth()->toDateString() }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="text-center">
                        <button type="button" class="btn btn-primary search">
                            <i class="fas fa-search"></i>
                        </button>
                        <button type="button" class="btn btn-secondary reset">
                            <i class="fas fa-redo-alt"></i>
                        </button>
                        <button class="btn btn-success">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>