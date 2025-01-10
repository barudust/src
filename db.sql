-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS financiamiento;
USE financiamiento;

-- Tabla usuario
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('Usuario', 'Administrador') NOT NULL
);

-- Tabla adeudo
CREATE TABLE adeudo (
    id_adeudo INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(50),
    monto DECIMAL(10,2) NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    estado ENUM('Pendiente', 'Pagado') DEFAULT 'Pendiente',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE deuda (
    id_deuda INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    entidad VARCHAR(100) NOT NULL,
    monto_deuda DECIMAL(10,2) NOT NULL,
    tasa_interes DECIMAL(5,2) DEFAULT 0.00,
    descripcion TEXT,  -- Descripción adicional de la deuda
    estatus ENUM('pendiente', 'pagado') DEFAULT 'pendiente',  -- Estado de la deuda
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);


-- Tabla transaccion
CREATE TABLE transaccion (
    id_transaccion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    tipo ENUM('Ingreso', 'Egreso') NOT NULL,
    descripcion TEXT,
    categoria varchar(30),
    monto DECIMAL(10,2) NOT NULL,
    fecha DATE NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Tabla inversion
CREATE TABLE inversion (
    id_inversion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    rendimiento DECIMAL(5,2),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Tabla presupuesto
CREATE TABLE presupuesto (
    id_presupuesto INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(50),
    fecha_inicio DATE NOT NULL,  -- Fecha de inicio de la quincena
    monto DECIMAL(10,2),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
