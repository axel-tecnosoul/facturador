<?php 
session_start(); 
if(empty($_SESSION['user']))
{
	header("Location: index.php");
	die("Redirecting to index.php"); 
}
include 'database.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	  <?php include('head_tables.php');?>
    <style>
      #balance td, 
      #balance th {
          white-space: nowrap;
          width: 1%;
      }

      .ing_eg{
        text-align:right;
        font-size:10px;
      }
      .saldo{
        text-align:right;
        font-size:13px;
        text-decoration: underline;
        font-weight: bold;
      }
      .cardClickeable:hover{
        cursor: pointer;
        border: solid 1px;
        border-radius: 5px;
      }
    </style>
  </head>
  <body class="light-only">
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
      <!-- Page Header Start-->
      <?php include('header.php');?>
     
      <!-- Page Header Ends                              -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <?php include('menu.php');?>
        <!-- Page Sidebar Ends-->
        <!-- Right sidebar Start-->
        <!-- Right sidebar Ends-->
        <div class="page-body">
          <div class="container-fluid">
            <div class="page-header">
              <div class="row">
                <div class="col-10">
                  <div class="page-header-left">
                    <h3><?php include("title.php"); ?></h3>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="#"><i data-feather="home"></i></a></li>
                      <li class="breadcrumb-item">Balance de Almacenes</li>
                    </ol>
                  </div>
                </div>
                <!-- Bookmark Start-->
                <div class="col-2">
                  <div class="bookmark pull-right">
                    <ul>
                      <li><a  target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?php echo date('d-m-Y');?>"><i data-feather="calendar"></i></a></li>
                    </ul>
                  </div>
                </div>
                <!-- Bookmark Ends-->
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
              <!-- Zero Configuration  Starts-->
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Balance de Almacenes</h5>
                  </div>
                  <div class="card-body">
                    <div class="row mb-3">
                      <div class="col-12">
                        <div class="alert alert-danger">
                          <strong>Atencion!</strong> Los datos mostrados en este reporte son generados de manera aleatoria cada vez que se carga la pagina.
                        </div>
                      </div>
                    </div><?php
                    include_once 'database.php';
                    $pdo = Database::connect();

                    $sql = "SELECT id,almacen FROM almacenes WHERE id_tipo=1";
                    //echo $sql;
                    $aAlmacenes=[];
                    foreach ($pdo->query($sql) as $row) {
                      $aAlmacenes[$row["id"]]=$row["almacen"];
                    }
                    //var_dump($aAlmacenes);

                    /*$sql = "SELECT msc.id AS id_motivo,msc.motivo,tp.id AS id_tipo_motivo,tp.nombre AS tipo_motivo FROM motivos_salidas_caja msc INNER JOIN tipos_motivos tp ON msc.id_tipo_motivo=tp.id";
                    //echo $sql;
                    $aBalance=[];
                    $aMotivos=[];
                    foreach ($pdo->query($sql) as $row) {
                      $aMotivos[$row["id_motivo"]]=[
                        "motivo"=>$row["motivo"],
                        "id_tipo_motivo"=>$row["id_tipo_motivo"],
                        "tipo_motivo"=>$row["tipo_motivo"]
                      ];
                      foreach ($aAlmacenes as $id => $almacen) {
                        $aBalance[$id][$row["id_motivo"]]=[
                          "id_tipo_motivo"=>$row["id_tipo_motivo"],
                          "ingresos"=>0,
                          "egresos"=>0,
                        ];
                      }
                    }*/
                    $sql = "SELECT tp.id AS id_tipo_motivo,tp.nombre AS tipo_motivo FROM tipos_motivos tp";
                    //echo $sql;
                    $aBalance=[];
                    $aTipoMotivos=[0=>"Ventas"];
                    foreach ($pdo->query($sql) as $row) {
                      $aTipoMotivos[$row["id_tipo_motivo"]]=$row["tipo_motivo"];
                    }
                    
                    foreach ($aTipoMotivos as $id_tipo_motivo => $tipo_motivo) {
                      foreach ($aAlmacenes as $id => $almacen) {
                        $aBalance[$id][$id_tipo_motivo]=[
                          //"id_tipo_motivo"=>$row["id_tipo_motivo"],
                          "ingresos"=>rand(0, 150000),//0,
                          "egresos"=>rand(0, 150000),//0,
                        ];
                      }
                    }
                    
                    //var_dump($aBalance);?>
                    <div class="row">
                    <?php
                            foreach($aAlmacenes as $id => $almacen){
                              $sumaIngresos = 0;
                              $sumaEgresos = 0;
                              foreach ($aBalance[$id] as $registro) {
                                  //foreach ($registro as $item) {
                                      $sumaIngresos += $registro['ingresos'];
                                      $sumaEgresos += $registro['egresos'];
                                  //}
                              }?>
                              <div class="col-xl-6 col-md-12">
                                <div class="card o-hidden">
                                  <div class="bar-chart-widget">
                                    <div class="top-content bg-primary">
                                      <div class="row">
                                        <div class="col-7">
                                          <div class="bar-chart card-body pb-0" id="chart-bar-widget-first"></div>
                                        </div>
                                        <div class="col-5">
                                          <div class="earning-details">
                                            <div class="text-left"><span><?=$almacen?></span>
                                              <h4 class="mt-1 num mb-0">$ <span class="counter"><?=number_format(($sumaIngresos-$sumaEgresos),2)?></span></h4>
                                            </div><i class="icon-announcement"></i>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="bottom-content card-body">
                                      <div class="row">
                                        <div class="col-6 cardClickeable btnIngresos" data-id-almacen="<?$id?>">
                                          <div>
                                            <!-- <span class="num font-primary">12%<i class="icon-angle-up f-12 ml-1"></i></span> -->
                                            <span class="text-muted mt-2 mb-2 block-bottom">Ingresos</span>
                                            <h4 class="num m-0">$<span class="counter color-bottom"><?=number_format($sumaIngresos,2)?></span></h4>
                                          </div>
                                        </div>
                                        <div class="col-6 cardClickeable btnEgresos" data-id-almacen="<?$id?>">
                                          <div>
                                            <!-- <span class="num font-primary">15%<i class="icon-angle-up f-12 ml-1"></i></span> -->
                                            <span class="text-muted mt-2 mb-2 block-bottom">Egresos</span>
                                            <h4 class="num m-0">$<span class="counter color-bottom"><?=number_format($sumaEgresos,2)?></span></h4>
                                          </div>
                                        </div>
                                        <!-- <div class="col-4">
                                          <div><span class="num font-primary">34%<i class="icon-angle-up f-12 ml-1"></i></span><span class="text-muted mt-2 mb-2 block-bottom">Advertise</span>
                                            <h4 class="num m-0">$<span class="counter color-bottom"> 4219</span></h4>
                                          </div>
                                        </div> -->
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <?php
                              /*echo "<th class='ing_eg'>$".number_format($sumaIngresos,2)."</th>";
                              echo "<th class='ing_eg'>$".number_format($sumaEgresos,2)."</th>";
                              echo "<th class='saldo'>$".number_format(($sumaIngresos-$sumaEgresos),2)."</th>";*/
                            }?>
                    </div>
                    <div class="dt-ext table-responsive d-none">
                      <table id="balance" class="table display nowrap table-bordered">
                        <thead style="background-color: antiquewhite;">
                          <tr>
                            <th rowspan="3" style='text-align: center;vertical-align: middle;'>Tipos de Motivo</th><?php
                            foreach($aAlmacenes as $id => $almacen){
                              echo "<th colspan='3' style='text-align:center'>$almacen</th>";
                            }?>
                          </tr>
                          <tr><?php
                            foreach($aAlmacenes as $id => $almacen){
                              echo "<th style='text-align:center'>Ingresos</th>";
                              echo "<th style='text-align:center'>Egresos</th>";
                              echo "<th style='text-align:center'>Saldo</th>";
                            }?>
                          </tr>
                          <tr><?php
                            foreach($aAlmacenes as $id => $almacen){
                              $sumaIngresos = 0;
                              $sumaEgresos = 0;
                              foreach ($aBalance[$id] as $registro) {
                                  //foreach ($registro as $item) {
                                      $sumaIngresos += $registro['ingresos'];
                                      $sumaEgresos += $registro['egresos'];
                                  //}
                              }
                              echo "<th class='ing_eg'>$".number_format($sumaIngresos,2)."</th>";
                              echo "<th class='ing_eg'>$".number_format($sumaEgresos,2)."</th>";
                              echo "<th class='saldo'>$".number_format(($sumaIngresos-$sumaEgresos),2)."</th>";
                            }?>
                          </tr>
                        </thead>
                        <tbody><?php
                            foreach($aTipoMotivos as $id_tipo_motivo => $tipo_motivo){
                              echo "<tr>";
                              echo "<td>".$tipo_motivo."</td>";
                              foreach ($aAlmacenes as $id => $almacen) {
                                $ingresos=$aBalance[$id][$id_tipo_motivo]["ingresos"];
                                $egresos=$aBalance[$id][$id_tipo_motivo]["egresos"];
                                echo "<td class='ing_eg'>$".number_format($ingresos,2)."</td>";
                                echo "<td class='ing_eg'>$".number_format($egresos,2)."</td>";
                                echo "<td class='saldo'>$".number_format(($ingresos-$egresos),2)."</td>";
                              }
                              echo "</tr>";
                            }?>
                        </tbody>
                      </table>
                    </div>


                    <div class="d-none">
                    <?php

                    //var_dump($aMotivos);
                    //var_dump($aBalance);

                    $saldoTotal=0;
                    //obtenemos las ventas
                    $sql = " SELECT a.almacen, date_format(v.fecha_hora,'%d/%m/%Y') AS fecha_formatted,v.fecha_hora,SUM(v.total_con_descuento) AS monto,v.tipo_comprobante,v.id_forma_pago FROM ventas v inner join almacenes a on a.id = v.id_almacen WHERE v.anulada = 0 GROUP BY v.id_almacen, DATE(fecha_hora)";
                    //echo $sql;
                    $sumaVentas=0;
                    foreach ($pdo->query($sql) as $row) {
                      $monto=$row["monto"];
                      if(in_array($row["tipo_comprobante"],["NCA","NCB"])){
                        $monto*=-1;
                      }
                      $sumaVentas+=$monto;
                      $aCaja[$row["almacen"]][]=[
                        "fecha"=>$row["fecha_formatted"],
                        "motivo"=>"Ventas",
                        "monto"=>$monto,
                      ];
                    }

                    $saldoTotal+=$sumaVentas;
                    echo "Ventas: $".number_format($sumaVentas,2)."<br>";

                    //obtenemos las pagos a proveedoras
                    $sql = " SELECT p.id_proveedor,date_format(v.fecha_hora,'%d/%m/%Y') AS fecha_formatted,CONCAT(apellido,' ',nombre) AS proveedor,SUM(deuda_proveedor) AS suma_deuda_proveedor,GROUP_CONCAT('+',vd.cantidad,' ',p.descripcion,': $',FORMAT(vd.deuda_proveedor,2,'de_DE') SEPARATOR '<br>') AS detalle_productos,a.almacen FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id INNER JOIN productos p ON vd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id INNER JOIN almacenes a ON vd.id_almacen=a.id INNER JOIN forma_pago fp ON vd.id_forma_pago=fp.id WHERE pagado=1 GROUP BY vd.id_almacen,p.id_proveedor, DATE(vd.fecha_hora_pago)";
                    //echo $sql;
                    $sumaPagoProveedores=0;
                    foreach ($pdo->query($sql) as $row) {
                      $sumaPagoProveedores+=$row["suma_deuda_proveedor"];
                      $aCaja[$row["almacen"]][]=[
                        "id_proveedor"=>$row["id_proveedor"],
                        "fecha"=>$row["fecha_formatted"],
                        //"detalle"=>"Pago a proveedores: ".$row["proveedor"]."",
                        "motivo"=>"Pago a proveedores",
                        "detalle"=>$row["proveedor"],
                        "monto"=>$row["suma_deuda_proveedor"],
                      ];
                    }

                    $saldoTotal-=$sumaPagoProveedores;
                    echo "Pagos a proveedores: $".number_format($sumaPagoProveedores,2)."<br>";

                    //obtenemos los movimientos de las cajas segun si son ingresos o egresos los mostramos en crédio o débito
                    $sql = " SELECT mc.id AS id_movimiento,a.almacen, date_format(mc.fecha_hora,'%d/%m/%Y') AS fecha_formatted,mc.fecha_hora,mc.monto AS total,msc.motivo,mc.detalle,mc.tipo_movimiento FROM movimientos_caja mc inner join almacenes a on a.id = mc.id_almacen_corresponde INNER JOIN motivos_salidas_caja msc ON mc.id_motivo=msc.id WHERE anulado=0";//GROUP BY DATE(fecha_hora),mc.id_almacen_corresponde
                    //echo $sql;
                    $sumaMovimientos=0;
                    foreach ($pdo->query($sql) as $row) {

                      $monto=$row["total"];
                      if($row["tipo_movimiento"]=="Ingreso"){
                        $monto*=-1;
                      }
                      $sumaMovimientos+=$monto;
                      $aCaja[$row["almacen"]][]=[
                        "id_movimiento"=>$row["id_movimiento"],
                        "fecha_hora"=>$row["fecha_formatted"],
                        "motivo"=>$row["motivo"],
                        "detalle"=>$row["detalle"],
                        "monto"=>$monto,
                        "detalle_productos"=>$row["motivo"].": ".$row["detalle"],
                      ];
                    }

                    $saldoTotal+=$sumaMovimientos;

                    echo "Movimientos: $".number_format($sumaMovimientos,2)."<br>";
                    echo "Saldo total: $".number_format($saldoTotal,2)."<br>";

                    //var_dump($aCaja);

                    /*$sql = " SELECT a.almacen, date_format(v.fecha_hora,'%d/%m/%Y') AS fecha, fp.forma_pago,SUM(v.total_con_descuento) FROM ventas v inner join almacenes a on a.id = v.id_almacen inner join forma_pago fp on fp.id = v.id_forma_pago WHERE anulada=0 GROUP BY a.id,fp.id,date(v.fecha_hora)";
                    //echo $sql;
                    foreach ($pdo->query($sql) as $row) {
                      var_dump($row);
                    }*/

                    $_GET["desde"]="2023-04-01";
                    $_GET["hasta"]="2023-04-22";
                    $_GET["forma_pago"]="";
                    $_GET["id_almacen"]="";
                    $_GET["motivo"]="";
                    
                    ?>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Zero Configuration  Ends-->
              <!-- Feature Unable /Disable Order Starts-->
            </div>
          </div>
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
        <?php include("footer.php"); ?>
      </div>
    </div>
    <!-- latest jquery-->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap js-->
    <script src="assets/js/bootstrap/popper.min.js"></script>
    <script src="assets/js/bootstrap/bootstrap.js"></script>
    <!-- feather icon js-->
    <script src="assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="assets/js/icons/feather-icon/feather-icon.js"></script>
    <!-- Sidebar jquery-->
    <script src="assets/js/sidebar-menu.js"></script>
    <script src="assets/js/config.js"></script>
    <!-- Plugins JS start-->
    <script src="assets/js/datatable/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.buttons.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/jszip.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.colVis.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/pdfmake.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/vfs_fonts.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.autoFill.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.select.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.html5.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.print.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.responsive.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/responsive.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.keyTable.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.colReorder.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.fixedHeader.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.rowReorder.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.scroller.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/custom.js"></script>
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
	  <script>
      getCaja()
      function getCaja(){
        let desde=$("#desde").val();
        let hasta=$("#hasta").val();
        let forma_pago=$("#forma_pago").val();
        let motivo=$("#id_motivo").val();
        let id_almacen=$("#id_almacen").val();

        desde="2023-04-01"
        hasta="2023-04-22"
        forma_pago=""
        motivo=""
        id_almacen=""
        
        /*$.get("listarCajaChicaGetData.php", { desde: desde,hasta: hasta,forma_pago: forma_pago,motivo: motivo,id_almacen: id_almacen}, function(data, status){
          console.log("Data: " + data + "\nStatus: " + status);
        });*/
      }
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>