<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Ventas
                </div>
                <!--Listado de ventas-->
                <template v-if="listado===1">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterio">
                                        <option value="tipo_comprobante">Tipo Comprobante</option>
                                        <option value="num_comprobante">Numero Comprobante</option>
                                        <option value="fecha_hora">Fecha-Hora</option>
                                    </select>
                                    <input type="text" v-model="buscar" @keyup.enter="listarVenta(1, buscar, criterio)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="listarVenta(1, buscar, criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                            <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Usuario</th>
                                <th>Cliente</th>
                                <th>Tipo Comprobante</th>
                                <th>Serie Comprobante</th>
                                <th>Numero Comprobante</th>
                                <th>Fecha Hora</th>
                                <th>Total</th>
                                <th>Impuesto</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="venta in arrayVenta" :key="venta.id">
                                <td>
                                    <button type="button" @click="verVenta(venta.id)" class="btn btn-success btn-sm">
                                        <i class="icon-eye"></i>
                                    </button>
                                    <button type="button" @click="pdfVenta(venta.id)" class="btn btn-info btn-sm">
                                        <i class="icon-doc"></i>
                                    </button>&nbsp;
                                </td>
                                <td v-text="venta.usuario"></td>
                                <td v-text="venta.nombre"></td> <!--Nombre del cliente-->
                                <td v-text="venta.tipo_comprobante"></td>
                                <td v-text="venta.serie_comprobante"></td>
                                <td v-text="venta.num_comprobante"></td>
                                <td v-text="venta.fecha_hora"></td>
                                <td v-text="venta.total"></td>
                                <td v-text="venta.impuesto"></td>
                                <!--<td v-text="venta.estado"></td>-->
                                <td>
                                    <div v-if="venta.estado==='Registrado'">
                                        <span class="badge badge-success">Registrado</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">Anulado</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
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
                    <!--Fin Listado de ventas-->
                </template>
                <!-- ./ Ver detalles de venta /. -->
                <template v-else-if="listado===2">
                    <!--Detalle ventas-->
                    <div class="card-body">
                        <div class="form-group row border">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="">Cliente</label>
                                    <p v-text="cliente"></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="">Impuesto</label>
                                <p v-text="impuesto"></p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo Comprobante</label>
                                    <p v-text="tipo_comprobante"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Serie Comprobante</label>
                                    <p v-text="serie_comprobante"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Numero Comprobante</label>
                                    <p v-text="num_comprobante"></p>
                                </div>
                            </div>

                        </div>
                        <div class="form-group row border">
                            <!--Listado de articulos agregados al detalle-->
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th>Articulo</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Descuento</th>
                                        <th>Subtotal</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                    <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                                        <td v-text="detalle.articulo"></td>
                                        <td v-text="detalle.precio">
                                        </td>
                                        <td v-text="detalle.cantidad">
                                        </td>
                                        <td v-text="detalle.descuento">
                                        </td>
                                        <td>
                                            {{ detalle.precio*detalle.cantidad-detalle.descuento }}
                                        </td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total Parcial:</strong></td>
                                        <td>{{ totalParcial = (total-totalImpuesto).toFixed(2) }}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total Impuesto:</strong></td>
                                        <td>{{ totalImpuesto = ((total*impuesto)).toFixed(2) }}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total Neto:</strong></td>
                                        <td>{{ total }}</td>
                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <td colspan="5">
                                            No hay articulos agregados
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" @click="cerrarDetalle()">Cerrar</button>
                            </div>
                        </div>
                    </div>
                    <!--Fin Detalle ventas-->
                </template>
                <!-- ./ Fin detalles de venta -->
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>
    </main>
</template>
<!--
    Axios es una biblioteca que nos permite revisar petiociones http desde el browser
    Transforma automaticamente las peticiones en json
-->
<script>
    // Importamos el componente vue-select
    import vSelect from 'vue-select';
    export default {
        data() {
            return {
                // Variables que vamos a utilizar
                venta_id: 0,
                idcliente: 0,
                cliente: '',
                tipo_comprobante: 'BOLETA',
                serie_comprobante: '',
                num_comprobante: '',
                impuesto: 0.18,
                total: 0.0,
                totalImpuesto: 0.0,
                totalParcial: 0.0,
                arrayVenta: [],
                arrayCliente: [],
                arrayDetalle: [],
                listado: 1, // Almacena si se visualiza o no el listado
                modal: 0,    // Indica si se muestra/oculta el modal
                tituloModal: '',
                tipoAccion: 0,
                errorVenta: 0,
                errorMostrarMsjVenta: [],
                pagination: {
                    'total'         : 0,    // Total de registros
                    'current_page'  : 0,    // La pag actual
                    'per_page'      : 0,    // Nros de reg por pag
                    'last_page'     : 0,    // La ultima pag
                    'from'          : 0,    // Desde la pag
                    'to'            : 0,    // Hasta la pag
                },
                offset: 3,
                criterio: 'num_comprobante', // Indica cual es el campo de busqueda
                criterioA: 'nombre',
                buscar: '',
                buscarA: '',
                arrayArticulo: [],
                idarticulo: 0,
                codigo: '',
                articulo: '',   // Nombre del articulo
                precio: 0,
                cantidad: 0,
                descuento: 0,
                stock: 0
            }
        },
        components: {
            vSelect
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
            },
            calcularTotal: function () {
                var resultado = 0;
                for (var i=0;i<this.arrayDetalle.length;i++) {
                    resultado = resultado+(this.arrayDetalle[i].precio*this.arrayDetalle[i].cantidad-this.arrayDetalle[i].descuento);
                }
                return resultado;
            }
        },
        methods : {
          // Metodo que retornara el listado de ventas
            listarVenta(page, buscar, criterio) {
                let me = this;
                var url = '/venta?page='+page+'&buscar='+buscar+'&criterio='+criterio;
                axios.get(url).then(function (response) { // todo lo que nos devuelve el index del controlador
                    var respuesta = response.data;
                        me.arrayVenta = respuesta.ventas.data;
                        me.pagination = respuesta.pagination;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            pdfVenta(id) {
                window.open('http://127.0.0.1:8000/venta/pdf/'+id+'.'+ '_blank');
                //http://127.0.0.1:8000/venta/pdf/1,_blank
            },
            cambiarPagina(page, buscar, criterio) {
                // Recibe de parametro el nro de la pag a mostrar
                let me = this;
                // Actualiza la pag actual
                me.pagination.current_page = page;
                // Envia la peticion para visualizar la data de esa pag
                me.listarVenta(page, buscar, criterio);
            },
            mostrarDetalle() {
                let me = this;
                this.listado = 0;
                me.idcliente = 0;
                me.tipo_comprobante = 'BOLETA';
                me.serie_comprobante = '';
                me.num_comprobante = '';
                me.impuesto = 0.18;
                me.total = 0.0;
                me.idarticulo = 0;
                me.articulo = 0;
                me.cantidad = 0;
                me.precio = 0;
                me.arrayDetalle = [];
            },
            cerrarDetalle() {
                this.listado = 1;
                this.errorMostrarMsjVenta = []
            },
            verVenta(id) {
                let me = this;
                me.listado = 2;
                // Obtener los datos del venta
                var arrayVentaT = [];
                var url = '/venta/obtenerCabecera?id='+id;
                axios.get(url).then(function (response) { // tod0 lo que nos devuelve el index del controlador
                    var respuesta        = response.data;
                    arrayVentaT          = respuesta.venta;

                    me.cliente           = arrayVentaT[0]['nombre'];
                    me.tipo_comprobante  = arrayVentaT[0]['tipo_comprobante'];
                    me.serie_comprobante = arrayVentaT[0]['serie_comprobante'];
                    me.num_comprobante   = arrayVentaT[0]['num_comprobante'];
                    me.impuesto          = arrayVentaT[0]['impuesto'];
                    me.total             = arrayVentaT[0]['total'];
                }).catch(function (error) {
                    // atrapamos el error
                    console.log(error);
                });

                // Obtener los datos del los detalles
                var urlD = '/venta/obtenerDetalles?id='+id;
                axios.get(urlD).then(function (response) { // tod0 lo que nos devuelve el index del controlador
                    var respuesta = response.data;
                    me.arrayDetalle = respuesta.detalles;
                }).catch(function (error) {
                    // atrapamos el error
                    console.log(error);
                })
            }
        },
        mounted() {
            // Hacemos referencia a los metodos creados
            this.listarVenta(1, this.buscar, this.criterio);
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

    @media (min-width: 600px) {
        .btnagregar {
            margin-top: 2rem;
        }
    }
</style>
