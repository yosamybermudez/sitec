-- REINICIO DEL SISTEMA
DELETE FROM invent_materia_prima_movimiento;
DELETE FROM invent_materia_prima_entrada;
DELETE FROM invent_materia_prima;
DELETE FROM registro_acceso_sistema;
DELETE FROM registro_asistencia;
DELETE FROM  operac_jornada;
DELETE FROM sistema_comprobante_operac;
DELETE FROM sistema_operac_contable;
DELETE FROM operac_jornada;
DELETE FROM operac_orden_reparacion;
DELETE FROM operac_dictamen_tecnico;
DELETE FROM operac_orden_trabajo;
DELETE FROM ingreso_gasto_trabajador;
DELETE FROM relacion_rol_usuario;
UPDATE sistema_usuario set cargo = null;
DELETE FROM sistema_cargo;
DELETE FROM sistema_equipo_tipo;
DELETE FROM sistema_evento;
DELETE FROM sistema_usuario;
DELETE FROM sistema_rol;


/* crear usuario administrador */

INSERT INTO sistema_usuario (id, username, password, email, activo, created, updated)
VALUES ('54682e4c028ad53b119b82825b6aacc4', 'admin', '$argon2i$v=19$m=65536,t=4,p=1$TmJqaGlzMzh2eTl6V21CbQ$ApoEuOlxw24mrkqoGW4VsvZeG/t7RQTUk1X8BRkgxGY',
'admin@sitec.cu', TRUE, now(), now());


INSERT INTO sistema_rol (id, nombre, identificador,
descripcion, rango, created, updated) VALUES
('0bf614fe9ecfe2cc34df17b18fddb292', 'Administrador del sistema', 
'administrador_sistema', 'Tiene acceso pleno a todas las funcionalidades del sistema',
0, now(), now());

INSERT INTO sistema_rol (id, nombre, identificador,
descripcion, rango, created, updated) VALUES
('dfeb8fd7bf1f9978fa6dc67598a805f9', 'Administrador del negocio', 
'administrador_negocio', 'Tiene acceso pleno a todas las funcionalidades del negocio',
1, now(), now());

INSERT INTO sistema_rol (id, nombre, identificador,
descripcion, rango, created, updated) VALUES
('dfeb8fd7bf1f9978fa6dc67598a80a9d', 'Administracion', 
'administracion', '',
2, now(), now());

INSERT INTO sistema_rol (id, nombre, identificador,
descripcion, rango, created, updated) VALUES
('dfeb8fd7bf1f9978fa6dc67598a80a7d', 'Técnico', 
'tecnico', '',
3, now(), now());

INSERT INTO sistema_rol (id, nombre, identificador,
descripcion, rango, created, updated) VALUES
('dfeb8fd7bf1f9978fa6dc67598a80a8d', 'Recepcionista', 
'recepcionista', '',
3, now(), now());

INSERT INTO relacion_rol_usuario (usuario_id, rol_id) 
VALUES
('54682e4c028ad53b119b82825b6aacc4', '0bf614fe9ecfe2cc34df17b18fddb292');