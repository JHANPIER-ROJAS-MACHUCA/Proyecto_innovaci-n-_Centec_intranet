<?php include('head.php') ?>

<div class="row" x-data="profile">
    <div class="col-lg-12">
        <div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
            <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
                USUARIO MODIFICAR IMAGEN
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-4">

                        <div class="form-group">
                            <label>Dni :</label>
                            <input readonly tabindex="1" required name="txtdni" type="text" class="form-control" placeholder="Ingrese Dni" value="<?php echo $request->user()->dniU ?>">
                        </div>

                    </div>
                    <div class="col-lg-4">
                        <div style="border: 1px solid #ABB2B9; width: 200px; height: 200px; border-radius: 5px; overflow: hidden;">
                            <img x-show="image?.path" :src="image.path + image.name" style="width: 100%; height: 100%; object-fit: cover;" target="Perfil usuario" />
                            <img x-show="image === null && user.img" :src="'../storage/profiles/' + user.img " style="width: 100%; height: 100%; object-fit: cover;" />
                        </div>
                        <div x-show="image !== null" style="margin-top: 10px;">
                            <button class="btn btn-sm btn-primary" @click="updateUserAvatar">Actualizar perfil</button>
                            <button class="btn btn-sm btn-danger" @click="image = null">Cancelar</button>
                        </div>
                        <div x-show="image === null" style="margin-top: 10px;">
                            <label class="btn btn-sm btn-primary">
                                Seleccionar imagen
                                <input type="file" style="display: none;" @change="handleChangeImage" />
                            </label>
                            <button class="btn btn-sm btn-danger" x-show="user.img" @click="deleteUserAvatar">Eliminar</button>
                        </div>
                    </div>
                    <!-- /.col-lg-6 (nested) -->
                    <div class="col-lg-4">

                        <div class="form-group">
                            <label></label>
                            <input readonly tabindex="6" name="txtidusuario" type="hidden" class="form-control" value="<?php echo $request->user()->idU ?>">
                        </div>
                    </div>
                    <!-- /.col-lg-6 (nested) -->
                </div>
            </div>
            <!-- /.panel-body -->
            <div class="panel-footer">

            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('profile', () => ({
            user: <?php echo $request->user() ?>,
            uploading: false,
            image: null,
            handleChangeImage(event) {
                if (this.uploading) return

                const file = event.target.files[0]
                const sizeByte = file.size;

                // el tipo tiene que ser imagen
                if (!file.type.startsWith('image/')) {
                    swal({
                        title: 'El archivo debe ser una imagen.',
                        icon: 'error'
                    });
                    return;
                }

                // mayor a 2MB
                if (sizeByte > 2097152) {
                    event.target.value = null;

                    swal({
                        title: 'Solo se permite 2MB como maximo en tamaño.',
                        text: 'Puedes comprimir tu foto de perfil en https://tinypng.com',
                        icon: 'info'
                    })
                    return;
                }

                this.uploading = true

                const formData = new FormData()
                formData.append('file', event.target.files[0])

                fetch(`../app/api/uploadFile.php`, {
                        method: 'post',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.image = data.data
                        }
                    })
                    .finally(_ => {
                        event.target.value = null
                        this.uploading = false
                    })
            },
            updateUserAvatar() {
                if (this.uploading) return;

                this.uploading = true;

                if (this.image === null) return;

                fetch(`./../app/api/updateUserAvatar.php`, {
                        method: 'post',
                        body: JSON.stringify({
                            user_id: this.user.idU,
                            name: this.image.name
                        }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.user.img = this.image.name;
                            this.image = null;
                            swal({
                                title: 'Se actualizo tu foto de perfil.',
                                icon: 'success'
                            });
                        }
                    })
                    .finally(_ => this.uploading = false);
            },
            deleteUserAvatar() {
                if (this.deleting) return;

                if (!confirm('¿Seguro que desea eliminar su foto de perfil?')) return;

                this.deleting = true;

                fetch(`./../app/api/deleteUserAvatar.php?user_id=${this.user.idU}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.user.img = null;
                            swal({
                                title: 'Foto de perfil eliminado con exito!!',
                                icon: 'success'
                            });
                        } else {
                            swal({
                                title: 'Lo sentimos, no se pudo eliminar tu foto de perfil.',
                                icon: 'error'
                            });
                        }
                    })
                    .finally(_ => this.deleting = false);
            }
        }))
    })
</script>
<script src="./../public/resource/js/alpine.3.10.3.min.js"></script>
<?php include('footer.php') ?>