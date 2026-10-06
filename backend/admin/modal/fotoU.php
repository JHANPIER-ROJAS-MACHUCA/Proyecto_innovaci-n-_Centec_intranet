<div class="modal fade" id="fotoU<?php echo $dataU['idU']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" align="center">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Foto</h4>
      </div>
      <div class="modal-body">
 <form id="uploadU" action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <input required  name="archivo" type="file" class="file" data-overwrite-initial="false" >
            <input type="hidden" value="<?php echo $dataU['idU']; ?>" name="id">
        </div>
      <div class="modal-footer">
       <button type="submit" class="btn btn-primary">Actualizar</button>

      </div>
      </form>            
      </div>
    </div>
  </div>
</div>
