# Manual Técnico - Jikko Puntos Dashboard v4

## Información General

**Nombre del Sistema:** Jikko Puntos Dashboard  
**Versión:** 4.0  
**Fecha de Creación:** Abril 2024  
**Última Actualización:** Abril 2026  
**Desarrollador:** Equipo de Desarrollo Jikkosoft  
**Plataforma:** Web (PHP/PostgreSQL)  
**Licencia:** Propietaria  

## Índice

1. [Introducción](#introducción)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Requisitos del Sistema](#requisitos-del-sistema)
4. [Instalación y Configuración](#instalación-y-configuración)
5. [Estructura de Base de Datos](#estructura-de-base-de-datos)
6. [API REST](#api-rest)
7. [Interfaz de Usuario](#interfaz-de-usuario)
8. [Configuración de Google Sheets](#configuración-de-google-sheets)
9. [Sincronización de Datos](#sincronización-de-datos)
10. [Seguridad](#seguridad)
11. [Mantenimiento y Monitoreo](#mantenimiento-y-monitoreo)
12. [Solución de Problemas](#solución-de-problemas)
13. [Glosario](#glosario)

---

## 1. Introducción

### 1.1 Propósito del Sistema

El Sistema de Gestión de Puntos Jikko es una plataforma web diseñada para centralizar, automatizar y gestionar el programa de incentivos y reconocimientos de la empresa Jikkosoft. El sistema permite:

- Centralizar información dispersa en múltiples fuentes de datos (Google Sheets)
- Automatizar el cálculo de puntos según actividades de participación
- Facilitar la consulta y generación de reportes consolidados
- Transparentar el proceso de asignación de puntos e incentivos
- Optimizar el tiempo del área de Recursos Humanos

### 1.2 Alcance Funcional

#### Módulos Incluidos:
- **Módulo de Campañas Corporativas:** Registro y contabilización de participación
- **Módulo de Inglés:** Seguimiento de asistencia a clases
- **Módulo de Pausas Activas:** Control de participación en pausas
- **Módulo de Puntos Adicionales:** Registro de reconocimientos especiales
- **Módulo de Totales:** Consolidación y reporte general
- **Módulo de Sincronización:** Carga masiva desde Google Sheets

#### Usuarios:
- Administrador del área de Recursos Humanos (acceso completo)
- Empleados (consulta indirecta vía RH)

### 1.3 Beneficios

- **Eficiencia:** Reducción del tiempo en gestión de puntos de horas a minutos
- **Precisión:** Eliminación de errores de cálculo manual
- **Transparencia:** Visibilidad completa del estado de puntos
- **Escalabilidad:** Capacidad para manejar crecimiento del programa
- **Integración:** Conexión automática con fuentes de datos existentes

---

## 2. Arquitectura del Sistema

### 2.1 Arquitectura General

El sistema sigue una arquitectura web tradicional de 3 capas:

```
[Cliente Web] ←→ [Servidor Web + PHP] ←→ [Base de Datos PostgreSQL]
     ↑                                                ↑
[HTML/CSS/JS]                                   [Google Sheets API]
```

### 2.2 Componentes Técnicos

#### Frontend:
- **HTML5:** Estructura de la interfaz
- **CSS3:** Estilos y diseño responsive
- **JavaScript (Vanilla):** Lógica del cliente y AJAX
- **Font Awesome:** Iconografía
- **XLSX Library:** Exportación a Excel

#### Backend:
- **PHP 7.4+:** Lenguaje de servidor
- **PostgreSQL 12+:** Base de datos relacional
- **PDO/PostgreSQL:** Conexión a base de datos

#### Integraciones:
- **Google Sheets API:** Sincronización de datos
- **CSV Processing:** Procesamiento de archivos CSV

### 2.3 Estructura de Archivos

```
jikkopuntos_v4/
├── index.php                 # Dashboard principal
├── database.sql             # Script de creación de BD
├── README.md                # Documentación básica
├── DOCUMENTO_FORMAL.md      # Especificaciones formales
│
├── config/
│   ├── database.php         # Configuración PostgreSQL
│   └── google_sheets_config.json  # Config Google Sheets
│
├── api/                     # Endpoints REST
│   ├── get_data.php
│   ├── sync_google_sheets.php
│   ├── save_config.php
│   ├── get_sync_history.php
│   ├── test_connection.php
│   ├── delete_row.php
│   ├── clear_tables.php
│   └── reset_database.php
│
└── assets/
    ├── css/
    │   └── style.css        # Estilos CSS
    └── js/
        └── main.js          # JavaScript principal
```

---

## 3. Requisitos del Sistema

### 3.1 Requisitos de Hardware

#### Servidor Mínimo:
- CPU: 1 GHz dual-core
- RAM: 2 GB
- Disco: 10 GB disponible
- Red: 10 Mbps

#### Servidor Recomendado:
- CPU: 2 GHz quad-core
- RAM: 4 GB
- Disco: 50 GB SSD
- Red: 100 Mbps

### 3.2 Requisitos de Software

#### Sistema Operativo:
- Windows 10/11 (desarrollo)
- Linux (producción recomendada)
- macOS (desarrollo alternativo)

#### Servidor Web:
- Apache 2.4+ (incluido en XAMPP)
- Nginx 1.18+ (alternativo)

#### Base de Datos:
- PostgreSQL 12+
- pgAdmin 4+ (herramienta de administración)

#### PHP:
- Versión: 7.4 o superior
- Extensiones requeridas:
  - pgsql
  - pdo_pgsql
  - mbstring
  - json
  - curl (para Google Sheets API)

#### Navegador Web:
- Chrome 90+
- Firefox 88+
- Edge 90+
- Safari 14+

### 3.3 Requisitos de Red

- Conexión a Internet para sincronización con Google Sheets
- Puerto 80/443 abierto para acceso web
- Puerto 5432 abierto para PostgreSQL (local)

---

## 4. Instalación y Configuración

### 4.1 Instalación de XAMPP

1. Descargar XAMPP desde https://www.apachefriends.org/
2. Ejecutar instalador como administrador
3. Seleccionar componentes: Apache, PHP, PostgreSQL
4. Instalar en directorio por defecto (C:\xampp)

### 4.2 Configuración de PostgreSQL

#### Crear Base de Datos:
```sql
-- Ejecutar en psql o pgAdmin
CREATE DATABASE jikkopuntos_v4;
```

#### Ejecutar Script de Tablas:
```bash
# Desde línea de comandos
psql -U postgres -d jikkopuntos_v4 -f database.sql
```

### 4.3 Configuración de PHP

#### Habilitar Extensiones en php.ini:
```ini
extension=pgsql
extension=pdo_pgsql
extension=mbstring
extension=json
extension=curl
```

#### Reiniciar Apache:
```bash
# Servicios de Windows
net stop apache2.4
net start apache2.4
```

### 4.4 Despliegue del Proyecto

1. Copiar carpeta del proyecto a `C:\xampp\htdocs\jikkopuntos_v4\`
2. Verificar permisos de escritura en carpeta `config/`
3. Acceder vía navegador: `http://localhost/jikkopuntos_v4/`

### 4.5 Configuración Inicial

#### Archivo config/database.php:
```php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'jikkopuntos_v4');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres');
```

#### Variables de Entorno (Producción):
```bash
export DB_HOST="produccion.example.com"
export DB_USER="jikko_user"
export DB_PASS="secure_password"
```

---

## 5. Estructura de Base de Datos

### 5.1 Diagrama de Entidades

```
campañas (id, nombre, descripcion, fecha_inicio, fecha_fin, estado, puntos_base)
    ↓
participacion_campanas (id, empleado_id, empleado_nombre, nombre_campana, fecha, empresa)

ingles (id, empleado_id, empleado_nombre, nivel, puntos, fecha_evaluacion, certificacion)

pausas (id, empleado_id, empleado_nombre, tipo_pausa, duracion, fecha, hora_inicio, hora_fin, puntos_deducidos)

puntos_adicionales (id, empleado_id, empleado_nombre, concepto, puntos, fecha, aprobado_por, observaciones)

empleados (id, nombre, email, departamento, puesto, activo)

sync_log (id, tabla_nombre, registros_procesados, registros_exitosos, registros_fallidos, fecha_sync, estado, mensaje)
```

### 5.2 Descripción de Tablas

#### Tabla `campañas`:
- **Propósito:** Definición de campañas corporativas
- **Campos clave:** nombre, descripcion, fecha_inicio, fecha_fin, puntos_base
- **Relaciones:** Una campaña puede tener múltiples participaciones

#### Tabla `participacion_campanas`:
- **Propósito:** Registro de participación de empleados en campañas
- **Campos clave:** empleado_id, empleado_nombre, nombre_campana, fecha
- **Índices:** empleado_nombre, fecha

#### Tabla `ingles`:
- **Propósito:** Evaluaciones y asistencia a clases de inglés
- **Campos clave:** empleado_id, empleado_nombre, nivel, puntos, fecha_evaluacion
- **Índices:** empleado_nombre

#### Tabla `pausas`:
- **Propósito:** Registro de pausas activas
- **Campos clave:** empleado_id, empleado_nombre, tipo_pausa, fecha, puntos_deducidos
- **Índices:** empleado_nombre, fecha

#### Tabla `puntos_adicionales`:
- **Propósito:** Reconocimientos especiales
- **Campos clave:** empleado_id, empleado_nombre, concepto, puntos, aprobado_por
- **Índices:** empleado_nombre

#### Tabla `sync_log`:
- **Propósito:** Historial de sincronizaciones
- **Campos clave:** tabla_nombre, fecha_sync, estado, mensaje

### 5.3 Índices y Rendimiento

```sql
-- Índices existentes
CREATE INDEX idx_campañas_estado ON campañas(estado);
CREATE INDEX idx_ingles_empleado ON ingles(empleado_nombre);
CREATE INDEX idx_pausas_empleado ON pausas(empleado_nombre);
CREATE INDEX idx_pausas_fecha ON pausas(fecha);
CREATE INDEX idx_puntos_empleado ON puntos_adicionales(empleado_nombre);
CREATE INDEX idx_empleados_activo ON empleados(activo);
```

### 5.4 Backup y Restauración

#### Backup:
```bash
pg_dump -U postgres -d jikkopuntos_v4 > backup_$(date +%Y%m%d_%H%M%S).sql
```

#### Restauración:
```bash
psql -U postgres -d jikkopuntos_v4 < backup_file.sql
```

---

## 6. API REST

### 6.1 Endpoints Disponibles

#### GET /api/get_data.php
- **Propósito:** Obtener datos de tablas
- **Parámetros:** table (campañas|ingles|pausas|puntos_adicionales)
- **Respuesta:** JSON con array de registros

#### POST /api/sync_google_sheets.php
- **Propósito:** Sincronizar datos desde Google Sheets
- **Parámetros:** urls (JSON con URLs de hojas)
- **Respuesta:** Estado de sincronización

#### POST /api/save_config.php
- **Propósito:** Guardar configuración de Google Sheets
- **Parámetros:** config (JSON con configuración)
- **Respuesta:** Confirmación de guardado

#### GET /api/get_sync_history.php
- **Propósito:** Obtener historial de sincronizaciones
- **Respuesta:** Array de registros de sync_log

#### GET /api/test_connection.php
- **Propósito:** Probar conexión a base de datos
- **Respuesta:** Estado de conexión

#### DELETE /api/delete_row.php
- **Propósito:** Eliminar registro específico
- **Parámetros:** table, id
- **Respuesta:** Confirmación de eliminación

#### POST /api/clear_tables.php
- **Propósito:** Limpiar todas las tablas
- **Respuesta:** Estado de limpieza

#### POST /api/reset_database.php
- **Propósito:** Resetear base de datos completa
- **Respuesta:** Estado de reseteo

### 6.2 Formato de Respuestas

#### Respuesta Exitosa:
```json
{
    "success": true,
    "data": [...],
    "count": 150
}
```

#### Respuesta de Error:
```json
{
    "success": false,
    "message": "Descripción del error"
}
```

### 6.3 Códigos de Estado HTTP

- **200:** OK - Operación exitosa
- **400:** Bad Request - Parámetros inválidos
- **404:** Not Found - Recurso no encontrado
- **500:** Internal Server Error - Error del servidor

### 6.4 Autenticación y Seguridad

**Nota:** La API actual no implementa autenticación. Para producción, se recomienda:

- Implementar JWT tokens
- Usar HTTPS
- Validar origen de requests (CORS)
- Rate limiting

---

## 7. Interfaz de Usuario

### 7.1 Estructura General

#### Layout Principal:
- **Sidebar:** Navegación entre secciones
- **Header:** Título de sección y información de usuario
- **Content Area:** Contenido dinámico por sección

#### Secciones Disponibles:
1. Campañas
2. Inglés
3. Pausas
4. Puntos Adicionales
5. Totales
6. Sincronizar Google Sheets
7. Configuración (comentada en código)

### 7.2 Componentes JavaScript

#### main.js - Funciones Principales:
- `cargarSeccion()`: Carga contenido dinámico
- `cargarDatosTabla()`: Obtiene datos de API
- `aplicarFiltros()`: Filtra datos en tablas
- `sincronizarTodo()`: Sincronización completa
- `exportarTotalesExcel()`: Exportación a Excel

#### Funcionalidades Clave:
- **Navegación:** Sidebar responsive con toggle
- **Tablas:** Ordenamiento, filtrado, paginación
- **Estadísticas:** Cálculos automáticos en tiempo real
- **Sincronización:** Progreso visual con indicadores
- **Exportación:** Generación de archivos Excel

### 7.3 Estilos CSS

#### Arquitectura CSS:
- **Variables CSS:** Colores y dimensiones centralizadas
- **Responsive Design:** Media queries para móviles
- **Componentes:** Clases reutilizables (.btn, .table, .card)
- **Animaciones:** Transiciones suaves en sidebar

#### Temas Visuales:
- **Colores Primarios:** Azul (#007bff), Verde (#28a745)
- **Tipografía:** Sans-serif moderna
- **Espaciado:** Sistema de 8px (0.5rem)
- **Sombras:** Efectos sutiles para profundidad

---

## 8. Configuración de Google Sheets

### 8.1 Requisitos Previos

1. **Cuenta Google:** Con acceso a Google Cloud Console
2. **Proyecto GCP:** Crear proyecto en console.cloud.google.com
3. **API Habilitada:** Google Sheets API
4. **Credenciales:** API Key o Service Account

### 8.2 Configuración de Hojas

#### Estructura Requerida:

**Hoja "Campañas":**
```
nombre | descripcion | fecha_inicio | fecha_fin | estado | puntos_base
Campaña 1 | Descripción | 2024-01-01 | 2024-01-31 | activa | 50
```

**Hoja "Ingles":**
```
empleado_id | empleado_nombre | nivel | puntos | fecha_evaluacion | certificacion
12345678 | Juan Pérez | B1 | 30 | 2024-01-15 | Sí
```

**Hoja "Pausas":**
```
empleado_id | empleado_nombre | tipo_pausa | duracion | fecha | hora_inicio | hora_fin | puntos_deducidos
12345678 | Juan Pérez | Tipo 1 | 15 | 2024-01-15 | 10:00 | 10:15 | 15
```

**Hoja "PuntosAdicionales":**
```
empleado_id | empleado_nombre | concepto | puntos | fecha | aprobado_por | observaciones
12345678 | Juan Pérez | Excelente desempeño | 100 | 2024-01-15 | María García | Reconocimiento mensual
```

### 8.3 Publicación de Hojas

#### Pasos para Publicar:
1. Abrir Google Sheet
2. Archivo → Compartir → Publicar en la web
3. Seleccionar hoja específica
4. Formato: "Valores separados por comas (.csv)"
5. Copiar enlace generado

#### Formato de URL:
```
https://docs.google.com/spreadsheets/d/{SPREADSHEET_ID}/pub?gid={SHEET_GID}&single=true&output=csv
```

### 8.4 Configuración en el Sistema

#### Archivo google_sheets_config.json:
```json
{
    "campañas_url": "https://docs.google.com/...",
    "ingles_url": "https://docs.google.com/...",
    "pausas_url": "https://docs.google.com/...",
    "puntos_adicionales_url": "https://docs.google.com/..."
}
```

---

## 9. Sincronización de Datos

### 9.1 Proceso de Sincronización

#### Pasos Automáticos:
1. **Validación:** Verificar URLs configuradas
2. **Descarga:** Obtener CSV desde Google Sheets
3. **Parsing:** Procesar datos CSV a arrays
4. **Limpieza:** TRUNCATE de tablas existentes
5. **Inserción:** Insertar nuevos registros
6. **Logging:** Registrar en sync_log
7. **Estadísticas:** Actualizar contadores

#### Flujo de Datos:
```
Google Sheets → CSV → PHP Parser → PostgreSQL → UI Update
```

### 9.2 Manejo de Errores

#### Tipos de Error:
- **Conexión:** Fallo de red o API
- **Formato:** CSV malformado
- **Datos:** Valores inválidos
- **Base de Datos:** Error de inserción

#### Recuperación:
- Rollback automático en errores
- Logging detallado de fallos
- Reintento manual por usuario

### 9.3 Rendimiento

#### Optimizaciones:
- **Batch Inserts:** Inserción masiva de registros
- **Índices:** Optimización de consultas
- **Conexión Persistente:** Reutilización de conexiones DB

#### Límites:
- **Tamaño CSV:** Máximo 10MB por hoja
- **Registros:** Sin límite teórico
- **Tiempo:** Timeout de 5 minutos por sincronización

---

## 10. Seguridad

### 10.1 Vulnerabilidades Actuales

#### Críticas:
- **Sin Autenticación:** Acceso directo sin login
- **SQL Injection:** Uso de pg_escape_identifier (parcialmente mitigado)
- **XSS:** Sin sanitización de inputs del usuario
- **CSRF:** Sin protección contra cross-site request forgery

#### Mitigaciones Existentes:
- **Prepared Statements:** Uso de parámetros en queries
- **Input Validation:** Validación básica de tablas
- **CORS:** Restricción de orígenes (no implementado)

### 10.2 Recomendaciones de Seguridad

#### Para Producción:
1. **Autenticación:** Implementar login con sesiones
2. **Autorización:** Control de acceso basado en roles
3. **HTTPS:** Certificado SSL obligatorio
4. **Input Sanitization:** Filtrar todos los inputs
5. **Rate Limiting:** Limitar requests por IP
6. **Auditoría:** Logging de todas las operaciones

#### Configuración de Servidor:
```apache
# .htaccess para Apache
<RequireAll>
    Require all granted
    Require ssl
</RequireAll>
```

### 10.3 Gestión de Credenciales

#### Variables de Entorno:
```bash
# .env file
DB_HOST=localhost
DB_USER=jikko_user
DB_PASS=secure_password_123
GOOGLE_API_KEY=AIzaSy...
```

#### En PHP:
```php
$host = getenv('DB_HOST') ?: 'localhost';
```

---

## 11. Mantenimiento y Monitoreo

### 11.1 Tareas de Mantenimiento

#### Diarias:
- Verificar logs de error
- Monitorear espacio en disco
- Backup automático de base de datos

#### Semanales:
- Limpiar logs antiguos
- Verificar integridad de datos
- Actualizar dependencias

#### Mensuales:
- Análisis de rendimiento
- Revisión de seguridad
- Backup completo del sistema

### 11.2 Monitoreo del Sistema

#### Métricas a Monitorear:
- **Disponibilidad:** Uptime del servicio
- **Rendimiento:** Tiempo de respuesta de API
- **Uso de Recursos:** CPU, memoria, disco
- **Errores:** Tasa de error por endpoint
- **Sincronizaciones:** Éxito/fallo de sync

#### Herramientas Recomendadas:
- **Nagios/Icinga:** Monitoreo general
- **pgBadger:** Análisis de logs PostgreSQL
- **New Relic:** APM para PHP

### 11.3 Backup Strategy

#### Tipos de Backup:
- **Completo:** Base de datos + archivos (semanal)
- **Incremental:** Cambios diarios
- **Configuración:** Archivos de config (diario)

#### Retención:
- **Diario:** 7 días
- **Semanal:** 4 semanas
- **Mensual:** 12 meses

---

## 12. Solución de Problemas

### 12.1 Problemas Comunes

#### Error de Conexión a BD:
**Síntomas:** "No se pudo conectar a PostgreSQL"
**Causas:** Servicio detenido, credenciales incorrectas
**Solución:**
```bash
# Verificar servicio
net start postgresql-x64-12

# Probar conexión
psql -U postgres -d jikkopuntos_v4 -c "SELECT 1;"
```

#### Error de Sincronización:
**Síntomas:** "Error al procesar CSV"
**Causas:** URL inválida, hoja no pública
**Solución:**
1. Verificar URL en navegador
2. Confirmar publicación de hoja
3. Revisar formato CSV

#### Tablas Vacías:
**Síntomas:** No se muestran datos
**Causas:** Sincronización fallida, permisos
**Solución:**
1. Verificar logs en sync_log
2. Reintentar sincronización
3. Comprobar permisos de BD

### 12.2 Logs y Debugging

#### Ubicación de Logs:
- **PHP:** C:\xampp\php\logs\php_error_log
- **Apache:** C:\xampp\apache\logs\error.log
- **PostgreSQL:** C:\xampp\pgsql\data\log

#### Habilitar Debug:
```php
// En php.ini
error_reporting = E_ALL
display_errors = On
log_errors = On
```

### 12.3 Contacto de Soporte

**Equipo de Desarrollo:** desarrollo@jikkosoft.com  
**Área de RH:** rh@jikkosoft.com  
**Documentación:** https://docs.jikkosoft.com/jikko-puntos

---

## 13. Glosario

**AJAX:** Asynchronous JavaScript and XML - Técnica para comunicación asíncrona  
**API:** Application Programming Interface - Interfaz de programación de aplicaciones  
**CSV:** Comma-Separated Values - Formato de archivo de valores separados por comas  
**CRUD:** Create, Read, Update, Delete - Operaciones básicas de base de datos  
**Frontend:** Parte del sistema que interactúa con el usuario  
**Backend:** Parte del sistema que maneja la lógica y datos  
**HTTP:** HyperText Transfer Protocol - Protocolo de comunicación web  
**JSON:** JavaScript Object Notation - Formato de intercambio de datos  
**PDO:** PHP Data Objects - Extensión para acceso a bases de datos  
**PostgreSQL:** Sistema de gestión de base de datos relacional  
**REST:** Representational State Transfer - Estilo de arquitectura web  
**SQL:** Structured Query Language - Lenguaje de consulta estructurado  
**SSL:** Secure Sockets Layer - Protocolo de seguridad para comunicaciones  
**TRUNCATE:** Comando SQL para eliminar todos los registros de una tabla  
**UI:** User Interface - Interfaz de usuario  
**UX:** User Experience - Experiencia de usuario  
**XAMPP:** Paquete de software que incluye Apache, MySQL, PHP y Perl

---

**Fin del Manual Técnico**

Para convertir este documento a PDF, puede usar herramientas como:
- Pandoc: `pandoc MANUAL_TECNICO.md -o MANUAL_TECNICO.pdf`
- Online converters como markdown-pdf.com
- VS Code extensions como "Markdown PDF"</content>
<parameter name="filePath">c:\xampp\htdocs\jikkopuntos_v4\MANUAL_TECNICO.md