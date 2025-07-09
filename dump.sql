-- Create database
CREATE DATABASE IF NOT EXISTS portal_noticias;
USE portal_noticias;

-- Create users table
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    sexo ENUM('M', 'F', 'O') DEFAULT 'O',
    codigo_recuperacao VARCHAR(6),
    codigo_expiracao DATETIME,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1
);

-- Create news table
CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    noticia TEXT NOT NULL,
    autor INT NOT NULL,
    imagem VARCHAR(255),
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (autor) REFERENCES usuarios(id) ON DELETE CASCADE
);

ALTER TABLE usuarios ADD COLUMN ativo TINYINT(1) DEFAULT 1;