<template>
<main class="main">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
    </ol>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <!--Grafico de compras-->
                        <div class="card card-chart">
                            <div class="card-header">
                                <h4>Ingresos</h4>
                            </div>
                            <div class="card-content">
                                <div class="ct-chart">
                                    <canvas id="ingresos"> <!--./ Aqui van los graficos -->

                                    </canvas>
                                </div>
                            </div>
                            <div class="card-footer">
                                <p>Compras de los ultimos meses.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!--Grafico de ventas-->
                        <div class="card card-chart">
                            <div class="card-header">
                                <h4>Ventas</h4>
                            </div>
                            <div class="card-content">
                                <div class="ct-chart">
                                    <canvas id="ventas"> <!--./ Aqui van los graficos -->

                                    </canvas>
                                </div>
                            </div>
                            <div class="card-footer">
                                <p>Ventas de los ultimos meses.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</template>

<script>
    export default {
        data () {
            return {
                varIngreso: null,
                charIngreso: null,  // Crea grafico, alimentado por valores vinculado con el id del obj canvas
                ingresos: [],       // Listado de ingresos
                varTotalIngreso: [],// Almacena total de ingresos de cada mes
                varMesIngreso: [],   // Almacena nombre de meses para el grafico

                varVenta: null,
                charVenta: null,
                ventas: [],
                varTotalVenta: [],
                varMesVenta: []
            }
        },
        methods : {
            getIngresos() {
                let me = this;
                var url = '/dashboard';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.ingresos = respuesta.ingresos;
                    // Cargamos los datos del chart
                    me.loadIngresos();
                }).catch(function (error) {
                    console.log(error);
                })
            },
            getVentas() {
                let me = this;
                var url = '/dashboard';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.ventas = respuesta.ventas;
                    // Cargamos los datos del chart
                    me.loadVentas();
                }).catch(function (error) {
                    console.log(error);
                })
            },
            loadIngresos() {
                let me = this;
                me.ingresos.map(function (x) {
                    me.varMesIngreso.push(x.mes);
                    me.varTotalIngreso.push(x.total);
                });
                // Establece el lugar donde se mostrara el grafico
                me.varIngreso = document.getElementById('ingresos').getContext('2d');

                me.charIngreso = new Chart(me.varIngreso, {
                    type: 'bar',
                    data: {
                        labels: me.varMesIngreso,
                        datasets: [{
                            label: 'Ingresos',
                            data: me.varTotalIngreso,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
                /*me.charIngreso = new Chart(me.varIngreso, {
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: me.varTotalIngreso,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            label: 'Ingresos'
                        }],
                        labels: me.varMesIngreso
                    },
                    options: {
                        responsive: true
                    }
                });*/
            },
            loadVentas() {
                let me = this;
                me.ventas.map(function (x) {
                    me.varMesVenta.push(x.mes);
                    me.varTotalVenta.push(x.total);
                });
                // Establece el lugar donde se mostrara el grafico
                me.varVenta = document.getElementById('ventas').getContext('2d');

                me.charVenta = new Chart(me.varVenta, {
                    type: 'bar',
                    data: {
                        labels: me.varMesVenta,
                        datasets: [{
                            label: 'Ventas',
                            data: me.varTotalVenta,
                            backgroundColor: 'rgba(55, 162, 235, 0.2)',
                            borderColor: 'rgba(55, 162, 235, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            }
        },
        mounted () {
            this.getIngresos();
            this.getVentas();
        }
    }
</script>

<style scoped>

</style>