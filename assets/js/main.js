// Sidebar Toggle
const toggleSidebarButton = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');

function updateToggleButtonState() {
    const icon = toggleSidebarButton.querySelector('i');
    if (sidebar.classList.contains('collapsed')) {
        icon.className = 'fas fa-chevron-right';
        toggleSidebarButton.setAttribute('title', 'Expandir barra');
        toggleSidebarButton.setAttribute('aria-label', 'Expandir barra');
        toggleSidebarButton.setAttribute('aria-expanded', 'false');
    } else {
        icon.className = 'fas fa-bars';
        toggleSidebarButton.setAttribute('title', 'Colapsar barra');
        toggleSidebarButton.setAttribute('aria-label', 'Colapsar barra');
        toggleSidebarButton.setAttribute('aria-expanded', 'true');
    }
}

toggleSidebarButton.addEventListener('click', function() {
    sidebar.classList.toggle('collapsed');
    updateToggleButtonState();
});

// Ajuste inicial
updateToggleButtonState();

// Menu Navigation

// Menu Navigation
document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        
        // determine previously active section (if any)
        const prevItem = document.querySelector('.menu-item.active');
        const prevSection = prevItem ? prevItem.dataset.section : null;

        // Remove active class from all items
        document.querySelectorAll('.menu-item').forEach(mi => mi.classList.remove('active'));
        
        // Add active class to clicked item
        this.classList.add('active');
        
        // Hide all sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
        });
        
        // Show selected section
        const sectionName = this.dataset.section;
        const section = document.getElementById(sectionName + '-section');
        if (section) {
            section.classList.add('active');
            document.getElementById('page-title').textContent = this.querySelector('span').textContent;
        }
        
        // clear filters from previous section
        if (prevSection) {
            clearFiltersForSection(prevSection);
        }
        // also ensure filters for the new section start empty
        if (sectionName && sectionName !== prevSection) {
            clearFiltersForSection(sectionName);
        }
        
        // Load data for the section
        loadSectionData(sectionName);
    });
});

// Load data for specific section
function loadSectionData(section) {
    switch(section) {
        case 'campañas':
            loadCampañas();
            break;
        case 'ingles':
            loadIngles();
            break;
        case 'pausas':
            loadPausas();
            break;
        case 'puntos-adicionales':
            loadPuntosAdicionales();
            break;
        case 'totales':
            loadTotales();
            break;
        case 'sync':
            loadSyncHistory();
            break;
    }
}

// Clear filters for a given section
function clearFiltersForSection(section) {
    switch(section) {
        case 'campañas':
            limpiarFiltrosCampanas();
            break;
        case 'ingles':
            limpiarFiltrosIngles();
            break;
        case 'pausas':
            limpiarFiltrosPausas();
            break;
        case 'puntos-adicionales':
            limpiarFiltros();
            break;
        case 'totales':
            limpiarFiltrosTotales();
            break;
        // sync has no filters
    }
}

// Variables globales para almacenar todos los datos
let allCampanasData = [];
let allTotalesData = [];
let allInglesData = [];
let allPausasData = [];
let allPuntosAdicionalesData = [];

// --- sorting helpers ---------------------------------------------------
function sortDataArray(section, key, direction) {
    let arr;
    switch(section) {
        case 'campañas': arr = allCampanasData; break;
        case 'ingles': arr = allInglesData; break;
        case 'pausas': arr = allPausasData; break;
        case 'puntos-adicionales': arr = allPuntosAdicionalesData; break;
        case 'totales': arr = allTotalesData; break;
        default: return;
    }
    // perform sort
    arr.sort((a, b) => {
        let va, vb;
        if (section === 'pausas' && key === 'puntos') {
            va = calcularPuntosPausa(a.tipo_pausa);
            vb = calcularPuntosPausa(b.tipo_pausa);
        } else {
            va = a[key];
            vb = b[key];
        }
        if (va === undefined || va === null) va = '';
        if (vb === undefined || vb === null) vb = '';
        // try numeric comparison
        const na = parseFloat(va);
        const nb = parseFloat(vb);
        if (!isNaN(na) && !isNaN(nb)) {
            va = na;
            vb = nb;
        }
        if (va < vb) return direction === 'asc' ? -1 : 1;
        if (va > vb) return direction === 'asc' ? 1 : -1;
        return 0;
    });
    // update global variable so subsequent filters/resorts preserve order
    switch(section) {
        case 'campañas': allCampanasData = arr; break;
        case 'ingles': allInglesData = arr; break;
        case 'pausas': allPausasData = arr; break;
        case 'puntos-adicionales': allPuntosAdicionalesData = arr; break;
        case 'totales': allTotalesData = arr; break;
    }
    // re-render
    switch(section) {
        case 'campañas': renderCampañasTable(arr); break;
        case 'ingles': renderInglesTable(arr); break;
        case 'pausas': renderPausasTable(arr); break;
        case 'puntos-adicionales': renderPuntosAdicionalesTable(arr); break;
        case 'totales': renderTotalesTable(arr); break;
    }
}

function updateSortIcons(table, activeTh, direction) {
    table.querySelectorAll('th.sortable i').forEach(i => {
        i.className = 'fas fa-sort';
    });
    const icon = activeTh.querySelector('i');
    if (direction === 'asc') {
        icon.className = 'fas fa-sort-up';
    } else {
        icon.className = 'fas fa-sort-down';
    }
}

function attachSortHandlers() {
    document.querySelectorAll('table.data-table').forEach(table => {
        const section = table.dataset.section;
        if (!section) return;
        table.querySelectorAll('th.sortable').forEach(th => {
            th.addEventListener('click', () => {
                const key = th.dataset.key;
                const current = th.dataset.direction || 'asc';
                const direction = current === 'asc' ? 'desc' : 'asc';
                // clear directions from siblings
                table.querySelectorAll('th.sortable').forEach(x => { delete x.dataset.direction; });
                th.dataset.direction = direction;
                updateSortIcons(table, th, direction);
                sortDataArray(section, key, direction);
            });
        });
    });
}

