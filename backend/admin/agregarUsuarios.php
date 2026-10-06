<?php

use CrediSoporte\Domain\Models\User;

include('head.php');
if ($_COOKIE['tuser'] != '1' && $_COOKIE['tuser'] != '7') {
  echo "<script>location.href='index.php'</script>";
}

$offices = $database->table('toficina')->get();

$users = User::leftJoin('toficina', 'tusuario.idO', 'toficina.idO')
  ->select(
    'tusuario.idU',
    'tusuario.apU',
    'tusuario.amU',
    'tusuario.nomU',
    'tusuario.dniU',
    'tusuario.celU',
    'tusuario.direcU',
    'tusuario.correoU',
    'tusuario.tipoU',
    'tusuario.img',
    'tusuario.estadoU',
    'tusuario.birthdate',
    'tusuario.idO',
    'toficina.direccion'
  )
  ->get();
?>

<div x-data="users" class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">
      <button class="btn btn-primary" @click="modal_factory = true">
        <i class="fa fa-plus-circle"></i> Nuevo usuario
      </button>
    </div>

    <h5 style="color:white">Reporte de Usuarios <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body">
    <div style="display: flex; justify-content: center; align-items: center;">
      <label style="margin-right: 20px;">Buscar</label>
      <input type="text" class="form-control" x-model="search" placeholder="Buscar por dni o nombre" style="max-width: 350px;">
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));gap: 20px; margin-top: 30px;">
      <template x-for="user in filteredData">
        <div style="display: flex; flex-direction: column;border: 1px solid #e7eaec;">
          <div style="flex: 1 1 auto; padding: 20px;">
            <div style="text-align: center;">
              <template x-if="user.img">
                <img x-bind:src="'./../storage/profiles/' + user.img" width="75" height="75" style="border-radius: 50%;" alt="">
              </template>
              <template x-if="!user.img">
                <div style="width: 75px; height: 75px; border: 1px solid #e7eaec; border-radius: 50%; margin: 0 auto; background: #e7eaec;"></div>
              </template>
            </div>
            <div x-text="user.apU + ' ' + user.amU + ' ' + user.nomU" style="font-weight: 600; font-size: 16px; text-align: center; margin-top: 15px;"></div>
            <div x-text="role(user.tipoU)" style="text-align: center;"></div>
            <div style="margin-top: 20px;">
              <span style="font-weight: bold;">DNI:</span>
              <span x-text="user.dniU"></span>
            </div>
            <div>
              <span style="font-weight: bold;">Celular:</span>
              <span x-text="user.celU"></span>
            </div>
            <div>
              <span style="font-weight: bold;">Correo:</span>
              <span x-text="user.correoU"></span>
            </div>
            <div>
              <span style="font-weight: bold;">Dirección:</span>
              <span x-text="user.direcU"></span>
            </div>
            <div>
              <span style="font-weight: bold;">Oficina:</span>
              <span x-text="user.direccion"></span>
            </div>
          </div>
          <div style="padding: 20px; text-align: center;">
            <button class="btn btn-sm btn-light" @click="restorePassword(user.idU)">Restablecer contraseña</button>
            <button class="btn btn-sm btn-secundary" @click="showUpdateUser(user.idU)">Editar</button>
            <button x-show="user.estadoU == 1" class="btn btn-sm btn-danger" @click="toggleEstadoUsuario(user.idU)">Eliminar</button>
            <button x-show="user.estadoU == 2" class="btn btn-sm btn-info" @click="toggleEstadoUsuario(user.idU)">Activar</button>
          </div>
        </div>
      </template>
    </div>

    <template x-if="modal_factory">
      <div @keyup.escape.window="closeModalFactory" style="display: flex; justify-content: center; align-items: center; position: fixed;inset: 0;background: rgba(0,0,0,0.5); z-index: 3000; padding: 20px;">
        <div style="display: flex; flex-direction: column; max-height: 100%; background: white; width: 100%; max-width: 600px; border-radius: 5px;">
          <div style="padding: 20px;">
            <h4>Registar usuario</h4>
          </div>
          <div style="padding: 0 20px; overflow-y: auto;">

            <div style="text-align: center;">
              <label style="border: 1px solid #D5D8DC; width: 125px; height: 125px; margin: 0 auto; border-radius: 50%; cursor: pointer; overflow: hidden;">
                <template x-if="imageFactory">
                  <img :src="imageFactory" width="100%" height="100%" alt="">
                </template>
                <input type="file" style="display: none;" @change="uploadFileFactory">
              </label>
            </div>

            <div style="flex: 1 1 auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
              <div>
                <label>DNI</label>
                <input type="text" class="form-control" x-model="factory.dniU" @input.debounce.500ms="fetchApiDniFactory">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.dniU" x-text="errors?.dniU"></div>
              </div>
              <div>
                <label>Nombres</label>
                <input type="text" class="form-control" x-model="factory.nomU">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.nomU" x-text="errors?.nomU"></div>
              </div>
              <div>
                <label>Apellido paterno</label>
                <input type="text" class="form-control" x-model="factory.apU">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.apU" x-text="errors?.apU"></div>
              </div>
              <div>
                <label>Apellido materno</label>
                <input type="text" class="form-control" x-model="factory.amU">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.amU" x-text="errors?.amU"></div>
              </div>
              <div>
                <label>Celular</label>
                <input type="tel" class="form-control" x-model="factory.celU">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.celU" x-text="errors?.celU"></div>
              </div>
              <div>
                <label>Correo</label>
                <input type="email" class="form-control" x-model="factory.correoU">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.correoU" x-text="errors?.correoU"></div>
              </div>
              <div>
                <label>Rol</label>
                <select class="form-control" x-model="factory.tipoU">
                  <option value="" selected disabled>Seleccione</option>
                  <option value="1">Gerente</option>
                  <option value="2">Administrador</option>
                  <option value="3">Operador</option>
                  <option value="4">Asesor</option>
                </select>
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.tipoU" x-text="errors?.tipoU"></div>
              </div>
              <div>
                <label>Oficina</label>
                <select class="form-control" x-model="factory.idO">
                  <option value="" selected disabled>Seleccione</option>
                  <?php foreach ($offices as $office) { ?>
                    <option value="<?php echo $office->idO ?>"><?php echo $office->direccion ?></option>
                  <?php } ?>
                </select>
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.idO" x-text="errors?.idO"></div>
              </div>
              <div>
                <label>Fecha de nacimiento</label>
                <input type="date" class="form-control" x-model="factory.birthdate">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.birthdate" x-text="errors?.birthdate"></div>
              </div>
            </div>

            <div style="margin-top: 20px;">
              <label>Dirección</label>
              <textarea class="form-control" x-model="factory.direcU"></textarea>
              <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.direcU" x-text="errors?.direcU"></div>
            </div>

          </div>
          <div style="padding: 20px; text-align: right;">
            <button class="btn btn-secundary" style="margin-right: 10px;" @click="closeModalFactory">Cancelar</button>
            <button class="btn btn-primary" @click="handleSubmitCreate">Guardar usuario</button>
          </div>
        </div>
      </div>
    </template>

    <template x-if="update_user">
      <div @keyup.escape.window="closeModalUpdateUser" style="display: flex; justify-content: center; align-items: center; position: fixed;inset: 0;background: rgba(0,0,0,0.5); z-index: 3000; padding: 20px;">
        <div style="display: flex; flex-direction: column; max-height: 100%; background: white; width: 100%; max-width: 600px; border-radius: 5px;">
          <div style="padding: 20px;">
            <h4>Registar usuario</h4>
          </div>
          <div style="padding: 0 20px; overflow-y: auto;">

            <div style="text-align: center;">
              <label style="border: 1px solid #D5D8DC; width: 125px; height: 125px; margin: 0 auto; border-radius: 50%; cursor: pointer; overflow: hidden;">
                <template x-if="imageUserUpdate">
                  <img :src="imageUserUpdate" width="100%" height="100%" alt="">
                </template>
                <input type="file" style="display: none;" @change="uploadFileFactory">
              </label>
            </div>

            <div style="flex: 1 1 auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
              <div>
                <label>DNI</label>
                <input type="text" class="form-control" x-model="update_user.dniU" @input.debounce.500ms="fetchApiDniUpdateUser">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.dniU" x-text="errors?.dniU"></div>
              </div>
              <div>
                <label>Nombres</label>
                <input type="text" class="form-control" x-model="update_user.nomU">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.nomU" x-text="errors?.nomU"></div>
              </div>
              <div>
                <label>Apellido paterno</label>
                <input type="text" class="form-control" x-model="update_user.apU">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.apU" x-text="errors?.apU"></div>
              </div>
              <div>
                <label>Apellido materno</label>
                <input type="text" class="form-control" x-model="update_user.amU">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.amU" x-text="errors?.amU"></div>
              </div>
              <div>
                <label>Celular</label>
                <input type="tel" class="form-control" x-model="update_user.celU">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.celU" x-text="errors?.celU"></div>
              </div>
              <div>
                <label>Correo</label>
                <input type="email" class="form-control" x-model="update_user.correoU">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.correoU" x-text="errors?.correoU"></div>
              </div>
              <div>
                <label>Rol</label>
                <select class="form-control" x-model="update_user.tipoU">
                  <option value="">Seleccione</option>
                  <option value="1">Gerente</option>
                  <option value="2">Administrador</option>
                  <option value="3">Operador</option>
                  <option value="4">Asesor</option>
                </select>
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.tipoU" x-text="errors?.tipoU"></div>
              </div>
              <div>
                <label>Oficina</label>
                <select class="form-control" x-model="update_user.idO">
                  <option value="">Seleccione</option>
                  <?php foreach ($offices as $office) { ?>
                    <option value="<?php echo $office->idO ?>"><?php echo $office->direccion ?></option>
                  <?php } ?>
                </select>
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.idO" x-text="errors?.idO"></div>
              </div>
              <div>
                <label>Estado</label>
                <select class="form-control" x-model="update_user.estadoU">
                  <option value="1">Activo</option>
                  <option value="2">Inactivo</option>
                </select>
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.tipoU" x-text="errors?.tipoU"></div>
              </div>
              <div>
                <label>Fecha de nacimiento</label>
                <input type="date" class="form-control" x-model="update_user.birthdate">
                <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.birthdate" x-text="errors?.birthdate"></div>
              </div>
            </div>

            <div style="margin-top: 20px;">
              <label>Dirección</label>
              <textarea class="form-control" x-model="update_user.direcU"></textarea>
              <div style="color: rgb(236, 112, 99); margin-top: 5px;" x-show="errors?.direcU" x-text="errors?.direcU"></div>
            </div>

          </div>
          <div style="padding: 20px; text-align: right;">
            <button class="btn btn-secundary" style="margin-right: 10px;" @click="closeModalUpdateUser">Cancelar</button>
            <button class="btn btn-primary" @click="handleSubmitUpdate">Guardar usuario</button>
          </div>
        </div>
      </div>
    </template>

    <div id="listarU">

    </div>
  </div>

  <script>
    const factoryDefault = {
      dniU: '',
      apU: '',
      amU: '',
      nomU: '',
      celU: '',
      direcU: '',
      correoU: '',
      tipoU: '',
      img: '',
      idO: '',
      birthdate: ''
    }

    document.addEventListener('alpine:init', () => {
      Alpine.data('users', () => ({
        data: <?php echo $users; ?>,
        factory: factoryDefault,
        upload: null,
        errors: null,
        modal_factory: false,
        modal_update: false,
        update_user: null,
        search: '',
        get filteredData() {
          const searchValue = this.search.trim();
          if (!searchValue) {
            return this.data;
          }

          return this.data.filter(i => {
            const sss = searchValue.toLowerCase().split(' ');
            var nombre = i.apU + ' ' + i.amU + ' ' + i.nomU;
            nombre = nombre.toLowerCase();

            var encontrado = true;
            sss.forEach(value => {
              if (encontrado) {
                encontrado = nombre.includes(value);
              };
            });
            return encontrado || i.dniU.startsWith(searchValue);
          });
        },
        get imageFactory() {
          if (this.upload) {
            return this.upload.path + this.upload.name;
          } else if (this.factory.img) {
            return '../storage/profiles/' + this.factory.img;
          } else {
            return '';
          }
        },
        get imageUserUpdate() {
          if (!this.update_user) {
            return ''
          }

          if (this.upload) {
            return this.upload.path + this.upload.name
          } else if (this.update_user.img) {
            return '../storage/profiles/' + this.update_user.img;
          } else {
            return ''
          }
        },
        showUpdateUser(userId) {
          const user = this.data.find(item => item.idU == userId);
          if (user) this.update_user = {
            ...user
          };
        },
        fetchApiDniFactory() {
          searchDni(this.factory.dniU, (data) => {
            this.factory.apU = data.father_first_surname;
            this.factory.amU = data.mother_first_surname;
            this.factory.nomU = data.name;
          });
        },
        fetchApiDniUpdateUser() {
          if (!this.update_user) {
            return;
          }

          searchDni(this.update_user.dniU, (data) => {
            this.update_user.apU = data.father_first_surname;
            this.update_user.amU = data.mother_first_surname;
            this.update_user.nomU = data.name;
          });
        },
        handleSubmitCreate() {
          this.errors = null;
          fetch('./../app/api/registrarUsuario.php', {
              method: 'post',
              body: JSON.stringify({
                ...this.factory,
                img: this.upload ? this.upload.name : ''
              }),
              headers: {
                'Content-Type': 'application/json',
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                this.data = [...this.data, data.data];
                this.modal_factory = false;
                this.factory = factoryDefault
                swal({
                  title: 'Usuario registrado con exito!!',
                  icon: 'success'
                });
              } else {
                this.errors = data.errors
              }
            });
        },
        uploadFileFactory(e) {
          if (!e.target.files[0]) {
            return;
          }

          const file = e.target.files[0];

          // el tipo tiene que ser imagen
          if (!file.type.startsWith('image/')) {
            swal({
              title: 'El archivo debe ser una imagen.',
              icon: 'error'
            });
            return;
          }

          // maximo de subida 2MB
          if (file.size > 2097152) {
            swal({
              title: 'El archivo no debe ser mayor a 2MB.',
              icon: 'error'
            });
            return;
          }

          const formData = new FormData();
          formData.append('file', e.target.files[0]);
          fetch('./../app/api/uploadFile.php', {
              method: 'post',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              this.upload = data.data
            });

        },
        handleSubmitUpdate() {
          this.errors = null;
          fetch('./../app/api/actualizarUsuario.php', {
              method: 'post',
              body: JSON.stringify({
                ...this.update_user,
                img: this.upload ? this.upload.name : this.update_user.img
              }),
              headers: {
                'Content-Type': 'application/json',
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                this.data = this.data.map(item => {
                  if (item.idU === data.data.idU) {
                    return data.data
                  }

                  return item
                });
                this.update_user = null;
                this.upload = null;
                swal({
                  title: 'Usuario actualizado con exito!!',
                  icon: 'success'
                });
              } else {
                this.errors = data.errors
              }
            });
        },
        closeModalFactory() {
          this.factory = factoryDefault;
          this.modal_factory = false;
          this.errors = null;
          this.upload = null;
        },
        closeModalUpdateUser() {
          this.update_user = null;
          this.errors = null;
          this.upload = null;
        },
        toggleEstadoUsuario(userId) {
          fetch(`./../app/api/toggleEstadoUsuario.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                this.data = this.data.map(item => {
                  if (item.idU == userId) {
                    return {
                      ...item,
                      estadoU: data.estado
                    }
                  }

                  return item;
                })

                swal({
                  title: 'Operación realizado con exito!!',
                  icon: 'success'
                });
              }
            })
        },
        restorePassword(userId) {
          swal({
              title: '¿Seguro que desea restablecer la contraseña?',
              text: 'Se restablecera la contraseña a "123456".',
              icon: 'info',
              buttons: ['Cancelar', 'Si, restablecer contaseña']
            })
            .then(option => {
              if (option) {
                fetch(`../app/api/restorePasswordUser.php?user_id=${userId}`)
                  .then(response => response.json())
                  .then(data => {
                    if (data.success) {
                      swal({
                        title: 'Contraseña restablecida.',
                        icon: 'success'
                      })
                    } else {
                      swal({
                        title: 'Lo sentimos, no se pudo restablecer la contraseña.',
                        icon: 'error'
                      })
                    }
                  });
              }
            })
        }
      }));
    })

    function searchDni(dni, callback) {
      fetch(`../app/api/queryDni.php?number=${dni}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            callback(data.data)
          }
        });
    }

    function role(value) {
      switch (value) {
        case '1':
          return 'Gerente';
          break;
        case '2':
          return 'Administrador';
          break;
        case '3':
          return 'Operador'
          break;
        case '4':
          return 'Asesor'
          break;

        default:
          return ''
      }
    }
  </script>

  <script src="../public/resource/js/alpine.3.10.3.min.js" defer></script>

  <?php include('footer.php'); ?>