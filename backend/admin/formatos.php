<?php

use CrediSoporte\Domain\Models\Attachment;

include('head.php');

$attachments = Attachment::where(function ($query) use ($request) {
    if ($request->get('search')) {
        $query->where('description', 'like', '%' . $request->get('search') . '%');
    }
})
    ->where(function ($query) use ($request) {
        if (!$request->user()->can('see_all_attachments')) {
            $query->where('status', true);
        }
    })
    ->get();
?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <?php if ($request->user()->can('create_attachment')) { ?>
            <div class="btn-group pull-right">
                <a href="formato.php" class="btn btn-primary" target="_blank"><i class="fa fa-plus-circle"></i> Nuevo formato</a>
            </div>
        <?php } ?>

        <h5 style="color:white">Propuestas<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>
    <div class="panel-body">
        <div style="display: flex; justify-content: space-between;">
            <h3 style="font-weight: bold;">FORMATOS</h3>

            <form action="<?php echo $_SERVER["PHP_SELF"] ?>">
                <div style="display: flex; align-items: center;">
                    <label>Buscar</label>
                    <input class="form-control" type="text" name="search" style="max-width: 150px; margin-left: 10px;" value="<?php echo $request->get('search') ?>">
                    <button type="submit" class="btn btn-primary" style="margin-left: 10px;">BUSCAR</button>
                </div>
            </form>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Fecha de registro</th>
                    <?php if ($request->user()->can('see_all_attachments')) { ?>
                        <th>Fecha de vencimiento</th>
                    <?php } ?>
                    <th>Condición</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attachments as $attachment) { ?>
                    <tr>
                        <td><?php echo $attachment->description ?></td>
                        <td><?php echo $attachment->created_at ?></td>
                        <td><?php echo $attachment->expiration_at ?></td>
                        <?php if ($request->user()->can('see_all_attachments')) { ?>
                            <td><?php echo $attachment->status ? 'ACTIVO' : 'INACTIVO' ?></td>
                        <?php } ?>
                        <td>
                            <a href="<?php echo $attachment->path . $attachment->name ?>" target="_blank" class="btn btn-sm btn-light">Ver archivo</a>

                            <?php if ($request->user()->can('update_attachment')) { ?>
                                <a href="actualizarFormato.php?attachmentId=<?php echo $attachment->id ?>" target="_blank" class="btn btn-sm btn-success">Editar</a>
                            <?php } ?>

                            <?php if ($request->user()->can('delete_attachment')) { ?>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalDelete<?php echo $attachment->id ?>">
                                    Eliminar
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="modalDelete<?php echo $attachment->id ?>" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="modalDeleteLabel">¿Eliminar formato?</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div>¿Seguro que deseas eliminar el fomato?</div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">No, cerrar</button>
                                                <a href="../app/Controllers/DeleteAttachmentController.php?attachmentId=<?php echo $attachment->id ?>" type="button" class="btn btn-primary">Si, eliminar</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include('footer.php');
?>