// ----------------------------------------------------------------------
// Date filter validation helpers
// ----------------------------------------------------------------------

function getTodayString() {
    return new Date().toISOString().split('T')[0];
}

function validateDateFilters(desdeId, hastaId) {
    const desdeInput = document.getElementById(desdeId);
    const hastaInput = document.getElementById(hastaId);
    const today = getTodayString();
    
    if (!desdeInput || !hastaInput) return;
    
    const desdeValue = desdeInput.value;
    const hastaValue = hastaInput.value;
    
    // Disable "hasta" if "desde" is not selected
    hastaInput.disabled = !desdeValue;
    
    // Validate dates are not in the future
    if (desdeValue && desdeValue > today) {
        alert('La fecha "desde" no puede ser mayor a la fecha actual.');
        desdeInput.value = '';
        hastaInput.disabled = true;
        return;
    }
    
    if (hastaValue && hastaValue > today) {
        alert('La fecha "hasta" no puede ser mayor a la fecha actual.');
        hastaInput.value = '';
        return;
    }
    
    // Validate desde <= hasta
    if (desdeValue && hastaValue && desdeValue > hastaValue) {
        alert('La fecha "desde" no puede ser mayor a la fecha "hasta".');
        desdeInput.value = '';
        hastaInput.disabled = true;
        return;
    }
}

function setupDateFilterValidation(desdeId, hastaId) {
    const desdeInput = document.getElementById(desdeId);
    const hastaInput = document.getElementById(hastaId);
    
    if (desdeInput) {
        desdeInput.addEventListener('change', () => validateDateFilters(desdeId, hastaId));
    }
    if (hastaInput) {
        hastaInput.addEventListener('change', () => validateDateFilters(desdeId, hastaId));
    }
    
    // Initial validation
    validateDateFilters(desdeId, hastaId);
}

// ----------------------------------------------------------------------

// Función helper para calcular puntos de pausas
function calcularPuntosPausa(tipoPausa) {
    if (!tipoPausa) return 15;
    
    const tipo = tipoPausa.toString().trim();
    
    // Jueves de pausar con todos = 50 puntos
    if (tipo.toLowerCase().includes('jueves de pausar con todos')) {
        return 50;
    }
    
    // Si es un número (1, 2, 3), dar 15 puntos fijos
    if (tipo === '1' || tipo === '2' || tipo === '3') {
        return 15;
    }
    
    // Por defecto
    return 15;
}

// Load Campañas
async function loadCampañas() {
    try {
        const response = await fetch('api/get_data.php?table=participacion_campanas');
        const data = await response.json();
        
        if (data.success) {
            // Filtrar registros sin fecha
            allCampanasData = data.data.filter(row => row.fecha && row.fecha.trim() !== '');
            updateCampañasStats(allCampanasData);
            renderCampañasTable(allCampanasData);
        }
    } catch (error) {
        console.error('Error loading campañas:', error);
    }
}

function updateCampañasStats(data) {
    document.getElementById('total-campañas').textContent = data.length;
    const empleadosUnicos = new Set(data.map(c => c.empleado_id)).size;
    document.getElementById('campañas-activas').textContent = empleadosUnicos;
}

function renderCampañasTable(data) {
    const tbody = document.getElementById('campañas-table-body');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="no-data">No hay datos disponibles.</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(row => `
        <tr>
            <td>${row.empleado_id || '-'}</td>
            <td>${row.empleado_nombre}</td>
            <td>${row.nombre_campana || '-'}</td>
            <td>${row.fecha || '-'}</td>
            <td>${row.empresa || '-'}</td>
        </tr>
    `).join('');
}

// Aplicar Filtros de Campañas
function aplicarFiltrosCampanas() {
    const nombreFilter = document.getElementById('filter-campanas-nombre').value.toLowerCase();
    const documentoFilter = document.getElementById('filter-campanas-documento').value;
    const empresaFilter = document.getElementById('filter-campanas-empresa').value.toLowerCase();
    const fechaDesde = document.getElementById('filter-campanas-fecha-desde').value;
    const fechaHasta = document.getElementById('filter-campanas-fecha-hasta').value;
    
    const filteredData = allCampanasData.filter(row => {
        // Filtro por nombre
        if (nombreFilter && !row.empleado_nombre.toLowerCase().includes(nombreFilter)) {
            return false;
        }
        
        // Filtro por documento
        if (documentoFilter && !String(row.empleado_id).includes(documentoFilter)) {
            return false;
        }
        
        // Filtro por empresa
        if (empresaFilter && row.empresa && !row.empresa.toLowerCase().includes(empresaFilter)) {
            return false;
        }
        
        // Filtro por fecha desde
        if (fechaDesde && row.fecha && row.fecha < fechaDesde) {
            return false;
        }
        
        // Filtro por fecha hasta
        if (fechaHasta && row.fecha && row.fecha > fechaHasta) {
            return false;
        }
        
        return true;
    });
    
    updateCampañasStats(filteredData);
    renderCampañasTable(filteredData);
    
    // Mostrar mensaje con resultados
    const mensaje = `Se encontraron ${filteredData.length} registros de ${allCampanasData.length} totales`;
    console.log(mensaje);
}

// Limpiar Filtros de Campañas
function limpiarFiltrosCampanas() {
    document.getElementById('filter-campanas-nombre').value = '';
    document.getElementById('filter-campanas-documento').value = '';
    document.getElementById('filter-campanas-empresa').value = '';
    document.getElementById('filter-campanas-fecha-desde').value = '';
    document.getElementById('filter-campanas-fecha-hasta').value = '';
    
    // Re-enable hasta input
    document.getElementById('filter-campanas-fecha-hasta').disabled = true;
    
    updateCampañasStats(allCampanasData);
    renderCampañasTable(allCampanasData);
}

