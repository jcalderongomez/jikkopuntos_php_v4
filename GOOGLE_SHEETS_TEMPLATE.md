# Ejemplos de Datos para Google Sheets

## 📊 Formato de las Hojas

A continuación se muestran ejemplos de cómo deben estructurarse los datos en Google Sheets.

### Hoja: Campañas

**Encabezados (Fila 1):**
```
nombre | descripcion | fecha_inicio | fecha_fin | estado | puntos_base
```

**Datos de ejemplo:**
```
Campaña Q1 2026 | Objetivos primer trimestre | 2026-01-01 | 2026-03-31 | activa | 100
Campaña Verano | Promoción temporada verano | 2026-06-01 | 2026-08-31 | inactiva | 150
Campaña Navidad | Ventas fin de año | 2026-12-01 | 2026-12-31 | activa | 200
```

---

### Hoja: Ingles

**Encabezados (Fila 1):**
```
empleado_id | empleado_nombre | nivel | puntos | fecha_evaluacion | certificacion
```

**Datos de ejemplo:**
```
1001 | Juan Pérez | B2 | 80 | 2026-02-01 | TOEFL
1002 | María García | C1 | 100 | 2026-02-05 | Cambridge
1003 | Carlos López | A2 | 50 | 2026-01-28 | Interno
```

**Niveles sugeridos:** A1, A2, B1, B2, C1, C2

---

### Hoja: Pausas

**Encabezados (Fila 1):**
```
empleado_id | empleado_nombre | tipo_pausa | duracion | fecha | hora_inicio | hora_fin | puntos_deducidos
```

**Datos de ejemplo:**
```
1001 | Juan Pérez | Almuerzo | 60 | 2026-02-10 | 13:00:00 | 14:00:00 | 0
1002 | María García | Café | 15 | 2026-02-10 | 10:30:00 | 10:45:00 | 0
1003 | Carlos López | Personal | 30 | 2026-02-10 | 15:00:00 | 15:30:00 | 5
```

**Tipos de pausa sugeridos:** Almuerzo, Café, Baño, Personal, Emergencia

---

### Hoja: PuntosAdicionales

**Encabezados (Fila 1):**
```
empleado_id | empleado_nombre | concepto | puntos | fecha | aprobado_por | observaciones
```

**Datos de ejemplo:**
```
1001 | Juan Pérez | Proyecto especial | 50 | 2026-02-08 | Gerente TI | Excelente desempeño en proyecto
1002 | María García | Ayuda a compañeros | 20 | 2026-02-09 | Supervisor | Colaboración excepcional
1003 | Carlos López | Meta cumplida | 100 | 2026-02-10 | Director | Superó objetivos mensuales
```

---

## 🔗 Cómo Crear el Google Sheet

1. **Crear nuevo Google Sheet:**
   - Ir a https://sheets.google.com
   - Crear nueva hoja de cálculo
   - Nombrarla "Jikko Puntos Data"

2. **Crear las pestañas:**
   - Renombrar la primera pestaña a "Campañas"
   - Crear 3 pestañas adicionales: "Ingles", "Pausas", "PuntosAdicionales"

3. **Agregar encabezados:**
   - En cada pestaña, copiar los encabezados exactos de arriba en la fila 1
   - Los encabezados deben coincidir exactamente con los nombres de columnas

4. **Agregar datos de prueba:**
   - Copiar algunos datos de ejemplo en cada hoja
   - Respetando el formato de fechas (YYYY-MM-DD) y horas (HH:MM:SS)

5. **Compartir la hoja:**
   - Clic en "Compartir" (botón superior derecha)
   - Opción A: Cambiar a "Cualquier persona con el enlace puede ver"
   - Opción B: Compartir con el email del Service Account

6. **Obtener el ID:**
   - Copiar el ID de la URL: `https://docs.google.com/spreadsheets/d/[ESTE_ES_EL_ID]/edit`
   - Usarlo en la configuración del dashboard

---

## 📝 Plantilla Lista para Copiar

Puedes crear una copia de este sheet de ejemplo (crear tu propio sheet con esta estructura):

**Estructura completa:**

```
┌─ Campañas ──────────────────────────────────────────────────┐
│ nombre | descripcion | fecha_inicio | fecha_fin | estado | puntos_base │
├──────────────────────────────────────────────────────────────┤
│ (tus datos aquí)                                              │
└──────────────────────────────────────────────────────────────┘

┌─ Ingles ────────────────────────────────────────────────────┐
│ empleado_id | empleado_nombre | nivel | puntos | fecha_evaluacion | certificacion │
├──────────────────────────────────────────────────────────────┤
│ (tus datos aquí)                                              │
└──────────────────────────────────────────────────────────────┘

┌─ Pausas ────────────────────────────────────────────────────┐
│ empleado_id | empleado_nombre | tipo_pausa | duracion | fecha | hora_inicio | hora_fin | puntos_deducidos │
├──────────────────────────────────────────────────────────────┤
│ (tus datos aquí)                                              │
└──────────────────────────────────────────────────────────────┘

┌─ PuntosAdicionales ─────────────────────────────────────────┐
│ empleado_id | empleado_nombre | concepto | puntos | fecha | aprobado_por | observaciones │
├──────────────────────────────────────────────────────────────┤
│ (tus datos aquí)                                              │
└──────────────────────────────────────────────────────────────┘
```

---

## ⚠️ Notas Importantes

1. **Formato de fechas:** Usar formato YYYY-MM-DD (ejemplo: 2026-02-10)
2. **Formato de horas:** Usar formato HH:MM:SS (ejemplo: 14:30:00)
3. **Números:** Sin símbolos de moneda o separadores de miles
4. **Texto:** Evitar caracteres especiales que puedan causar problemas
5. **Celdas vacías:** Dejar vacías si no hay dato (no usar "-" o "N/A")

---

## 🎯 Rangos Recomendados

Al configurar en el dashboard, usar estos rangos:

- **Campañas:** `Campañas!A1:G100`
- **Inglés:** `Ingles!A1:F100`
- **Pausas:** `Pausas!A1:H100`
- **Puntos Adicionales:** `PuntosAdicionales!A1:G100`

Ajustar el número final (100) según la cantidad de datos que tengas.
