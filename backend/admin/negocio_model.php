<?php
?>
<?php


class AlumnoModel
{
	 

	private $pdo;
	
	public function __CONSTRUCT()
	{
		try
		{
			include ('conection/construct.php');
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);		        
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function Listar($idCG)
	{
		try
		{
			$result = array();
			$stm = $this->pdo->prepare("SELECT * FROM tclie_negocio where idCG=$idCG");
			$stm->execute();

			foreach($stm->fetchAll(PDO::FETCH_OBJ) as $r)
			{
				$alm = new Alumno();
				$alm->__SET('idCN', $r->idCN);
				$alm->__SET('idCG', $r->idCG);
				$alm->__SET('direccion', $r->direccion);
				$alm->__SET('tipo', $r->tipo);
				$alm->__SET('tipoLocal', $r->tipoLocal);
				$alm->__SET('tipoNegocio', $r->tipoNegocio);
				$alm->__SET('tiempo', $r->tiempo);
				$result[] = $alm;
			}

			return $result;
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function Obtener($idCN)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM tclie_negocio WHERE idCN = ?");
			$stm->execute(array($idCN));
			$r = $stm->fetch(PDO::FETCH_OBJ);
			$alm = new Alumno();
			$alm->__SET('idCN', $r->idCN);
			$alm->__SET('idCG', $r->idCG);
			$alm->__SET('direccion', $r->direccion);
			$alm->__SET('tipo', $r->tipo);
			$alm->__SET('tipoLocal', $r->tipoLocal);
			$alm->__SET('tipoNegocio', $r->tipoNegocio);
			$alm->__SET('tiempo', $r->tiempo);
			return $alm;
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Eliminar($idCN)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("DELETE FROM tclie_negocio WHERE idCN = ?");			          

			$stm->execute(array($idCN));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Actualizar(Alumno $data)
	{
		try 
		{
			$sql = "UPDATE tclie_negocio SET 
						idCG          = ?, 
						direccion          = ?, 
						tipo        = ?,
						tipoLocal            = ?, 
						tipoNegocio = ?,
						tiempo = ?
				    WHERE idCN = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				array(
					$data->__GET('idCG'), 
					$data->__GET('direccion'), 
					$data->__GET('tipo'), 
					$data->__GET('tipoLocal'),
					$data->__GET('tipoNegocio'),
					$data->__GET('tiempo'),
					$data->__GET('idCN')
					)
				);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(Alumno $data)
	{
		try 
		{
		$sql = "INSERT INTO tclie_negocio (idCG,direccion,tipo,tipoLocal,tipoNegocio,tiempo) 
		        VALUES (?,?,?,?,?,?)";

		$this->pdo->prepare($sql)
		     ->execute(
			array(
				$data->__GET('idCG'), 
				$data->__GET('direccion'), 
				$data->__GET('tipo'), 
				$data->__GET('tipoLocal'),
				$data->__GET('tipoNegocio'),
				$data->__GET('tiempo')
				)
			);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}

?>