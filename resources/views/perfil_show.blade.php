@extends('tablar::page')

@section('title', 'Perfil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-8">
            <div class="card shadow-lg border border-secondary-subtle rounded-3">
                <div class="card-header bg-primary text-white text-center border-0 py-3">
                    <h4 class="m-0 text-white">{{ __('Perfil de Usuario') }}</h4>
                </div>

                <div class="card-body p-4">
                    
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-document">{{ __('Documento de identidad') }}</label>
                            <div id="perfil-readonly-document" class="perfil-readonly-field">{{ $userCard }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-name">{{ __('Nombre de Usuario') }}</label>
                            <div id="perfil-readonly-name" class="perfil-readonly-field">{{ $displayName }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-mail">{{ __('Correo Electrónico') }}</label>
                            <div id="perfil-readonly-mail" class="perfil-readonly-field">{{ $userMail }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-phone">{{ __('Número telefónico') }}</label>
                            <div id="perfil-readonly-phone" class="perfil-readonly-field">{{ $userPhone }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-role">{{ __('Rol') }}</label>
                            <div id="perfil-readonly-role" class="perfil-readonly-field">{{ $nameUserRole }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-program">{{ __('Programa') }}</label>
                            <div id="perfil-readonly-program" class="perfil-readonly-field">{{ $userProgram }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-city">{{ __('Ciudad') }}</label>
                            <div id="perfil-readonly-city" class="perfil-readonly-field">{{ $userCity }}</div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
