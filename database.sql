-- Crear la base de datos (ejecutar como superusuario)
-- CREATE DATABASE jikkopuntos_v4;

-- Conectarse a la base de datos jikkopuntos_v4 y ejecutar lo siguiente:

-- Tabla de campañas
CREATE TABLE IF NOT EXISTS campañas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    estado VARCHAR(50) DEFAULT 'activa',
    puntos_base INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de inglés
CREATE TABLE IF NOT EXISTS ingles (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER,
    empleado_nombre VARCHAR(255) NOT NULL,
    nivel VARCHAR(50),
    puntos INTEGER DEFAULT 0,
    fecha_evaluacion DATE,
    certificacion VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de pausas
CREATE TABLE IF NOT EXISTS pausas (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER,
    empleado_nombre VARCHAR(255) NOT NULL,
    tipo_pausa VARCHAR(100),
    duracion INTEGER, -- en minutos
    fecha DATE,
    hora_inicio TIME,
    hora_fin TIME,
    puntos_deducidos INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de puntos adicionales
CREATE TABLE IF NOT EXISTS puntos_adicionales (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER,
    empleado_nombre VARCHAR(255) NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    puntos INTEGER DEFAULT 0,
    fecha DATE,
    aprobado_por VARCHAR(255),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de empleados (opcional, para normalizar datos)
CREATE TABLE IF NOT EXISTS empleados (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    departamento VARCHAR(100),
    puesto VARCHAR(100),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de registro de sincronización con Google Sheets
CREATE TABLE IF NOT EXISTS sync_log (
    id SERIAL PRIMARY KEY,
    tabla_nombre VARCHAR(100) NOT NULL,
    registros_procesados INTEGER DEFAULT 0,
    registros_exitosos INTEGER DEFAULT 0,
    registros_fallidos INTEGER DEFAULT 0,
    fecha_sync TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(50) DEFAULT 'completado',
    mensaje TEXT
);

-- Índices para mejorar el rendimiento
CREATE INDEX IF NOT EXISTS idx_campañas_estado ON campañas(estado);
CREATE INDEX IF NOT EXISTS idx_ingles_empleado ON ingles(empleado_nombre);
CREATE INDEX IF NOT EXISTS idx_pausas_empleado ON pausas(empleado_nombre);
CREATE INDEX IF NOT EXISTS idx_pausas_fecha ON pausas(fecha);
CREATE INDEX IF NOT EXISTS idx_puntos_empleado ON puntos_adicionales(empleado_nombre);
CREATE INDEX IF NOT EXISTS idx_empleados_activo ON empleados(activo);
