<?php

?>
<?php


class AlumnoModel2
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

	public function Listar2($idCG)
	{
		try
		{
			$result = array();
			$stm = $this->pdo->prepare("SELECT * FROM tclie_direccion d,ta_dis di,ta_prov p,ta_depa de where idCG=$idCG and d.iddis=di.iddis and di.idprov=p.idprov and p.iddepa=de.iddepa");
			$stm->execute();

			foreach($stm->fetchAll(PDO::FETCH_OBJ) as $r)
			{
				$alm2 = new Alumno2();
				$alm2->__SET('idCD', $r->idCD);
				$alm2->__SET('idCG', $r->idCG);
				$alm2->__SET('iddis', $r->iddis);
				$alm2->__SET('distri', $r->distri);
				$alm2->__SET('provi', $r->provi);
				$alm2->__SET('depa', $r->depa);
				$alm2->__SET('anexo', $r->anexo);
				$alm2->__SET('direc', $r->direc);
				$alm2->__SET('referencia', $r->referencia);
				$result[] = $alm2;
			}

			return $result;
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function Obtener2($idCD)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM tclie_direccion WHERE idCD = ?");
			$stm->execute(array($idCD));
			$r = $stm->fetch(PDO::FETCH_OBJ);
			$alm2 = new Alumno2();
				$alm2->__SET('idCD', $r->idCD);
				$alm2->__SET('idCG', $r->idCG);
				$alm2->__SET('iddis', $r->iddis);
				$alm2->__SET('anexo', $r->anexo);
				$alm2->__SET('direc', $r->direc);
				$alm2->__SET('referencia', $r->referencia);
			return $alm2;
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Eliminar2($idCD)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("DELETE FROM tclie_direccion WHERE idCD = ?");			          

			$stm->execute(array($idCD));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Actualizar2(Alumno2 $data)
	{
		try 
		{
			$sql = "UPDATE tclie_direccion SET 
						idCG          = ?, 
						iddis          = ?, 
						anexo        = ?,
						direc            = ?, 
						referencia = ?
				
				    WHERE idCD = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				array(
					$data->__GET('idCG'), 
					$data->__GET('iddis'), 
					$data->__GET('anexo'), 
					$data->__GET('direc'),
					$data->__GET('referencia'),
					$data->__GET('idCD')
					)
				);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar2(Alumno2 $data)
	{
		try 
		{
		$sql = "INSERT INTO tclie_direccion (idCG,iddis,anexo,direc,referencia) 
		        VALUES (?,?,?,?,?)";

		$this->pdo->prepare($sql)
		     ->execute(
			array(
				$data->__GET('idCG'), 
					$data->__GET('iddis'), 
					$data->__GET('anexo'), 
					$data->__GET('direc'),
					$data->__GET('referencia'),
			
				)
			);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}

?>