<?php
// Módulo Usuarios — repositorio (movido de repositories/, phase2-modular).
require_once __DIR__ . '/../../../utils/Password.php';

// Usuarios en esquema CENTECPC: tusuarios (credencial) + tdatosu (datos).
// Login por DNI (tdatosu.dniU), como antes, pero contra tablas originales.

class UserRepository
{
    public static function findActiveByDni(string $dni)
    {
        global $capsule;
        $row = $capsule->table('tusuarios as u')->join('tdatosu as d', 'd.idU', 'u.idU')
            ->leftJoin('tusuario as leg', 'leg.idU', 'u.idU') // puente legacy: idO de caja
            ->where('d.dniU', $dni)->where('u.idEstado', 1)
            ->select('u.*', 'd.dniU', 'd.nomU', 'd.apU', 'd.amU', 'd.celU', 'd.direcU', 'd.correoU', 'd.fotoU', 'leg.idO')->first();
        if (!$row) return null;
        return new \App\Models\User((array) $row);
    }

    public static function dniExists(string $dni): bool
    {
        global $capsule;
        return $capsule->table('tdatosu')->where('dniU', $dni)->exists();
    }

    public static function create(array $data)
    {
        global $capsule;
        $conn = $capsule->getConnection();
        $conn->beginTransaction();
        try {
            $user = \App\Models\User::create([
                'userU' => $data['userU'], 'pass' => $data['pass'],
                'idRol' => $data['idRol'], 'esSuperAdmin' => $data['esSuperAdmin'] ?? 0,
                'idEstado' => 1,
            ]);
            \App\Models\Tdatosu::create([
                'idU' => $user->idU, 'dniU' => $data['dniU'],
                'nomU' => $data['nomU'], 'apU' => $data['apU'], 'amU' => $data['amU'],
                'celU' => $data['celU'] ?? '', 'direcU' => $data['direcU'] ?? '',
                'correoU' => $data['correoU'] ?? '', 'fotoU' => $data['fotoU'] ?? null,
            ]);
            $conn->commit();
            return $user;
        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public static function resetPassword(int $idU): void
    {
        $u = \App\Models\User::where('idU', $idU)->first();
        if ($u) { $u->pass = Password::defaultHash(); $u->save(); }
    }
}
