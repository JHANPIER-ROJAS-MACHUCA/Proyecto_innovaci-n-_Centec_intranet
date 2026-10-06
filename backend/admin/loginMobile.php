<?php

use CrediSoporte\Domain\Request\Request;

require_once '../vendor/autoload.php';
require_once '../src/Domain/Database/bootstrap.php';


$request = new Request();

if ($request->user()) {
    header('Location: ' . './cobroMobile.php');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicia session</title>
    <script src="./../public/resource/js/tailwind.js"></script>
</head>

<body>
    <div class="px-6 py-8 max-w-lg mx-auto" x-data="login">
        <h4 class="text-xl text-center font-semibold">Inicia sessión</h4>

        <form @submit.prevent="login">
            <div class="mt-6">
                <label class="text-sm font-medium">DNI</label>
                <input type="number" class="border border-gray-300 rounded-md px-3 py-2 mt-1 w-full" x-model="auth.usu">
            </div>
            <div class="mt-4">
                <label class="text-sm font-medium">Contraseña</label>
                <input type="password" class="border border-gray-300 rounded-md px-3 py-2 mt-1 w-full" x-model="auth.pas">
            </div>

            <div x-show="error" x-text="error" class="text-red-500 bg-red-50 px-3 py-2 mt-4 text-sm font-medium"></div>

            <button type="submit" class="px-3 py-2 w-full bg-green-500 text-white rounded-md mt-6">
                <span x-show="loading">Cargando...</span>
                <span x-show="!loading">Iniciar sesión</span>
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('login', () => ({
                auth: {
                    usu: '',
                    pas: '',
                },
                error: '',
                loading: false,
                login() {
                    if (this.loading) {
                        return;
                    }

                    this.loading = true;
                    this.error = '';

                    fetch(`./../app/api/login.php`, {
                            method: 'post',
                            body: JSON.stringify(this.auth)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data === 1) {
                                window.location = "./cobroMobile.php"
                            } else {
                                this.error = 'Las credenciales que ingresaste son incorrectas.'
                            }
                        }).finally(_ => this.loading = false);
                }
            }))
        })
    </script>

    <script src="../public/resource/js/alpine.3.10.3.min.js" defer></script>
</body>

</html>