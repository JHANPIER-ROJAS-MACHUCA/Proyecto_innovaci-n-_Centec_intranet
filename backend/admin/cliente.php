<?php

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Request\Request;

include('head.php');

$request = new Request();

$customer = Customer::with('riskProfile', 'portfolio', 'attachments', 'relations')
    ->find($request->customerId);

if (!$customer) {
    echo "";
    die();
}

// $creditsActives = Credit::where('idCG', $request->customerId)->where('estado', 4)->get();
$allCredits = Credit::join('credit_types', 'tprestamo.credit_type_id', 'credit_types.id')
    ->where('tprestamo.idCG', $request->customerId)
    // ->whereIn('tprestamo.estado', [4, 5])
    ->orderBy('tprestamo.idP', 'desc')
    ->limit('20')
    ->get();

?>

<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">

    <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
        <div class="btn-group pull-right">
            <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
        </div>
        <h5 style="color:white">Perfil del Cliente<small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
    </div>

    <div class="container" style="padding-top: 40px; padding-bottom: 40px;">
        <div class="row">
            <div class="col-lg-4">
                <div style="width: 250px; height: 250px; border: 1px solid black; border-radius: 50%; overflow: hidden;">
                    <img src="img/<?php echo $customer->sexo === "F" ? "profile2.jpg" : "profile.jpg" ?>" style="width: 100%; height: 100%;" />
                </div>
                <div style="font-weight: bold; font-size: 1.2em; margin-top: 20px;"><?php echo $customer->ap . " " . $customer->am . " " . $customer->nom ?></div>
                <div style="font-size: 1.1em;"><?php echo "DNI: " . $customer->dni ?></div>
                <div style="margin-top: 20px;">
                    <div style="padding-top: 7px; padding-bottom: 7px;">
                        <div style="font-size: 0.9em; margin-bottom: 3px;">Celular</div>
                        <div style="font-weight: 600;"><?php echo $customer->cel ? $customer->cel : "---" ?></div>
                    </div>
                    <div style="padding-top: 7px; padding-bottom: 7px;">
                        <div style="font-size: 0.9em; margin-bottom: 3px;">Correo</div>
                        <div style="font-weight: 600;"><?php echo $customer->correo ? $customer->correo : "---" ?></div>
                    </div>
                    <div style="padding-top: 7px; padding-bottom: 7px;">
                        <div style="font-size: 0.9em; margin-bottom: 3px;">Dirección</div>
                        <div style="font-weight: 600;"><?php echo $customer->direc ? $customer->direc : "---" ?></div>
                    </div>
                    <div style="padding-top: 7px; padding-bottom: 7px;">
                        <div style="font-size: 0.9em; margin-bottom: 3px;">Referencia</div>
                        <div style="font-weight: 600;"><?php echo $customer->referencia ? $customer->referencia : "---" ?></div>
                    </div>
                    <div style="padding-top: 7px; padding-bottom: 7px;">
                        <div style="font-size: 0.9em; margin-bottom: 3px;">Perfil de riesgo</div>
                        <div style="font-weight: 600;"><?php echo $customer->riskProfile ? $customer->riskProfile->description : "---" ?></div>
                    </div>

                    <?php if ($customer->portfolio) { ?>
                        <div style="padding-top: 7px; padding-bottom: 7px;">
                            <div style="font-size: 0.9em; margin-bottom: 3px;">Cartera</div>
                            <div style="font-weight: 600;">
                                <?php echo $customer->portfolio->apU . " " . $customer->portfolio->amU . " " . $customer->portfolio->nomU ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>


                <!-- <div style="margin-top: 10px;">
                    <button class="btn btn-default" style="width: 100%;">Editar</button>
                </div> -->

            </div>
            <div class="col-lg-8">

                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-1">Resumen</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2">Galeria</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-3">Relaciones</a></li>
                </ul>
                <div class="tab-content">
                    <!-- Resumen -->
                    <div id="tab-1" class="tab-pane active">
                        <div style="margin-top: 30px; text-align: right;">
                            <a href="<?php echo $_ENV['SERVER2'] ?>/credits/new?customerId=<?php echo $request->customerId ?>" class="btn btn-info">Nuevo prestamo</a>
                        </div>
                        <div style="overflow-x: auto; margin-top: 10px;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Codigo</th>
                                        <th>Número credito</th>
                                        <th>Tipo de prestamo</th>
                                        <th>Monto aprobado</th>
                                        <th>Tasa</th>
                                        <th>Pago</th>
                                        <th>Cuotas</th>
                                        <th>Mora por día</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allCredits as $key => $credit) { ?>
                                        <tr>
                                            <td><?php echo $credit->idP ?></td>
                                            <td><?php echo $credit->n_credito ?></td>
                                            <td><?php echo $credit->name ?></td>
                                            <td style="text-align: right;">
                                                <?php echo number_format($credit->capital / 10, 2) ?>
                                            </td>
                                            <td><?php echo $credit->interest_rate . "%" ?></td>
                                            <td><?php echo $credit->paymentPeriodToString() ?></td>
                                            <td><?php echo $credit->number_installments ?></td>
                                            <td style="text-align: right;"><?php echo number_format($credit->penalty / 10, 2) ?></td>
                                            <td><?php echo $credit->statusToString() ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Galeria -->
                     <div id="tab-2" class="tab-pane" x-data="gallery">
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 20px; margin-top: 30px;">
                            <label style="height: 150px; border-radius: 10px; overflow: hidden; display: flex; justify-content: center; align-items: center; background: #EAECEE;">
                                Agregar Imagen
                                <input type="file" style="display: none;" @change="uploadImage" />
                            </label>
                            <template x-if="image">
                                <div style="height: 150px; border-radius: 10px; border: 1px solid black; position: relative; display: flex; justify-content: center; align-items: center;">
                                    <img :src="image.path + image.name" style="width: auto; height: auto; max-width: 100%; max-height: 100%;" />
                                    <div style="position: absolute; top: 100%; left: 0; right: 0; z-index: 10; text-align: center; margin-top: 5px;">
                                        <button class="btn btn-sm btn-default" @click="image = null">Cancelar</button>
                                        <button class="btn btn-sm btn-primary" @click="addAttachmentCustomer">Guardar</button>
                                    </div>
                                </div>
                            </template>

                            <style>
                                .gallery-item-hover:hover .gallery-item-buttons {
                                    display: block;
                                }

                                .gallery-item-buttons {
                                    display: none;
                                }

                                .popup {
                                  position: fixed;
                                  top: 50%;
                                  left: 50%;  
                                  transform: translate(-50%, -50%);
                                  background-color: white;
                                  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);  
                                  max-width: 80%;
                                  overflow-y: auto;
                                  width: 700px;  
                                  height: auto;
                                  z-index: 9999;
                                }
                                
                                .popup-image {
                                  width: 100%;
                                  height: 100%;
                                  object-fit: contain;
                                }
                                
                                .overlay {
                                  position: fixed;
                                  top: 0;
                                  left: 0;
                                  width: 100%;
                                  height: 100%;
                                  background-color: rgba(0, 0, 0, 0.5);
                                  z-index: 9998;
                                }
                                
                                @media (max-width: 768px) {
                                
                                  .popup {
                                    width: 100%;
                                    height: auto;
                                    left: 50%;
                                    transform: translateX(-50%);
                                  }
                                
                                  .popup-image {
                                    width: 100%;
                                    height: auto;
                                    max-width: 100%; 
                                  }
                                
                                }
                                
                                @media (max-width: 468px) {
                                
                                  .popup {
                                    width: 100%;
                                    height: auto;
                                    overflow-y: auto;
                                  }
                                
                                  .popup-image {
                                    height: auto; 
                                  }
                                
                                }
                                    
                              
                            </style>

                            <template x-for="gallery in galleries" :key="gallery.id">
                                <div class="gallery-item-hover" style="height: 150px; border-radius: 10px; border: 1px solid black; position: relative; display: flex; justify-content: center; align-items: center;">
                                    <img :src="gallery.path + gallery.name" @click="showPopup(gallery)" style="width: auto; height: auto; max-width: 100%; max-height: 100%;" />
                                    <div x-show="gallery.popupVisible" @click.away="gallery.popupVisible = false" class="popup">
                                        <img :src="gallery.path + gallery.name" class="popup-image" alt="cliente imagen">
                                    </div>
                                    <div x-show="gallery.popupVisible" class="overlay"></div>
                                    <div class="gallery-item-buttons" style="position: absolute; bottom: 3px; left: 0; right: 0; z-index: 10; text-align: center; margin-top: 5px;">
                                        <button class="btn btn-sm btn-danger" @click="deleteAttachment(gallery.id)">Eliminar</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <!-- Relacion -->
                    <div id="tab-3" class="tab-pane" x-data="addRelation">
                        <form @submit="handleSubmit">
                            <div style="display: flex; margin-top: 20px;">
                                <div style="margin-right: 20px; position: relative;">
                                    <label>Relación</label>

                                    <div x-show="!factory.customer" style="position: relative;">
                                        <input type="text" class="form-control" style="width: 250px;" x-model.debounce.500ms="search" placeholder="Buscar por nombre o dni" />

                                        <style>
                                            .result_item:hover {
                                                background: #EAECEE;
                                                cursor: pointer;
                                            }
                                        </style>

                                        <div x-show="search" @click.outside="search = ''" style="position: absolute; top: 100%; left: 0; right: 0; border: 1px solid #EAECEE; margin-top: 3px;background-color: white; border-radius: 5px; max-height: 250px; overflow-y: auto;">
                                            <div x-show="searching" style="text-align: center; padding: 10px;">Buscando...</div>
                                            <div x-show="resultSearch.length === 0 && !searching" style="text-align: center; padding: 10px;">No hay nada que mostrar</div>
                                            <template x-for="item in resultSearch" :key="item.idCG">
                                                <div x-text="item.idCG + ' ' + item.am + ' ' + item.ap + ' ' + item.nom" class="result_item" style="padding: 10px;" @click="factory.customer = item, search = ''"></div>
                                            </template>
                                        </div>
                                    </div>

                                    <div x-show="factory.customer" style="border: 1px solid #EAECEE; padding: 7px 10px; width: 250px;">
                                        <div style="display: flex; justify-content: space-between;">
                                            <div x-text="factory.customer?.ap + ' ' + factory.customer?.am + ' ' + factory.customer?.nom"></div>
                                            <div>
                                                <button type="button" style="border: 0; background-color: transparent; padding: 0; margin: 0;" @click="factory.customer = null">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="error?.relation_id">
                                        <div x-text="error?.relation_id" style="color: #E74C3C;"></div>
                                    </div>
                                </div>
                                <div style="margin-right: 20px;">
                                    <label>Tipo</label>
                                    <select class="form-control" x-model="factory.type">
                                        <option value="" disabled selected>Seleccione</option>
                                        <option value="spouse">Conyuge</option>
                                        <option value="endorsement">Aval</option>
                                    </select>
                                    <div x-show="error?.type">
                                        <div x-text="error?.type" style="color: #E74C3C;"></div>
                                    </div>
                                </div>
                                <div>
                                    <label style="color: transparent;">Texto</label>
                                    <div>
                                        <button class="btn btn-primary">Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div style="margin-top: 20px;">
                            <template x-for="relation in relations">
                                <div style="display: flex; border: 1px solid #EAECEE; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                                    <div style="flex: none; width: 35px; height: 35px; border: 1px solid black; margin-right: 10px;">
                                        <img :src="relation.sexo === 'F' ? 'img/profile2.jpg' : 'img/profile2.jpg'" style="width: 100%; height: 100%;" />
                                    </div>
                                    <div>
                                        <div style="font-weight: 600;" x-text="relation.ap + ' ' + relation.am + ' ' + relation.nom"></div>
                                        <div>
                                            <span style="color: #85929E;" x-text="relation.dni"></span>
                                            <span>-</span>
                                            <span style="color: #85929E;" x-text="relationTypeToString(relation.pivot.type)"></span>
                                            <!-- <span>-</span>
                                            <span style="color: #85929E;"><?php echo "Alto riesgo" ?></span> -->
                                        </div>
                                    </div>
                                    <div style="margin-left: auto;">
                                        <a :href="'<?php echo $_SERVER["PHP_SELF"] . "?customerId=" ?>' + relation.idCG" class="btn btn-link">Ver perfil</a>
                                        <button class="btn btn-danger" @click="deleteRelation(relation.pivot.customer_id,relation.pivot.relationable_id)">Eliminar</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function relationTypeToString(relationType) {
        switch (relationType) {
            case "spouse":
                return "Conyuge"
                break;

            case "endorsement":
                return "Aval"
                break;

            default:
                return ""
                break;
        }
    }

    const factoryRelation = {
        customer: null,
        type: ""
    }
    document.addEventListener('alpine:init', () => {
        Alpine.data('gallery', () => ({
            galleries: <?php echo $customer->attachments ?>,
            uploadingImage: false,
            image: null,
            creating: false,
            popupImage: null,
            popupVisible: false,
            showPopup(gallery) {
        gallery.popupVisible = true;
    },
            deleting: false,
            uploadImage(event) {
                if (this.uploadingImage) return;
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
                        text: 'Puedes comprimir la imagen en https://tinypng.com',
                        icon: 'info'
                    })
                    return;
                }

                this.uploadingImage = true

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
                        this.uploadingImage = false
                    })
            },

            addAttachmentCustomer() {
                if (!this.image) return;

                if (this.creating) return;

                fetch(`../app/api/addAttachmentCustomer.php`, {
                        method: 'post',
                        body: JSON.stringify({
                            customer_id: <?php echo $request->customerId ?>,
                            name: this.image.name,
                            extension: this.image.extension,
                            size: this.image.size
                        }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.galleries = data.attachments
                            this.image = null

                            swal({
                                title: 'Imagen subido con exito!!',
                                icon: 'success'
                            });
                        } else {
                            swal({
                                title: 'Lo sentimos, no se pudo subir la imange.',
                                icon: 'error'
                            });
                        }
                    })
                    .finally(_ => this.creating = false)
            },

            deleteAttachment(attachmentId) {
                if (this.deleting) return;

                swal({
                        title: "Eliminar imagen",
                        text: "¿Seguro que deseas eliminar la imagen?",
                        icon: 'info',
                        buttons: ['Cancelar', 'Si, eliminar imagen']
                    })
                    .then(value => {
                        if (value) {
                            this.deleting = true;

                            fetch(`../app/api/deleteCustomerAttachment.php?attachment_id=${attachmentId}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        this.galleries = data.attachments
                                        swal({
                                            title: 'Imagen eliminado',
                                            icon: 'success'
                                        })
                                    } else {
                                        swal({
                                            title: 'Lo sentimos, no se pudo eliminar la imagen',
                                            icon: 'error'
                                        })
                                    }
                                })
                                .finally(_ => this.deleting = false);
                        }
                    })
            }
        }))

        Alpine.data('addRelation', () => ({
            relations: <?php echo json_encode($customer->relations) ?>,
            factory: {
                ...factoryRelation
            },
            search: "",
            resultSearch: [],
            error: null,
            searching: false,
            creating: false,
            deleting: false,

            init() {
                this.$watch('search', (value) => {
                    this.searching = true

                    if (value) {
                        fetch('./../app/api/searchCustomerByDocumentOrNames.php?search=' + this.search)
                            .then(response => response.json())
                            .then(data => this.resultSearch = data)
                            .finally(_ => this.searching = false);
                    } else {
                        this.resultSearch = [];
                    }
                });
            },

            handleSubmit(e) {
                e.preventDefault();

                if (this.creating) return

                this.creating = true
                this.error = null

                fetch('./../app/api/addCustomerRelationship.php', {
                        method: "post",
                        body: JSON.stringify({
                            customer_id: <?php echo $request->customerId ?>,
                            type: this.factory.type,
                            relation_id: this.factory.customer?.idCG
                        }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.factory = {
                                ...factoryRelation
                            }
                            this.relations = data.relations

                            swal({
                                title: "Relación creado con exito!",
                                icon: "success"
                            })
                        } else {
                            this.error = data.errors
                        }
                    })
                    .catch(_ => {
                        swal({
                            title: 'Error',
                            text: 'Lo sentimos, se produjo un error desconocido.',
                            icon: 'error'
                        })
                    })
                    .finally(_ => this.creating = false)

            },

            deleteRelation(customerId, relationId) {
                swal({
                        title: "Eliminar relación",
                        text: "¿Seguro que deseas eliminar la relacion?",
                        icon: 'info',
                        buttons: ['Salir', 'Si, eliminar relación']
                    })
                    .then(value => {
                        if (value) {
                            if (this.deleting) return

                            this.deleting = true

                            fetch('./../app/api/deleteCustomerRelationship.php?relation_id=' + relationId + '&customer_id=' + customerId, {
                                    method: "delete",
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        this.relations = data.relations
                                        swal({
                                            title: "Relacion eliminado con exito!",
                                            icon: "success"
                                        })
                                    } else {
                                        swal({
                                            title: "Error",
                                            text: "Lo sentimos, se produjo un error desconocido.",
                                            icon: "error"
                                        })
                                    }
                                })
                                .catch(_ => {
                                    swal({
                                        title: "Error",
                                        text: "Lo sentimos, se produjo un error desconocido.",
                                        icon: "error"
                                    })
                                })
                                .finally(_ => this.deleting = false);

                        }
                    })
            }
        }))
    })
</script>
<script src="../public/resource/js/alpine.3.10.3.min.js" defer></script>

<?php include('footer.php'); ?>