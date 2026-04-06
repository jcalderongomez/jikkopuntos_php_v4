# Documento de Requerimientos del Sistema
## Sistema de Gestión de Puntos Jikko - Jikko Puntos v4

---

## 1. INFORMACIÓN GENERAL

**Cliente:** Jikkosoft  
**Proyecto:** Sistema de Gestión de Puntos para Empleados  
**Versión:** 4.0  
**Fecha:** Marzo 2026  
**Responsable:** Área de Recursos Humanos

---

## 2. CONTEXTO Y JUSTIFICACIÓN

### 2.1 Situación Actual
La empresa Jikkosoft necesita un sistema centralizado para gestionar y calcular puntos de incentivos para sus empleados. Actualmente, la información se encuentra dispersa en múltiples hojas de Google Sheets sin un sistema unificado de consulta y reporte.

### 2.2 Objetivo General
Desarrollar una aplicación web que centralice la información de participación de empleados en diferentes actividades corporativas, calculando automáticamente los puntos acumulados y permitiendo generar reportes consolidados.

### 2.3 Alcance
El sistema debe gestionar cuatro categorías principales de actividades que generan puntos:
- Participación en campañas corporativas
- Asistencia a clases de inglés
- Administración de pausas activas
- Puntos adicionales por reconocimientos especiales

---

## 3. REQUERIMIENTOS FUNCIONALES

### 3.1 Gestión de Campañas

**RF-001:** El sistema debe permitir cargar información de campañas desde Google Sheets.

**RF-002:** Cada participación en campaña debe registrar:
- Número de identificación del empleado
- Nombre completo del empleado
- Nombre de la campaña o actividad
- Fecha de participación
- Empresa (Jikkosoft/Sofka)
- Observaciones

**RF-003:** Cada participación en campaña otorga **50 puntos** al empleado.

**RF-004:** El sistema debe mostrar estadísticas de:
- Total de registros de campañas
- Total de empleados únicos participantes

**RF-005:** Debe permitir filtrar campañas por:
- Nombre del empleado
- Número de documento
- Empresa
- Rango de fechas

### 3.2 Gestión de Inglés

**RF-006:** El sistema debe cargar participaciones en clases de inglés desde Google Sheets.

**RF-007:** Cada asistencia a clase de inglés debe registrar:
- Número de identificación del empleado
- Nombre completo del empleado
- Nivel de inglés
- Fecha de evaluación/asistencia
- Certificación u observaciones

**RF-008:** Cada asistencia a clase de inglés otorga **30 puntos** al empleado.

**RF-009:** El sistema debe mostrar:
- Total de evaluaciones/asistencias
- Total de puntos acumulados (asistencias × 30)

**RF-010:** No se deben mostrar registros sin fecha válida.

### 3.3 Gestión de Pausas

**RF-011:** El sistema debe cargar pausas desde Google Sheets (3 fuentes diferentes).

**RF-012:** Cada pausa debe registrar:
- Número de identificación del empleado
- Nombre completo del empleado
- Tipo de pausa (1, 2, 3, o "Jueves de pausar con todos")
- Fecha
- Evidencia (URL o documento)

**RF-013:** Sistema de puntos para pausas:
- Tipo 1, 2 o 3: **15 puntos fijos**
- "Jueves de pausar con todos": **50 puntos**

**RF-014:** Debe permitir filtrar pausas por:
- Nombre del empleado
- Número de documento
- Rango de fechas

**RF-015:** No debe existir filtro por tipo de pausa.

### 3.4 Gestión de Puntos Adicionales

**RF-016:** El sistema debe cargar puntos adicionales desde Google Sheets.

**RF-017:** Cada registro de puntos adicionales debe contener:
- Número de identificación del empleado
- Nombre completo del empleado
- Concepto (motivo del reconocimiento)
- Cantidad de puntos asignados
- Fecha
- Persona que aprueba
- Observaciones

**RF-018:** Los puntos adicionales se suman tal cual están definidos en cada registro.

**RF-019:** Debe permitir filtrar por:
- Nombre del empleado
- Número de documento
- Puntos específicos
- Rango de fechas
- Actividad/concepto

