# 🚀 Guía Rápida de Configuración - Jikko Puntos Dashboard

## ⚡ Instalación Rápida (5 pasos)

### 1️⃣ Instalar PostgreSQL
```bash
# Descargar de: https://www.postgresql.org/download/
# Durante la instalación, usar:
# - Usuario: postgres
# - Contraseña: postgres
# - Puerto: 5432
```

### 2️⃣ Crear Base de Datos
```bash
# Opción A: Usar el instalador automático (Windows)
install.bat

# Opción B: Manual con psql
psql -U postgres
CREATE DATABASE jikkopuntos_v4;
\c jikkopuntos_v4
\i database.sql
\q
```

### 3️⃣ Habilitar Extensión PHP
```ini
# Editar php.ini (en XAMPP: C:\xampp\php\php.ini)
# Descomentar o agregar estas líneas:
extension=pgsql
extension=pdo_pgsql

# Reiniciar Apache desde el panel de XAMPP
```

### 4️⃣ Configurar Google Sheets

**A. Crear proyecto en Google Cloud:**
1. Ir a https://console.cloud.google.com/
2. Crear nuevo proyecto "Jikko Puntos"
3. Habilitar "Google Sheets API"
4. Crear credenciales → API Key
5. Copiar API Key generada

**B. Preparar Google Sheet:**
1. Crear nuevo Google Sheet
2. Crear 4 pestañas: Campañas, Ingles, Pausas, PuntosAdicionales
3. Agregar encabezados según GOOGLE_SHEETS_TEMPLATE.md
4. Hacer la hoja pública: Compartir → "Cualquiera con el enlace puede ver"
5. Copiar ID del sheet (está en la URL)

### 5️⃣ Configurar Dashboard
1. Abrir http://localhost/jikkopuntos_v4/
2. Ir a "Sincronizar Google Sheets"
3. Ingresar:
   - ID de Hoja de Cálculo
   - Rangos (ejemplo: Campañas!A1:G100)
4. Guardar configuración
5. En cada sección, clic en "Cargar desde Google Sheets"

---

## ✅ Verificación

### Probar Conexión PostgreSQL
1. Ir a "Configuración" en el dashboard
2. Clic en "Probar Conexión"
3. Debe mostrar: ✓ Conexión exitosa

### Probar Carga de Datos
1. Ir a "Campañas"
2. Clic en "Cargar desde Google Sheets"
3. Debe mostrar mensaje de éxito y datos en la tabla

---

## 🐛 Solución Rápida de Problemas

### ❌ Error: No se puede conectar a PostgreSQL
```bash
# Verificar que PostgreSQL esté corriendo
# En Windows: Servicios → postgresql-x64-xx debe estar "Iniciado"

# Probar conexión manual
psql -U postgres -d jikkopuntos_v4
```

### ❌ Error: Extension pgsql not found
```ini
# Verificar php.ini
php -m | findstr pgsql

# Si no aparece, editar php.ini y descomentar:
extension=pgsql
extension=pdo_pgsql

# Reiniciar Apache
```

### ❌ Error: Cannot access Google Sheets
- Verificar que la API Key sea válida
- Verificar que Google Sheets API esté habilitada
- Verificar que la hoja sea pública
- Verificar que el ID del sheet sea correcto
- Verificar que los rangos estén bien escritos

### ❌ Error: Headers don't match
- Verificar que los encabezados en Google Sheets coincidan EXACTAMENTE
- Primera fila debe tener los nombres de columnas
- No debe haber espacios extras ni caracteres especiales

---

## 📊 Estructura de Datos Esperada

### Google Sheets - Encabezados Requeridos

**Campañas:**
```
nombre | descripcion | fecha_inicio | fecha_fin | estado | puntos_base
```

**Ingles:**
```
empleado_id | empleado_nombre | nivel | puntos | fecha_evaluacion | certificacion
```

**Pausas:**
```
empleado_id | empleado_nombre | tipo_pausa | duracion | fecha | hora_inicio | hora_fin | puntos_deducidos
```

**PuntosAdicionales:**
```
empleado_id | empleado_nombre | concepto | puntos | fecha | aprobado_por | observaciones
```

---

## 🔐 Seguridad para Producción

Para usar en producción, aplicar estos cambios:

1. **Cambiar credenciales de PostgreSQL**
2. **Usar HTTPS** (certificado SSL)
3. **Implementar autenticación de usuarios**
4. **Usar Service Account en lugar de API Key**
5. **Agregar validación de inputs**
6. **Configurar backups automáticos**

---

## 📞 Comandos Útiles

### PostgreSQL
```bash
# Conectar a base de datos
psql -U postgres -d jikkopuntos_v4

# Ver tablas
\dt

# Ver datos de una tabla
SELECT * FROM campañas;

# Limpiar una tabla
TRUNCATE TABLE campañas RESTART IDENTITY CASCADE;

# Salir
\q
```

### PHP
```bash
# Ver extensiones cargadas
php -m

# Ver información de PHP
php -i

# Verificar errores
# Ver: C:\xampp\apache\logs\error.log
```

---

## 🎯 Checklist de Instalación

- [ ] PostgreSQL instalado y corriendo
- [ ] Base de datos jikkopuntos_v4 creada
- [ ] Tablas creadas (ejecutar database.sql)
- [ ] Extensión PHP pgsql habilitada
- [ ] Apache reiniciado
- [ ] Proyecto accesible en http://localhost/jikkopuntos_v4/
- [ ] Google Cloud proyecto creado
- [ ] Google Sheets API habilitada
- [ ] API Key generada
- [ ] Google Sheet creado con pestañas correctas
- [ ] Encabezados agregados a cada pestaña
- [ ] Hoja compartida públicamente
- [ ] Configuración guardada en dashboard
- [ ] Prueba de conexión exitosa
- [ ] Datos de prueba cargados correctamente

---

## 📱 URLs Importantes

- **Dashboard:** http://localhost/jikkopuntos_v4/
- **Google Cloud Console:** https://console.cloud.google.com/
- **Google Sheets:** https://sheets.google.com/
- **PostgreSQL Download:** https://www.postgresql.org/download/

---

**¡Listo para usar! 🎉**

Si todo está marcado en el checklist, tu dashboard está completamente funcional.
