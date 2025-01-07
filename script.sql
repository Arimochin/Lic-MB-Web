/* Crear base de datos */
DROP TABLE IF EXISTS DATOS_PERSONALES;
CREATE TABLE DATOS_PERSONALES (
    DNI numeric NOT NULL,
    firstname varchar(50) NOT NULL,
    secondname varchar(50) NOT NULL,
    adress varchar(50) NOT NULL,
    phone numeric NOT NULL,
    os varchar(50) NOT NULL,
    dev bytea,
    autor bytea
);

/* Crear usuarios */
REVOKE ALL PRIVILEGES ON DATABASE "lic-MB-DB" FROM patient;
REVOKE ALL PRIVILEGES ON ALL TABLES IN SCHEMA public FROM patient;
DROP USER IF EXISTS patient;
CREATE USER patient WITH PASSWORD 'patient';
CREATE USER terapista WITH PASSWORD 'consultorio7484';

/* Dar privilegios */
GRANT INSERT ON DATOS_PERSONALES TO patient;
