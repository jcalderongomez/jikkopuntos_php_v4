# SISTEMA DE GESTIÓN DE PUNTOS JIKKO
## Jikko Puntos v4.0

---

## PROPÓSITO

El presente documento tiene como propósito establecer los lineamientos, políticas y condiciones generales para la implementación y operación del Sistema de Gestión de Puntos Jikko (Jikko Puntos v4), una plataforma web diseñada para centralizar, automatizar y gestionar el programa de incentivos y reconocimientos de la empresa Jikkosoft.

El sistema busca:
- **Centralizar** la información dispersa en múltiples fuentes de datos (Google Sheets)
- **Automatizar** el cálculo de puntos según las actividades de participación de los empleados
- **Facilitar** la consulta y generación de reportes consolidados
- **Transparentar** el proceso de asignación de puntos e incentivos
- **Optimizar** el tiempo del área de Recursos Humanos en la gestión de reconocimientos

---

## ALCANCE

### Alcance Funcional

El Sistema de Gestión de Puntos Jikko abarca las siguientes áreas:

#### 1. Módulos Incluidos
- **Módulo de Campañas Corporativas:** Registro y contabilización de participación en campañas internas
- **Módulo de Inglés:** Seguimiento de asistencia a clases de Conversation Club y English Classes
- **Módulo de Pausas Activas:** Control de participación en pausas y "Jueves de Pausar con Todos"
- **Módulo de Puntos Adicionales:** Registro de reconocimientos especiales y puntos extraordinarios
- **Módulo de Totales:** Consolidación y reporte general de puntos por empleado
- **Módulo de Sincronización:** Carga masiva de datos desde fuentes externas (Google Sheets)

#### 2. Usuarios Beneficiados
- **Área de Recursos Humanos:** Usuario administrador con acceso completo
- **Empleados de Jikkosoft y Sofka:** Beneficiarios del programa de puntos (consulta indirecta vía RH)

#### 3. Cobertura Geográfica
- Operación en ambiente local (sede principal)
- Acceso mediante red interna o local

#### 4. Exclusiones del Alcance
El sistema **NO incluye**:
- Portal de autogestión para empleados (solo acceso administrativo)
- Sistema de autenticación multinivel (acceso directo sin login)
- Gestión de canje de puntos por premios
- Integración con sistemas de nómina o ERP
- Aplicación móvil
- Módulo de reportes avanzados con gráficos interactivos

---

## DEFINICIONES

Para efectos de la correcta interpretación del presente documento, se establecen las siguientes definiciones:

**SISTEMA:** Plataforma web Jikko Puntos v4, compuesta por frontend (HTML/CSS/JavaScript) y backend (PHP/PostgreSQL).

**EMPLEADO:** Persona colaboradora de Jikkosoft o Sofka identificada mediante número de documento.

**PUNTOS:** Unidad de medida utilizada para cuantificar la participación de empleados en actividades corporativas.

**CAMPAÑAS:** Actividades, eventos o iniciativas corporativas en las cuales los empleados pueden participar.

**ASISTENCIA A INGLÉS:** Participación confirmada en Conversation Club o English Class.

**PAUSAS ACTIVAS:** Momentos de descanso activo durante la jornada laboral.

**JUEVES DE PAUSAR CON TODOS:** Actividad especial de integración realizada los días jueves.

**PUNTOS ADICIONALES:** Reconocimientos extraordinarios otorgados por logros, comportamientos destacados o situaciones especiales.

**SINCRONIZACIÓN:** Proceso de carga masiva de datos desde Google Sheets hacia la base de datos del sistema.

**GOOGLE SHEETS:** Herramienta de hojas de cálculo en línea de Google utilizada como fuente primaria de datos.

**CSV:** Formato de archivo de valores separados por comas (Comma-Separated Values).

**TRUNCATE:** Operación de base de datos que elimina todos los registros de una tabla.

**EXPORTACIÓN:** Proceso de generación de archivo Excel (.xlsx) con datos consolidados.

**FILTRO:** Criterio de búsqueda aplicado para segmentar información.

**ADMINISTRADOR:** Usuario del área de Recursos Humanos con acceso completo al sistema.

---

## POLÍTICAS

### 1. Política de Asignación de Puntos

#### 1.1 Puntos por Participación en Campañas
- Cada participación en campaña corporativa otorga **50 puntos** al empleado.
- Solo se contabilizan participaciones con fecha válida registrada.
- No hay límite de participaciones por empleado en diferentes campañas.
- Campañas duplicadas el mismo día para el mismo empleado cuentan como participaciones independientes.

#### 1.2 Puntos por Asistencia a Inglés
- Cada asistencia a Conversation Club o English Class otorga **30 puntos** al empleado.
- Solo se contabilizan asistencias con fecha válida registrada.
- No se requiere certificación de nivel para obtener puntos.
- El nivel de inglés es informativo y no afecta la cantidad de puntos.

#### 1.3 Puntos por Pausas Activas
- Pausa tipo 1, 2 o 3: **15 puntos fijos** cada una
- "Jueves de Pausar con Todos": **50 puntos**
- Solo se contabilizan pausas con fecha válida registrada.
- Las pausas se acumulan independientemente del tipo.

