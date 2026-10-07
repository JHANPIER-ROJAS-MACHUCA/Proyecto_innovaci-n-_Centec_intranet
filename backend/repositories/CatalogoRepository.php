<?php
// NOTA phase2: ReporteRepository migró a Modules/Reportes/Repositories/.

class OficinaRepository
{
    public static function listar()
    {
        global $capsule;
        return $capsule->table('toficina')->get();
    }
}

class EmpresaRepository
{
    public static function ver(): ?object
    {
        global $capsule;
        return $capsule->table('tdatos')->first();
    }

    public static function actualizar(array $data): void
    {
        global $capsule;
        $allowed = ['logo', 'titulo', 'nombreEmpresa', 'siglas', 'subnombre', 'comentario', 'color', 'abre', 'ruc', 'representante', 'dnir', 'direccion', 'partida'];
        $capsule->table('tdatos')->where('id', 1)->update(array_intersect_key($data, array_flip($allowed)));
    }
}

class RelationRepository
{
    public static function listar(int $idCG)
    {
        return \App\Models\Relation::where('customer_id', $idCG)->get();
    }

    public static function vinculacionCompleta($idV): ?object
    {
        global $capsule;
        return $capsule->table('tvinculacion')
            ->leftJoin('tclie_general as aval', 'tvinculacion.aval', 'aval.idCG')
            ->leftJoin('tclie_general as conyuge', 'tvinculacion.conyugue', 'conyuge.idCG')
            ->where('idV', $idV)
            ->select(
                'conyuge.ap as conyuge_ap',
                'conyuge.am as conyuge_am',
                'conyuge.nom as conyuge_nom',
                'conyuge.dni as conyuge_dni',
                'aval.ap as aval_ap',
                'aval.am as aval_am',
                'aval.nom as aval_nom',
                'aval.dni as aval_dni'
            )->first();
    }

    public static function crear(array $data)
    {
        return \App\Models\Relation::create($data);
    }

    public static function eliminar(int $id): void
    {
        \App\Models\Relation::where('id', $id)->delete();
    }
}

class AdjuntoRepository
{
    public static function listar(int $customerId)
    {
        return \App\Models\CustomerAttachment::where('customer_id', $customerId)->get();
    }

    public static function crear(array $data)
    {
        return \App\Models\CustomerAttachment::create($data);
    }

    public static function eliminar(int $id): void
    {
        \App\Models\CustomerAttachment::where('id', $id)->delete();
    }
}

class JustificacionRepository
{
    public static function listar(?int $creditId)
    {
        $q = \App\Models\Comment::orderBy('id', 'desc')->limit(100);
        if ($creditId) $q->where('credit_id', $creditId);
        return $q->get();
    }

    public static function crear(array $data)
    {
        return \App\Models\Comment::create($data);
    }

    public static function actualizar(int $id, array $data)
    {
        return \App\Models\Comment::where('id', $id)->update($data);
    }

    public static function eliminar(int $id): void
    {
        \App\Models\Comment::where('id', $id)->delete();
    }
}

// NOTA phase2: PropuestaRepository migró a Modules/Propuestas/Repositories/.

class MetaRepository
{
    public static function listar()
    {
        return \App\Models\Goal::orderBy('id', 'desc')->limit(100)->get();
    }

    public static function porUsuario()
    {
        global $capsule;
        return $capsule->table('goals as g')->join('tusuarios as u', 'g.user_id', 'u.idU')
            ->leftJoin('tdatosu as d', 'd.idU', 'u.idU')
            ->select('g.*', 'd.dniU')->orderBy('g.id', 'desc')->limit(100)->get();
    }

    public static function crear(array $data)
    {
        return \App\Models\Goal::create($data);
    }

    public static function actualizar(int $id, array $data)
    {
        return \App\Models\Goal::where('id', $id)->update($data);
    }

    public static function eliminar(int $id): void
    {
        \App\Models\Goal::where('id', $id)->delete();
    }
}

class CpanelRepository
{
    public static function conteos(): array
    {
        global $capsule;
        return [
            'clientes' => (int) $capsule->table('tclie_general')->count(),
            'creditosActivos' => (int) $capsule->table('tprestamo')->where('estado', 4)->count(),
            'creditosPropuestos' => (int) $capsule->table('tprestamo')->where('estado', 1)->count(),
            'morasPendientes' => (int) $capsule->table('tpresta_detalle as d')
                ->join('tprestamo as p', 'd.idP', 'p.idP')
                ->where('p.estado', 4)->where('d.estado', '!=', 1)->where('d.fechaProg', '<', date('Y-m-d'))->count(),
            'extornosPendientes' => (int) $capsule->table('textorno')->count(),
            'billetajePendiente' => (int) $capsule->table('tbilletaje')->where('estado', '2')->count(),
            'usuariosActivos' => (int) $capsule->table('tusuarios')->where('idEstado', 1)->count(),
            'oficinas' => (int) $capsule->table('toficina')->count(),
            'metas' => (int) $capsule->table('goals')->count(),
        ];
    }

    public static function carteraPropia($idU): array
    {
        global $capsule;
        return [
            'misClientes' => (int) $capsule->table('tclie_general')->where('idU', $idU)->count(),
            'misCreditos' => (int) $capsule->table('tprestamo as p')
                ->join('tclie_general as c', 'p.idCG', 'c.idCG')
                ->where('p.estado', 4)->where('c.idU', $idU)->count(),
            'misCobrosHoy' => (int) $capsule->table('tpresta_detalle as d')
                ->join('tprestamo as p', 'd.idP', 'p.idP')
                ->join('tclie_general as c', 'p.idCG', 'c.idCG')
                ->where('p.estado', 4)->where('d.expiration_at', date('Y-m-d'))->where('c.idU', $idU)->count(),
        ];
    }

    public static function cobrosHoyEquipo(): int
    {
        global $capsule;
        return (int) $capsule->table('tpresta_detalle as d')
            ->join('tprestamo as p', 'd.idP', 'p.idP')
            ->where('p.estado', 4)->where('d.expiration_at', date('Y-m-d'))->count();
    }
}
