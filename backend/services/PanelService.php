<?php
require_once __DIR__ . '/../repositories/CatalogoRepository.php';
require_once __DIR__ . '/../Modules/Caja/Repositories/CajaRepository.php';
require_once __DIR__ . '/../repositories/FinancieraRepository.php';
require_once __DIR__ . '/../repositories/UbigeoRepository.php';

class CpanelService
{
    public static function resumen(array $user): array
    {
        $tipo = (int) $user['tipoU'];
        $caja = CajaRepository::habilitada($user);
        $data = array_merge(
            ['caja' => $caja ? array_merge(['habilitada' => true], CajaRepository::resumen($caja)) : ['habilitada' => false, 'efectivo' => 0, 'digital' => 0]],
            array_intersect_key(CpanelRepository::conteos(), array_flip(['clientes', 'creditosActivos', 'creditosPropuestos']))
        );
        $todos = CpanelRepository::conteos();
        if (in_array($tipo, [8, 5, 1], true)) {
            $data['morasPendientes'] = $todos['morasPendientes'];
            $data['extornosPendientes'] = $todos['extornosPendientes'];
            $data['billetajePendiente'] = $todos['billetajePendiente'];
        }
        if ($tipo === 5) {
            $data['usuariosActivos'] = $todos['usuariosActivos'];
            $data['oficinas'] = $todos['oficinas'];
        }
        if (in_array($tipo, [7, 2], true)) {
            $data = array_merge($data, CpanelRepository::carteraPropia($user['idU']));
        }
        if ($tipo === 5) {
            $data['metas'] = $todos['metas'];
            $data['cobrosHoy'] = CpanelRepository::cobrosHoyEquipo();
        }
        return $data;
    }
}

// NOTA phase2: CampoService migró a Modules/Campo/Services/; FormatoService a Modules/Creditos/Services/.


class DniService
{
    public static function consultar(string $number): array
    {
        $number = preg_replace('/\D/', '', $number);
        if (strlen($number) !== 8) throw new DomainException('DNI de 8 dÃ­gitos requerido.');
        $token = $_ENV['DNI_TOKEN'] ?? null;
        if (!$token) throw new DomainException('Servicio DNI no configurado.');
        $ch = curl_init('https://api.apis.net.pe/v1/dni?numero=' . $number);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token]]);
        $raw = curl_exec($ch);
        curl_close($ch);
        $r = json_decode($raw);
        if (!$r || !empty($r->error)) throw new DomainException($r->error ?? 'Sin respuesta.');
        return [
            'document' => $r->numeroDocumento, 'name' => $r->nombres,
            'father_first_surname' => $r->apellidoPaterno, 'mother_first_surname' => $r->apellidoMaterno,
        ];
    }
}

class UbigeoService
{
    public static function departamentos()
    {
        return UbigeoRepository::departamentos();
    }

    public static function provincias(?int $departmentId)
    {
        return UbigeoRepository::provincias($departmentId);
    }

    public static function distritos(?int $provinceId)
    {
        return UbigeoRepository::distritos($provinceId);
    }
}
