/* Crear base de datos */
DROP TABLE IF EXISTS DATOS_PERSONALES;
CREATE TABLE DATOS_PERSONALES (
    DNI numeric NOT NULL,
    firstname varchar(50) NOT NULL,
    secondname varchar(50) NOT NULL,
    date_of_birth date NOT NULL,
    adress varchar(50) NOT NULL,
    phone numeric NOT NULL,
    os varchar(50) NOT NULL,
    schedule varchar(400) NOT NULL,
    dev varchar(500),
    autor varchar(500),
    PRIMARY KEY (DNI)
);
DROP TABLE IF EXISTS FICHA_MEDICA;
CREATE TABLE FICHA_MEDICA (
    DNI numeric NOT NULL,
    firstname varchar(50) NOT NULL,
    secondname varchar(50) NOT NULL,
    date_of_birth date NOT NULL,
    adress varchar(50) NOT NULL,
    phone numeric NOT NULL,
    os varchar(50) NOT NULL,
    schedule varchar(400) NOT NULL,
    diagnosis varchar(4000),
    evaluation varchar(4000),
    surgery_date date,
    discharge_date date,
    observations varchar(10000),
    dev varchar(500),
    autor varchar(500),
    PRIMARY KEY (DNI)
);
DROP TABLE IF EXISTS PATIENT_IMAGES;
CREATE TABLE PATIENT_IMAGES (
    DNI numeric NOT NULL,
    IMG numeric NOT NULL,
    src varchar(500),
    PRIMARY KEY (DNI, IMG)
);

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

/* Crear usuarios */
REVOKE ALL PRIVILEGES ON DATABASE "lic-MB-DB" FROM patient;
REVOKE ALL PRIVILEGES ON ALL TABLES IN SCHEMA public FROM patient;
DROP USER IF EXISTS patient;
CREATE USER patient WITH PASSWORD 'patient';
CREATE USER terapista WITH PASSWORD 'consultorio7484';

/* Dar privilegios */
GRANT INSERT ON DATOS_PERSONALES TO patient;
GRANT SELECT ON users TO terapista;
GRANT SELECT ON DATOS_PERSONALES TO terapista;
GRANT INSERT ON DATOS_PERSONALES TO terapista;
GRANT UPDATE ON DATOS_PERSONALES TO terapista;
GRANT DELETE ON DATOS_PERSONALES TO terapista;
GRANT INSERT ON FICHA_MEDICA TO terapista;
GRANT UPDATE ON FICHA_MEDICA TO terapista;
GRANT SELECT ON FICHA_MEDICA TO terapista;
GRANT DELETE ON FICHA_MEDICA TO terapista;
GRANT DELETE ON FICHA_MEDICA TO terapista;
GRANT DELETE ON PATIENT_IMAGES TO terapista;
GRANT UPDATE ON PATIENT_IMAGES TO terapista;
GRANT INSERT ON PATIENT_IMAGES TO terapista;
GRANT SELECT ON PATIENT_IMAGES TO terapista;
/*Extension para cifrado de passwords*/
CREATE EXTENSION pgcrypto;
/* Insertar usuarios en tabla */
INSERT INTO users (username, password) VALUES ('terapista', crypt('consultorio7484', gen_salt('bf')));