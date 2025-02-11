<div class="offcanvas offcanvas-start" tabindex="-1" id="staticBackdrop" aria-labelledby="staticBackdropLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="staticBackdropLabel">Filtros de búsqueda avanzados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="id_estacion" class="form-label"><i class="fas fa-train"></i> Estación</label>
                    <select class="form-select" id="id_estacion">
                        <option value="" selected>Todas...</option>
                        @foreach($estaciones as $id => $estacion)
                        <option value="{{ $id }}">{{ $estacion }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="numeroParte" class="form-label"><i class="fa fa-cogs"></i> Número de Parte</label>
                    <select class="form-select" id="numeroParte">
                        <option selected>Seleccione...</option>
                        <!-- Opciones -->
                    </select>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label for="id_tipoatencion" class="form-label"><i class="fa fa-info-circle"></i> Tipo de Atención</label>
                    <select class="form-select" id="id_tipoatencion">
                        <option value="" selected>Todas...</option>
                        <option value="1">Prensa</option>
                        <option value="2">Taller</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_turno" class="form-label"><i class="fa fa-clock"></i> Turno</label>
                    <select class="form-select" id="id_turno">
                        <option value="" selected>Todos...</option>
                        <option value="1">Turno de dia</option>
                        <option value="2">Turno de noche</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="usuarioRegistro" class="form-label"><i class="fa fa-user"></i> Usuario Registró</label>
                    <select class="form-select" id="usuarioRegistro">
                        <option selected>Seleccione...</option>
                        <!-- Opciones -->
                    </select>
                </div>
            </div>
            <hr>
            <div class="row mt-0">
                <p class="text-center text-decoration-underline">Fecha emisión de la orden:</p>
                <div class="col-md-6 mb-3">
                    <label for="fecha_emision_inicio" class="form-label"><i class="fa fa-calendar-alt"></i> Fecha de Inicio</label>
                    <input type="date" class="form-control" id="fecha_emision_inicio" value="{{ now()->startOfMonth()->toDateString() }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fecha_emision_fin" class="form-label"><i class="fa fa-calendar-alt"></i> Fecha de Fin</label>
                    <input type="date" class="form-control" id="fecha_emision_fin" value="{{ now()->endOfMonth()->toDateString() }}">
                </div>
            </div>
            <div class="row g-3">
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