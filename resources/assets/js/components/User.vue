<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Usuarios
                    <button type="button" @click="abrirModal('persona', 'registrar')" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterio">
                                    <option value="nombre">Nombre</option>
                                    <option value="num_documento">Documento</option>
                                    <option value="email">Email</option>
                                    <option value="telefono">Telefono</option>
                                </select>
                                <input type="text" v-model="buscar" @keyup.enter="listarPersona(1, buscar, criterio)" class="form-control" placeholder="Texto a buscar">
                                <button type="submit" @click="listarPersona(1, buscar, criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th>Opciones</th>
                            <th>Nombre</th>
                            <th>Tipo Documento</th>
                            <th>Numero</th>
                            <th>Direccion</th>
                            <th>Telefono</th>
                            <th>Email</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="persona in arrayPersona" :key="persona.id">
                            <td>
                                <button type="button" @click="abrirModal('persona', 'actualizar', persona)" class="btn btn-warning btn-sm">
                                    <i class="icon-pencil"></i>
                                </button>&nbsp;
                                <template v-if="persona.condicion"> <!-- Si la condicion es 1(activo), mostrar boton desactivar -->
                                    <button type="button" class="btn btn-danger btn-sm" @click="desactivarUsuario(persona.id)">
                                        <i class="icon-trash"></i>
                                    </button>
                                </template>
                                <template v-else> <!-- Si la condicion es 0(inactivo), mostrar boton activar -->
                                    <button type="button" class="btn btn-info btn-sm" @click="activarUsuario(persona.id)">
                                        <i class="icon-check"></i>
                                    </button>
                                </template>
                            </td>
                            <td v-text="persona.nombre"></td>
                            <td v-text="persona.tipo_documento"></td>
                            <td v-text="persona.num_documento"></td>
                            <td v-text="persona.direccion"></td>
                            <td v-text="persona.telefono"></td>
                            <td v-text="persona.email"></td>
                            <td v-text="persona.usuario"></td>
                            <td v-text="persona.rol"></td>
                        </tr>
                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1"> <!--Si la pag actual es mayor a 1. Solo muestra boton si estamos desde la pag 2 en adelante-->
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1, buscar, criterio)">Ant</a> <!--Con cada click, restamos 1 a la pag actual-->
                            </li>
                            <!--Aca usamos la prop computada
                            Mostramos todos los nros dentro de la seccion de paginacion
                            -->
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page === isActived? 'active': '']"> <!--El boton nos inidica que estamos en la pag actual-->
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, buscar, criterio)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page"> <!--Mientras la pag actual sea menor que la ultima pag, puede mostrar la pag siguiente -->
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1, buscar, criterio)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <!--bindin {{}} anexa un resultado al attr class-->
        <!--El modal anexara la clase mostrar si la variable modal es true-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="nombre" class="form-control" placeholder="Nombre de la persona">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Tipo Documento</label>
                                <div class="col-md-9">
                                    <select v-model="tipo_documento" class="form-control">
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                        <option value="PASS">PASS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Numero</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="num_documento" class="form-control" placeholder="Numero de documento">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Direccion</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="direccion" class="form-control" placeholder="Ingrese la direccion">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Telefono</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="telefono" class="form-control" placeholder="Ingrese el telefono">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Email</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="email" class="form-control" placeholder="Ingrese el email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Rol (*)</label>
                                <div class="col-md-9">
                                    <select class="form-control" v-model="idrol">
                                        <option value="0">Seleccione un rol</option>
                                        <option v-for="rol in arrayRol" :key="rol.id" :value="rol.id" v-text="rol.nombre"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Usuario (*)</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="usuario" class="form-control" placeholder="Ingrese el nombre del usuario">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Password (*)</label>
                                <div class="col-md-9">
                                    <input type="password" v-model="password" class="form-control" placeholder="Ingrese el password">
                                </div>
                            </div>
                            <div v-show="errorPersona" class="form-group row div-error">
                                <div class="text-center text-error">
                                    <div v-for="error in errorMostrarMsjPersona" :key="error" v-text="error">

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        <button type="button" v-if="tipoAccion===1" class="btn btn-primary" @click="registrarPersona()">Guardar</button>
                        <button type="button" v-if="tipoAccion===2" class="btn btn-primary" @click="actualizarPersona()">Actualizar</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->
    </main>
</template>
<!--
    Axios es una biblioteca que nos permite revisar petiociones http desde el browser
    Transforma automaticamente las peticiones en json
