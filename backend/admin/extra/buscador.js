$(function(){

	// Lista de departamentos
	$.post( 'extra/departamentos.php' ).done( function(respuesta)
	{
		$( '#txtdepa' ).html( respuesta );

	});


	// lista de provincias
	$('#txtdepa').change(function()
	{
		var el_depa = $(this).val();

		// Lista de provincias
		$.post( 'extra/provincias.php', { depa: el_depa} ).done( function( respuesta )
		{
			$( '#txtprovi' ).html( respuesta );
    	$( '#txtdis' ).html('<option value="" disabled selected>- -Seleccione- -</option>' );
		})

	});

  //listar distritos

  // lista de provincias
	$('#txtprovi').change(function()
	{
		var la_provi = $(this).val();

		// Lista de provincias
		$.post( 'extra/distritos.php', { prov: la_provi} ).done( function( respuesta )
		{
			$( '#txtdis' ).html( respuesta );
		});
	});

	// Lista de Ciudades
/*	$( '#txtprovi' ).change( function()
	{
		var pais = $(this).children('option:selected').html();
		//alert( 'Lista de Ciudades de ' + pais );
	});
*/
})
