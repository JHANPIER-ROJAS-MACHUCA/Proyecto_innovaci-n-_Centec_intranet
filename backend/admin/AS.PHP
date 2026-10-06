<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INSPINIA | Advanced Form Elements</title>

    <link href="../css/plugins/switchery/switchery.css" rel="stylesheet">



</head>

<body>


                    <div class="ibox float-e-margins">

                        <div class="ibox-content">
                            <input type="checkbox" class="js-switch" checked />
                            <input type="checkbox" class="js-switch_2" checked />
                            <br/>
                            <br/>
                            <input type="checkbox" class="js-switch_3"  />
                            <input type="checkbox" class="js-switch_4" checked />
                        </div>
                    </div>
<div class="col-md-12">
  <?php
  if("2019-02-03"<"2019-03-05")
  {
    echo "hola";
  }
  else {
    echo "error";
  }
   ?>
</div>


    <!-- Mainly scripts -->
    <script src="../js/jquery-3.1.1.min.js"></script>
   <!-- Data picker -->
   <script src="../js/plugins/datapicker/bootstrap-datepicker.js"></script>
   <!-- Switchery -->
   <script src="../js/plugins/switchery/switchery.js"></script>
    <!-- iCheck -->
    <script src="../js/plugins/iCheck/icheck.min.js"></script>
    <!-- Image cropper -->
    <script src="../js/plugins/cropper/cropper.min.js"></script>
    <!-- Tags Input -->
    <script src="../js/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>

    <script>
        $(document).ready(function(){


            var elem = document.querySelector('.js-switch');
            var switchery = new Switchery(elem, { color: '#1AB394' });

            var elem_2 = document.querySelector('.js-switch_2');
            var switchery_2 = new Switchery(elem_2, { color: '#ED5565' });

            var elem_3 = document.querySelector('.js-switch_3');
            var switchery_3 = new Switchery(elem_3, { color: '#1AB394' });

            var elem_4 = document.querySelector('.js-switch_4');
            var switchery_4 = new Switchery(elem_4, { color: '#f8ac59' });
              //  switchery_4.disable();


        });


    </script>

</body>

</html>
