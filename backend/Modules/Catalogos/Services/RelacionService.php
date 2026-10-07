<?php
// Módulo Catalogos — servicio de vinculaciones (extraído de
// services/CatalogoService.php, phase2-modular; idéntico).
require_once __DIR__ . '/../Repositories/RelationRepository.php';

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