#### 1.4 Puntos Adicionales
- Los puntos adicionales se asignan según el criterio del aprobador.
- Deben tener concepto claramente definido.
- Requieren aprobación formal (nombre del aprobador registrado).
- Solo se contabilizan puntos adicionales con fecha válida registrada.

### 2. Política de Exclusión de Datos

#### 2.1 Registros sin Fecha
- **NO se mostrarán** en ninguna sección del sistema los registros sin fecha válida.
- **NO se contabilizarán** en totales ni estadísticas.
- **NO se exportarán** a reportes Excel.
- Fechas consideradas inválidas: vacías, NULL, guión "-", texto no reconocible.

#### 2.2 Registros Duplicados
- Se permiten registros duplicados si corresponden a eventos distintos.
- La responsabilidad de evitar duplicados recae en la fuente (Google Sheets).

### 3. Política de Sincronización

#### 3.1 Frecuencia
- La sincronización es **bajo demanda** (manual mediante botón).
- No hay sincronización automática programada.
- Se recomienda sincronizar al menos una vez por semana.

#### 3.2 Limpieza de Datos
- Cada sincronización ejecuta un **TRUNCATE** (eliminación total) de las tablas.
- Los datos históricos no se conservan en la base de datos (solo en Google Sheets).
- No hay versionamiento de datos en el sistema.

#### 3.3 Fuentes de Datos
- Las URLs de Google Sheets para Campañas, Inglés y Pausas están **hardcodeadas** en el código.
- Solo la URL de Puntos Adicionales es configurable vía interfaz.
- Modificar URLs de fuentes hardcodeadas requiere acceso al código fuente.

### 4. Política de Acceso y Seguridad

