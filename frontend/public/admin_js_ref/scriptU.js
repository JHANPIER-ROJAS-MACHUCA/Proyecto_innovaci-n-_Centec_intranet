$(document).ready(function (e) {
	$("#uploadU").on('submit',(function(e) {
		e.preventDefault();
		$("#messageU").empty(); 
		$('#loadingU').show();
		$.ajax({
        	url: "./imgU.php",   	// URL a la que se envía la solicitud
			type: "POST",      				// Tipo de solicitud que se enviará, llamado como método 
			data:  new FormData(this), 		// Datos enviados al servidor 
			contentType: false,       		// El tipo de contenido utilizado al enviar datos al servidor. El valor predeterminado es: "application / x-www-form-urlencoded"
    	    cache: false,					// Para no poder solicitar que las páginas se almacenen en caché
			processData:false,  			// Para enviar DOMDocument o archivo de datos no procesados, se establece en falso (es decir, los datos no deben estar en forma de cadena)
			success: function(data)  		// Una función a ser llamada si la solicitud tiene éxito
		    {
			$('#loadingU').hide();
			$("#messageU").html(data);			
		    }	        
	   });
		setInterval("actualizar()",600);
	}));

// Función para previsualizar la image

});
