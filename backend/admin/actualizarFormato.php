<?php

require_once '../vendor/autoload.php';
require_once '../src/Domain/Database/bootstrap.php';

use CrediSoporte\Domain\Models\Attachment;
use CrediSoporte\Domain\Request\Request;
use CrediSoporte\Domain\Session\Session;

session_start();

$request = new Request();
$session = new Session();

if (is_null($request->user()) || !$request->user()->can('update_attachment')) {
    http_response_code(404);
    exit();
}


$attachment = Attachment::find($request->get('attachmentId'))

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar formato</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
</head>

<body>
    <div class="max-w-xl mx-auto px-4">
        <?php if ($session->has('message')) { ?>
            <div class="text-green-500 font-semibold bg-green-50 p-3 border border-green-500 mt-10">
                <?php echo $session->get('message') ?>
            </div>
        <?php } ?>

        <h1 class="text-4xl font-bold text-center mt-10">
            ACTUALIZAR FORMATO
        </h1>

        <form action="../app/Controllers/UpdateAttachmentController.php" method="post" id="form_propuesta">
            <input type="text" name="id" value="<?php echo $attachment->id ?>" hidden>
            <div class="space-y-3 mt-10">
                <div>
                    <label for="">Adjuntar archivo</label>
                    <div class="flex items-center justify-center h-24 border border-dashed border-2 border-sky-500 rounded-md mt-2" id="uploaded" style="<?php echo $attachment->name_original == '' ? 'display: none;' : '' ?>">
                        <div class="overflow-hidden px-5">
                            <div class="font-semibold text-center truncate" id="uploaded_file_name">
                                <?php echo $attachment->name_original ?>
                            </div>
                            <div class="text-center">
                                <button type="button" class="hover:text-red-500" onclick="onDeleteFileUploaded()">ELIMINAR</button>
                            </div>
                        </div>
                    </div>

                    <label class="flex items-center justify-center h-24 border border-dashed border-2 border-sky-500 rounded-md mt-2" id="upload" style="<?php echo $attachment->name_original !== '' ? 'display: none;' : '' ?>">
                        <div>
                            <div class="text-center">SUBE TU ARCHIBO AQUI</div>
                            <div class="text-center text-sm text-gray-500">Tamaño máximo de archivo permitido para cargar: 10 MB</div>
                            <input type="file" id="file" hidden>
                        </div>
                    </label>

                    <div class="flex items-center justify-center h-24 border border-dashed border-2 border-sky-500 rounded-md mt-2" id="uploading" style="display: none;">
                        <div>
                            <div class="text-center text-4xl font-medium" id="status_progress">0%</div>
                            <div class="text-center">Subiendo...</div>
                        </div>
                    </div>

                    <input type="text" name="path" id="input_path" value="<?php echo $attachment->path ?>" hidden>
                    <input type="text" name="name" id="input_name" value="<?php echo $attachment->name ?>" hidden>
                    <input type="text" name="name_original" id="input_name_original" value="<?php echo $attachment->name_original ?>" hidden>
                    <input type="text" name="extension" id="input_extension" value="<?php echo $attachment->extension ?>" hidden>
                    <div id="error_upload" class="text-red-500 text-sm mt-2"></div>
                </div>
                <div>
                    <label for="">Descripcion</label>
                    <textarea class="w-full border-gray-300 rounded-md mt-2" name="description" rows="3"><?php echo $attachment->description ?></textarea>
                </div>
                <div>
                    <label for="">Fecha de vencimiento</label>
                    <input type="date" class="w-full border-gray-300 rounded-md mt-2" name="expiration_at" value="<?php echo $attachment->expiration_at ?>">
                </div>
                <div>
                    <label for="">Condición</label>
                    <div class="mt-2 space-x-4">
                        <label>
                            ACTIVO
                            <input type="radio" name="status" value="true" <?php echo $attachment->status == true ? 'checked' : '' ?>>
                        </label>
                        <label>
                            INACTIVO
                            <input type="radio" name="status" value="false" <?php echo $attachment->status == false ? 'checked' : '' ?>>
                        </label>
                    </div>
                </div>
                <?php if ($session->has('errors') && count($session->get('errors')) > 0) { ?>
                    <ul class="mt-10">
                        <?php foreach ($session->get('errors') as $error) { ?>
                            <li class="text-red-500 font-medium text-sm">- <?php echo $error->getMessage() ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                <div class="text-center mt-10">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-full text-sm" id="button_submit_attachment">Actualizar formato</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.getElementById('file').onchange = (e) => {
            const file = e.target.files[0]
            const sizeByte = file.size;

            document.getElementById('button_submit_attachment').disabled = true;
            document.getElementById('error_upload').innerHTML = "";

            // mayor a 8MB
            if (sizeByte > 8388608) {
                document.getElementById('error_upload').innerHTML = 'El archivo subido supera los 8MB.';
                document.getElementById('button_submit_attachment').disabled = true;
                e.target.value = null;
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            document.getElementById('uploading').style = "display: flex;";
            document.getElementById('upload').style = "display: none;";
            document.getElementById('uploaded').style = "display: none;";

            axios.request({
                    method: 'post',
                    url: '../app/api/uploadFile.php',
                    data: formData,
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    onUploadProgress: p => {
                        document.getElementById('status_progress').innerHTML = Math.round((p.loaded / p.total) * 100) + '%';
                    }
                })
                .then(response => {
                    document.getElementById('uploading').style.setProperty('display', 'none');
                    document.getElementById('uploaded').style.setProperty('display', 'flex');

                    document.getElementById('uploaded_file_name').innerHTML = response.data.data.name_original;

                    document.getElementById('input_path').value = response.data.data.path;
                    document.getElementById('input_name').value = response.data.data.name;
                    document.getElementById('input_name_original').value = response.data.data.name_original;
                    document.getElementById('input_extension').value = response.data.data.extension;
                })
                .catch(error => {
                    document.getElementById('status_progress').innerHTML = 'FAIL';
                    console.log(error);
                })
                .finally(_ => {
                    e.target.value = null;
                    document.getElementById('button_submit_attachment').disabled = false;
                });
        }

        function onDeleteFileUploaded() {
            document.getElementById('input_path').value = "";
            document.getElementById('input_name').value = "";
            document.getElementById('input_name_original').value = "";
            document.getElementById('input_extension').value = "";

            document.getElementById('uploading').style = "display: none;";
            document.getElementById('uploaded').style = "display: none;";
            document.getElementById('upload').style = "display: flex;";
        }
    </script>
</body>

</html>

<?php
unset($_SESSION['flash']);
?>