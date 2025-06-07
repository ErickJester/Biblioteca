use base_biblioteca
-- Tabla principal de usuarios
CREATE TABLE Tipo_usuario(
	tipo_usuario INT PRIMARY KEY
);

CREATE TABLE Usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    tipo_usuario INT,
    contrasena VARCHAR(21),
    code_user VARCHAR(20),
    nombre VARCHAR(60),
    apellidoP VARCHAR(60),
    apellidoM VARCHAR(60),
    fecha_nacimiento DATE,
    fecha_registro DATETIME,
    FOREIGN KEY (tipo_usuario) REFERENCES Tipo_usuario(tipo_usuario)
);


-- Roles especializados
CREATE TABLE Administrador (
    id_admin INT PRIMARY KEY,
    FOREIGN KEY (id_admin) REFERENCES Usuario(id_usuario)
);

CREATE TABLE Bibliotecario (
    id_biblio INT PRIMARY KEY,
    telefono VARCHAR(15),
    FOREIGN KEY (id_biblio) REFERENCES Usuario(id_usuario)
);

CREATE TABLE Lector (
    id_lector INT PRIMARY KEY,
    telefono VARCHAR(15),
    correo VARCHAR(40),
    FOREIGN KEY (id_lector) REFERENCES Usuario(id_usuario)
);

-- Materiales
CREATE TABLE Material (
    id_material INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(150),
    autor VARCHAR(100),
    fecha_registro DATE,
    cantidad INT,
    genero VARCHAR(50)
);

-- Subtipos de materiales
CREATE TABLE Libro (
    id_libro INT PRIMARY KEY,
    edicion VARCHAR(50),
    editorial VARCHAR(50),
    calificacion INT,
    FOREIGN KEY (id_libro) REFERENCES Material(id_material)
);

CREATE TABLE Revista (
    id_revista INT PRIMARY KEY,
    edicion VARCHAR(50),
    FOREIGN KEY (id_revista) REFERENCES Material(id_material)
);

CREATE TABLE Digital (
    id_digital INT PRIMARY KEY,
    FOREIGN KEY (id_digital) REFERENCES Material(id_material)
);

-- Préstamos
CREATE TABLE Prestamo (
    id_prestamo INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT,
    id_material INT,
    fecha_prestamo DATE,
    fecha_vencimiento DATE,
    fecha_devolucion DATE,
    estado VARCHAR(20),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_material) REFERENCES Material(id_material)
);

-- Multas
CREATE TABLE Multa (
    id_multa INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT,
    id_prestamo INT,
    monto INT,
    motivo TEXT,
    fecha_multa DATE,
    pagada BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_prestamo) REFERENCES Prestamo(id_prestamo)
);

-- Pagos de multas
CREATE TABLE PagoMulta (
    id_pago INT PRIMARY KEY AUTO_INCREMENT,
    id_multa INT,
    fecha_pago DATE,
    FOREIGN KEY (id_multa) REFERENCES Multa(id_multa)
);

-- Reportes mensuales
CREATE TABLE ReporteMensual (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    mes INT,
    anio INT,
    fecha_generacion DATETIME,
    ruta_archivo VARCHAR(255)
);

CREATE TABLE IntentoLogin (
    code_user VARCHAR(20) PRIMARY KEY,
    intentos INT DEFAULT 0,
    ultimo_intento DATETIME DEFAULT CURRENT_TIMESTAMP
);


