<!--</div> lo borre para el contenido principal-->
</div>
<div class="footer" style="display: flex; justify-content: space-between;">
  <div style="flex: 1 1 0%;"></div>
  <div style="flex: 1 1 auto; text-align: center;">SISTEMA INTEGRAL DE PRESTAMOS Y AHORROS DIARIOS - SIPAD V.2.0</div>
  <div style="flex: 1 1 0%; text-align: right;">
    &copy;Copyright <strong><a target="_blank" href="https://www.centecp.com">CENTECP</strong></a>
  </div>
</div>
</div>
</div>

<!--<script src="../js/jquery-3.1.1.min.js"></script>-->

<script src="../public/resource/js/bootstrap.min.js"></script>
<script src="../public/resource/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="../public/resource/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="../public/resource/js/inspinia.js"></script>
<script src="../public/resource/js/plugins/pace/pace.min.js"></script>
<script src="../public/resource/js/plugins/select2/select2.full.min.js"></script>
<script src="../public/resource/js/plugins/select2/select2.full.min.js"></script>
<!-- profile-->
<!-- <script src="../public/resource/js/plugins/summernote/summernote.min.js"></script>
<script src="../public/resource/js/plugins/datapicker/bootstrap-datepicker.js"></script> -->
<!-- IMG-->
<script src="extra/swetalert.js"></script>
<script src="functiones/esencia.js"> </script>
<script type="text/javascript">
  var interval = null;

  // interval = setInterval(() => {
  //   fetch(`../app/api/logout.php`)
  //     .then(response => response.json())
  //     .then(data => {
  //       console.log(data);
  //       window.location.href = '../';
  //     });
  // }, 60000 * 30);

  // window.addEventListener('click', () => {
  //   clearInterval(interval);
  //   interval = setInterval(() => {
  //     fetch(`../app/api/logout.php`)
  //       .then(response => response.json())
  //       .then(data => {
  //         console.log(data);
  //         window.location.href = '../';
  //       });
  //   }, 60000 * 30);
  // });

  function vl(dato) {
    var resul = $('#' + dato).val();
    return resul;
  }
  //#region verificar datos que se esconden al required

  function valimos(id) {
    var resul = false;
    if (vl(id) != null) {
      resul = true;
    }
    return resul;
  }

  function verifica23(dato) {
    var resul = 1;
    if (vl(dato) != null && vl(dato) != "") {
      resul = 0;
    }
    return resul;
  }
</script>
</body>

</html>


<?php
if (isset($_SESSION['flash'])) {
  unset($_SESSION['flash']);
}
?>