// Aplicar Filtros de Inglés
function aplicarFiltrosIngles() {
    const nombreFilter = document.getElementById('filter-ingles-nombre').value.toLowerCase();
    const documentoFilter = document.getElementById('filter-ingles-documento').value;
    const nivelFilter = document.getElementById('filter-ingles-nivel').value.toLowerCase();
    const fechaDesde = document.getElementById('filter-ingles-fecha-desde').value;
    const fechaHasta = document.getElementById('filter-ingles-fecha-hasta').value;
    
    const filteredData = allInglesData.filter(row => {
        if (nombreFilter && !row.empleado_nombre.toLowerCase().includes(nombreFilter)) {
            return false;
        }
        if (documentoFilter && !String(row.empleado_id).includes(documentoFilter)) {
            return false;
        }
        if (nivelFilter && row.nivel && !row.nivel.toLowerCase().includes(nivelFilter)) {
            return false;
        }
        if (fechaDesde && row.fecha_evaluacion && row.fecha_evaluacion < fechaDesde) {
            return false;
        }
        if (fechaHasta && row.fecha_evaluacion && row.fecha_evaluacion > fechaHasta) {
            return false;
        }
        return true;
    });
    
    updateInglesStats(filteredData);
    renderInglesTable(filteredData);
}

// Limpiar Filtros de Inglés
function limpiarFiltrosIngles() {
    document.getElementById('filter-ingles-nombre').value = '';
    document.getElementById('filter-ingles-documento').value = '';
    document.getElementById('filter-ingles-nivel').value = '';
    document.getElementById('filter-ingles-fecha-desde').value = '';
    document.getElementById('filter-ingles-fecha-hasta').value = '';

    // Re-enable hasta input
    document.getElementById('filter-ingles-fecha-hasta').disabled = true;

    updateInglesStats(allInglesData);
    renderInglesTable(allInglesData);
}

// Load Inglés
async function loadIngles() {
    try {
        const response = await fetch('api/get_data.php?table=ingles');
        const data = await response.json();
        
        if (data.success) {
            // Filtrar registros sin fecha
            allInglesData = data.data.filter(row => row.fecha_evaluacion && row.fecha_evaluacion.trim() !== '');
            updateInglesStats(allInglesData);
            renderInglesTable(allInglesData);
        }
    } catch (error) {
        console.error('Error loading inglés:', error);
    }
}

function updateInglesStats(data) {
    document.getElementById('total-ingles').textContent = data.length;
    // Cada asistencia da 30 puntos
    const totalPuntos = data.length * 30;
    document.getElementById('puntos-ingles').textContent = totalPuntos;
}

function renderInglesTable(data) {
    const tbody = document.getElementById('ingles-table-body');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="no-data">No hay datos disponibles.</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(row => `
        <tr>
            <td>${row.empleado_id || '-'}</td>
            <td>${row.empleado_nombre}</td>
            <td>${row.nivel || '-'}</td>
            <td><strong>30</strong></td>
            <td>${row.fecha_evaluacion || '-'}</td>
        </tr>
    `).join('');
}

// Load Pausas
async function loadPausas() {
    try {
        const response = await fetch('api/get_data.php?table=pausas');
        const data = await response.json();
        
        if (data.success) {
            // Filtrar registros sin fecha
            allPausasData = data.data.filter(row => row.fecha && row.fecha.trim() !== '');
            updatePausasStats(allPausasData);
            renderPausasTable(allPausasData);
        }
    } catch (error) {
        console.error('Error loading pausas:', error);
    }
}

function updatePausasStats(data) {
    document.getElementById('total-pausas').textContent = data.length;
    
    // Calcular puntos totales
    const puntosTotales = data.reduce((sum, row) => sum + calcularPuntosPausa(row.tipo_pausa), 0);
    document.getElementById('puntos-pausas').textContent = puntosTotales.toLocaleString();
    
    // Contar distintos tipos de pausa
    const tiposUnicos = new Set(data.map(row => row.tipo_pausa)).size;
    document.getElementById('duracion-pausas').textContent = tiposUnicos;
}

function renderPausasTable(data) {
    const tbody = document.getElementById('pausas-table-body');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="no-data">No hay datos disponibles.</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(row => {
        // Calcular puntos según el tipo de pausa
        const puntos = calcularPuntosPausa(row.tipo_pausa);
        
        // Extraer URL de evidencia de observaciones
        const evidenciaMatch = row.observaciones && row.observaciones.match(/Evidencia: (https?:\/\/[^\s|]+)/);
        const evidenciaUrl = evidenciaMatch ? evidenciaMatch[1] : null;
        const evidenciaHtml = evidenciaUrl ? 
            `<a href="${evidenciaUrl}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-image"></i> Ver</a>` : 
            '-';
        
        return `
        <tr>
            <td>${row.empleado_id || '-'}</td>
            <td>${row.empleado_nombre}</td>
            <td>${row.tipo_pausa || '-'}</td>
            <td><strong>${puntos}</strong></td>
            <td>${row.fecha || '-'}</td>
            <td>${evidenciaHtml}</td>
        </tr>
        `;
    }).join('');
}

// Aplicar Filtros de Pausas
function aplicarFiltrosPausas() {
    const nombreFilter = document.getElementById('filter-pausas-nombre').value.toLowerCase();
    const documentoFilter = document.getElementById('filter-pausas-documento').value;
    const fechaDesde = document.getElementById('filter-pausas-fecha-desde').value;
    const fechaHasta = document.getElementById('filter-pausas-fecha-hasta').value;
    
    const filteredData = allPausasData.filter(row => {
        // Filtro por nombre
        if (nombreFilter && !row.empleado_nombre.toLowerCase().includes(nombreFilter)) {
            return false;
        }
        
        // Filtro por documento
        if (documentoFilter && !String(row.empleado_id).includes(documentoFilter)) {
            return false;
        }
        
        // Filtro por fecha desde
        if (fechaDesde && row.fecha && row.fecha < fechaDesde) {
            return false;
        }
        
        // Filtro por fecha hasta
        if (fechaHasta && row.fecha && row.fecha > fechaHasta) {
            return false;
        }
        
        return true;
    });
    
    updatePausasStats(filteredData);
    renderPausasTable(filteredData);
    
    // Mostrar mensaje con resultados
    const mensaje = `Se encontraron ${filteredData.length} registros de ${allPausasData.length} totales`;
    console.log(mensaje);
}

// Limpiar Filtros de Pausas
function limpiarFiltrosPausas() {
    document.getElementById('filter-pausas-nombre').value = '';
    document.getElementById('filter-pausas-documento').value = '';
    document.getElementById('filter-pausas-fecha-desde').value = '';
    document.getElementById('filter-pausas-fecha-hasta').value = '';
    
    // Re-enable hasta input
    document.getElementById('filter-pausas-fecha-hasta').disabled = true;
    
    updatePausasStats(allPausasData);
    renderPausasTable(allPausasData);
}

// Load Puntos Adicionales
async function loadPuntosAdicionales() {
    try {
        const response = await fetch('api/get_data.php?table=puntos_adicionales');
        const data = await response.json();
        
        if (data.success) {
            // Filtrar registros sin fecha
            allPuntosAdicionalesData = data.data.filter(row => row.fecha && row.fecha.trim() !== '');
            updatePuntosAdicionalesStats(allPuntosAdicionalesData);
            renderPuntosAdicionalesTable(allPuntosAdicionalesData);
        }
    } catch (error) {
        console.error('Error loading puntos adicionales:', error);
    }
}

function updatePuntosAdicionalesStats(data) {
    document.getElementById('total-puntos-adicionales').textContent = data.length;
    const totalPuntos = data.reduce((sum, row) => sum + parseInt(row.puntos || 0), 0);
    document.getElementById('suma-puntos-adicionales').textContent = totalPuntos;
}

function renderPuntosAdicionalesTable(data) {
    const tbody = document.getElementById('puntos-adicionales-table-body');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="no-data">No hay datos disponibles.</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(row => `
        <tr>
            <td>${row.empleado_id || '-'}</td>
            <td>${row.empleado_nombre}</td>
            <td>${row.concepto}</td>
            <td>${row.fecha || '-'}</td>
            <td><strong>${row.puntos}</strong></td>
            <td>${row.aprobado_por || '-'}</td>
        </tr>
    `).join('');
}

// Load from Google Sheets
async function loadFromGoogleSheets(table) {
    if (!confirm(`¿Desea cargar/actualizar los datos de ${table} desde Google Sheets?`)) {
        return;
    }
    
    try {
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="loading"></span> Cargando...';
        btn.disabled = true;
        
        // Determinar endpoint según la tabla (campañas, inglés y pausas usan endpoints especiales)
        let endpoint = 'api/sync_google_sheets.php';
        let method = 'POST';
        let options = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ table: table })
        };
        
        if (table === 'campañas') {
            endpoint = 'api/load_campanas_multiple.php';
            method = 'GET';
            options = {};
        } else if (table === 'ingles') {
            endpoint = 'api/load_ingles_multiple.php';
            method = 'GET';
            options = {};
        } else if (table === 'pausas') {
            endpoint = 'api/load_pausas_multiple.php';
            method = 'GET';
            options = {};
        }
        
        const response = await fetch(endpoint, options);
        const data = await response.json();
        
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.success) {
            alert(`✓ Datos cargados exitosamente!\nRegistros procesados: ${data.processed}\nRegistros exitosos: ${data.successful}`);
            loadSectionData(table.replace('_', '-'));
        } else {
            alert(`✗ Error al cargar datos: ${data.message}`);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al conectar con el servidor');
    }
}

