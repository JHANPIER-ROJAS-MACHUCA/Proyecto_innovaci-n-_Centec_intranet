$(document).ready(function (e) {
	$("#uploadimage3").on('submit',(function(e) {
		e.preventDefault();
		$("#message3").empty(); 
		$('#loading3').show();
		$.ajax({
        	url: "ajax_php_file3.php",   	// URL a la que se envía la solicitud
			type: "POST",      				// Tipo de solicitud que se enviará, llamado como método 
			data:  new FormData(this), 		// Datos enviados al servidor 
			contentType: false,       		// El tipo de contenido utilizado al enviar datos al servidor. El valor predeterminado es: "application / x-www-form-urlencoded"
    	    cache: false,					// Para no poder solicitar que las páginas se almacenen en caché
			processData:false,  			// Para enviar DOMDocument o archivo de datos no procesados, se establece en falso (es decir, los datos no deben estar en forma de cadena)
			success: function(data)  		// Una función a ser llamada si la solicitud tiene éxito
		    {
			$('#loading3').hide();
			$("#message3").html(data);			
		    }	        
	   });
	}));

// Función para previsualizar la imagen
	$(function() {
        $("#file3").change(function() {
			$("#message3").empty();         // Para eliminar el mensaje de error anterior
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/png","image/jpg"];	
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
			{
			$('#previewing3').attr('src','img2/croquis/maps2.jpg');
			$("#message3").html("<p id='error'>Selecciona un archivo de imagen válido</p>"+"<h4>Nota</h4>"+"<span id='error_message'>Solo jpeg, jpg y png Tipo de imágenes permitidas</span>");
			return false;
			}
            else
			{
                var reader = new FileReader();	
                reader.onload = imageIsLoaded;
                reader.readAsDataURL(this.files[0]);
            }		
        });
    });
	function imageIsLoaded(e) { 
		$("#file3").css("color","green");
        $('#image_preview3').css("display", "block");
        $('#previewing3').attr('src', e.target.result);
		$('#previewing3').attr('width', '250px');
		$('#previewing3').attr('height', '230px');
	};
});