-->
<script>
    export default {
        data() {
            return {
                // Variables que vamos a utilizar
                persona_id: 0,
                nombre: '',
                tipo_documento: 'DNI',
                num_documento: '',
                direccion: '',
                telefono: '',
                email: '',
                usuario: '',
                password: '',
                idrol: 0,
                arrayPersona: [], // Almacenamos el response de la peticion, listado de todas las personas
                arrayRol: [],
                modal: 0,    // Indica si se muestra/oculta el modal
                tituloModal: '',
                tipoAccion: 0,
                errorPersona: 0,
                errorMostrarMsjPersona: [],
                pagination: {
                    'total'         : 0,    // Total de registros
                    'current_page'  : 0,    // La pag actual
                    'per_page'      : 0,    // Nros de reg por pag
                    'last_page'     : 0,    // La ultima pag
                    'from'          : 0,    // Desde la pag
                    'to'            : 0,    // Hasta la pag
                },
                offset: 3,
                criterio: 'nombre', // Indica cual es el campo de busqueda
                buscar: ''
            }
        },
        // Propiedad computada
        computed : {
            isActived: function () {
                // Retornamos la pag actual
                return this.pagination.current_page;
            },
            // Calcula el nro de elementos de la paginacion
            pagesNumber: function () {
                // Si la pag es dif al ultimo elemento de la pag actual
                if (!this.pagination.to) {
                    return [];
                }

                // Almacenamos la resta de la pag actual y el offset
                let from = this.pagination.current_page - this.offset;
                // Evaluamos si la pag actual es 0 o menos, se establece la pag actual a 1
                if (from < 1) {
                    from = 1;
                }

                // Almacena la suma de la pag actual + el doble de offset
                let to = from + (this.offset * 2);
                // Si to es mayor o igual que la ultima pag, establemos ese valor a to
                if (to >= this.pagination.last_page) {
                    to = this.pagination.last_page;
                }

                let pagesArray = [];
                // Mientras la pag actual se menor o igual a la ultima pag
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }

                return pagesArray;
            }
        },
        methods : {
          // Metodo que retornara el listado de registros
            listarPersona(page, buscar, criterio) {
                let me = this;
                var url = '/user?page='+page+'&buscar='+buscar+'&criterio='+criterio;
                axios.get(url).then(function (response) { // todo lo que nos devuelve el index del controlador
                    var respuesta = response.data;
                        // Si toudo sale bien
                        me.arrayPersona = respuesta.personas.data;
                        me.pagination = respuesta.pagination;
                    })
                    .catch(function (error) {
                        // atrapamos el error
                        console.log(error);
                    })
            },
            selectRol() {
                let me = this;
                var url = '/rol/selectRol';
                axios.get(url).then(function (response) { // todo lo que nos devuelve el index del controlador
                    var respuesta = response.data;
                    me.arrayRol = respuesta.roles;
                }).catch(function (error) {
                        // atrapamos el error
                        console.log(error);
                })
            },
            cambiarPagina(page, buscar, criterio) {
                // Recibe de parametro el nro de la pag a mostrar
                let me = this;
                // Actualiza la pag actual
                me.pagination.current_page = page;
                // Envia la peticion para visualizar la data de esa pag
                me.listarPersona(page, buscar, criterio);
            },
            registrarPersona() {
                if (this.validarPersona()) {
                    // En caso de que haya errores
                    return;
                }

               let me = this; // Hace ref a este mismo archivo
                // Mediante AJAX y AXIOS, recibe 2 parametros; ruta + parametros
                axios.post('/user/registrar', {
                    'nombre': this.nombre,
                    'tipo_documento': this.tipo_documento,
                    'num_documento': this.num_documento,
                    'direccion': this.direccion,
                    'telefono': this.telefono,
                    'email': this.email,
                    'usuario': this.usuario,
                    'password': this.password,
                    'idrol': this.idrol
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarPersona(1, '', 'nombre');
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarPersona() {
                if (this.validarPersona()) {  // Validamos campos obligatorios
                    // En caso de que haya errores
                    return;
                }

                let me = this; // Hace ref a este mismo archivo
                // AXIOS, recibe 2 parametros; ruta + parametros
                axios.put('/user/actualizar', {
                    'nombre': this.nombre,
                    'tipo_documento': this.tipo_documento,
                    'num_documento': this.num_documento,
                    'direccion': this.direccion,
                    'telefono': this.telefono,
                    'email': this.email,
                    'usuario': this.usuario,
                    'password': this.password,
                    'idrol': this.idrol,
                    'id': this.persona_id
                }).then(function (response) { // Si todo va bien
                    me.cerrarModal();
                    // me.listarPersona(1, this.buscar, 'nombre');
                }).catch(function (error) { // Si hay errores
                    console.log(error);
                });
                me.listarPersona(1, this.buscar, this.criterio);
            },
            validarPersona() {
                this.errorPersona = 0;
                this.errorMostrarMsjPersona = [];

                // Verificamos si algun campo esta vacio
                if(!this.nombre) this.errorMostrarMsjPersona.push("El nombre de la persona no puede estar vacio");
                if(!this.usuario) this.errorMostrarMsjPersona.push("El nombre del usuario no puede estar vacio");
                if(!this.password) this.errorMostrarMsjPersona.push("El password del usuario no puede estar vacio");
                if(this.idrol===0) this.errorMostrarMsjPersona.push("Debes seleccionar un rol para el usuario");

                // Evaluamos si tenemos errores
                if(this.errorMostrarMsjPersona.length) this.errorPersona = 1;

                return this.errorPersona;
            },
            cerrarModal() {
                this.modal = 0;
                this.tituloModal = '';
                this.nombre = '';
                this.tipo_documento = 'DNI';
                this.num_documento = '';
                this.direccion = '';
                this.telefono = '';
                this.email = '';
                this.usuario = '';
                this.password = '';
                this.idrol = 0;
                this.errorPersona = 0;
                this.errorMostrarMsjPersona = [];
            },
            abrirModal(modelo, accion, data = []) {
                this.selectRol();
                switch (modelo) {
                    case "persona": {
                        switch (accion) {
                            case 'registrar': {
                                this.modal = 1;
                                this.tituloModal    = 'REGISTRAR USUARIO';
                                this.nombre         = '';
                                this.tipo_documento = 'DNI';
                                this.num_documento  = '';
                                this.direccion      = '';
                                this.telefono       = '';
                                this.email          = '';
                                this.usuario        = '';
                                this.password       = '';
                                this.idrol          = 0;
                                this.tipoAccion     = 1;
                                break;
                            }
                            case 'actualizar': {
                                //console.log(data);
                                this.modal = 1;
                                this.tituloModal    = 'ACTUALIZAR USUARIO';
                                this.tipoAccion     = 2;
                                this.persona_id     = data['id'];
                                this.nombre         = data['nombre'];
                                this.tipo_documento = data['tipo_documento'];
                                this.num_documento  = data['num_documento'];
                                this.direccion      = data['direccion'];
                                this.telefono       = data['telefono'];
                                this.email          = data['email'];
                                this.usuario        = data['usuario'];
                                this.password       = data['password'];
                                this.idrol          = data['idrol'];
                                break;
                            }
                        }
                    }
                }
            },
            desactivarUsuario(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                });

                swalWithBootstrapButtons({
                    title: 'Estas seguro de desactivar este usuario?',
                    //text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Si, continuar!',
                    cancelButtonText: 'No, cancelar!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        // Utilizamos axios para realizar una petiocion http para desactivar
                        let me = this; // Hace ref a este mismo archivo
                        // AXIOS, recibe 2 parametros; ruta + parametros
                        axios.put('/user/desactivar', {
                            'id': id
                        }).then(function (response) { // Si todo va bien
                            me.listarPersona(1, '', 'nombre');
                            swalWithBootstrapButtons(
                                'Desactivado!',
                                'El registro a sido desactivado con exito.',
                                'success'
                            )
                        }).catch(function (error) { // Si hay errores
                            console.log(error);
                        });
                    } else if (
                        // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                    ) {
                        // Mensaje cuando clickeamos en CANCELAR
                        // swalWithBootstrapButtons(
                        //     'Cancelled',
                        //     'Your imaginary file is safe :)',
                        //     'error'
                        // )
                    }
                })
            },
            activarUsuario(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    // Comente para separar los botones, no carga las clases de bootstrap
                    //confirmButtonClass: 'btn btn-success',
                    //cancelButtonClass: 'btn btn-danger',
                    //buttonsStyling: false,
                });

                swalWithBootstrapButtons({
                    title: 'Estas seguro de activar este usuario?',
                    //text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Si, continuar!',
                    cancelButtonText: 'No, cancelar!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        // Utilizamos axios para realizar una peticion http para activar
                        let me = this; // Hace ref a este mismo archivo
                        // AXIOS, recibe 2 parametros; ruta + parametros
                        axios.put('/user/activar', {
                            'id': id
                        }).then(function (response) { // Si todo va bien
                            me.listarPersona(1, '', 'nombre');
                            swalWithBootstrapButtons(
                                'Activado!',
                                'El registro a sido activado con exito.',
                                'success'
                            )
                        }).catch(function (error) { // Si hay errores
                            console.log(error);
                        });
                    } else if (
                        // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                    ) {
                        // Aqui muestra mensaje cuando clickeamos en CANCELAR
                        // swalWithBootstrapButtons(
                        //     'Cancelled',
                        //     'Your imaginary file is safe :)',
                        //     'error'
                        // )
                    }
                })
            }
        },
        mounted() {
            // Hacemos referencia a los metodos creados
            this.listarPersona(1, this.buscar, this.criterio);
        }
    }
</script>
<style>
    .modal-content {
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar {
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .div-error {
        display: flex;
        justify-content: center;
    }
    .text-error {
        color: red !important;
        font-weight: bold;
    }
</style>
