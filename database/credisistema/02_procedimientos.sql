-- Procedimientos almacenados CENTECPC
-- Extraídos de cent3cpcom_crediinversion (5).sql
-- Subir APARTE con phpMyAdmin: pestaña Importar / o cuadro SQL con delimitador $$
-- Nota: se quitó DEFINER para evitar error de usuario inexistente en cPanel.
-- Nota: la BD de cPanel es crediinversion_centecp.

DELIMITER $$

DROP PROCEDURE IF EXISTS `CompromisoPagoDetail`$$

CREATE PROCEDURE `CompromisoPagoDetail` (IN `idCliente` INT)
BEGIN
	DECLARE prestamo_ int;
    DECLARE date_ date;
    DECLARE mora_ int;

    DECLARE done BOOLEAN DEFAULT FALSE;

    DECLARE prestamos_cliente CURSOR FOR
        Select idp,fechaDesembolso,penalty
        from tprestamo
        where idCG=idCliente and finished_at is null and estado=4;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    CREATE TEMPORARY TABLE IF NOT EXISTS tempCopromiso (
        idpr INT,
		fecha DATE,
        mora DECIMAL(10, 2)
    );

			OPEN prestamos_cliente;

			bucle_juve: LOOP

				FETCH prestamos_cliente INTO prestamo_,date_,mora_;

				IF done THEN
					LEAVE bucle_juve;
				END IF;

                call DetallePrestamoCompromiso(prestamo_,@resultado);
                if @resultado then
					insert into tempCopromiso (idpr,fecha,mora) values (prestamo_,date_,mora_);
                end if;

 			END LOOP bucle_juve;

			CLOSE prestamos_cliente;

		SELECT * FROM tempCopromiso;

		DROP TEMPORARY TABLE tempCopromiso;
END$$

DROP PROCEDURE IF EXISTS `datosClientes`$$

CREATE PROCEDURE `datosClientes` (IN `idcliente` INT)
BEGIN
	DECLARE dni_ int;
    DECLARE dir_ text;
    DECLARE datos text;

    DECLARE idg2 int;
    DECLARE tipo_ text;
    DECLARE dni_2 int;
    DECLARE datos_2 text;
    DECLARE relacion text;


	SELECT dni,direc,concat(ap,' ', am, ' ',nom)as datos into dni_,dir_,datos FROM tclie_general where idcg=idcliente;

    SELECT relationable_id,type  into idg2,tipo_  FROM relations where customer_id=idcliente and relationable_type='App\\Models\\Customer';

    SELECT dni,concat(ap,' ', am, ' ',nom)as datos into dni_2, datos_2 FROM tclie_general where idcg=idg2;

    select dni_,datos,dir_,dni_2,datos_2,tipo_;
END$$

DROP PROCEDURE IF EXISTS `DetallePrestamoCompromiso`$$

CREATE PROCEDURE `DetallePrestamoCompromiso` (IN `idprestamo` INT, OUT `resultado` BOOL)
BEGIN
	set resultado=false;
	select if(count(*)>0,true,false) into resultado from tpresta_detalle where idp=3247 and datediff(curdate(),expiration_at)>3 and is_finished=0;
END$$

DELIMITER ;