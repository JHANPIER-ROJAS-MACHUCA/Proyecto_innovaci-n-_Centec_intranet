<?php
require_once __DIR__ . '/../Repositories/ClienteRepository.php';
require_once __DIR__ . '/../../Creditos/Repositories/CreditoRepository.php';
require_once __DIR__ . '/../../../repositories/CatalogoRepository.php';
require_once __DIR__ . '/../../../repositories/FinancieraRepository.php';

class ClienteService
{
    public static function validate(array $in): array
    {
        $errors = [];
        if (empty($in['dni']) || strlen((string)$in['dni']) !== 8) $errors['dni'] = 'El dni debe tener 8 dígitos.';
        elseif (ClienteRepository::dniExists($in['dni'])) $errors['dni'] = 'El dni ya esta en uso.';
        foreach (['ap', 'am', 'nom'] as $f) if (empty($in[$f])) $errors[$f] = "El campo {$f} es requerido.";
        if (!empty($in['correo']) && !filter_var($in['correo'], FILTER_VALIDATE_EMAIL)) $errors['correo'] = 'Correo inválido.';
        return $errors;
    }

    public static function create(array $in): array
    {
        $allowed = ['risk_profile_id','client_type','dni','direc','correo','telefono','rubro','cel','nom','ap','am','fec_nac','n_hijos','grado_inst','estado_civil','lugar_nac','referencia','tipo','comentario','sexo','idU','coordinate_lat','coordinate_lng','ubigeo_id'];
        $data = array_intersect_key($in, array_flip($allowed));
        $data['idO'] = 1;
        $data['status'] = 'ACTIVE';
        return ClienteRepository::create($data)->toArray();
    }

    public static function update(int $idCG, array $in): void
    {
        if (!$idCG) throw new DomainException('idCG requerido.');
        \App\Models\Customer::where('idCG', $idCG)->update($in);
    }

    public static function detalle(int $idCG): array
    {
        $c = ClienteRepository::find($idCG);
        if (!$c) throw new DomainException('Cliente no existe.');
        $data = $c->toArray();
        $data['risk_profile'] = $c->riskProfile ?? null;
        $data['ubigeo'] = ClienteRepository::ubigeo($c->ubigeo_id);
        $data['creditos'] = CreditoRepository::byCustomer($c->idCG)->toArray();
        $data['attachments'] = AdjuntoRepository::listar($c->idCG)->toArray();
        return $data;
    }

    public static function operaciones(int $idCG)
    {
        return TransaccionRepository::operacionesCliente($idCG);
    }

    public static function buscar(string $q)
    {
        $q = trim($q);
        if ($q === '') return ClienteRepository::recientes();
        return ClienteRepository::search($q);
    }
}
