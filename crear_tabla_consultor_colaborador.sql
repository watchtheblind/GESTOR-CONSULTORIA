-- Crear tabla para manejar la relación entre consultores principales y colaboradores
CREATE TABLE `consultor_colaborador` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `consultor_principal_id` bigint(20) NOT NULL,
  `colaborador_id` bigint(20) NOT NULL,
  `fecha_asignacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_consultor_colaborador` (`consultor_principal_id`,`colaborador_id`),
  FOREIGN KEY (`consultor_principal_id`) REFERENCES `usuarios` (`id`),
  FOREIGN KEY (`colaborador_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 