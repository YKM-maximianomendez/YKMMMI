@extends('layouts.app')
@section('styles')
<style>
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .vertical-center {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
@endsection
@section('content')
<div class="container h-100">
    <div class="row justify-content-center vertical-center">
        <div class="col-md-4">
            <div class="card border-secondary p-3">
                <div class="card-body">
                    <div class="text-center">
                        <img class="mb-4" src="{{ asset('ykm.png') }}" alt="" width="230">
                    
                        <h1 class="h3 mb-3 fw-bolder fw-custom">
                            <strong>{{ __('YKMMMI - Mantenimiento') }}</strong>
                        </h1>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input id="numero_nomina" type="text" placeholder="Número de Nómina" class="form-control @error('numero_nomina') is-invalid @enderror" name="numero_nomina" value="{{ old('numero_nomina') }}" required autocomplete="off" autofocus>

                                @error('numero_nomina')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
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
                        <button type="submit" class="btn btn-primary fw-bold w-100">Iniciar Sesión</button>
                    </form>
                </div>
            </div>
            <div class="text-center">
                <a role="button" class="btn btn-link" target="_blank" href="{{ route('mantenimiento.tablero.control-correctivos.index') }}">Tablero de Control: Correctivos</a>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {
        const inputUsername = document.getElementById('numero_nomina');
        IMask(inputUsername, {
            mask: '00000'
        });
    })
</script>
@endpush