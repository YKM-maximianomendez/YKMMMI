@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-secondary">
                <div class="card-header border-secondary">{{ __('Dashboard') }}</div>

                <div class="card-body text-center">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <img class="mb-0 p-5" src="{{ asset('ykm.png') }}" alt="YKM" width="500">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
