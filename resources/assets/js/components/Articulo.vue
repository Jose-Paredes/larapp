<template>
    <main class="main">
        <!-- Breadcrumb MIGAS DE PAN-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Articulos
                    <button type="button" @click="abrirModal('articulo', 'registrar')" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterio">
                                    <option value="nombre">Nombre</option>
                                    <option value="descripcion">Descripción</option>
                                </select>
                                <input type="text" v-model="buscar" @keyup.enter="listarArticulo(1, buscar, criterio)" class="form-control" placeholder="Texto a buscar">
                                <button type="submit" @click="listarArticulo(1, buscar, criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th>Opciones</th>
                            <th>Codigo</th>
                            <th>Nombre</th>
                            <th>Categoria</th>
                            <th>Precio venta</th>
                            <th>Stock</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                            <td>
                                <button type="button" @click="abrirModal('articulo', 'actualizar', articulo)" class="btn btn-warning btn-sm">
                                    <i class="icon-pencil"></i>
                                </button> &nbsp;
                                <template v-if="articulo.condicion"> <!-- Si la condicion es 1(activo), mostrar boton desactivar -->
                                    <button type="button" class="btn btn-danger btn-sm" @click="desactivarArticulo(articulo.id)">
                                        <i class="icon-trash"></i>
                                    </button>
                                </template>
                                <template v-else> <!-- Si la condicion es 0(inactivo), mostrar boton activar -->
                                    <button type="button" class="btn btn-info btn-sm" @click="activarArticulo(articulo.id)">
                                        <i class="icon-check"></i>
                                    </button>
                                </template>
                            </td>
                            <td v-text="articulo.codigo"></td>
                            <td v-text="articulo.nombre"></td>
                            <td v-text="articulo.nombre_categoria"></td>
                            <td v-text="articulo.precio_venta"></td>
                            <td v-text="articulo.stock"></td>
                            <td v-text="articulo.descripcion"></td>
                            <td>
                                <div v-if="articulo.condicion">
                                    <span class="badge badge-success">Activo</span>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Desactivado</span>
                                </div>
                            </td>
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
                                <label class="col-md-3 form-control-label" for="text-input">Categoria</label>
                                <div class="col-md-9">
                                    <select class="form-control" v-model="idcategoria">
                                        <option value="0" disabled>Seleccione</option>
                                        <option v-for="categoria in arrayCategoria" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Codigo</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="codigo" class="form-control" placeholder="Codigo de barras">
                                    <barcode :value="codigo" :options="{ format: 'EAN-13' }">
                                        Generando codigo de barras...
                                    </barcode>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="nombre" class="form-control" placeholder="Nombre de articulo">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Precio de venta</label>
                                <div class="col-md-9">
                                    <input type="number" v-model="precio_venta" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Stock</label>
                                <div class="col-md-9">
                                    <input type="number" v-model="stock" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Descripción</label>
                                <div class="col-md-9">
                                    <input type="email" v-model="descripcion" class="form-control" placeholder="Ingrese la descripcion">
                                </div>
                            </div>
                            <div v-show="errorArticulo" class="form-group row div-error">
                                <div class="text-center text-error">
                                    <div v-for="error in errorMostrarMsjArticulo" :key="error" v-text="error">

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        <button type="button" v-if="tipoAccion===1" class="btn btn-primary" @click="registrarArticulo()">Guardar</button>
                        <button type="button" v-if="tipoAccion===2" class="btn btn-primary" @click="actualizarArticulo()">Actualizar</button>
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
    import VueBarcode from 'vue-barcode';
    export default {
        data() {
            return {
                // Variables que vamos a utilizar
                articulo_id: 0,
                idcategoria: 0,
                nombre_categoria: '',
                codigo: '',
                nombre: '',
                precio_venta: 0,
                stock: 0,
                descripcion: '',
                arrayArticulo: [], // Almacenamos el response de la peticion, listado de todas los articulos
                modal: 0,    // Indica si se muestra/oculta el modal
                tituloModal: '',
                tipoAccion: 0,
                errorArticulo: 0,
                errorMostrarMsjArticulo: [],
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
                buscar: '',
                arrayCategoria: [] // Almacena el listado de todas las categorias
            }
        },
        components: {
            'barcode': VueBarcode
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
            listarArticulo(page, buscar, criterio) {
                let me = this;
                var url = '/articulo?page='+page+'&buscar='+buscar+'&criterio='+criterio;
                axios.get(url).then(function (response) { // todo lo que nos devuelve el index del controlador
                    var respuesta = response.data;
                    // Si toudo sale bien
                    me.arrayArticulo = respuesta.articulos.data;
                    me.pagination = respuesta.pagination;
                }).catch(function (error) {
                    // atrapamos el error
                    console.log(error);
                })
            },
            selectCategoria() {
                let me = this;
                var url = '/categoria/selectCategoria';
                axios.get(url).then(function (response) { // todo lo que nos devuelve el index del controlador
                    var respuesta = response.data;
                    me.arrayCategoria = respuesta.categorias;
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
                me.listarArticulo(page, buscar, criterio);
            },
            registrarArticulo() {
                if (this.validarArticulo()) {
                    // En caso de que haya errores
                    return;
                }

                let me = this; // Hace ref a este mismo archivo
                // AXIOS, recibe 2 parametros; ruta + parametros
                axios.post('/validar/articulo', { 'nombre': this.nombre}).then(function (response) {
                    if(response.data.length !== 0) {
                        swal(
                            'Duplicado',
                            'Ya existe un articulo registrado con ese nombre!',
                            'warning'
                        )
                    } else {
                        axios.post('/articulo/registrar', {
                            'idcategoria': me.idcategoria,
                            'codigo': me.codigo,
                            'nombre': me.nombre,
                            'stock': me.stock,
                            'precio_venta': me.precio_venta,
                            'descripcion': me.descripcion
                        }).then(function (response) {
                            me.cerrarModal();
                            me.listarArticulo(1, '', 'nombre');
                        }).catch(function (error) {
                            console.log(error);
                        });
                    }
                });
            },
            actualizarArticulo() {
                if (this.validarArticulo()) {  // Validamos campos obligatorios
                    // En caso de que haya errores
                    return;
                }

                let me = this; // Hace ref a este mismo archivo
                // AXIOS, recibe 2 parametros; ruta + parametros
                axios.put('/articulo/actualizar', {
                    'idcategoria': this.idcategoria,
                    'codigo': this.codigo,
                    'nombre': this.nombre,
                    'stock': this.stock,
                    'precio_venta': this.precio_venta,
                    'descripcion': this.descripcion,
                    'id': this.articulo_id
                }).then(function (response) { // Si todo va bien
                    me.cerrarModal();
                }).catch(function (error) { // Si hay errores
                    console.log(error);
                });
                me.listarArticulo(1, this.buscar, this.criterio);
            },
            desactivarArticulo(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                });

                swalWithBootstrapButtons({
                    title: 'Estas seguro de desactivar este articulo?',
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
                        axios.put('/articulo/desactivar', {
                            'id': id
                        }).then(function (response) { // Si todo va bien
                            me.listarArticulo(1, '', 'nombre');
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
            activarArticulo(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    // Comente para separar los botones, no carga las clases de bootstrap
                    //confirmButtonClass: 'btn btn-success',
                    //cancelButtonClass: 'btn btn-danger',
                    //buttonsStyling: false,
                });

                swalWithBootstrapButtons({
                    title: 'Estas seguro de activar este articulo?',
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
                        axios.put('/articulo/activar', {
                            'id': id
                        }).then(function (response) { // Si todo va bien
                            me.listarArticulo(1, '', 'nombre');
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
            },
            validarArticulo() {
                this.errorArticulo = 0;
                this.errorMostrarMsjArticulo = [];

                // Verificamos si algun campo esta vacio
                if(this.idcategoria === 0) this.errorMostrarMsjArticulo.push("Seleccione  una categoria!");
                if(!this.nombre) this.errorMostrarMsjArticulo.push("El nombre del articulo no puede estar vacio!");
                if(!this.stock) this.errorMostrarMsjArticulo.push("El stock del articulo debe ser un numero y no puede quedar vacio!");
                if(!this.precio_venta) this.errorMostrarMsjArticulo.push("El precio de venta del articulo debe ser un numero y no puede quedar vacio!");

                // Evaluamos si tenemos errores
                if(this.errorMostrarMsjArticulo.length) this.errorArticulo = 1;

                return this.errorArticulo;
            },
            cerrarModal() {
                this.modal = 0;
                this.tituloModal = '';
                this.idcategoria = 0;
                this.nombre_categoria = '';
                this.codigo = '';
                this.nombre = '';
                this.precio_venta = 0;
                this.stock = 0;
                this.descripcion = '';
                this.errorArticulo = 0;
                this.errorMostrarMsjArticulo = [];
            },
            // PARAMETROS: nombre del modelo(ej. articulo)
            // ACCION: registar/actualizar
            // DATA: El objeto que se encuentra en la fila seleccionada
            abrirModal(modelo, accion, data = []) {
                switch (modelo) {
                    case "articulo": {
                        switch (accion) {
                            case 'registrar': {
                                this.modal = 1;
                                this.tituloModal = 'REGISTRAR ARTICULO';
                                this.idcategoria = 0;
                                this.nombre_categoria = '';
                                this.codigo = '';
                                this.nombre = '';
                                this.precio_venta = 0;
                                this.stock = 0;
                                this.descripcion = '';
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar': {
                                //console.log(data);
                                this.modal = 1;
                                this.tituloModal = 'ACTUALIZAR ARTICULO';
                                this.tipoAccion = 2;
                                this.articulo_id = data['id'];
                                this.idcategoria = data['idcategoria'];
                                this.codigo = data['codigo'];
                                this.nombre = data['nombre'];
                                this.stock = data['stock'];
                                this.precio_venta = data['precio_venta'];
                                this.descripcion = data['descripcion'];
                                break;
                            }
                        }
                    }
                }
                this.selectCategoria();
            }
        },
        mounted() {
            // Hacemos referencia a los metodos creados
            this.listarArticulo(1, this.buscar, this.criterio);
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
