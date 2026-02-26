<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jikko Puntos Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-star"></i> Jikko Puntos</h2>
                <button class="toggle-btn" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <nav class="sidebar-menu">
                <a href="#" class="menu-item active" data-section="campañas">
                    <i class="fas fa-bullhorn"></i>
                    <span>Campañas</span>
                </a>
                <a href="#" class="menu-item" data-section="ingles">
                    <i class="fas fa-language"></i>
                    <span>Inglés</span>
                </a>
                <a href="#" class="menu-item" data-section="pausas">
                    <i class="fas fa-pause-circle"></i>
                    <span>Pausas</span>
                </a>
                <a href="#" class="menu-item" data-section="puntos-adicionales">
                    <i class="fas fa-plus-circle"></i>
                    <span>Puntos Adicionales</span>
                </a>
                <a href="#" class="menu-item" data-section="totales">
                    <i class="fas fa-chart-pie"></i>
                    <span>Totales</span>
                </a>
                <div class="menu-divider"></div>
                <a href="#" class="menu-item" data-section="sync">
                    <i class="fas fa-sync"></i>
                    <span>Sincronizar Google Sheets</span>
                </a>
                <a href="#" class="menu-item" data-section="config">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1 id="page-title">Campañas</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>Administrador</span>
                </div>
            </header>

            <div class="content-area">
                <!-- Sección Campañas -->
                <section id="campañas-section" class="content-section active">
                    <div class="section-header">
                        <h2>Gestión de Campañas</h2>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <i class="fas fa-bullhorn"></i>
                            <div class="stat-info">
                                <h3 id="total-campañas">0</h3>
                                <p>Total Campañas</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-check-circle"></i>
                            <div class="stat-info">
                                <h3 id="campañas-activas">0</h3>
                                <p>Empleados</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="filters-container">
                        <h3><i class="fas fa-filter"></i> Filtros</h3>
                        <div class="filters-grid">
                            <div class="filter-group">
                                <label for="filter-campanas-nombre"><i class="fas fa-user"></i> Nombre</label>
                                <input type="text" id="filter-campanas-nombre" class="filter-input" placeholder="Buscar por nombre">
                            </div>
                            <div class="filter-group">
                                <label for="filter-campanas-documento"><i class="fas fa-id-card"></i> Documento</label>
                                <input type="text" id="filter-campanas-documento" class="filter-input" placeholder="Buscar por documento">
                            </div>
                            <div class="filter-group">
                                <label for="filter-campanas-empresa"><i class="fas fa-building"></i> Empresa</label>
                                <input type="text" id="filter-campanas-empresa" class="filter-input" placeholder="Buscar por empresa">
                            </div>
                            <div class="filter-group">
                                <label for="filter-campanas-fecha-desde"><i class="fas fa-calendar"></i> Fecha Desde</label>
                                <input type="date" id="filter-campanas-fecha-desde" class="filter-input">
                            </div>
                            <div class="filter-group">
                                <label for="filter-campanas-fecha-hasta"><i class="fas fa-calendar"></i> Fecha Hasta</label>
                                <input type="date" id="filter-campanas-fecha-hasta" class="filter-input">
                            </div>
                        </div>
                        <div class="filter-actions">
                            <button class="btn btn-primary" onclick="aplicarFiltrosCampanas()">
                                <i class="fas fa-search"></i> Aplicar Filtros
                            </button>
                            <button class="btn btn-secondary" onclick="limpiarFiltrosCampanas()">
                                <i class="fas fa-times"></i> Limpiar Filtros
                            </button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Nombre</th>
                                    <th>Campaña / Actividad</th>
                                    <th>Fecha</th>
                                    <th>Empresa</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="campañas-table-body">
                                <tr>
                                    <td colspan="6" class="no-data">No hay datos disponibles. Cargue información desde Google Sheets.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Sección Inglés -->
                <section id="ingles-section" class="content-section">
                    <div class="section-header">
                        <h2>Evaluaciones de Inglés</h2>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <i class="fas fa-users"></i>
                            <div class="stat-info">
                                <h3 id="total-ingles">0</h3>
                                <p>Total Evaluaciones</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-trophy"></i>
                            <div class="stat-info">
                                <h3 id="puntos-ingles">0</h3>
                                <p>Puntos Totales</p>
                            </div>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Empleado</th>
                                    <th>Nivel</th>
                                    <th>Puntos</th>
                                    <th>Fecha Evaluación</th>
                                    <th>Certificación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="ingles-table-body">
                                <tr>
                                    <td colspan="7" class="no-data">No hay datos disponibles. Cargue información desde Google Sheets.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Sección Pausas -->
                <section id="pausas-section" class="content-section">
                    <div class="section-header">
                        <h2>Registro de Pausas</h2>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <i class="fas fa-pause"></i>
                            <div class="stat-info">
                                <h3 id="total-pausas">0</h3>
                                <p>Total Pausas</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-trophy"></i>
                            <div class="stat-info">
                                <h3 id="puntos-pausas">0</h3>
                                <p>Puntos Totales</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-list"></i>
                            <div class="stat-info">
                                <h3 id="duracion-pausas">0</h3>
                                <p>Tipos de Pausas</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="filters-container">
                        <div class="filters-grid">
                            <div class="filter-group">
                                <label><i class="fas fa-user"></i> Nombre</label>
                                <input type="text" id="filter-pausas-nombre" class="filter-input" placeholder="Buscar por nombre">
                            </div>
                            
                            <div class="filter-group">
                                <label><i class="fas fa-id-card"></i> Documento</label>
                                <input type="text" id="filter-pausas-documento" class="filter-input" placeholder="Buscar por documento">
                            </div>
                            
                            <div class="filter-group">
                                <label><i class="fas fa-calendar-alt"></i> Fecha Inicio</label>
                                <input type="date" id="filter-pausas-fecha-desde" class="filter-input">
                            </div>
                            
                            <div class="filter-group">
                                <label><i class="fas fa-calendar-check"></i> Fecha Fin</label>
                                <input type="date" id="filter-pausas-fecha-hasta" class="filter-input">
                            </div>
                        </div>
                        
                        <div class="filter-actions">
                            <button class="btn btn-filter" onclick="aplicarFiltrosPausas()">
                                <i class="fas fa-filter"></i> Aplicar Filtros
                            </button>
                            <button class="btn btn-clear" onclick="limpiarFiltrosPausas()">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Nombre</th>
                                    <th>Tipo de Pausa</th>
                                    <th>Puntos</th>
                                    <th>Fecha</th>
                                    <th>Evidencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="pausas-table-body">
                                <tr>
                                    <td colspan="7" class="no-data">No hay datos disponibles. Cargue información desde Google Sheets.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Sección Puntos Adicionales -->
                <section id="puntos-adicionales-section" class="content-section">
                    <div class="section-header">
                        <h2>Puntos Adicionales</h2>
                        <div>
                            <button class="btn btn-info" onclick="window.open('api/diagnostico.php', '_blank')">
                                <i class="fas fa-clipboard-check"></i> Diagnóstico
                            </button>
                        </div>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <i class="fas fa-award"></i>
                            <div class="stat-info">
                                <h3 id="total-puntos-adicionales">0</h3>
                                <p>Total Registros</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-star"></i>
                            <div class="stat-info">
                                <h3 id="suma-puntos-adicionales">0</h3>
                                <p>Puntos Otorgados</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="filters-container">
                        <div class="filters-grid">
                            <div class="filter-group">
                                <label><i class="fas fa-user"></i> Nombre</label>
                                <input type="text" id="filter-nombre" class="filter-input" placeholder="Buscar por nombre">
                            </div>
                            
                            <div class="filter-group">
                                <label><i class="fas fa-id-card"></i> Documento</label>
                                <input type="text" id="filter-documento" class="filter-input" placeholder="Buscar por documento">
                            </div>
                            
                            <div class="filter-group">
                                <label><i class="fas fa-star"></i> Puntos</label>
                                <select id="filter-puntos" class="filter-select">
                                    <option value="">Todos</option>
                                    <option value="25">25 puntos</option>
                                    <option value="50">50 puntos</option>
                                    <option value="100">100 puntos</option>
                                    <option value="900">900 puntos</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label><i class="fas fa-calendar-alt"></i> Fecha Inicio</label>
                                <input type="date" id="filter-fecha-desde" class="filter-input">
                            </div>
                            
                            <div class="filter-group">
                                <label><i class="fas fa-calendar-check"></i> Fecha Fin</label>
                                <input type="date" id="filter-fecha-hasta" class="filter-input">
                            </div>
                            
                            <div class="filter-group">
                                <label><i class="fas fa-tasks"></i> Actividad</label>
                                <input type="text" id="filter-actividad" class="filter-input" placeholder="Buscar por actividad">
                            </div>
                        </div>
                        
                        <div class="filter-actions">
                            <button class="btn btn-filter" onclick="aplicarFiltros()">
                                <i class="fas fa-filter"></i> Aplicar Filtros
                            </button>
                            <button class="btn btn-clear" onclick="limpiarFiltros()">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Fecha</th>
                                    <th>Nombre</th>
                                    <th>Actividad</th>
                                    <th># de Puntos</th>
                                    <th>Responsable</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="puntos-adicionales-table-body">
                                <tr>
                                    <td colspan="7" class="no-data">No hay datos disponibles. Cargue información desde Google Sheets.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Sección Totales -->
                <section id="totales-section" class="content-section">
                    <div class="section-header">
                        <h2>Resumen General - Puntos por Empleado</h2>
                        <div>
                            <button class="btn btn-primary" onclick="loadTotales()">
                                <i class="fas fa-sync"></i> Actualizar Totales
                            </button>
                            <button class="btn btn-success" onclick="exportarTotalesExcel()">
                                <i class="fas fa-file-excel"></i> Exportar a Excel
                            </button>
                        </div>
                    </div>
                    
                    <div class="puntos-info-grid">
                        <div class="info-card">
                            <i class="fas fa-language"></i>
                            <span>Inglés: <strong>30 puntos</strong> por asistencia</span>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-pause-circle"></i>
                            <span>Pausas: <strong>15 puntos</strong> por pausa | Jueves pausar: <strong>50pts</strong></span>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-bullhorn"></i>
                            <span>Campañas: <strong>50 puntos</strong> por participación</span>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-plus-circle"></i>
                            <span>Adicionales: <strong>Puntos asignados</strong></span>
                        </div>
                    </div>

                    <div class="stats-summary">
                        <div class="summary-item">
                            <h3 id="total-empleados-sistema">0</h3>
                            <p>Empleados en el Sistema</p>
                        </div>
                        <div class="summary-item">
                            <h3 id="total-puntos-sistema">0</h3>
                            <p>Total de Puntos</p>
                        </div>
                        <div class="summary-item">
                            <h3 id="promedio-puntos-sistema">0</h3>
                            <p>Promedio de Puntos</p>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="filters-container">
                        <h3><i class="fas fa-filter"></i> Filtros</h3>
                        <div class="filters-grid">
                            <div class="filter-group">
                                <label for="filter-totales-nombre"><i class="fas fa-user"></i> Nombre</label>
                                <input type="text" id="filter-totales-nombre" class="filter-input" placeholder="Buscar por nombre">
                            </div>
                            <div class="filter-group">
                                <label for="filter-totales-documento"><i class="fas fa-id-card"></i> Documento</label>
                                <input type="text" id="filter-totales-documento" class="filter-input" placeholder="Buscar por documento">
                            </div>
                            <div class="filter-group">
                                <label for="filter-totales-fecha-desde"><i class="fas fa-calendar"></i> Fecha Desde</label>
                                <input type="date" id="filter-totales-fecha-desde" class="filter-input">
                            </div>
                            <div class="filter-group">
                                <label for="filter-totales-fecha-hasta"><i class="fas fa-calendar"></i> Fecha Hasta</label>
                                <input type="date" id="filter-totales-fecha-hasta" class="filter-input">
                            </div>
                        </div>
                        <div class="filter-actions">
                            <button class="btn btn-primary" onclick="aplicarFiltrosTotales()">
                                <i class="fas fa-search"></i> Aplicar Filtros
                            </button>
                            <button class="btn btn-secondary" onclick="limpiarFiltrosTotales()">
                                <i class="fas fa-times"></i> Limpiar Filtros
                            </button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Nombre</th>
                                    <th>Inglés</th>
                                    <th>Pausas</th>
                                    <th>Campañas</th>
                                    <th>Adicionales</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="totales-table-body">
                                <tr>
                                    <td colspan="7" class="no-data">Cargando datos...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Sección Sincronizar -->
                <section id="sync-section" class="content-section">
                    <div class="section-header">
                        <h2>Sincronización con Google Sheets</h2>
                    </div>
                    
                    <!-- Botón principal de sincronización -->
                    <div class="sync-main-action">
                        <div class="sync-main-card">
                            <div class="sync-main-icon">
                                <i class="fas fa-sync-alt fa-3x"></i>
                            </div>
                            <h3>Sincronizar Todos los Datos</h3>
                            <p>Carga toda la información de Google Sheets (Campañas, Inglés, Pausas y Puntos Adicionales) en una sola operación.</p>
                            <button class="btn btn-primary btn-lg" id="btn-sync-all" onclick="sincronizarTodo()">
                                <i class="fas fa-cloud-download-alt"></i> Sincronizar Todo Ahora
                            </button>
                            <div id="sync-progress" class="sync-progress" style="display:none;">
                                <div class="progress-bar">
                                    <div class="progress-fill" id="progress-fill"></div>
                                </div>
                                <p id="sync-status">Iniciando sincronización...</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="sync-container">
                        <div class="sync-card">
                            <h3><i class="fas fa-table"></i> Configurar Google Sheets (URLs CSV)</h3>
                            <p>Ingrese las URLs públicas de Google Sheets en formato CSV para cada tabla.</p>
                            <p class="info-box"><strong>Cómo obtener el enlace CSV:</strong><br>
                            1. Abrir Google Sheet → Archivo → Compartir → Publicar en la web<br>
                            2. Seleccionar la hoja específica<br>
                            3. Elegir formato "Valores separados por comas (.csv)"<br>
                            4. Copiar el enlace generado</p>
                            
                            <div class="form-group">
                                <label>URL CSV para Campañas:</label>
                                <input type="text" id="csv-url-campañas" class="form-control" placeholder="https://docs.google.com/spreadsheets/.../pub?output=csv">
                            </div>
                            
                            <div class="form-group">
                                <label>URL CSV para Inglés:</label>
                                <input type="text" id="csv-url-ingles" class="form-control" placeholder="https://docs.google.com/spreadsheets/.../pub?output=csv">
                            </div>
                            
                            <div class="form-group">
                                <label>URL CSV para Pausas:</label>
                                <input type="text" id="csv-url-pausas" class="form-control" placeholder="https://docs.google.com/spreadsheets/.../pub?output=csv" value="https://docs.google.com/spreadsheets/d/e/2PACX-1vS8Lo9tq18jV_YjjXxkyZuiH3KJYX7DWlVXQ3PSa7q9tle9PbnDbc4zOGD-8-T3oMy7giLw-9G05mz-/pub?gid=1986324007&single=true&output=csv">
                            </div>
                            
                            <div class="form-group">
                                <label>URL CSV para Puntos Adicionales:</label>
                                <input type="text" id="csv-url-puntos" class="form-control" placeholder="https://docs.google.com/spreadsheets/.../pub?output=csv" value="https://docs.google.com/spreadsheets/d/e/2PACX-1vT_YovS-kYOHIkiwx_YQyzMvixS52UQSihicIpKL0mv3Z2QZZShLLk-NnrANoQIKE7ZcbbWdxO40lQa/pub?gid=0&single=true&output=csv">
                            </div>
                            
                            <button class="btn btn-success" onclick="saveGoogleSheetsConfig()">
                                <i class="fas fa-save"></i> Guardar Configuración
                            </button>
                            <button class="btn btn-info" onclick="loadCurrentConfig()">
                                <i class="fas fa-sync"></i> Cargar Configuración Actual
                            </button>
                        </div>

                        <div class="sync-card">
                            <h3><i class="fas fa-history"></i> Historial de Sincronización</h3>
                            <div id="sync-history" class="sync-history">
                                <p class="no-data">No hay historial de sincronización disponible.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Sección Configuración -->
                <section id="config-section" class="content-section">
                    <div class="section-header">
                        <h2>Configuración del Sistema</h2>
                    </div>
                    
                    <div class="config-container">
                        <div class="config-card">
                            <h3><i class="fas fa-database"></i> Base de Datos</h3>
                            <p><strong>Host:</strong> localhost</p>
                            <p><strong>Puerto:</strong> 5432</p>
                            <p><strong>Base de Datos:</strong> jikkopuntos_v4</p>
                            <button class="btn btn-info" onclick="testDatabaseConnection()">
                                <i class="fas fa-plug"></i> Probar Conexión
                            </button>
                        </div>

                        <div class="config-card">
                            <h3><i class="fas fa-tools"></i> Mantenimiento</h3>
                            <button class="btn btn-warning" onclick="clearAllTables()">
                                <i class="fas fa-trash"></i> Limpiar Todas las Tablas
                            </button>
                            <button class="btn btn-danger" onclick="resetDatabase()">
                                <i class="fas fa-exclamation-triangle"></i> Resetear Base de Datos
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
