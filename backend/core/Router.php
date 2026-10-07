<?php
class Router
{
    private array $routes = [];

    public function add(string $method, string $prefix, string $controller, string $action): void
    {
        $this->routes[] = [$method, $prefix, $controller, $action];
    }

    public function dispatch(AppRequest $req): void
    {
        $path = rtrim($req->path, '/');
        if ($path === '') $path = '/';
        // 1) Coincidencia exacta primero (evita que /api/cobros capture /api/cobros/simular)
        foreach ($this->routes as [$method, $prefix, $controller, $action]) {
            if ($req->method === $method && $path === rtrim($prefix, '/')) {
                $controller::$action($req);
                return;
            }
        }
        // 2) Prefijo con límite de segmento (/api/x no captura /api/xyz)
        foreach ($this->routes as [$method, $prefix, $controller, $action]) {
            $p = rtrim($prefix, '/');
            if ($req->method === $method && ($path === $p || strpos($path . '/', $p . '/') === 0)) {
                $controller::$action($req);
                return;
            }
        }
        Response::error('Ruta no encontrada: ' . $req->path, 404);
    }
}
