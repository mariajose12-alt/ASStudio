<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 sidebar">

            <!-- Logo empresa -->
            <div class="logo">
                <img src="img/logo-as-studio.png" alt="Logo Abraham Sánchez">
                <div class="company-name">Abraham Sánchez</div>
            </div>

            <!-- Usuario activo -->
            <div class="user-box" style="margin-top: 1.5rem;">
                <img src="img/admin-user.jpg" alt="Usuario activo">
                <h6 class="text-white mb-1">Admin Principal</h6>
                <small class="text-light">Administrador</small>
            </div>

            <!-- Menú Principal -->
            <h6>Principal</h6>
            <nav class="nav flex-column">
                <a class="nav-link active" href="/fotografos/dashboard">Dashboard</a>
                <a class="nav-link" href="/fotografos/reservas-pendientes">Reservas Pendientes</a>
                <a class="nav-link" href="/fotografos/sesiones-confirmadas">Sesiones Confirmadas</a>
                <a class="nav-link" href="/fotografos/historial">Historial</a>
            </nav>

            <!-- Menú Gestión -->
            <h6>Gestión</h6>
            <nav class="nav flex-column">
                <a class="nav-link" href="/admin/fotografos">Fotógrafos</a>
                <a class="nav-link" href="/admin/pagos/index">Comprobantes Pendientes</a>
                <a class="nav-link" href="/admin/paquetes-servicios">Paquetes y Servicios</a>
                <a class="nav-link" href="/admin/nomina">Nómina</a>
                <a class="nav-link" href="/admin/estudio">Estudio</a>
            </nav>

            <!-- Menú Configuración -->
            <h6>Configuración</h6>
            <nav class="nav flex-column">
                <a class="nav-link" href="#">Ajustes</a>
                <a class="nav-link text-danger" href="#">Cerrar Sesión</a>
            </nav>

        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="col-md-9 col-lg-10 content-area">

            <!-- Barra superior -->
            <div class="topbar d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">Panel de Administrador</h3>
                    <small class="text-muted">Bienvenido de nuevo, Admin Principal</small>
                </div>
                <button class="btn btn-primary">+ Nueva Reserva</button>
            </div>

            <!-- Tarjetas de ejemplo -->
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-dashboard p-3">
                        <h5>Reservas Pendientes</h5>
                        <p class="display-6">12</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-dashboard p-3">
                        <h5>Sesiones Confirmadas</h5>
                        <p class="display-6">8</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-dashboard p-3">
                        <h5>Fotógrafos Activos</h5>
                        <p class="display-6">5</p>
                    </div>
                </div>
            </div>

        </main>

    </div>
</div>