### 3.5 Reporte de Totales

**RF-020:** El sistema debe generar un reporte consolidado que muestre por empleado:
- Número de documento
- Nombre completo
- Puntos de Inglés (suma de asistencias × 30)
- Puntos de Pausas (suma según tipo)
- Puntos de Campañas (participaciones × 50)
- Puntos Adicionales (suma de puntos asignados)
- **Total general de puntos**

**RF-021:** El reporte debe mostrar estadísticas globales:
- Total de empleados en el sistema
- Total de puntos acumulados
- Promedio de puntos por empleado

**RF-022:** El reporte debe ordenarse por total de puntos (mayor a menor).

**RF-023:** Debe permitir filtrar el reporte por:
- Nombre del empleado
- Número de documento
- Rango de fechas (filtrando cada fuente por su fecha correspondiente)

**RF-024:** El sistema debe permitir exportar el reporte a Excel (.xlsx) con:
- Todas las columnas del reporte
- Fila de totales generales al final
- Formato profesional con columnas ajustadas
- Nombre de archivo: `Jikko_Totales_YYYY-MM-DD.xlsx`
- Si hay filtros de fecha, incluirlos en el nombre del archivo

### 3.6 Sincronización de Datos

**RF-025:** El sistema debe tener un botón único "Sincronizar Todo Ahora" que cargue:
- Campañas (2 fuentes de Google Sheets)
- Inglés (1 fuente de Google Sheets)
- Pausas (3 fuentes de Google Sheets)
- Puntos Adicionales (1 fuente de Google Sheets configurada)

**RF-026:** La sincronización debe:
- Mostrar barra de progreso
- Indicar qué tabla se está procesando
- Mostrar resumen al finalizar con:
  - Registros procesados por tabla
  - Registros exitosos
  - Registros fallidos
  - Errores si existen

**RF-027:** Durante la sincronización se debe limpiar (TRUNCATE) cada tabla antes de cargar los nuevos datos.

**RF-028:** Las URLs de Google Sheets están hardcodeadas en el código para Campañas, Inglés y Pausas.

**RF-029:** Solo Puntos Adicionales requiere configuración manual de URL CSV.

### 3.7 Reglas de Exclusión de Datos

**RF-030:** **NO** se deben mostrar ni contabilizar registros sin fecha en ninguna sección:
- Campañas: si `fecha` está vacía o es "-"
- Inglés: si `fecha_evaluacion` está vacía o es "-"
- Pausas: si `fecha` está vacía o es "-"
- Puntos Adicionales: si `fecha` está vacía o es "-"

**RF-031:** Los filtros de fecha solo deben aplicar a registros que tengan fecha válida.

---

## 4. REQUERIMIENTOS NO FUNCIONALES

### 4.1 Tecnología

**RNF-001:** Frontend: HTML5, CSS3, JavaScript (vanilla)

**RNF-002:** Backend: PHP 7.4+

**RNF-003:** Base de datos: PostgreSQL

**RNF-004:** Servidor web: Apache (XAMPP)

**RNF-005:** Librería de exportación: SheetJS (xlsx)

### 4.2 Interfaz de Usuario

**RNF-006:** La interfaz debe ser responsive y compatible con navegadores modernos.

**RNF-007:** Debe incluir menú lateral con las secciones:
- Campañas
- Inglés
- Pausas
- Puntos Adicionales
- Totales
- Sincronizar

**RNF-008:** Cada sección debe mostrar tarjetas estadísticas relevantes.

**RNF-009:** Las tablas deben ser paginables visualmente con scroll.

**RNF-010:** Los botones deben tener iconos de Font Awesome para mejor UX.

### 4.3 Rendimiento

**RNF-011:** La sincronización completa no debe exceder 5 minutos.

**RNF-012:** Las consultas de totales deben procesar eficientemente miles de registros.

**RNF-013:** La exportación a Excel debe completarse en menos de 10 segundos.

### 4.4 Seguridad

**RNF-014:** Validar todos los datos provenientes de Google Sheets.

**RNF-015:** Usar prepared statements en todas las consultas SQL.

**RNF-016:** Sanitizar entradas de usuario en filtros.

---

