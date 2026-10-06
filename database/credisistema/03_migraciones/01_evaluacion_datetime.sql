-- Guardar fecha con hora + control de modificación en evaluaciones
-- Ejecutar en la misma BD del sistema (cent3cpcom_crediinversion).

ALTER TABLE eval_credito MODIFY fecha DATETIME NOT NULL;
ALTER TABLE eval_indicadores MODIFY fecha DATETIME NOT NULL;

-- updated_at se actualiza automáticamente en cada modificación.
ALTER TABLE eval_credito MODIFY updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE eval_indicadores MODIFY updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;