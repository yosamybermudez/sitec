/* crear usuario administrador */

INSERT INTO sistema_usuario (id, updated_by, created_by, username, password, email, activo, created, updated)
VALUES ('54682e4c028ad53b119b82825b6aacc4', '54682e4c028ad53b119b82825b6aacc4', '54682e4c028ad53b119b82825b6aacc4', 'admin', '$argon2i$v=19$m=65536,t=4,p=1$TmJqaGlzMzh2eTl6V21CbQ$ApoEuOlxw24mrkqoGW4VsvZeG/t7RQTUk1X8BRkgxGY',
'admin@sitec.cu', TRUE, now(), now());


INSERT INTO sistema_rol (id, updated_by, created_by, nombre, identificador,
descripcion, rango, created, updated) VALUES
('0bf614fe9ecfe2cc34df17b18fddb292', '54682e4c028ad53b119b82825b6aacc4', 
'54682e4c028ad53b119b82825b6aacc4', 'Administrador del sistema', 
'administrador_sistema', 'Tiene acceso pleno a todas las funcionalidades del sistema',
0, now(), now());

INSERT INTO sistema_rol (id, updated_by, created_by, nombre, identificador,
descripcion, rango, created, updated) VALUES
('dfeb8fd7bf1f9978fa6dc67598a805f9', '54682e4c028ad53b119b82825b6aacc4', 
'54682e4c028ad53b119b82825b6aacc4', 'Administrador del negocio', 
'administrador_negocio', 'Tiene acceso pleno a todas las funcionalidades del negocio',
1, now(), now());

INSERT INTO sistema_rol (id, updated_by, created_by, nombre, identificador,
descripcion, rango, created, updated) VALUES
('dfeb8fd7bf1f9978fa6dc67598a80a9d', '54682e4c028ad53b119b82825b6aacc4', 
'54682e4c028ad53b119b82825b6aacc4', 'Administracion', 
'administracion', '',
2, now(), now());

INSERT INTO sistema_rol (id, updated_by, created_by, nombre, identificador,
descripcion, rango, created, updated) VALUES
('dfeb8fd7bf1f9978fa6dc67598a80a7d', '54682e4c028ad53b119b82825b6aacc4', 
'54682e4c028ad53b119b82825b6aacc4', 'Técnico', 
'tecnico', '',
3, now(), now());

INSERT INTO sistema_rol (id, updated_by, created_by, nombre, identificador,
descripcion, rango, created, updated) VALUES
('dfeb8fd7bf1f9978fa6dc67598a80a8d', '54682e4c028ad53b119b82825b6aacc4', 
'54682e4c028ad53b119b82825b6aacc4', 'Recepcionista', 
'recepcionista', '',
3, now(), now());

INSERT INTO relacion_rol_usuario (usuario_id, rol_id) 
VALUES
('54682e4c028ad53b119b82825b6aacc4', '0bf614fe9ecfe2cc34df17b18fddb292');