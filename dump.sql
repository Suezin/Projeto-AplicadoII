 -- Create database
CREATE DATABASE IF NOT EXISTS portal_noticias;
USE portal_noticias;


CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    sexo CHAR(1) NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    noticia TEXT NOT NULL,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    autor INT NOT NULL,
    imagem VARCHAR(255),
    FOREIGN KEY (autor) REFERENCES usuarios(id) ON DELETE CASCADE
); 
ALTER TABLE usuarios
ADD COLUMN codigo_recuperacao VARCHAR(6) NULL,
ADD COLUMN codigo_expiracao DATETIME NULL;

drop table usuarios;
drop table noticias;

ALTER TABLE usuarios ADD COLUMN ativo TINYINT(1) DEFAULT 1;
select * from usuarios;

ALTER TABLE usuarios ADD COLUMN codigo_verificacao VARCHAR(10) DEFAULT NULL;
ALTER TABLE usuarios ADD COLUMN codigo_verificacao VARCHAR(10) DEFAULT NULL;


ALTER TABLE noticias MODIFY imagem VARCHAR(255) DEFAULT NULL;

create table anuncio (
	id int AUTO_INCREMENT PRIMARY KEY, 
    nome varchar(255) not null, 
    imagem varchar(300) not null, 
    link varchar(300) not null,
    texto varchar(200) not null, 
    ativo boolean not null,
	destaque boolean NOT null,
    data_cadastro datetime not null,
    valorAnuncio decimal not null
    );