## 5. ESTRUCTURA DE DATOS

### 5.1 Tabla: participacion_campanas
```
- id (SERIAL PRIMARY KEY)
- empleado_id (INTEGER)
- empleado_nombre (VARCHAR 255) NOT NULL
- nombre_campana (VARCHAR 255)
- fecha (DATE)
- empresa (VARCHAR 100)
- observaciones (TEXT)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### 5.2 Tabla: ingles
```
- id (SERIAL PRIMARY KEY)
- empleado_id (INTEGER)
- empleado_nombre (VARCHAR 255) NOT NULL
- nivel (VARCHAR 50)
- puntos (INTEGER DEFAULT 0) -- Siempre 30
- fecha_evaluacion (DATE)
- certificacion (VARCHAR 255)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### 5.3 Tabla: pausas
```
- id (SERIAL PRIMARY KEY)
- empleado_id (INTEGER)
- empleado_nombre (VARCHAR 255) NOT NULL
- tipo_pausa (VARCHAR 100)
- duracion (INTEGER) -- en minutos
- fecha (DATE)
- hora_inicio (TIME)
- hora_fin (TIME)
- puntos_deducidos (INTEGER DEFAULT 0)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### 5.4 Tabla: puntos_adicionales
```
- id (SERIAL PRIMARY KEY)
- empleado_id (INTEGER)
- empleado_nombre (VARCHAR 255) NOT NULL
- concepto (VARCHAR 255) NOT NULL
- puntos (INTEGER DEFAULT 0)
- fecha (DATE)
- aprobado_por (VARCHAR 255)
- observaciones (TEXT)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

---

## 6. REGLAS DE NEGOCIO

### RN-001: Cálculo de Puntos
**Inglés:** Por cada asistencia = 30 puntos  
**Pausas:** Tipo 1, 2 o 3 = 15 puntos | Jueves pausar = 50 puntos  
**Campañas:** Por cada participación = 50 puntos  
**Adicionales:** Puntos variables según aprobación

### RN-002: Exclusión de Datos
Solo se contabilizan y muestran registros con fecha válida (no vacía, no "-", no null).

### RN-003: Sincronización
La sincronización limpia (TRUNCATE) las tablas antes de cargar nuevos datos desde Google Sheets.

### RN-004: Totales
Los totales se calculan en tiempo real sumando todas las fuentes de puntos por empleado.

### RN-005: Exportación
La exportación refleja los datos filtrados actualmente visibles en la tabla de totales.

---

## 7. CASOS DE USO PRINCIPALES

### CU-001: Sincronizar Todos los Datos
**Actor:** Administrador  
**Flujo:**
1. Usuario accede a la sección "Sincronizar"
2. Usuario hace clic en "Sincronizar Todo Ahora"
3. Sistema confirma la acción
4. Sistema muestra barra de progreso
5. Sistema carga datos de cada fuente en secuencia
6. Sistema muestra resumen de resultados
7. Sistema actualiza datos en memoria

### CU-002: Consultar Totales por Empleado
**Actor:** Administrador  
**Flujo:**
1. Usuario accede a la sección "Totales"
2. Sistema carga y muestra tabla consolidada
3. Usuario puede aplicar filtros (nombre, documento, fechas)
4. Sistema recalcula y muestra datos filtrados
5. Usuario puede exportar a Excel

### CU-003: Exportar Reporte a Excel
**Actor:** Administrador  
**Flujo:**
1. Usuario está en la sección "Totales"
2. Usuario aplica filtros deseados (opcional)
3. Usuario hace clic en "Exportar a Excel"
4. Sistema genera archivo .xlsx con los datos visibles
5. Sistema descarga el archivo al navegador
6. Sistema muestra notificación de éxito

---

## 8. INTEGRACIONES

### 8.1 Google Sheets
- Formato: CSV exportable públicamente
- Método: Lectura mediante URLs públicas de publicación web
- Frecuencia: Bajo demanda (manual mediante botón de sincronización)

### 8.2 Archivos Fuente (Hardcodeados)

**Campañas:**
- Fuente 1: Google Sheet publicado en CSV
- Fuente 2: Google Sheet publicado en CSV

