# Jikko Puntos Dashboard v4

Dashboard moderno para la gestión de puntos de empleados con integración a Google Sheets y PostgreSQL.

## 🚀 Características

- ✅ Dashboard moderno con sidebar responsive
- ✅ Gestión de Campañas
- ✅ Evaluaciones de Inglés
- ✅ Registro de Pausas
- ✅ Puntos Adicionales
- ✅ Sincronización con Google Sheets
- ✅ Base de datos PostgreSQL
- ✅ Interfaz intuitiva y moderna

## 📋 Requisitos

- XAMPP con PHP 7.4 o superior
- PostgreSQL 12 o superior
- Extensión PHP PostgreSQL (php_pgsql)
- Navegador web moderno
- Cuenta de Google Cloud (para Google Sheets API)

## 🔧 Instalación

### 1. Configurar PostgreSQL

1. Instalar PostgreSQL desde https://www.postgresql.org/download/
2. Crear la base de datos:

```bash
# Abrir psql o pgAdmin
CREATE DATABASE jikkopuntos_v4;
```

3. Ejecutar el script de creación de tablas:

```bash
psql -U postgres -d jikkopuntos_v4 -f database.sql
```

O desde pgAdmin, ejecutar el contenido del archivo `database.sql`

### 2. Configurar PHP

Asegurarse de que la extensión PostgreSQL esté habilitada en `php.ini`:

```ini
extension=pgsql
extension=pdo_pgsql
```

Reiniciar Apache después de modificar php.ini.

### 3. Configurar el Proyecto

El proyecto ya está configurado para conectarse a PostgreSQL con:
- Host: localhost
- Puerto: 5432
- Base de datos: jikkopuntos_v4
- Usuario: postgres
- Contraseña: postgres

Si necesita cambiar estas credenciales, edite el archivo `config/database.php`.

### 4. Configurar Google Sheets API

#### Opción A: API Key (Solo lectura, más simple)

1. Ir a https://console.cloud.google.com/
2. Crear un nuevo proyecto o seleccionar uno existente
3. Habilitar "Google Sheets API"
4. Crear credenciales → API Key
5. Copiar la API Key

#### Opción B: Service Account (Recomendado para producción)

1. Ir a https://console.cloud.google.com/
2. Crear un nuevo proyecto
3. Habilitar "Google Sheets API"
4. Crear credenciales → Service Account
5. Descargar el JSON de credenciales
6. Compartir la hoja de Google Sheets con el email del service account

### 5. Preparar Google Sheets

Crear un Google Sheet con las siguientes hojas (pestañas):

#### Hoja "Campañas"
```
nombre | descripcion | fecha_inicio | fecha_fin | estado | puntos_base
```

#### Hoja "Ingles"
```
empleado_id | empleado_nombre | nivel | puntos | fecha_evaluacion | certificacion
```

#### Hoja "Pausas"
```
empleado_id | empleado_nombre | tipo_pausa | duracion | fecha | hora_inicio | hora_fin | puntos_deducidos
```

#### Hoja "PuntosAdicionales"
```
empleado_id | empleado_nombre | concepto | puntos | fecha | aprobado_por | observaciones
```

**Importante:** La primera fila de cada hoja debe contener los encabezados exactamente como se muestran arriba.

## 🎯 Uso

### 1. Acceder al Dashboard

Abrir en el navegador:
```
http://localhost/jikkopuntos_v4/
```

### 2. Configurar Google Sheets

1. Ir a la sección "Sincronizar Google Sheets" en el sidebar
2. Ingresar:
   - ID de la hoja de cálculo (lo encuentras en la URL: `https://docs.google.com/spreadsheets/d/ID_AQUI/edit`)
   - Rangos para cada tabla (ejemplo: `Campañas!A1:G100`)
3. Guardar configuración

### 3. Sincronizar Datos

En cada sección (Campañas, Inglés, Pausas, Puntos Adicionales):
1. Hacer clic en el botón "Cargar desde Google Sheets"
2. Confirmar la acción
3. Los datos se cargarán automáticamente en la base de datos

### 4. Ver y Gestionar Datos

- Los datos se muestran en tablas con estadísticas
- Puedes editar y eliminar registros directamente
- Las estadísticas se actualizan automáticamente

## 📁 Estructura del Proyecto

```
jikkopuntos_v4/
├── index.php                 # Dashboard principal
├── database.sql             # Script de creación de tablas
├── README.md               # Este archivo
│
├── config/
│   ├── database.php        # Configuración de PostgreSQL
│   └── google_sheets_config.json  # Configuración de Google Sheets (se crea automáticamente)
│
├── api/
│   ├── get_data.php        # Obtener datos de tablas
│   ├── sync_google_sheets.php  # Sincronizar con Google Sheets
│   ├── save_config.php     # Guardar configuración
│   ├── get_sync_history.php  # Historial de sincronización
│   ├── test_connection.php  # Probar conexión a BD
│   ├── delete_row.php      # Eliminar registro
│   ├── clear_tables.php    # Limpiar tablas
│   └── reset_database.php  # Resetear base de datos
│
└── assets/
    ├── css/
    │   └── style.css       # Estilos del dashboard
    └── js/
        └── main.js         # JavaScript del dashboard
```

## 🔒 Seguridad

**Importante para producción:**

1. Cambiar credenciales de PostgreSQL
2. Usar variables de entorno para credenciales sensibles
3. Implementar autenticación de usuarios
4. Usar HTTPS
5. Validar y sanitizar todos los inputs
6. Implementar rate limiting en la API
7. Usar Service Account para Google Sheets en lugar de API Key

## 🐛 Solución de Problemas

### Error de conexión a PostgreSQL

1. Verificar que PostgreSQL esté ejecutándose
2. Verificar credenciales en `config/database.php`
3. Verificar que la extensión `php_pgsql` esté habilitada
4. Revisar el firewall y permisos de PostgreSQL

### Error al sincronizar con Google Sheets

1. Verificar que la API Key sea válida
2. Verificar que Google Sheets API esté habilitada
3. Verificar que la hoja sea pública o esté compartida
4. Verificar que los rangos estén correctamente especificados
5. Revisar que los encabezados coincidan con los esperados

### Tablas no se crean

1. Ejecutar manualmente el archivo `database.sql` en psql o pgAdmin
2. Verificar permisos del usuario postgres
3. Revisar logs de PostgreSQL

## 📝 Configuración Avanzada

### Cambiar credenciales de base de datos

Editar `config/database.php`:

```php
define('DB_HOST', 'tu_host');
define('DB_PORT', 'tu_puerto');
define('DB_NAME', 'tu_base_de_datos');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### Personalizar estilos

Los estilos se encuentran en `assets/css/style.css`. Puedes modificar las variables CSS en la sección `:root` para cambiar colores y temas.

### Agregar más tablas

1. Agregar la tabla en `database.sql`
2. Agregar el item en el sidebar en `index.php`
3. Crear la sección en `index.php`
4. Agregar funciones de carga en `assets/js/main.js`
5. Agregar el caso en `api/sync_google_sheets.php`

## 📞 Soporte

Para reportar problemas o solicitar características, contactar al equipo de desarrollo.

## 📄 Licencia

Este proyecto es de uso interno de Jikko.

---

**Versión:** 4.0  
**Última actualización:** Febrero 2026
