<?php
// Módulo Catalogos — repositorio de empresa singleton tdatos (extraído de
// repositories/CatalogoRepository.php, phase2-modular; idéntico).

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
