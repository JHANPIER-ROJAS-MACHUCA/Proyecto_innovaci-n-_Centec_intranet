 <?php

?>
<?php
class Alumno
{
	

	private $idCN;
	private $idCG;
	private $direccion;
	private $tipo;
	private $tipoLocal;
	private $tipoNegocio;
	private $tiempo;

	public function __GET($k){ return $this->$k; }
	public function __SET($k, $v){ return $this->$k = $v; }


}

?>