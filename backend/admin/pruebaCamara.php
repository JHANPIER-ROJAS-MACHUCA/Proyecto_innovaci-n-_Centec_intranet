<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="./../public/resource/js/tailwind.js"></script>
</head>

<body>
    <div>
        <select name="listaDeDispositivos" id="listaDeDispositivos"></select>
        <p id="estado"></p>
    </div>
    <div class="absolute inset-0 bg-gray-900 flex justify-center items-center">
        <button class="absolute top-5 right-5 p-0 border-2 w-14 h-14"></button>
        <video muted="muted" id="video" class="mx-auto hidden" style="width: 50%;"></video>

        <button id="boton" class="absolute bottom-10 left-1/2 transform -translate-1/2 border-2 text-white">Tomar foto</button>

        <button class="inline-block absolute bottom-10 left-1/2 transform -translate-x-1/2 w-14 h-14 border-4 border-white"></button>

        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 space-x-3">
            <button class="border-2 border-white bg-green-500 w-14 h-14 rounded-full"></button>
            <button class="border-2 border-white bg-red-500 w-14 h-14 rounded-full"></button>
        </div>
    </div>
    <canvas id="canvas" class="border"></canvas>

    <script>
        // const constraints = {
        //     video: true,
        //     // video: {
        //     //     width: 1280,
        //     //     height: 720
        //     // },
        // };

        // navigator.mediaDevices
        //     .getUserMedia(constraints)
        //     .then((mediaStream) => {
        //         const video = document.querySelector("video");
        //         video.srcObject = mediaStream;
        //         video.onloadedmetadata = () => {
        //             video.play();
        //         };
        //     })
        //     .catch((err) => {
        //         // always check for errors at the end.
        //         console.error(`${err.name}: ${err.message}`);
        //     });

        function tieneSoporteUserMedia() {
            return !!(navigator.getUserMedia || (navigator.mozGetUserMedia || navigator.mediaDevices.getUserMedia) || navigator.webkitGetUserMedia || navigator.msGetUserMedia)
        }

        function _getUserMedia() {
            return (navigator.getUserMedia || (navigator.mozGetUserMedia || navigator.mediaDevices.getUserMedia) || navigator.webkitGetUserMedia || navigator.msGetUserMedia).apply(navigator, arguments);
        }

        // Declaramos elementos del DOM
        const $video = document.querySelector("video"),
            $canvas = document.querySelector("#canvas"),
            $boton = document.querySelector("#boton"),
            $estado = document.querySelector("#estado"),
            $listaDeDispositivos = document.querySelector("#listaDeDispositivos");

        $canvas.onmousemove = (event) => {
            console.log(event);
        }

        // La función que es llamada después de que ya se dieron los permisos
        // Lo que hace es llenar el select con los dispositivos obtenidos
        const llenarSelectConDispositivosDisponibles = () => {

            navigator
                .mediaDevices
                .enumerateDevices()
                .then(function(dispositivos) {
                    const dispositivosDeVideo = [];
                    dispositivos.forEach(function(dispositivo) {
                        const tipo = dispositivo.kind;
                        if (tipo === "videoinput") {
                            dispositivosDeVideo.push(dispositivo);
                        }
                    });

                    // Vemos si encontramos algún dispositivo, y en caso de que si, entonces llamamos a la función
                    if (dispositivosDeVideo.length > 0) {
                        // Llenar el select
                        dispositivosDeVideo.forEach(dispositivo => {
                            const option = document.createElement('option');
                            option.value = dispositivo.deviceId;
                            option.text = dispositivo.label;
                            $listaDeDispositivos.appendChild(option);
                            console.log("$listaDeDispositivos => ", $listaDeDispositivos)
                        });
                    }
                });
        }

        (function() {
            // Comenzamos viendo si tiene soporte, si no, nos detenemos
            if (!tieneSoporteUserMedia()) {
                alert("Lo siento. Tu navegador no soporta esta característica");
                $estado.innerHTML = "Parece que tu navegador no soporta esta característica. Intenta actualizarlo.";
                return;
            }
            //Aquí guardaremos el stream globalmente
            let stream;

            // Comenzamos pidiendo los dispositivos
            navigator
                .mediaDevices
                .enumerateDevices()
                .then(function(dispositivos) {
                    // Vamos a filtrarlos y guardar aquí los de vídeo
                    const dispositivosDeVideo = [];

                    // Recorrer y filtrar
                    dispositivos.forEach(function(dispositivo) {
                        const tipo = dispositivo.kind;
                        if (tipo === "videoinput") {
                            dispositivosDeVideo.push(dispositivo);
                        }
                    });

                    // Vemos si encontramos algún dispositivo, y en caso de que si, entonces llamamos a la función
                    // y le pasamos el id de dispositivo
                    if (dispositivosDeVideo.length > 0) {
                        // Mostrar stream con el ID del primer dispositivo, luego el usuario puede cambiar
                        mostrarStream(dispositivosDeVideo[0].deviceId);
                    }
                });

            llenarSelectConDispositivosDisponibles();


            const mostrarStream = idDeDispositivo => {
                _getUserMedia({
                        video: {
                            // Justo aquí indicamos cuál dispositivo usar
                            deviceId: idDeDispositivo,
                        }
                    },
                    function(streamObtenido) {
                        // Aquí ya tenemos permisos, ahora sí llenamos el select,
                        // pues si no, no nos daría el nombre de los dispositivos

                        // Escuchar cuando seleccionen otra opción y entonces llamar a esta función
                        $listaDeDispositivos.onchange = () => {
                            // Detener el stream
                            if (stream) {
                                stream.getTracks().forEach(function(track) {
                                    track.stop();
                                });
                            }
                            // Mostrar el nuevo stream con el dispositivo seleccionado
                            mostrarStream($listaDeDispositivos.value);
                        }

                        // Simple asignación
                        stream = streamObtenido;

                        // Mandamos el stream de la cámara al elemento de vídeo
                        $video.srcObject = stream;
                        $video.play();

                        //Escuchar el click del botón para tomar la foto
                        $boton.addEventListener("click", function() {

                            //Pausar reproducción
                            $video.pause();

                            //Obtener contexto del canvas y dibujar sobre él
                            let contexto = $canvas.getContext("2d");
                            $canvas.width = $video.videoWidth;
                            $canvas.height = $video.videoHeight;
                            contexto.drawImage($video, 0, 0, $canvas.width, $canvas.height);

                            $image = new Image();
                            $image.src = $canvas.toDataURL();
                            document.body.appendChild($image);

                            // let foto = $canvas.toDataURL(); //Esta es la foto, en base 64
                            // $estado.innerHTML = "Enviando foto. Por favor, espera...";
                            // fetch("./guardar_foto.php", {
                            //         method: "POST",
                            //         body: encodeURIComponent(foto),
                            //         headers: {
                            //             "Content-type": "application/x-www-form-urlencoded",
                            //         }
                            //     })
                            //     .then(resultado => {
                            //         // A los datos los decodificamos como texto plano
                            //         return resultado.text()
                            //     })
                            //     .then(nombreDeLaFoto => {
                            //         // nombreDeLaFoto trae el nombre de la imagen que le dio PHP
                            //         console.log("La foto fue enviada correctamente");
                            //         $estado.innerHTML = `Foto guardada con éxito. Puedes verla <a target='_blank' href='./${nombreDeLaFoto}'> aquí</a>`;
                            //     })

                            //Reanudar reproducción
                            $video.play();
                        });
                    },
                    function(error) {
                        console.log("Permiso denegado o error: ", error);
                        $estado.innerHTML = "No se puede acceder a la cámara, o no diste permiso.";
                    });
            }
        })();
    </script>
</body>

</html>