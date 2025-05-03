CREATE TABLE cliente_consultor (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    cliente_id BIGINT(20) NOT NULL,
    consultor_id BIGINT(20) NOT NULL,
    fecha_asignacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (consultor_id) REFERENCES usuarios(id),
    UNIQUE KEY unique_cliente_consultor (cliente_id, consultor_id)
); 