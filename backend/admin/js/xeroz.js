function dni(dato, ap, nomb) {
    var dni = $('#' + dato).val();
    if (dni.length == '8') {
        $.ajax({
            method: 'GET',
            url: "https://apiperu.dev/api/dni/" + dni,
            dataType: 'json',
            headers: {
                "Authorization": "Bearer b2170dc7bbee5b60a4e6e23519f10c52e996ada5fbb17907eb29cb1d892accc7"
            }
        }).done(function (response) {
            if (response.success) {
                $('#' + ap).val(response.data.apellido_paterno + " " + response.data.apellido_materno);
                $('#' + nomb).val(response.data.nombres);
            } else {
                $('#' + ap).val("");
                $('#' + nomb).val("");
            }
        });
    }
    else {
        $('#' + ap).val("");
        $('#' + nomb).val("");
    }
}

function dni1(dato, valor) {
    var dni = $('#' + dato).val();
    if (dni.length == '8') {
        $.ajax({
            method: 'GET',
            url: "https://apiperu.dev/api/dni/" + dni,
            dataType: 'json',
            headers: {
                "Authorization": "Bearer b2170dc7bbee5b60a4e6e23519f10c52e996ada5fbb17907eb29cb1d892accc7"
            }
        }).done(function (response) {
            if (response.success) {
                $('#' + valor).val(response.data.apellido_paterno + " " + response.data.apellido_materno + " " + response.data.nombres);
            } else {
                $('#' + valor).val("");
            }
        });
    }
    else {
        $('#' + valor).val("");
    }
}
async function dni2(dato, ap, am, nomb) {
    var dni = $('#' + dato).val();
    if (dni.length == '8') {
        try {
            const token = window.APISPERU_TOKEN || '';
            const con = await fetch(`https://dniruc.apisperu.com/api/v1/dni/${dni}?token=${token}`);
            const apiDni = await con.json();
            $('#' + ap).val(apiDni.apellidoPaterno);
            $('#' + am).val(apiDni.apellidoMaterno);
            $('#' + nomb).val(apiDni.nombres);
        } catch (error) {
            console.log(error);
        }
        //  $.ajax({
        //      method: 'GET',
        //      url: "https://api.apis.net.pe/v1/dni?numero=" + dni,
        //      dataType: 'json',
        //     //  headers: {
        //     //      "Authorization":"Bearer b2170dc7bbee5b60a4e6e23519f10c52e996ada5fbb17907eb29cb1d892accc7"
        //     //  }
        //  }).done(function (response) {
        //      console.log(response);
        //     //  if (response.success) {
        //     //      $('#'+ap).val(response.data.apellido_paterno);
        //     //      $('#'+am).val(response.data.apellido_materno);
        //     //      $('#'+nomb).val(response.data.nombres);
        //     //  }else{
        //     //      $('#'+ap).val("");
        //     //      $('#'+am).val("");
        //     //      $('#'+nomb).val("");
        //     //  }
        //  });
    }
    else {
        $('#' + ap).val("");
        $('#' + am).val("");
        $('#' + nomb).val("");
    }
}
async function consultaConyuge($dato, ap, am, nom, sex, fecha) {
    let dni = $("#" + $dato).val();
    if (dni.length === 8) {
        try {
            const con = await fetch('./api/searchCustomer.php?dni=' + dni);
            const client = await con.json();

            $('#' + ap).val(client.apellidoPaterno);
            $('#' + am).val(client.apellidoMaterno);
            $('#' + nom).val(client.nombres);
            $('#' + sex).val(client.sexo);
            $('#' + fecha).val(client.fechaNacimiento);
        } catch (error) {
            console.log(error);
        }
    }
}
async function consultaAval($dato, ap, am, nom,direc,cel,ocupacion) {
    let dni = $("#" + $dato).val();
    if (dni.length === 8) {
        try {
            const con = await fetch('./api/searchCustomer.php?dni=' + dni);
            const client = await con.json();

            $('#' + ap).val(client.apellidoPaterno);
            $('#' + am).val(client.apellidoMaterno);
            $('#' + nom).val(client.nombres);
            $('#' + direc).val(client.direc);
            $('#' + cel).val(client.cel);
            $('#' + ocupacion).val(client.rubro);
        } catch (error) {
            console.log(error);
        }
    }
}
