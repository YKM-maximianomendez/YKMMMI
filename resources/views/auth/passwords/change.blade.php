@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="alert alert-warning text-center" role="alert">
                <strong>¡Atención!</strong><br> Es necesario cambiar tu contraseña antes de continuar utilizando la plataforma.
            </div>

            <div class="card border-secondary p-3">
                <div class="card-body">
                    <form method="POST" action="{{ route('password.change') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" placeholder="Contraseña" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password_confirmation" type="password" placeholder="Confirmar Contraseña" class="form-control @error('password') is-invalid @enderror" name="password_confirmation" required autocomplete="current-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary fw-bold w-100">Actualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection