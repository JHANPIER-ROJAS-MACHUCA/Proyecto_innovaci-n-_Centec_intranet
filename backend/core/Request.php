<?php
class AppRequest
{
    public array $body;
    public array $query;
    public string $method;
    public string $path;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path = self::normalizePath();
        $json = json_decode(file_get_contents('php://input'), true);
        $this->body = is_array($json) ? $json : $_REQUEST;
        $this->query = $_GET;
    }

    private static function normalizePath(): string
    {
        // 1) Fallback explícito ?route=/api/...
        if (!empty($_GET['route'])) {
            return self::clean('/' . ltrim($_GET['route'], '/'));
        }
        // 2) PATH_INFO (URLs tipo index.php/api/... con AcceptPathInfo)
        if (!empty($_SERVER['PATH_INFO'])) {
            return self::clean($_SERVER['PATH_INFO']);
        }
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $uri = urldecode($uri);
        $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        // 3) Quitar el script completo: /base/public/index.php/api/x -> /api/x
        if ($script !== '' && $script !== '/' && strpos($uri, $script) === 0) {
            $rest = substr($uri, strlen($script));
            return self::clean($rest === '' ? '/' : $rest);
        }
        // 4) Quitar solo el directorio base (rewrite .htaccess): /base/public/api/x -> /api/x
        $dir = rtrim(str_replace('\\', '/', (string) dirname($script)), '/');
        if ($dir !== '' && $dir !== '/' && $dir !== '.' && strpos($uri, $dir . '/') === 0) {
            $rest = substr($uri, strlen($dir));
            // Solo recortar si lo que queda es la API o la raíz (no romper otras apps)
            if ($rest === '' || $rest === '/' || strpos($rest, '/api') === 0) {
                return self::clean($rest === '' ? '/' : $rest);
            }
        }
        return self::clean($uri);
    }

    private static function clean(string $p): string
    {
        if ($p === '' || $p[0] !== '/') $p = '/' . $p;
        if (strlen($p) > 1) $p = rtrim($p, '/');
        return $p;
    }

    public function input(string $key, $default = null)
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }
}
