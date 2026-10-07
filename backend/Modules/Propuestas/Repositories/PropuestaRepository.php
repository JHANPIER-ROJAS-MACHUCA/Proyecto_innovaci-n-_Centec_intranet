<?php
// Módulo Propuestas — repositorio (extraído de repositories/CatalogoRepository.php,
// phase2-modular; idéntico).

class PropuestaRepository
{
    public static function listar()
    {
        return \App\Models\Proposal::with('response')->orderBy('id', 'desc')->limit(100)->get();
    }

    public static function detalle(int $id): ?object
    {
        return \App\Models\Proposal::with('response')->where('id', $id)->first();
    }

    public static function crear(array $data)
    {
        return \App\Models\Proposal::create($data);
    }

    public static function evaluar(int $id, array $data): void
    {
        $allowed = ['amount', 'installment', 'rate', 'fee', 'about_business', 'about_destiny', 'about_experience', 'about_family', 'evaluation', 'guaranty'];
        \App\Models\Proposal::where('id', $id)->update(array_intersect_key($data, array_flip($allowed)));
    }

    public static function eliminar(int $id): void
    {
        \App\Models\Proposal::where('id', $id)->delete();
    }

    public static function responder(array $data)
    {
        return \App\Models\ProposalResponse::create($data);
    }
}
