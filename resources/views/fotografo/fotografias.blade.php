@extends('layouts.fotografo')
@section('title', 'Subir Fotografías')
@section('subtitle', 'Carga las fotografías de la sesión')

@section('topbar-actions')
    <a href="{{ route('fotografo.sesiones.index') }}" class="btn-volver">← Volver</a>
@endsection

@section('content')

    {{-- Info de la sesión --}}
    <div class="sesion-info-card">
        <div style="margin-bottom: 16px;">
            <h3 class="sesion-tipo">
                {{ $sesion->reserva->paquete->nombre ?? 'Sesión' }}
                —
                {{ $sesion->reserva->cliente->usuario->persona->nombre ?? '' }}
                {{ $sesion->reserva->cliente->usuario->persona->apellido ?? '' }}
            </h3>
        </div>
        <div class="sesion-meta">
            <div class="meta-item">
                <span class="meta-label">FECHA</span>
                <span class="meta-value">{{ $sesion->fecha_inicio->format('d \d\e F, Y') }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">HORARIO</span>
                <span class="meta-value">{{ $sesion->fecha_inicio->format('H:i') }} — {{ $sesion->fecha_fin->format('H:i') }} hrs</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">UBICACIÓN</span>
                <span class="meta-value">{{ $sesion->lugar ?? 'Estudio' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">ESTADO</span>
                <span class="meta-value">{{ $sesion->estado }}</span>
            </div>
        </div>
    </div>

    {{-- Fotos editadas de ayudantes pendientes de aprobación (solo el principal las ve) --}}
    @if($esPrincipal && $pendientesAprobacion->isNotEmpty())
        <div class="aprobacion-card" id="aprobacionCard">
            <div class="pendientes-header">
                <div class="pendientes-header-left">
                    <span class="aprobacion-badge">{{ $pendientesAprobacion->count() }}</span>
                    <h4 class="pendientes-title">Fotos editadas de ayudantes pendientes de aprobación</h4>
                </div>
            </div>

            <div class="pendientes-list" id="aprobacionList">
                @foreach($pendientesAprobacion as $foto)
                    <div class="aprobacion-item" id="aprobacion-item-{{ $foto->id }}">
                        <div class="pendiente-icon aprobacion-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="pendiente-nombre">{{ $foto->nombre_original ?? basename($foto->url) }}</span>
                        <span class="aprobacion-autor">
                            subida por {{ $foto->subidaPor?->getPersona()->nombre ?? 'un ayudante' }}
                        </span>
                        <div class="aprobacion-acciones">
                            <button type="button" class="btn-confirmar" onclick="aprobarFoto({{ $foto->id }})">Aprobar</button>
                            <button type="button" class="btn-rechazar" onclick="rechazarFoto({{ $foto->id }})">Rechazar</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Fotos pendientes de edición (solo si la sesión está en EN_EDICION) --}}
    @if($pendientesEdicion->isNotEmpty())
        <div class="pendientes-card">
            <div class="pendientes-header">
                <div class="pendientes-header-left">
                    <span class="pendientes-badge">{{ $pendientesEdicion->where('ya_editada', false)->count() }}</span>
                    <h4 class="pendientes-title">Fotos seleccionadas por el cliente</h4>
                </div>
                <span class="pendientes-hint">Búscalas en tu computadora y súbelas como <strong>Editada</strong></span>
            </div>

            <div class="pendientes-list">
                @foreach($pendientesEdicion as $foto)
                    <div class="pendiente-item {{ $foto->ya_editada ? 'ya-editada' : '' }}">
                        <div class="pendiente-icon">
                            @if($foto->ya_editada)
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-1.414.828l-3 1 1-3a4 4 0 01.828-1.414z"/>
                                </svg>
                            @endif
                        </div>
                        <span class="pendiente-nombre">{{ $foto->nombre_original ?? basename($foto->url) }}</span>
                        <span class="pendiente-estado {{ $foto->ya_editada ? 'entregada' : '' }}">
                            {{ $foto->ya_editada ? 'Editada ✓' : 'Pendiente de edición' }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Botón marcar como entregada — solo si hay fotos y todas están editadas solo el fotografo principal--}}
            @if($esPrincipal && $sesion->estado === 'EN_EDICION' && $pendientesEdicion->where('ya_editada', false)->count() === 0)
                <div style="margin-top: 20px; text-align: right;">
                    <button type="button" class="btn-entregar" id="btnEntregar" onclick="marcarEntregada()">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Marcar sesión como entregada
                    </button>
                </div>
            @elseif($esPrincipal && $sesion->estado === 'EN_EDICION')
                <div style="margin-top: 16px;">
                    <p class="entregar-hint">Sube todas las fotos editadas para poder marcar la sesión como entregada.</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Formulario de subida --}}
    <div class="upload-card">
        <div class="upload-header">
            <span class="upload-label">Seleccionar Archivos</span>
            @if($sesion->estado !== 'EN_EDICION')
                <div class="estado-selector">
                    <label class="estado-option">
                        <input type="radio" name="estado_sel" value="ORIGINAL" checked>
                        <span>RAW / Original</span>
                    </label>
                    <label class="estado-option">
                        <input type="radio" name="estado_sel" value="EDITADA">
                        <span>Editada</span>
                    </label>
                </div>
            @else
                {{-- Forzado silenciosamente, no hay que mostrarlo --}}
                <input type="hidden" name="estado_sel" value="EDITADA">
                <span class="upload-label" style="font-size:12px; color:var(--muted);">
                    Subiendo como <strong>Editada</strong>
                </span>
            @endif
        </div>

        @if(! $esPrincipal)
            <p class="ayudante-aviso">
                Estás subiendo fotos como ayudante. Las <strong>RAW/Original</strong> entran directo a la sesión;
                las <strong>Editadas</strong> quedarán pendientes de aprobación del fotógrafo principal.
            </p>
        @endif

        <div class="dropzone" id="dropzone">
            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #ccc; margin-bottom: 12px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            <p class="dropzone-title">Arrastra tus archivos aquí</p>
            <p class="dropzone-sub">O haz clic para seleccionar desde tu computadora</p>
            <button type="button" class="btn-select" onclick="document.getElementById('fileInput').click()">
                Seleccionar Archivos
            </button>
            <input type="file" id="fileInput" multiple accept=".jpg,.jpeg,.png,.webp,.raw,.cr2,.nef,.arw" style="display:none;">
        </div>

        {{-- Preview --}}
        <div id="filePreview" class="file-preview" style="display:none;">
            <div class="preview-header">
                <span id="fileCount" class="preview-count"></span>
                <button type="button" class="btn-clear" onclick="clearFiles()">Limpiar</button>
            </div>
            <div id="fileList" class="file-list"></div>
        </div>

        {{-- Progreso --}}
        <div id="progressSection" style="display:none; margin-top: 16px;">
            <div class="progress-bar-wrap">
                <div class="progress-bar" id="progressBar"></div>
            </div>
            <p id="progressText" class="progress-text">Preparando...</p>
        </div>

        {{-- Acciones: subir + terminé --}}
        <div id="uploadActions" style="display:none; margin-top: 20px; display: none; justify-content: space-between; align-items: center;">
            <button type="button" class="btn-upload" id="uploadBtn" onclick="uploadFiles()">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Subir Fotografías
            </button>
        </div>

        {{-- Botón "Terminé de subir" — aparece después de la primera subida exitosa --}}
        <div id="termineSection" style="display:none; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border); text-align: right;">
            <p style="font-size: 13px; color: var(--muted); margin: 0 0 10px 0;">¿Ya subiste todas las fotos de esta tanda?</p>
            <button type="button" class="btn-termine" onclick="abrirModalExito()">
                Confirmar subida
            </button>
        </div>
    </div>

    {{-- Fotos ya subidas --}}
    <div class="uploaded-section" id="uploadedSection" style="{{ $sesion->fotografias->count() === 0 ? 'display:none' : '' }}">
        <h4 class="uploaded-title">Fotos ya subidas (<span id="uploadedCount">{{ $sesion->fotografias->count() }}</span>)</h4>
        <div class="uploaded-grid" id="uploadedGrid">
            @foreach($sesion->fotografias as $foto)
                <div class="uploaded-item">
                    <div class="uploaded-thumb">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--muted);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="uploaded-info">
                        <span class="uploaded-name">{{ $foto->nombre_original ?? basename($foto->url) }}</span>
                        @if(! $foto->aprobada)
                            <span class="uploaded-estado pendiente-aprobacion">Pendiente de aprobación</span>
                        @else
                            <span class="uploaded-estado {{ strtolower($foto->estado) }}">{{ $foto->estado }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── MODAL ÉXITO (subida) ── --}}
    <div class="success-backdrop" id="successModal">
        <div class="success-modal">
            <h2 class="success-title">¡Fotografías Subidas!</h2>
            <p class="success-desc">Las fotografías han sido cargadas correctamente. Puedes seguir subiendo más o volver a la lista de sesiones.</p>

            <div class="success-stats">
                <div class="success-stat">
                    <span class="success-stat__num" id="statArchivos">0</span>
                    <span class="success-stat__label">ARCHIVOS</span>
                </div>
                <div class="success-stat">
                    <span class="success-stat__num" id="statTamano">0 MB</span>
                    <span class="success-stat__label">TAMAÑO TOTAL</span>
                </div>
                <div class="success-stat">
                    <span class="success-stat__num" id="statEstado">—</span>
                    <span class="success-stat__label">TIPO</span>
                </div>
            </div>

            <div class="success-actions">
                <button class="success-btn success-btn--outline" onclick="cerrarModalYSubirMas()">
                    Subir Más Fotos
                </button>
                <a href="{{ route('fotografo.sesiones.index') }}" class="success-btn success-btn--primary">
                    Volver a Sesiones
                </a>
            </div>
        </div>
    </div>

    {{-- ── MODAL CONFIRMACIÓN ENTREGADA ── --}}
    <div class="success-backdrop" id="entregadaModal">
        <div class="success-modal">
            <div style="margin-bottom: 20px;">
                <svg width="48" height="48" fill="none" stroke="#22c55e" viewBox="0 0 24 24" style="margin: 0 auto; display: block;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="success-title">Sesión Entregada</h2>
            <p class="success-desc">El cliente ha sido notificado por email y podrá descargar sus fotos desde su panel.</p>
            <div class="success-actions" style="justify-content: center;">
                <a href="{{ route('fotografo.sesiones.index') }}" class="success-btn success-btn--primary">
                    Volver a Sesiones
                </a>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .btn-volver {
                font-size: 13px;
                font-weight: 500;
                color: var(--navy);
                background: var(--white);
                border: 1px solid var(--border);
                padding: 7px 14px;
                border-radius: 8px;
                text-decoration: none;
                transition: background 0.15s;
            }
            .btn-volver:hover { background: var(--bg); }

            /* ── Info sesión ── */
            .sesion-info-card {
                background: var(--white);
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 20px 24px;
                margin-bottom: 20px;
            }

            .sesion-tipo {
                font-family: 'Playfair Display', serif;
                font-size: 17px;
                font-weight: 600;
                color: var(--navy);
                margin: 0 0 4px 0;
            }

            .sesion-id { font-size: 12px; color: var(--muted); }

            .sesion-meta {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 16px;
                padding-top: 16px;
                border-top: 1px solid var(--border);
            }
            @media (max-width: 640px) { .sesion-meta { grid-template-columns: repeat(2, 1fr); } }

            .meta-label {
                display: block;
                font-size: 10px;
                font-weight: 600;
                letter-spacing: 0.08em;
                color: var(--muted);
                margin-bottom: 4px;
            }
            .meta-value { font-size: 13px; color: var(--navy); font-weight: 500; }

            /* ── Upload card ── */
            .upload-card {
                background: var(--white);
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 24px;
                margin-bottom: 20px;
            }

            .upload-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 16px;
            }

            .upload-label { font-size: 14px; font-weight: 600; color: var(--navy); }

            .estado-selector { display: flex; gap: 16px; }

            .estado-option {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 13px;
                color: var(--navy);
                cursor: pointer;
            }
            .estado-option input[type="radio"] { accent-color: #E8A020; }

            .ayudante-aviso {
                font-size: 12px;
                color: #92400e;
                background: #fef3c7;
                border: 1px solid #fde68a;
                border-radius: 8px;
                padding: 10px 14px;
                margin: 0 0 16px 0;
                line-height: 1.5;
            }

            /* ── Dropzone ── */
            .dropzone {
                border: 2px dashed var(--border);
                border-radius: 10px;
                padding: 48px 24px;
                text-align: center;
                cursor: pointer;
                transition: border-color 0.2s, background 0.2s;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .dropzone:hover, .dropzone.dragover {
                border-color: #E8A020;
                background: #fdf8f0;
            }

            .dropzone-title {
                font-family: 'Playfair Display', serif;
                font-size: 18px;
                color: var(--navy);
                margin: 0 0 6px 0;
            }

            .dropzone-sub { font-size: 13px; color: var(--muted); margin: 0 0 20px 0; }

            .btn-select {
                background: #E8A020;
                color: #fff;
                border: none;
                padding: 10px 22px;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.15s;
            }
            .btn-select:hover { background: #c98b18; }

            /* ── File preview ── */
            .file-preview {
                margin-top: 16px;
                border-top: 1px solid var(--border);
                padding-top: 16px;
            }

            .preview-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }

            .preview-count { font-size: 13px; font-weight: 600; color: var(--navy); }

            .btn-clear {
                font-size: 12px;
                color: var(--muted);
                background: none;
                border: none;
                cursor: pointer;
                text-decoration: underline;
            }

            .file-list {
                display: flex;
                flex-direction: column;
                gap: 6px;
                max-height: 220px;
                overflow-y: auto;
            }

            .file-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px 12px;
                background: var(--bg, #f9f9f9);
                border-radius: 8px;
                font-size: 13px;
                gap: 8px;
            }

            .file-item-name {
                color: var(--navy);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                flex: 1;
            }

            .file-item-size { color: var(--muted); font-size: 12px; white-space: nowrap; }

            .file-item-status {
                font-size: 11px;
                font-weight: 600;
                padding: 2px 8px;
                border-radius: 10px;
                white-space: nowrap;
            }
            .file-item-status.pending    { background: #fef3c7; color: #92400e; }
            .file-item-status.uploading  { background: #dbeafe; color: #1e40af; }
            .file-item-status.done       { background: #d1fae5; color: #065f46; }
            .file-item-status.error      { background: #fee2e2; color: #991b1b; }

            /* ── Progress ── */
            .progress-bar-wrap {
                background: var(--border);
                border-radius: 20px;
                height: 6px;
                overflow: hidden;
            }
            .progress-bar {
                height: 100%;
                background: #E8A020;
                border-radius: 20px;
                width: 0%;
                transition: width 0.3s;
            }
            .progress-text { font-size: 12px; color: var(--muted); margin: 6px 0 0 0; text-align: center; }

            /* ── Botones de acción ── */
            .btn-upload {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                background: var(--navy, #1a2340);
                color: #fff;
                border: none;
                padding: 11px 24px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: opacity 0.15s;
            }
            .btn-upload:hover { opacity: 0.85; }
            .btn-upload:disabled { opacity: 0.5; cursor: not-allowed; }

            .btn-termine {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                background: #16a34a;
                color: #fff;
                border: none;
                padding: 11px 22px;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: opacity 0.15s;
            }
            .btn-termine:hover { opacity: 0.88; }

            .btn-entregar {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                background: #16a34a;
                color: #fff;
                border: none;
                padding: 11px 22px;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: opacity 0.15s;
            }
            .btn-entregar:hover { opacity: 0.88; }
            .btn-entregar:disabled { opacity: 0.5; cursor: not-allowed; }

            .entregar-hint {
                font-size: 12px;
                color: var(--muted);
                margin: 0;
            }

            /* ── Fotos ya subidas ── */
            .uploaded-section { margin-top: 8px; }
            .uploaded-title { font-size: 14px; font-weight: 600; color: var(--navy); margin: 0 0 12px 0; }

            .uploaded-grid { display: flex; flex-direction: column; gap: 6px; }

            .uploaded-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 14px;
                background: var(--white);
                border: 1px solid var(--border);
                border-radius: 8px;
            }

            .uploaded-thumb {
                width: 36px;
                height: 36px;
                background: var(--bg, #f5f5f5);
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .uploaded-info {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex: 1;
                gap: 8px;
            }

            .uploaded-name {
                font-size: 13px;
                color: var(--navy);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 260px;
            }

            .uploaded-estado {
                font-size: 11px;
                font-weight: 600;
                padding: 2px 8px;
                border-radius: 10px;
                white-space: nowrap;
            }
            .uploaded-estado.original              { background: #ede9fe; color: #5b21b6; }
            .uploaded-estado.editada               { background: #dbeafe; color: #1e40af; }
            .uploaded-estado.entregada             { background: #d1fae5; color: #065f46; }
            .uploaded-estado.pendiente_edicion     { background: #fef3c7; color: #92400e; }
            .uploaded-estado.pendiente-aprobacion  { background: #fee2e2; color: #991b1b; }

            /* ── Pendientes edición ── */
            .pendientes-card {
                background: var(--white);
                border: 1.5px solid #E8A020;
                border-radius: 12px;
                padding: 20px 24px;
                margin-bottom: 24px;
            }
            .pendientes-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 16px;
            }
            .pendientes-header-left { display: flex; align-items: center; gap: 10px; }
            .pendientes-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 26px;
                height: 26px;
                border-radius: 50%;
                background: #E8A020;
                color: #fff;
                font-size: 12px;
                font-weight: 700;
                flex-shrink: 0;
            }
            .pendientes-title { font-size: 14px; font-weight: 600; color: var(--navy); margin: 0; }
            .pendientes-hint  { font-size: 12px; color: var(--muted); }
            .pendientes-list  { display: flex; flex-direction: column; gap: 6px; }
            .pendiente-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 9px 12px;
                background: #FFFBF2;
                border: 1px solid #F5E0B0;
                border-radius: 8px;
                transition: background 0.2s, border-color 0.2s;
            }
            .pendiente-item.ya-editada {
                background: #f0fdf4;
                border-color: #bbf7d0;
            }
            .pendiente-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                border-radius: 6px;
                background: #FEF3DC;
                color: #E8A020;
                flex-shrink: 0;
            }
            .pendiente-item.ya-editada .pendiente-icon {
                background: #dcfce7;
                color: #16a34a;
            }
            .pendiente-nombre {
                flex: 1;
                font-size: 13px;
                font-weight: 500;
                color: var(--navy);
                font-family: 'Courier New', monospace;
                word-break: break-all;
            }
            .pendiente-estado {
                font-size: 11px;
                font-weight: 600;
                color: #C07800;
                background: #FEF3DC;
                border: 1px solid #F5D78A;
                padding: 2px 8px;
                border-radius: 20px;
                white-space: nowrap;
                flex-shrink: 0;
            }
            .pendiente-estado.entregada {
                color: #15803d;
                background: #dcfce7;
                border-color: #86efac;
            }

            /* ── Pendientes de aprobación (editadas de ayudantes) ── */
            .aprobacion-card {
                background: var(--white);
                border: 1.5px solid #b91c1c;
                border-radius: 12px;
                padding: 20px 24px;
                margin-bottom: 24px;
            }
            .aprobacion-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 26px;
                height: 26px;
                border-radius: 50%;
                background: #b91c1c;
                color: #fff;
                font-size: 12px;
                font-weight: 700;
                flex-shrink: 0;
            }
            .aprobacion-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 9px 12px;
                background: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 8px;
            }
            .aprobacion-icon {
                background: #fee2e2;
                color: #b91c1c;
            }
            .aprobacion-autor {
                font-size: 12px;
                color: var(--muted);
                white-space: nowrap;
            }
            .aprobacion-acciones {
                display: flex;
                gap: 8px;
                flex-shrink: 0;
            }
            .btn-confirmar {
                background: #16a34a;
                color: #fff;
                border: none;
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
                cursor: pointer;
            }
            .btn-rechazar {
                background: var(--white);
                color: #b91c1c;
                border: 1px solid #fecaca;
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
                cursor: pointer;
            }

            /* ── Modal ── */
            .success-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
                z-index: 200;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(4px);
            }
            .success-backdrop.open { display: flex; }

            .success-modal {
                background: var(--white);
                border-radius: 18px;
                padding: 48px 40px 40px;
                width: 100%;
                max-width: 500px;
                margin: 16px;
                text-align: center;
                box-shadow: 0 24px 80px rgba(0,0,0,0.18);
                animation: successIn 0.25s ease;
            }
            @keyframes successIn {
                from { opacity: 0; transform: translateY(16px) scale(0.97); }
                to   { opacity: 1; transform: translateY(0) scale(1); }
            }

            .success-title {
                font-family: 'Playfair Display', serif;
                font-size: 26px;
                font-weight: 600;
                color: var(--navy);
                margin: 0 0 12px;
            }

            .success-desc {
                font-size: 14px;
                color: var(--muted);
                line-height: 1.6;
                margin: 0 0 36px;
                max-width: 340px;
                margin-left: auto;
                margin-right: auto;
            }

            .success-stats {
                display: flex;
                justify-content: center;
                gap: 40px;
                margin-bottom: 40px;
                padding: 24px 0;
                border-top: 1px solid var(--border);
                border-bottom: 1px solid var(--border);
            }

            .success-stat {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 6px;
            }

            .success-stat__num {
                font-family: 'Playfair Display', serif;
                font-size: 28px;
                font-weight: 600;
                color: var(--navy);
            }

            .success-stat__label {
                font-size: 10px;
                font-weight: 600;
                letter-spacing: 0.1em;
                color: var(--muted);
            }
            .success-actions { display: flex; gap: 12px; justify-content: center; }
            .success-btn {
                padding: 11px 24px;
                border-radius: 9px;
                font-family: 'DM Sans', sans-serif;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                transition: opacity 0.15s, background 0.15s;
            }
            .success-btn--outline {
                background: var(--white);
                border: 1.5px solid var(--border);
                color: var(--navy);
            }
            .success-btn--outline:hover { background: var(--cloud); }
            .success-btn--primary {
                background: #E8A020;
                border: none;
                color: #fff;
            }
            .success-btn--primary:hover { opacity: 0.88; }
        </style>
    @endpush

    @push('scripts')
        <script>
            const sesionId  = {{ $sesion->id }};
            const csrfToken = '{{ csrf_token() }}';
            const entregaUrl = '{{ route('fotografo.fotografias.entregar', $sesion->id) }}';

            let selectedFiles  = [];
            let totalSubidas   = 0;  // acumulado de todas las tandas
            let totalMBSubidos  = 0;

            const dropzone        = document.getElementById('dropzone');
            const fileInput       = document.getElementById('fileInput');
            const filePreview     = document.getElementById('filePreview');
            const fileList        = document.getElementById('fileList');
            const fileCount       = document.getElementById('fileCount');
            const uploadActions   = document.getElementById('uploadActions');
            const progressSection = document.getElementById('progressSection');
            const progressBar     = document.getElementById('progressBar');
            const progressText    = document.getElementById('progressText');
            const termineSection  = document.getElementById('termineSection');
            const uploadedSection = document.getElementById('uploadedSection');
            const uploadedGrid    = document.getElementById('uploadedGrid');
            const uploadedCount   = document.getElementById('uploadedCount');

            // ── Dropzone events ──
            dropzone.addEventListener('dragover',  e => { e.preventDefault(); dropzone.classList.add('dragover'); });
            dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
            dropzone.addEventListener('drop', e => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
                handleFiles(Array.from(e.dataTransfer.files));
            });
            dropzone.addEventListener('click', e => {
                if (!e.target.closest('.btn-select')) fileInput.click();
            });
            fileInput.addEventListener('change', () => handleFiles(Array.from(fileInput.files)));

            function handleFiles(files) {
                selectedFiles = [...selectedFiles, ...files];
                renderFileList();
            }

            function renderFileList() {
                if (selectedFiles.length === 0) {
                    filePreview.style.display  = 'none';
                    uploadActions.style.display = 'none';
                    return;
                }
                filePreview.style.display   = 'block';
                uploadActions.style.display = 'flex';
                fileCount.textContent = `${selectedFiles.length} archivo${selectedFiles.length !== 1 ? 's' : ''} seleccionado${selectedFiles.length !== 1 ? 's' : ''}`;
                fileList.innerHTML = selectedFiles.map((f, i) => `
                    <div class="file-item" id="file-item-${i}">
                        <span class="file-item-name">${f.name}</span>
                        <span class="file-item-size">${(f.size / 1024 / 1024).toFixed(1)} MB</span>
                        <span class="file-item-status pending" id="status-${i}">Pendiente</span>
                    </div>
                `).join('');
            }

            function clearFiles() {
                selectedFiles = [];
                fileInput.value = '';
                filePreview.style.display    = 'none';
                uploadActions.style.display  = 'none';
                progressSection.style.display = 'none';
                progressBar.style.width = '0%';
            }

            async function uploadFiles() {
                if (selectedFiles.length === 0) return;

                const estadoInput = document.querySelector('input[name="estado_sel"]:checked')
                    ?? document.querySelector('input[name="estado_sel"]');
                const estado = estadoInput.value;
                const btn    = document.getElementById('uploadBtn');
                btn.disabled = true;
                progressSection.style.display = 'block';
                progressBar.style.width = '0%';

                let completados = 0;
                let errores     = 0;
                const tandaMB   = selectedFiles.reduce((acc, f) => acc + f.size, 0) / 1024 / 1024;

                for (let i = 0; i < selectedFiles.length; i++) {
                    const statusEl = document.getElementById(`status-${i}`);
                    statusEl.textContent = 'Subiendo...';
                    statusEl.className   = 'file-item-status uploading';

                    progressText.textContent = `Subiendo ${i + 1} de ${selectedFiles.length}...`;

                    const formData = new FormData();
                    formData.append('fotos[]', selectedFiles[i]);
                    formData.append('estado',  estado);
                    formData.append('_token',  csrfToken);

                    try {
                        const res  = await fetch(`/fotografo/sesiones/${sesionId}/fotografias`, {
                            method: 'POST',
                            body:   formData,
                        });
                        const data = await res.json();

                        if (res.ok) {
                            statusEl.textContent = 'Listo ✓';
                            statusEl.className   = 'file-item-status done';

                            // Agregar a la lista en vivo
                            if (data.fotos && data.fotos.length > 0) {
                                appendFotoAlGrid(data.fotos[0]);
                            }
                        } else {
                            statusEl.textContent = 'Error';
                            statusEl.className   = 'file-item-status error';
                            errores++;
                        }
                    } catch (e) {
                        statusEl.textContent = 'Error';
                        statusEl.className   = 'file-item-status error';
                        errores++;
                    }

                    completados++;
                    progressBar.style.width = Math.round((completados / selectedFiles.length) * 100) + '%';

                }
                // mostrar el mensaje de exito cuando el proceso de subida termina
                progressText.textContent = '¡Todas las fotos fueron subidas!';
                btn.disabled = false;

                // Calcular stats
                const totalMB = selectedFiles.reduce((acc, f) => acc + f.size, 0) / 1024 / 1024;
                document.getElementById('statArchivos').textContent = selectedFiles.length;
                document.getElementById('statTamano').textContent   = totalMB.toFixed(1) + ' MB';

                // Mostrar modal
                //document.getElementById('successModal').classList.add('open'); esperar a que el fotografo confirme la subida
                termineSection.style.display = 'block';

                progressText.textContent = '¡Todas las fotos fueron subidas!';
                btn.disabled = false;
                //setTimeout(() => window.location.reload(), 1500);
            }

            function abrirModalExito() {
                @if($sesion->estado === 'EN_EDICION')
                    termineSection.style.display = 'none';

                // Mostrar el botón de entregar dinámicamente
                const hint = document.querySelector('.entregar-hint');
                if (hint) hint.style.display = 'none';

                let btnEntregar = document.getElementById('btnEntregar');
                if (!btnEntregar) {
                    // El botón no existía en el DOM (aún había pendientes al cargar)
                    // lo creamos y lo insertamos al final de pendientes-card
                    const wrap = document.createElement('div');
                    wrap.id = 'btnEntregarWrap';
                    wrap.style.cssText = 'margin-top: 20px; text-align: right;';
                    wrap.innerHTML = `
                            <button type="button" class="btn-entregar" id="btnEntregar" onclick="marcarEntregada()">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Marcar sesión como entregada
                            </button>
                        `;
                    const card = document.querySelector('.pendientes-card');
                    if (card) card.appendChild(wrap);
                } else {
                    // El botón ya existía pero estaba oculto
                    btnEntregar.closest('div').style.display = 'block';
                    btnEntregar.disabled = false;
                }
                @else
                document.getElementById('successModal').classList.add('open');
                @endif
            }

            function cerrarModalYSubirMas() {
                document.getElementById('successModal').classList.remove('open');
                clearFiles();
                termineSection.style.display  = 'none';
                progressSection.style.display = 'none';
                progressBar.style.width = '0%';
            }

            function appendFotoAlGrid(foto) {
                // Mostrar sección si estaba oculta
                uploadedSection.style.display = '';

                // Actualizar contador
                const count = parseInt(uploadedCount.textContent || '0') + 1;
                uploadedCount.textContent = count;

                // Agregar item al grid
                const div = document.createElement('div');
                div.className = 'uploaded-item';

                const badgeClass = foto.aprobada ? foto.estado_lower : 'pendiente-aprobacion';
                const badgeTexto = foto.aprobada ? foto.estado : 'Pendiente de aprobación';

                div.innerHTML = `
                    <div class="uploaded-thumb">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--muted)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="uploaded-info">
                        <span class="uploaded-name">${foto.nombre}</span>
                        <span class="uploaded-estado ${badgeClass}">${badgeTexto}</span>
                    </div>
                `;
                uploadedGrid.appendChild(div);
            }

            async function marcarEntregada() {
                const btn = document.getElementById('btnEntregar');
                if (!btn) return;
                btn.disabled = true;
                btn.textContent = 'Procesando...';

                try {
                    const res = await fetch(entregaUrl, {
                        method:  'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                        },
                    });

                    if (res.ok) {
                        document.getElementById('entregadaModal').classList.add('open');
                    } else {
                        btn.disabled = false;
                        btn.textContent = 'Marcar sesión como entregada';
                        alert('Ocurrió un error al marcar la sesión. Intenta de nuevo.');
                    }
                } catch (e) {
                    btn.disabled = false;
                    btn.textContent = 'Marcar sesión como entregada';
                    alert('Error de conexión. Intenta de nuevo.');
                }
            }

            async function aprobarFoto(id) {
                try {
                    const res = await fetch(`/fotografo/fotografias/${id}/aprobar`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                    });
                    if (!res.ok) throw new Error('Error al aprobar');
                    document.getElementById(`aprobacion-item-${id}`).remove();
                    actualizarBadgeAprobacion();
                } catch (e) {
                    alert('No se pudo aprobar la foto. Intenta de nuevo.');
                }
            }

            async function rechazarFoto(id) {
                if (!confirm('¿Seguro que quieres rechazar y eliminar esta foto?')) return;
                try {
                    const res = await fetch(`/fotografo/fotografias/${id}/rechazar`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                    });
                    if (!res.ok) throw new Error('Error al rechazar');
                    document.getElementById(`aprobacion-item-${id}`).remove();
                    actualizarBadgeAprobacion();
                } catch (e) {
                    alert('No se pudo rechazar la foto. Intenta de nuevo.');
                }
            }

            function actualizarBadgeAprobacion() {
                const card = document.getElementById('aprobacionCard');
                if (!card) return;
                const restantes = card.querySelectorAll('.aprobacion-item').length;
                if (restantes === 0) {
                    card.remove();
                } else {
                    card.querySelector('.aprobacion-badge').textContent = restantes;
                }
            }

            // ── Simulación dev ──
            function simularSubida() {
                // Archivos falsos para simular
                const archivos = [
                    { name: 'DSC_0001.jpg', size: 4.2 },
                    { name: 'DSC_0002.jpg', size: 3.8 },
                    { name: 'DSC_0003.jpg', size: 5.1 },
                ];

                const total = archivos.length;
                let completados = 0;

                progressSection.style.display = 'block';
                progressBar.style.width = '0%';

                const intervalo = setInterval(() => {
                    completados++;
                    progressBar.style.width   = Math.round((completados / total) * 100) + '%';
                    progressText.textContent  = `Subiendo ${completados} de ${total}...`;

                    if (completados >= total) {
                        clearInterval(intervalo);
                        progressText.textContent = '¡Todas las fotos fueron subidas!';
                        totalSubidas   += total;
                        totalMBSubidos += archivos.reduce((acc, f) => acc + f.size, 0);
                        termineSection.style.display = 'block';
                    }
                }, 200);
            }
        </script>
    @endpush
@endsection
