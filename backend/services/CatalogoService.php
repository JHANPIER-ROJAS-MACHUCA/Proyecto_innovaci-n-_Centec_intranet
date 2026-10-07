<?php
require_once __DIR__ . '/../repositories/CatalogoRepository.php';

class AdjuntoService
{
    public static function listar(int $customerId)
    {
        return AdjuntoRepository::listar($customerId);
    }

    public static function crear(array $in)
    {
        return AdjuntoRepository::crear($in)->toArray();
    }

    public static function eliminar(int $id): void
    {
        AdjuntoRepository::eliminar($id);
    }

    public static function guardarArchivo(array $file): string
    {
        $name = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', basename($file['name']));
        $dest = dirname(__DIR__) . '/storage/uploads/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) throw new DomainException('No se pudo guardar.');
        return $name;
    }
}

class JustificacionService
{
    public static function listar(?int $creditId)
    {
        return JustificacionRepository::listar($creditId);
    }

    public static function crear(array $in)
    {
        return JustificacionRepository::crear($in)->toArray();
    }

    public static function actualizar(int $id, array $in)
    {
        return JustificacionRepository::actualizar($id, $in);
    }

    public static function eliminar(int $id): void
    {
        JustificacionRepository::eliminar($id);
    }
}

// NOTA phase2: PropuestaService migró a Modules/Propuestas/Services/.

class MetaService
{
    public static function listar()
    {
        return MetaRepository::listar();
    }

    public static function porUsuario()
    {
        return MetaRepository::porUsuario();
    }

    public static function crear(array $in)
    {
        return MetaRepository::crear($in)->toArray();
    }

    public static function actualizar(int $id, array $in)
    {
        return MetaRepository::actualizar($id, $in);
    }

    public static function eliminar(int $id): void
    {
        MetaRepository::eliminar($id);
    }
}

class RelacionService
{
    public static function listar(int $idCG): array
    {
        $out = [];
        foreach (RelationRepository::listar($idCG) as $r) {
            $nombre = '';
            $doc = '';
            if ($r->relationable_type === 'App\\Models\\Customer') {
                $c = \App\Models\Customer::where('idCG', $r->relationable_id)->first();
                if ($c) {
                    $nombre = trim("{$c->ap} {$c->am} {$c->nom}");
                    $doc = (string) $c->dni;
                }
            } else {
                $cr = \App\Models\Credit::where('idP', $r->relationable_id)->first();
                if ($cr) {
                    $nombre = "Crédito #{$cr->idP}";
                    $doc = (string) ($cr->montoPropuesto ?? '');
                }
            }
            $out[] = ['id' => $r->id, 'type' => $r->type, 'tipo' => \App\Models\Relation::typeToString($r->type), 'nombre' => $nombre, 'doc' => $doc];
        }
        return $out;
    }

    public static function agregar(array $in)
    {
        foreach (['customer_id', 'type', 'dni'] as $f) {
            if (empty($in[$f])) throw new DomainException("$f requerido.");
        }
        $c = \App\Models\Customer::where('dni', $in['dni'])->first(['idCG']);
        if (!$c) throw new DomainException('Relacionado no existe.');
        return RelationRepository::crear([
            'type' => $in['type'], 'customer_id' => (int) $in['customer_id'],
            'relationable_id' => $c->idCG, 'relationable_type' => 'App\\Models\\Customer',
        ])->toArray();
    }

    public static function eliminar(int $id): void
    {
        RelationRepository::eliminar($id);
    }
}

class OficinaService
{
    public static function listar()
    {
        return OficinaRepository::listar();
    }
}

class EmpresaService
{
    public static function ver(): ?object
    {
        return EmpresaRepository::ver();
    }

    public static function actualizar(array $in): void
    {
        EmpresaRepository::actualizar($in);
    }
}