// Save Google Sheets Config
async function saveGoogleSheetsConfig() {
    const config = {
        csv_urls: {
            campañas: document.getElementById('csv-url-campañas').value,
            ingles: document.getElementById('csv-url-ingles').value,
            pausas: document.getElementById('csv-url-pausas').value,
            puntos_adicionales: document.getElementById('csv-url-puntos').value
        }
    };
    
    try {
        const response = await fetch('api/save_config.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(config)
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✓ Configuración guardada exitosamente!');
        } else {
            alert('✗ Error al guardar configuración');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al conectar con el servidor');
    }
}

// Load current config
async function loadCurrentConfig() {
    try {
        const response = await fetch('api/get_config.php');
        const data = await response.json();
        
        if (data.success && data.config) {
            const config = data.config;
            if (config.csv_urls) {
                document.getElementById('csv-url-campañas').value = config.csv_urls.campañas || '';
                document.getElementById('csv-url-ingles').value = config.csv_urls.ingles || '';
                document.getElementById('csv-url-pausas').value = config.csv_urls.pausas || '';
                document.getElementById('csv-url-puntos').value = config.csv_urls.puntos_adicionales || '';
                alert('✓ Configuración cargada!');
            }
        }
    } catch (error) {
        console.error('Error loading config:', error);
        alert('Error al cargar configuración');
    }
}

// Load Sync History
async function loadSyncHistory() {
    try {
        const response = await fetch('api/get_sync_history.php');
        const data = await response.json();
        
        const historyDiv = document.getElementById('sync-history');
        
        if (data.success && data.data.length > 0) {
            historyDiv.innerHTML = data.data.map(log => `
                <div class="sync-log-item" style="padding: 15px; border-bottom: 1px solid var(--border-color);">
                    <h4>${log.tabla_nombre}</h4>
                    <p>Fecha: ${log.fecha_sync}</p>
                    <p>Procesados: ${log.registros_procesados} | Exitosos: ${log.registros_exitosos} | Fallidos: ${log.registros_fallidos}</p>
                    <p><span class="badge badge-${log.estado === 'completado' ? 'success' : 'warning'}">${log.estado}</span></p>
                </div>
            `).join('');
        } else {
            historyDiv.innerHTML = '<p class="no-data">No hay historial de sincronización disponible.</p>';
        }
    } catch (error) {
        console.error('Error loading sync history:', error);
    }
}

// Test Database Connection
async function testDatabaseConnection() {
    try {
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="loading"></span> Probando...';
        btn.disabled = true;
        
        const response = await fetch('api/test_connection.php');
        const data = await response.json();
        
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.success) {
            alert('✓ Conexión exitosa a PostgreSQL!');
        } else {
            alert('✗ Error de conexión: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al probar conexión');
    }
}

// Clear All Tables
async function clearAllTables() {
    if (!confirm('¿Está seguro de que desea eliminar todos los datos de todas las tablas?')) {
        return;
    }
    
    try {
        const response = await fetch('api/clear_tables.php', { method: 'POST' });
        const data = await response.json();
        
        if (data.success) {
            alert('✓ Todas las tablas han sido limpiadas');
            location.reload();
        } else {
            alert('✗ Error al limpiar tablas');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al conectar con el servidor');
    }
}

// Reset Database
async function resetDatabase() {
    if (!confirm('¿ADVERTENCIA! Esto eliminará y recreará todas las tablas. ¿Continuar?')) {
        return;
    }
    
    try {
        const response = await fetch('api/reset_database.php', { method: 'POST' });
        const data = await response.json();
        
        if (data.success) {
            alert('✓ Base de datos reseteada exitosamente');
            location.reload();
        } else {
            alert('✗ Error al resetear base de datos');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al conectar con el servidor');
    }
}

// Edit and Delete functions (placeholders)
function editRow(table, id) {
    alert(`Editar registro ${id} de la tabla ${table}`);
    // Implementar modal de edición
}

function deleteRow(table, id) {
    if (confirm('¿Está seguro de eliminar este registro?')) {
        // Implementar eliminación
        fetch(`api/delete_row.php?table=${table}&id=${id}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ Registro eliminado');
                    loadSectionData(table.replace('_', '-'));
                }
            });
    }
}

// Aplicar Filtros
function aplicarFiltros() {
    const nombreFilter = document.getElementById('filter-nombre').value.toLowerCase();
    const documentoFilter = document.getElementById('filter-documento').value;
    const puntosFilter = document.getElementById('filter-puntos').value;
    const fechaDesde = document.getElementById('filter-fecha-desde').value;
    const fechaHasta = document.getElementById('filter-fecha-hasta').value;
    const actividadFilter = document.getElementById('filter-actividad').value.toLowerCase();
    
    const filteredData = allPuntosAdicionalesData.filter(row => {
        // Filtro por nombre
        if (nombreFilter && !row.empleado_nombre.toLowerCase().includes(nombreFilter)) {
            return false;
        }
        
        // Filtro por documento
        if (documentoFilter && !String(row.empleado_id).includes(documentoFilter)) {
            return false;
        }
        
        // Filtro por puntos
        if (puntosFilter && String(row.puntos) !== puntosFilter) {
            return false;
        }
        
        // Filtro por fecha desde
        if (fechaDesde && row.fecha && row.fecha < fechaDesde) {
            return false;
        }
        
        // Filtro por fecha hasta
        if (fechaHasta && row.fecha && row.fecha > fechaHasta) {
            return false;
        }
        
        // Filtro por actividad
        if (actividadFilter && !row.concepto.toLowerCase().includes(actividadFilter)) {
            return false;
        }
        
        return true;
    });
    
    updatePuntosAdicionalesStats(filteredData);
    renderPuntosAdicionalesTable(filteredData);
    
    // Mostrar mensaje con resultados
    const mensaje = `Se encontraron ${filteredData.length} registros de ${allPuntosAdicionalesData.length} totales`;
    console.log(mensaje);
}

// Limpiar Filtros
function limpiarFiltros() {
    document.getElementById('filter-nombre').value = '';
    document.getElementById('filter-documento').value = '';
    document.getElementById('filter-puntos').value = '';
    document.getElementById('filter-fecha-desde').value = '';
    document.getElementById('filter-fecha-hasta').value = '';
    document.getElementById('filter-actividad').value = '';
    
    // Re-enable hasta input
    document.getElementById('filter-fecha-hasta').disabled = true;
    
    updatePuntosAdicionalesStats(allPuntosAdicionalesData);
    renderPuntosAdicionalesTable(allPuntosAdicionalesData);
}

// Load Totales
async function loadTotales() {
    try {
        // Cargar datos de todas las tablas en paralelo
        const [campanasRes, inglesRes, pausasRes, puntosRes] = await Promise.all([
            fetch('api/get_data.php?table=participacion_campanas'),
            fetch('api/get_data.php?table=ingles'),
            fetch('api/get_data.php?table=pausas'),
            fetch('api/get_data.php?table=puntos_adicionales')
        ]);
        
        const campanas = await campanasRes.json();
        const ingles = await inglesRes.json();
        const pausas = await pausasRes.json();
        const puntos = await puntosRes.json();
        
        // Guardar datos originales para filtros (filtrar registros sin fecha)
        if (campanas.success) allCampanasData = campanas.data.filter(row => row.fecha && row.fecha.trim() !== '');
        if (ingles.success) allInglesData = ingles.data.filter(row => row.fecha_evaluacion && row.fecha_evaluacion.trim() !== '');
        if (pausas.success) allPausasData = pausas.data.filter(row => row.fecha && row.fecha.trim() !== '');
        if (puntos.success) allPuntosAdicionalesData = puntos.data.filter(row => row.fecha && row.fecha.trim() !== '');
        
        // Crear mapa de empleados con sus puntos
        const empleadosMap = new Map();
        
        // Procesar Inglés (30 puntos por asistencia)
        if (ingles.success) {
            ingles.data.forEach(row => {
                const id = row.empleado_id;
                if (!empleadosMap.has(id)) {
                    empleadosMap.set(id, {
                        id: id,
                        nombre: row.empleado_nombre,
                        ingles: 0,
                        pausas: 0,
                        campanas: 0,
                        adicionales: 0
                    });
                }
                empleadosMap.get(id).ingles += 30;
            });
        }
        
        // Procesar Pausas (15 puntos fijos o 50 para jueves pausar)
        if (pausas.success) {
            pausas.data.forEach(row => {
                const id = row.empleado_id;
                if (!empleadosMap.has(id)) {
                    empleadosMap.set(id, {
                        id: id,
                        nombre: row.empleado_nombre,
                        ingles: 0,
                        pausas: 0,
                        campanas: 0,
                        adicionales: 0
                    });
                }
                empleadosMap.get(id).pausas += calcularPuntosPausa(row.tipo_pausa);
            });
        }
        
        // Procesar Campañas (50 puntos por participación)
        if (campanas.success) {
            campanas.data.forEach(row => {
                const id = row.empleado_id;
                if (!empleadosMap.has(id)) {
                    empleadosMap.set(id, {
                        id: id,
                        nombre: row.empleado_nombre,
                        ingles: 0,
                        pausas: 0,
                        campanas: 0,
                        adicionales: 0
                    });
                }
                empleadosMap.get(id).campanas += 50;
            });
        }
        
        // Procesar Puntos Adicionales (suma de puntos)
        if (puntos.success) {
            puntos.data.forEach(row => {
                const id = row.empleado_id;
                if (!empleadosMap.has(id)) {
                    empleadosMap.set(id, {
                        id: id,
                        nombre: row.empleado_nombre,
                        ingles: 0,
                        pausas: 0,
                        campanas: 0,
                        adicionales: 0
                    });
                }
                empleadosMap.get(id).adicionales += parseInt(row.puntos || 0);
            });
        }
        
        // Convertir a array y calcular totales
        const empleadosArray = Array.from(empleadosMap.values()).map(emp => ({
            ...emp,
            total: emp.ingles + emp.pausas + emp.campanas + emp.adicionales
        }));
        
        // Ordenar por total descendente
        empleadosArray.sort((a, b) => b.total - a.total);
        
        // Guardar en variable global para filtros
        allTotalesData = empleadosArray;
        
        // Renderizar tabla
        renderTotalesTable(empleadosArray);
        
        // Actualizar estadísticas globales
        updateSummaryStats(empleadosArray);
        
    } catch (error) {
        console.error('Error loading totales:', error);
        document.getElementById('totales-table-body').innerHTML = 
            '<tr><td colspan="7" class="no-data">Error al cargar los datos.</td></tr>';
    }
}

function renderTotalesTable(data) {
    const tbody = document.getElementById('totales-table-body');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="no-data">No hay datos disponibles.</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(row => `
        <tr>
            <td>${row.id || '-'}</td>
            <td>${row.nombre}</td>
            <td>${row.ingles.toLocaleString()}</td>
            <td>${row.pausas.toLocaleString()}</td>
            <td>${row.campanas.toLocaleString()}</td>
            <td>${row.adicionales.toLocaleString()}</td>
            <td><strong>${row.total.toLocaleString()}</strong></td>
        </tr>
    `).join('');
}

// Actualizar estadísticas del resumen de totales
function updateSummaryStats(data) {
    const totalEmpleados = data.length;
    const totalPuntos = data.reduce((sum, emp) => sum + emp.total, 0);
    const promedioPuntos = totalEmpleados > 0 ? Math.round(totalPuntos / totalEmpleados) : 0;
    
    document.getElementById('total-empleados-sistema').textContent = totalEmpleados;
    document.getElementById('total-puntos-sistema').textContent = totalPuntos.toLocaleString();
    document.getElementById('promedio-puntos-sistema').textContent = promedioPuntos.toLocaleString();
}

// Calcular totales con filtros de fecha
function calculateTotalesWithDateFilter(fechaDesde, fechaHasta) {
    const empleadosMap = new Map();
    
    // Filtrar y procesar Inglés (30 puntos por asistencia)
    allInglesData.forEach(row => {
        if (fechaDesde && row.fecha_evaluacion && row.fecha_evaluacion < fechaDesde) return;
        if (fechaHasta && row.fecha_evaluacion && row.fecha_evaluacion > fechaHasta) return;
        
        const id = row.empleado_id;
        if (!empleadosMap.has(id)) {
            empleadosMap.set(id, {
                id: id,
                nombre: row.empleado_nombre,
                ingles: 0,
                pausas: 0,
                campanas: 0,
                adicionales: 0
            });
        }
        empleadosMap.get(id).ingles += 30;
    });
    
    // Filtrar y procesar Pausas (15 puntos fijos o 50 para jueves pausar)
    allPausasData.forEach(row => {
        if (fechaDesde && row.fecha && row.fecha < fechaDesde) return;
        if (fechaHasta && row.fecha && row.fecha > fechaHasta) return;
        
        const id = row.empleado_id;
        if (!empleadosMap.has(id)) {
            empleadosMap.set(id, {
                id: id,
                nombre: row.empleado_nombre,
                ingles: 0,
                pausas: 0,
                campanas: 0,
                adicionales: 0
            });
        }
        empleadosMap.get(id).pausas += calcularPuntosPausa(row.tipo_pausa);
    });
    
    // Filtrar y procesar Campañas (50 puntos por participación)
    allCampanasData.forEach(row => {
        if (fechaDesde && row.fecha && row.fecha < fechaDesde) return;
        if (fechaHasta && row.fecha && row.fecha > fechaHasta) return;
        
        const id = row.empleado_id;
        if (!empleadosMap.has(id)) {
            empleadosMap.set(id, {
                id: id,
                nombre: row.empleado_nombre,
                ingles: 0,
                pausas: 0,
                campanas: 0,
                adicionales: 0
            });
        }
        empleadosMap.get(id).campanas += 50;
    });
    
    // Filtrar y procesar Puntos Adicionales (suma de puntos)
    allPuntosAdicionalesData.forEach(row => {
        if (fechaDesde && row.fecha && row.fecha < fechaDesde) return;
        if (fechaHasta && row.fecha && row.fecha > fechaHasta) return;
        
        const id = row.empleado_id;
        if (!empleadosMap.has(id)) {
            empleadosMap.set(id, {
                id: id,
                nombre: row.empleado_nombre,
                ingles: 0,
                pausas: 0,
                campanas: 0,
                adicionales: 0
            });
        }
        empleadosMap.get(id).adicionales += parseInt(row.puntos || 0);
    });
    
    // Convertir a array y calcular totales
    const empleadosArray = Array.from(empleadosMap.values()).map(emp => ({
        ...emp,
        total: emp.ingles + emp.pausas + emp.campanas + emp.adicionales
    }));
    
    // Ordenar por total descendente
    empleadosArray.sort((a, b) => b.total - a.total);
    
    return empleadosArray;
}

// Aplicar filtros de totales
function aplicarFiltrosTotales() {
    const fechaDesde = document.getElementById('filter-totales-fecha-desde').value;
    const fechaHasta = document.getElementById('filter-totales-fecha-hasta').value;
    
    // Si hay filtros de fecha, recalcular totales
    let dataToFilter = allTotalesData;
    if (fechaDesde || fechaHasta) {
        dataToFilter = calculateTotalesWithDateFilter(fechaDesde, fechaHasta);
    }
    
    // Aplicar otros filtros
    const nombreFilter = document.getElementById('filter-totales-nombre').value.toLowerCase().trim();
    const documentoFilter = document.getElementById('filter-totales-documento').value.trim();
    
    const filtered = dataToFilter.filter(emp => {
        // Filtro por nombre
        if (nombreFilter && !emp.nombre.toLowerCase().includes(nombreFilter)) {
            return false;
        }
        
        // Filtro por documento
        if (documentoFilter && !String(emp.id).includes(documentoFilter)) {
            return false;
        }
        
        return true;
    });
    
    renderTotalesTable(filtered);
    updateSummaryStats(filtered);
}

// Limpiar filtros de totales
function limpiarFiltrosTotales() {
    document.getElementById('filter-totales-nombre').value = '';
    document.getElementById('filter-totales-documento').value = '';
    document.getElementById('filter-totales-fecha-desde').value = '';
    document.getElementById('filter-totales-fecha-hasta').value = '';
    
    // Re-enable hasta input
    document.getElementById('filter-totales-fecha-hasta').disabled = true;
    
    renderTotalesTable(allTotalesData);
    updateSummaryStats(allTotalesData);
}

// Exportar totales filtrados a Excel
function exportarTotalesExcel() {
    // Obtener los datos actualmente mostrados en la tabla
    const fechaDesde = document.getElementById('filter-totales-fecha-desde').value;
    const fechaHasta = document.getElementById('filter-totales-fecha-hasta').value;
    const nombreFilter = document.getElementById('filter-totales-nombre').value.toLowerCase().trim();
    const documentoFilter = document.getElementById('filter-totales-documento').value.trim();
    
    // Si hay filtros de fecha, recalcular totales
    let dataToExport = allTotalesData;
    if (fechaDesde || fechaHasta) {
        dataToExport = calculateTotalesWithDateFilter(fechaDesde, fechaHasta);
    }
    
    // Aplicar filtros de nombre y documento
    const filtered = dataToExport.filter(emp => {
        if (nombreFilter && !emp.nombre.toLowerCase().includes(nombreFilter)) {
            return false;
        }
        if (documentoFilter && !String(emp.id).includes(documentoFilter)) {
            return false;
        }
        return true;
    });
    
    if (filtered.length === 0) {
        alert('No hay datos para exportar con los filtros aplicados.');
        return;
    }
    
    // Preparar datos para Excel
    const excelData = filtered.map(emp => ({
        'Documento': emp.id || '',
        'Nombre': emp.nombre,
        'Inglés': emp.ingles,
        'Pausas': emp.pausas,
        'Campañas': emp.campanas,
        'Adicionales': emp.adicionales,
        'Total': emp.total
    }));
    
    // Agregar fila de totales al final
    const totalGeneral = {
        'Documento': '',
        'Nombre': 'TOTAL GENERAL',
        'Inglés': filtered.reduce((sum, emp) => sum + emp.ingles, 0),
        'Pausas': filtered.reduce((sum, emp) => sum + emp.pausas, 0),
        'Campañas': filtered.reduce((sum, emp) => sum + emp.campanas, 0),
        'Adicionales': filtered.reduce((sum, emp) => sum + emp.adicionales, 0),
        'Total': filtered.reduce((sum, emp) => sum + emp.total, 0)
    };
    excelData.push(totalGeneral);
    
    // Crear libro de trabajo
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet(excelData);
    
    // Ajustar ancho de columnas
    const wscols = [
        {wch: 15}, // Documento
        {wch: 30}, // Nombre
        {wch: 10}, // Inglés
        {wch: 10}, // Pausas
        {wch: 12}, // Campañas
        {wch: 12}, // Adicionales
        {wch: 12}  // Total
    ];
    ws['!cols'] = wscols;
    
    // Agregar hoja al libro
    XLSX.utils.book_append_sheet(wb, ws, 'Totales');
    
    // Generar nombre de archivo con fecha
    const fecha = new Date().toISOString().split('T')[0];
    let filename = `Jikko_Totales_${fecha}`;
    
    // Agregar información de filtros al nombre si existen
    if (fechaDesde || fechaHasta) {
        const desde = fechaDesde || 'inicio';
        const hasta = fechaHasta || 'hoy';
        filename += `_${desde}_${hasta}`;
    }
    filename += '.xlsx';
    
    // Descargar archivo
    XLSX.writeFile(wb, filename);
    
    // Mostrar mensaje de éxito
    showNotification('Archivo Excel exportado exitosamente', 'success');
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    // Crear elemento de notificación si no existe
    let notification = document.getElementById('notification-toast');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification-toast';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: #28a745;
            color: white;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 10000;
            font-size: 14px;
            display: none;
        `;
        document.body.appendChild(notification);
    }
    
    // Establecer color según tipo
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    notification.style.background = colors[type] || colors.info;
    
    // Mostrar notificación
    notification.textContent = message;
    notification.style.display = 'block';
    
    // Ocultar después de 3 segundos
    setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
}

// Eventos de Enter en los inputs de filtro
document.addEventListener('DOMContentLoaded', function() {
    // NOTE: each section has its own Enter-key bindings defined below.
    // We intentionally avoid a generic handler to prevent wrong filter functions
    // from being triggered when pressing Enter in other sections.
    
    // Eventos de Enter para filtros de Pausas
    const pausasFilterInputs = document.querySelectorAll('#filter-pausas-nombre, #filter-pausas-documento, #filter-pausas-fecha-desde, #filter-pausas-fecha-hasta');
    pausasFilterInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                aplicarFiltrosPausas();
            }
        });
    });
    
    // Eventos de Enter para filtros de Campañas
    const campanasFilterInputs = document.querySelectorAll('#filter-campanas-nombre, #filter-campanas-documento, #filter-campanas-empresa, #filter-campanas-fecha-desde, #filter-campanas-fecha-hasta');
    campanasFilterInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                aplicarFiltrosCampanas();
            }
        });
    });
    // Eventos de Enter para filtros de Inglés
    const inglesFilterInputs = document.querySelectorAll('#filter-ingles-nombre, #filter-ingles-documento, #filter-ingles-nivel, #filter-ingles-fecha-desde, #filter-ingles-fecha-hasta');
    inglesFilterInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                aplicarFiltrosIngles();
            }
        });
    });
    
    // Eventos de Enter para filtros de Totales
    const totalesFilterInputs = document.querySelectorAll('#filter-totales-nombre, #filter-totales-documento, #filter-totales-fecha-desde, #filter-totales-fecha-hasta');
    totalesFilterInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                aplicarFiltrosTotales();
            }
        });
    });
    
    // Setup date filter validations
    setupDateFilterValidation('filter-campanas-fecha-desde', 'filter-campanas-fecha-hasta');
    setupDateFilterValidation('filter-pausas-fecha-desde', 'filter-pausas-fecha-hasta');
    setupDateFilterValidation('filter-fecha-desde', 'filter-fecha-hasta');
    setupDateFilterValidation('filter-totales-fecha-desde', 'filter-totales-fecha-hasta');
    setupDateFilterValidation('filter-ingles-fecha-desde', 'filter-ingles-fecha-hasta');
});

