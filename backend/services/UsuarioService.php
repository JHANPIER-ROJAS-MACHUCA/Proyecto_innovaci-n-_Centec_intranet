<?php
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../utils/Password.php';

// Roles creables por TI (códigos CENTECPC tabla `rol`).
class UsuarioService
{
    public const ROLES_CREABLES = [1, 2, 3, 5, 7, 8, 9];

    public static function validate(array $in): array
    {
        $errors = [];
        if (empty($in['dniU']) || strlen((string)$in['dniU']) !== 8) $errors['dniU'] = 'El dni debe tener 8 dígitos.';
        elseif (UserRepository::dniExists($in['dniU'])) $errors['dniU'] = 'El dni ya esta en uso.';
        foreach (['apU', 'amU', 'nomU'] as $f) if (empty($in[$f])) $errors[$f] = "El campo {$f} es requerido.";
        if (empty($in['idRol']) || !in_array((int)$in['idRol'], self::ROLES_CREABLES, true)) $errors['idRol'] = 'Rol inválido.';
        return $errors;
    }

    public static function create(array $in): array
    {
        // userU = DNI (login por DNI, como el legacy). idO legacy ya no se usa.
        $user = UserRepository::create([
            'userU' => $in['userU'] ?? $in['dniU'], 'pass' => Password::defaultHash(),
            'idRol' => (int) $in['idRol'], 'esSuperAdmin' => ((int) $in['idRol'] === 1) ? 1 : 0,
            'dniU' => $in['dniU'], 'apU' => $in['apU'], 'amU' => $in['amU'], 'nomU' => $in['nomU'],
            'celU' => $in['celU'] ?? null, 'direcU' => $in['direcU'] ?? null, 'correoU' => $in['correoU'] ?? null,
            'fotoU' => $in['img'] ?? null,
        ]);
        return $user->toArray();
    }

    public static function update(int $idU, array $in): void
    {
        if (!$idU) throw new DomainException('idU requerido.');
        $uCols = ['userU', 'idRol', 'esSuperAdmin', 'idEstado', 'idAsesorAsignado'];
        $dCols = ['dniU', 'nomU', 'apU', 'amU', 'celU', 'direcU', 'correoU', 'fotoU'];
        $u = array_intersect_key($in, array_flip($uCols));
        $d = array_intersect_key($in, array_flip($dCols));
        if ($u) \App\Models\User::where('idU', $idU)->update($u);
        if ($d) \App\Models\Tdatosu::where('idU', $idU)->update($d);
        if (!$u && !$d) throw new DomainException('Nada que actualizar.');
    }

    public static function toggleEstado(int $idU)
    {
        $u = \App\Models\User::where('idU', $idU)->first();
        if (!$u) throw new DomainException('Usuario no existe.');
        // user_status: 1 ACTIVO ↔ 2 INACTIVO
        $u->idEstado = ((int) $u->idEstado === 1) ? 2 : 1;
        $u->save();
        return $u;
    }

    public static function listar()
    {
        global $capsule;
        return $capsule->table('tusuarios as u')->leftJoin('tdatosu as d', 'd.idU', 'u.idU')
            ->leftJoin('rol as r', 'r.id', 'u.idRol')
            ->select('u.idU', 'u.userU', 'u.idRol', 'u.esSuperAdmin', 'u.idEstado', 'u.fecha_creacion', 'r.tipo as rolNombre', 'd.dniU', 'd.nomU', 'd.apU', 'd.amU', 'd.celU', 'd.correoU', 'd.fotoU')
            ->orderBy('u.idU')->get();
    }

    public static function setAvatar(int $idU, string $name): void
    {
        \App\Models\Tdatosu::where('idU', $idU)->update(['fotoU' => $name]);
    }

    public static function updateMiPerfil(int $idU, array $in): void
    {
        $allowed = ['celU', 'direcU', 'correoU'];
        \App\Models\Tdatosu::where('idU', $idU)->update(array_intersect_key($in, array_flip($allowed)));
    }

    public static function cambiarClave(int $idU, string $actual, string $nueva): void
    {
        $u = \App\Models\User::where('idU', $idU)->first();
        if (!$u || !Password::verify($actual, $u->pass)) throw new DomainException('Clave actual incorrecta.');
        if (strlen($nueva) < 6) throw new DomainException('La nueva clave debe tener 6+ caracteres.');
        $u->pass = Password::hash($nueva);
        $u->save();
    }

    public static function eliminarAvatar(int $idU): void
    {
        $d = \App\Models\Tdatosu::where('idU', $idU)->first(['fotoU']);
        if ($d && $d->fotoU) {
            @unlink(dirname(__DIR__) . '/storage/profiles/' . $d->fotoU);
            \App\Models\Tdatosu::where('idU', $idU)->update(['fotoU' => null]);
        }
    }
}
