# 🚀 Guía de Configuración con Enlaces CSV

## URL de Puntos Adicionales Configurada

✅ **URL CSV ya configurada:**
```
https://docs.google.com/spreadsheets/d/e/2PACX-1vT_YovS-kYOHIkiwx_YQyzMvixS52UQSihicIpKL0mv3Z2QZZShLLk-NnrANoQIKE7ZcbbWdxO40lQa/pub?gid=0&single=true&output=csv
```

## 📋 Pasos para Usar el Sistema

### 1. Verificar PostgreSQL
```bash
# Ejecutar el instalador
install.bat

# O manualmente
psql -U postgres
CREATE DATABASE jikkopuntos_v4;
\c jikkopuntos_v4
\i database.sql
\q
```

### 2. Probar Descarga del CSV
Abrir en el navegador:
```
http://localhost/jikkopuntos_v4/api/test_csv.php
```

Esto mostrará:
- Los datos del CSV
- Los encabezados detectados
- Verificación de que funciona correctamente

### 3. Acceder al Dashboard
```
http://localhost/jikkopuntos_v4/
```

### 4. Cargar Datos de Puntos Adicionales

1. Ir a la sección **"Puntos Adicionales"** en el sidebar
2. Hacer clic en el botón **"Cargar desde Google Sheets"**
3. Los datos se cargarán automáticamente desde el CSV

### 5. Configurar Otras Tablas (Opcional)

Si tienes URLs CSV para Campañas, Inglés y Pausas:

1. Ir a **"Sincronizar Google Sheets"** en el sidebar
2. La URL de Puntos Adicionales ya está configurada
3. Agregar las otras URLs si las tienes disponibles
4. Hacer clic en **"Guardar Configuración"**

## 📊 Cómo Obtener URLs CSV para Otras Tablas

Para cada Google Sheet que quieras sincronizar:

1. **Abrir el Google Sheet**
2. **Archivo → Compartir → Publicar en la web**
3. **Seleccionar la hoja específica** (pestaña)
4. **Formato:** Valores separados por comas (.csv)
5. **Copiar el enlace** generado
6. **Pegar en la configuración** del dashboard

El enlace debe verse similar a:
```
https://docs.google.com/spreadsheets/d/e/2PACX-.../pub?gid=0&single=true&output=csv
```

## 🎯 Estructura Esperada del CSV

### Puntos Adicionales
El CSV debe tener estos encabezados en la primera fila:
```
empleado_id,empleado_nombre,concepto,puntos,fecha,aprobado_por,observaciones
```

Ejemplo de datos:
```csv
empleado_id,empleado_nombre,concepto,puntos,fecha,aprobado_por,observaciones
1001,Juan Pérez,Proyecto especial,50,2026-02-08,Gerente TI,Excelente desempeño
1002,María García,Meta cumplida,100,2026-02-10,Director,Superó objetivos
```

### Campañas
```
nombre,descripcion,fecha_inicio,fecha_fin,estado,puntos_base
```

### Inglés
```
empleado_id,empleado_nombre,nivel,puntos,fecha_evaluacion,certificacion
```

### Pausas
```
empleado_id,empleado_nombre,tipo_pausa,duracion,fecha,hora_inicio,hora_fin,puntos_deducidos
```

## ✅ Ventajas del Sistema con CSV

- ✅ **No requiere API Key** - Más simple de configurar
- ✅ **Enlaces públicos** - Solo necesitas publicar el Google Sheet
- ✅ **Actualización automática** - Cada vez que cargas, obtiene los datos más recientes
- ✅ **Sin límites de cuota** - No hay restricciones de API
- ✅ **Más rápido** - Descarga directa sin autenticación

## 🔧 Solución de Problemas

### ❌ Error al cargar CSV
1. Verificar que el Google Sheet esté publicado
2. Verificar que la URL termine en `output=csv`
3. Probar la URL en el navegador directamente
4. Usar `test_csv.php` para diagnosticar

### ❌ Encabezados no coinciden
- Los encabezados del CSV deben coincidir exactamente
- No debe haber espacios extras
- Respetar mayúsculas/minúsculas

### ❌ No se cargan los datos
1. Ir a Configuración → Probar Conexión (verificar PostgreSQL)
2. Revisar que las tablas existan (`psql -U postgres -d jikkopuntos_v4`)
3. Ver logs de Apache: `C:\xampp\apache\logs\error.log`

## 📝 Próximos Pasos

1. ✅ Abrir http://localhost/jikkopuntos_v4/api/test_csv.php
2. ✅ Verificar que los datos se vean correctos
3. ✅ Ir al dashboard y cargar Puntos Adicionales
4. ✅ ¡Listo para usar!

---

**Sistema actualizado:** Febrero 10, 2026  
**Método:** Enlaces CSV públicos de Google Sheets