// Sincronizar todo desde Google Sheets
async function sincronizarTodo() {
    const btn = document.getElementById('btn-sync-all');
    const progressDiv = document.getElementById('sync-progress');
    const progressFill = document.getElementById('progress-fill');
    const statusText = document.getElementById('sync-status');
    
    if (!confirm('¿Desea sincronizar TODOS los datos desde Google Sheets?\n\nEsto cargará:\n• Campañas\n• Inglés\n• Pausas\n• Puntos Adicionales\n\nEsta operación puede tardar unos minutos.')) {
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<span class="loading"></span> Sincronizando...';
    progressDiv.style.display = 'block';
    
    const tablas = [
        { nombre: 'Campañas', endpoint: 'api/load_campanas_multiple.php', key: 'campañas' },
        { nombre: 'Inglés', endpoint: 'api/load_ingles_multiple.php', key: 'ingles' },
        { nombre: 'Pausas', endpoint: 'api/load_pausas_multiple.php', key: 'pausas' },
        { nombre: 'Puntos Adicionales', endpoint: 'api/sync_google_sheets.php', key: 'puntos_adicionales', method: 'POST' }
    ];
    
    let resultados = [];
    let totalProcesados = 0;
    let totalExitosos = 0;
    let totalFallidos = 0;
    
    for (let i = 0; i < tablas.length; i++) {
        const tabla = tablas[i];
        const progreso = ((i) / tablas.length) * 100;
        progressFill.style.width = progreso + '%';
        statusText.textContent = `Sincronizando ${tabla.nombre}... (${i + 1}/${tablas.length})`;
        
        try {
            const options = tabla.method === 'POST' ? {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ table: tabla.key })
            } : {};
            
            const response = await fetch(tabla.endpoint, options);
            const data = await response.json();
            
            if (data.success) {
                resultados.push(`✓ ${tabla.nombre}: ${data.successful || data.processed || 0} registros`);
                totalProcesados += (data.processed || 0);
                totalExitosos += (data.successful || 0);
                totalFallidos += (data.failed || 0);
            } else {
                resultados.push(`✗ ${tabla.nombre}: ERROR - ${data.message || data.error || 'Error desconocido'}`);
            }
        } catch (error) {
            resultados.push(`✗ ${tabla.nombre}: ERROR - ${error.message}`);
        }
    }
    
    // Completado
    progressFill.style.width = '100%';
    statusText.textContent = 'Sincronización completada';
    
    // Mostrar resumen
    const resumen = `SINCRONIZACIÓN COMPLETADA\n\n${resultados.join('\n')}\n\nTotal procesados: ${totalProcesados}\nTotal exitosos: ${totalExitosos}\nTotal fallidos: ${totalFallidos}`;
    alert(resumen);
    
    // Recargar datos de la sección activa
    const activeSection = document.querySelector('.menu-item.active');
    if (activeSection) {
        const section = activeSection.dataset.section;
        loadSectionData(section);
    }
    
    // Reset UI
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-cloud-download-alt"></i> Sincronizar Todo Ahora';
    setTimeout(() => {
        progressDiv.style.display = 'none';
        progressFill.style.width = '0%';
    }, 3000);
}

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    loadCampañas();
    attachSortHandlers();
});