**Inglés:**
- Fuente 1: Google Sheet publicado en CSV

**Pausas:**
- Fuente 1: Google Sheet publicado en CSV
- Fuente 2: Google Sheet publicado en CSV
- Fuente 3: Google Sheet publicado en CSV

**Puntos Adicionales:**
- Configuración manual vía interfaz (URL CSV)

---

## 9. CRITERIOS DE ACEPTACIÓN

### CA-001: Sincronización Completa
- ✅ Todas las tablas se cargan correctamente
- ✅ Se muestran registros procesados/exitosos/fallidos
- ✅ No hay errores de conexión
- ✅ Datos se reflejan en las secciones individuales

### CA-002: Cálculo de Totales
- ✅ Los puntos de cada categoría se suman correctamente
- ✅ Total general = suma de todas las categorías
- ✅ Estadísticas globales son precisas
- ✅ Ordenamiento correcto por puntos descendente

### CA-003: Filtros
- ✅ Filtros por texto funcionan con coincidencias parciales
- ✅ Filtros por fecha excluyen correctamente registros fuera del rango
- ✅ Registros sin fecha se incluyen cuando no hay filtro de fecha
- ✅ Múltiples filtros se aplican con lógica AND

### CA-004: Exportación Excel
- ✅ Archivo se descarga correctamente
- ✅ Incluye todos los datos filtrados visibles
- ✅ Fila de totales al final
- ✅ Formato legible y profesional
- ✅ Nombre de archivo con fecha/filtros

### CA-005: Exclusión de Registros sin Fecha
- ✅ No se muestran registros con fecha vacía
- ✅ No se contabilizan en totales
- ✅ No aparecen en estadísticas
- ✅ No se exportan a Excel

---

## 10. CONSIDERACIONES TÉCNICAS

### 10.1 Mapeo de Columnas
El sistema debe ser flexible para reconocer diferentes nombres de columnas en Google Sheets:
- **Documento:** "Número de identificación", "NÚMERO DOCUMENTO", "Documento", etc.
- **Nombre:** "Nombre completo", "NOMBRES Y APELLIDOS", "Nombre de la persona", etc.
- **Fecha:** "Marca temporal", "Fecha", "Fecha de inicio", "Timestamp", etc.

### 10.2 Conversión de Fechas
Soportar múltiples formatos:
- YYYY-MM-DD
- DD/MM/YYYY
- DD/MM/YYYY HH:MM:SS
- M/D/YYYY

### 10.3 Manejo de Errores
- Registros sin campos obligatorios se omiten y registran en log
- URLs inaccesibles generan error descriptivo
- Fallos de inserción se reportan en resumen final

---

## 11. ENTREGABLES

1. **Código fuente completo**
   - Frontend (HTML/CSS/JS)
   - Backend (PHP)
   - Scripts SQL

2. **Base de datos**
   - Script de creación (database.sql)
   - Índices para optimización

3. **Documentación**
   - README.md
   - QUICK_START.md
   - Manual de usuario
   - Documentación técnica

4. **Sistema instalado y funcional en servidor local (XAMPP)**

---

## 12. RESTRICCIONES Y SUPUESTOS

### Restricciones
- R1: El sistema funciona en entorno local (XAMPP)
- R2: Requiere PostgreSQL instalado y configurado
- R3: Los Google Sheets deben estar publicados en formato CSV
- R4: No hay autenticación de usuarios (acceso directo)

### Supuestos
- S1: Los datos de Google Sheets tienen estructura consistente
- S2: La red permite acceso a URLs públicas de Google
- S3: El administrador tiene conocimientos básicos de PHP/PostgreSQL
- S4: Los empleados usan número de documento como identificador único

---

## 13. GLOSARIO

**CSV**: Comma-Separated Values (Valores Separados por Comas)  
**Google Sheets**: Hoja de cálculo en línea de Google  
**PostgreSQL**: Sistema de gestión de bases de datos relacional  
**XAMPP**: Suite de software para servidor web local  
**Truncate**: Operación que elimina todos los registros de una tabla  
**SheetJS**: Librería JavaScript para manejo de archivos Excel  

---

**Fin del Documento de Requerimientos**