#### 4.1 Control de Acceso
- El sistema **no cuenta con autenticación** de usuarios.
- Acceso directo mediante URL (http://localhost/jikkopuntos_v4).
- La seguridad se gestiona mediante control de acceso físico/red.

#### 4.2 Manipulación de Datos
- Solo el administrador puede sincronizar datos.
- No hay funcionalidad de edición manual en el sistema principal.
- Modificaciones deben realizarse en Google Sheets y resincronizar.

#### 4.3 Exportación de Datos
- Cualquier usuario con acceso puede exportar reportes a Excel.
- Los reportes exportados reflejan el estado actual con filtros aplicados.

### 5. Política de Reportes

#### 5.1 Reporte de Totales
- El reporte consolida todas las fuentes de puntos por empleado.
- Se ordena automáticamente por total de puntos (mayor a menor).
- Incluye estadísticas globales: total empleados, total puntos, promedio.

#### 5.2 Exportación
- Formato: Microsoft Excel (.xlsx)
- Incluye fila de totales generales al final
- Nombre de archivo con fecha actual
- Si hay filtros de fecha, se incluyen en el nombre del archivo

---

## CONTEXTO

### Situación Organizacional

Jikkosoft es una empresa de desarrollo de software que cuenta con un equipo de colaboradores distribuidos en diferentes áreas y proyectos. La empresa ha implementado un programa de incentivos y reconocimientos basado en puntos, donde los empleados acumulan puntos por su participación en diversas actividades corporativas:

- **Campañas corporativas** de comunicación interna, cultura organizacional y bienestar
- **Clases de inglés** para desarrollo profesional
- **Pausas activas** para promover la salud y el bienestar
- **Reconocimientos especiales** por logros o comportamientos destacados

### Problemática Identificada

Antes de la implementación del sistema, la gestión del programa de puntos presentaba los siguientes desafíos:

1. **Dispersión de información:** Los datos se encontraban en múltiples hojas de Google Sheets sin conexión entre sí.
2. **Cálculo manual:** El área de RH debía calcular manualmente los puntos de cada empleado.
3. **Tiempo excesivo:** Generación de reportes consolidados tomaba varias horas.
4. **Errores humanos:** El proceso manual era propenso a errores de cálculo y omisiones.
5. **Falta de visibilidad:** No existía una forma rápida de consultar el estado de puntos.

### Solución Propuesta

El Sistema de Gestión de Puntos Jikko v4 se desarrolló para:
- Centralizar la consulta de información desde múltiples fuentes
- Automatizar completamente el cálculo de puntos
- Reducir el tiempo de generación de reportes de horas a minutos
- Minimizar errores mediante reglas de negocio programadas
- Facilitar la toma de decisiones del área de RH

### Tecnología Seleccionada

La solución se implementó con tecnologías accesibles y de fácil mantenimiento:
- **Frontend:** HTML5, CSS3, JavaScript (vanilla) - Sin frameworks complejos
- **Backend:** PHP 7.4+ - Ampliamente conocido y soportado
- **Base de datos:** PostgreSQL - Robusto y gratuito
- **Servidor:** Apache/XAMPP - Fácil instalación en entorno local
- **Integración:** APIs públicas de Google Sheets (formato CSV)

---

## CONDICIONES GENERALES

### 1. Responsabilidades

#### 1.1 Del Área de Recursos Humanos
- Mantener actualizadas las hojas de Google Sheets con información correcta.
- Ejecutar la sincronización periódicamente (recomendado: semanal).
- Validar la integridad de los datos antes de sincronizar.
- Responder consultas de empleados sobre sus puntos.
- Conservar respaldos de las hojas de Google Sheets.

#### 1.2 Del Área de Tecnología (si aplica)
- Mantener operativo el servidor XAMPP.
- Garantizar disponibilidad de PostgreSQL.
- Realizar respaldos periódicos de la base de datos.
- Actualizar URLs de Google Sheets cuando sea necesario (Campañas, Inglés, Pausas).
- Brindar soporte técnico ante incidencias.

#### 1.3 De los Empleados
- Registrar su participación en las actividades en los formularios de Google correspondientes.
- Consultar con RH sobre el estado de sus puntos cuando sea necesario.
- Reportar inconsistencias detectadas en sus registros.

### 2. Condiciones de Operación

#### 2.1 Requisitos de Infraestructura
- Servidor con XAMPP instalado (Apache + PHP + PostgreSQL)
- Conexión a Internet para sincronización con Google Sheets
- Navegador web moderno (Chrome, Firefox, Edge)
- Acceso de red al servidor donde está alojado el sistema

#### 2.2 Disponibilidad del Sistema
- El sistema está disponible mientras el servidor XAMPP esté activo.
- No hay garantía de disponibilidad 24/7 (entorno local).
- Mantenimientos deben coordinarse con el área de RH.

#### 2.3 Rendimiento Esperado
- Sincronización completa: máximo 5 minutos
- Consulta de totales: respuesta inmediata (< 2 segundos)
- Exportación a Excel: máximo 10 segundos
- Aplicación de filtros: inmediato

### 3. Limitaciones Conocidas

#### 3.1 Técnicas
- El sistema no cuenta con autenticación de usuarios.
- No hay auditoria de cambios o logs de acceso.
- La base de datos no conserva históricos (se sobrescribe en cada sincronización).
- Las URLs de Google Sheets están hardcodeadas (excepto Puntos Adicionales).

#### 3.2 Funcionales
- No permite edición manual de registros desde la interfaz.
- No hay funcionalidad de reversión o rollback.
- No integra con sistemas de nómina o ERP.
- No hay portal de autogestión para empleados.

#### 3.3 De Integración
- Dependencia total de Google Sheets como fuente de datos.
- Si Google Sheets no está disponible, no se puede sincronizar.
- Los cambios en la estructura de columnas de Google Sheets pueden afectar la carga.

### 4. Proceso de Soporte

#### 4.1 Niveles de Soporte
- **Nivel 1:** Área de RH (consultas de empleados sobre puntos)
- **Nivel 2:** Área de Tecnología (problemas técnicos del sistema)

#### 4.2 Canales de Reporte
- Correo electrónico al área correspondiente
- Ticket interno (si existe sistema de tickets)
- Comunicación directa presencial/telefónica

#### 4.3 Tiempos de Respuesta
- Consultas de puntos: 1 día hábil
- Problemas técnicos críticos: 4 horas
- Problemas técnicos no críticos: 2 días hábiles
- Solicitudes de mejoras: evaluación en siguiente ciclo

### 5. Mantenimiento y Actualizaciones

#### 5.1 Mantenimiento Preventivo
- Respaldo de base de datos: mensual (mínimo)
- Revisión de logs de sincronización: semanal
- Validación de integridad de datos: mensual

#### 5.2 Actualizaciones
- Las actualizaciones al sistema requieren aprobación de RH y Tecnología.
- Se debe notificar con 48 horas de anticipación.
- Las actualizaciones deben incluir pruebas en ambiente de desarrollo.

### 6. Cumplimiento y Normativas

#### 6.1 Protección de Datos Personales
- Los datos de empleados (nombres, documentos) son confidenciales.
- Solo personal autorizado debe tener acceso al sistema.
- No se comparten datos con terceros.
- Los reportes exportados deben manejarse con confidencialidad.

#### 6.2 Transparencia
- Los criterios de asignación de puntos son públicos y conocidos por todos.
- Cualquier empleado puede solicitar revisión de sus puntos.
- Las políticas del programa son comunicadas a todos los empleados.

### 7. Vigencia y Modificaciones

#### 7.1 Vigencia
Este documento entra en vigor a partir de su publicación y permanece vigente hasta nueva notificación.

#### 7.2 Modificaciones
- Cualquier modificación a estas políticas debe ser aprobada por la Gerencia de RH.
- Las modificaciones deben comunicarse a todos los usuarios con al menos 5 días de anticipación.
- Se debe generar nueva versión del documento con control de cambios.

#### 7.3 Revisión
Este documento debe revisarse al menos una vez al año o cuando circunstancias lo ameriten.

---

**Documento elaborado por:** Área de Tecnología y Recursos Humanos  
**Fecha de elaboración:** Marzo 2026  
**Versión:** 1.0  
**Estado:** Vigente

---

**Fin del Documento**
