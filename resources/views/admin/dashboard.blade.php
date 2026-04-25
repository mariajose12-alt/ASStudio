@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $totalEmpleados }}</div>
            <div class="stat-label">Empleados Registrados</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $totalPaquetes }}</div>
            <div class="stat-label">Paquetes Fotográficos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $totalCatalogos }}</div>
            <div class="stat-label">Catálogos Activos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $totalReservas }}</div>
            <div class="stat-label">Reservas Totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:#a07820;">{{ $reservasPendientes }}</div>
            <div class="stat-label">Reservas Pendientes</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Accesos Rápidos</h2>
        </div>
        <div class="card-body" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap:16px;">
            <a href="{{ route('admin.empleados.create') }}" class="btn btn-outline" style="justify-content:center; padding:20px;">
                + Nuevo Empleado
            </a>
            <a href="{{ route('admin.paquetes.create') }}" class="btn btn-outline" style="justify-content:center; padding:20px;">
                + Nuevo Paquete
            </a>
            <a href="{{ route('admin.catalogos.create') }}" class="btn btn-outline" style="justify-content:center; padding:20px;">
                + Nuevo Catálogo
            </a>
        </div>
    </div>
@endsection
