@extends('tablar::page')

@section('title', 'Perfil')

@section('content')
    @php
        $cardDisplay = $userCard ?: 'No registrado';
        $mailDisplay = $userMail ?: 'No registrado';
        $phoneDisplay = $userPhone ?: 'No registrado';
        $programDisplay = $userProgram && $userProgram !== 'N/A' ? $userProgram : 'No asignado';
        $cityDisplay = $userCity && $userCity !== 'N/A' ? $userCity : 'No asignada';
        $initials = collect(explode(' ', trim($displayName)))
            ->filter()
            ->take(2)
            ->map(fn ($segment) => strtoupper(substr($segment, 0, 1)))
            ->implode('');
        $initials = $initials !== '' ? $initials : 'U';
    @endphp

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Cuenta personal</div>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 7a4 4 0 1 1 8 0a4 4 0 0 1 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                        Perfil de usuario
                    </h2>
                    <div class="text-muted">Consulta la informacion principal asociada a tu cuenta.</div>
                </div>
                @if ($canEditCredentials)
                    <div class="col-12 col-md-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="{{ route('perfil.edit') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                    <path d="M16 5l3 3" />
                                </svg>
                                Editar acceso
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center g-4">
                                <div class="col-auto">
                                    <span class="avatar avatar-xl bg-primary-lt text-primary">{{ $initials }}</span>
                                </div>
                                <div class="col">
                                    <h3 class="card-title mb-1">{{ $displayName }}</h3>
                                    <div class="text-muted mb-2">{{ $mailDisplay }}</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge {{ \App\Helpers\UserRoleHelper::badgeClass($userRole) }}">{{ $nameUserRole }}</span>
                                        <span class="badge bg-secondary-lt">Documento {{ $cardDisplay }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-auto">
                                    <div class="row g-3 text-center">
                                        <div class="col-6 col-lg-12">
                                            <div class="text-muted small text-uppercase">Telefono</div>
                                            <div class="fw-semibold">{{ $phoneDisplay }}</div>
                                        </div>
                                        <div class="col-6 col-lg-12">
                                            <div class="text-muted small text-uppercase">Ciudad</div>
                                            <div class="fw-semibold">{{ $cityDisplay }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Informacion de cuenta</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-5 text-muted">Nombre completo</dt>
                                <dd class="col-sm-7">{{ $displayName }}</dd>

                                <dt class="col-sm-5 text-muted">Correo</dt>
                                <dd class="col-sm-7">{{ $mailDisplay }}</dd>

                                <dt class="col-sm-5 text-muted">Rol</dt>
                                <dd class="col-sm-7">
                                    <span class="badge {{ \App\Helpers\UserRoleHelper::badgeClass($userRole) }}">{{ $nameUserRole }}</span>
                                </dd>

                                <dt class="col-sm-5 text-muted">Estado</dt>
                                <dd class="col-sm-7">
                                    <span class="badge {{ ($user?->state ?? 0) === 1 ? 'bg-success-lt' : 'bg-danger-lt' }}">
                                        {{ ($user?->state ?? 0) === 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Informacion personal</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-5 text-muted">Documento</dt>
                                <dd class="col-sm-7">{{ $cardDisplay }}</dd>

                                <dt class="col-sm-5 text-muted">Telefono</dt>
                                <dd class="col-sm-7">{{ $phoneDisplay }}</dd>

                                <dt class="col-sm-5 text-muted">Programa</dt>
                                <dd class="col-sm-7">{{ $programDisplay }}</dd>

                                <dt class="col-sm-5 text-muted">Ciudad</dt>
                                <dd class="col-sm-7">{{ $cityDisplay }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                @if ($canEditCredentials)
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                <div>
                                    <h3 class="card-title mb-1">Seguridad de la cuenta</h3>
                                    <div class="text-muted">Puedes actualizar tu nombre, correo y contrasena desde la opcion de edicion.</div>
                                </div>
                                <div class="btn-list">
                                    <a href="{{ route('perfil.edit') }}" class="btn btn-outline-primary">Administrar credenciales</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
