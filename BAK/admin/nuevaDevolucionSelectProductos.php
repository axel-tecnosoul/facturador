<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}

//$desde=date("Y-m-d");
$desde = date("Y-m-d", strtotime("last month"));
$filtroDesde="";
if(isset($_GET["d"]) and $_GET["d"]!=""){
  $desde=$_GET["d"];
  //$filtroDesde=" AND DATE(vd.fecha_hora_pago)>='".$desde."'";
  $filtroDesde=" AND DATE(fecha_hora_pago)>='".$desde."'";
}
$hasta=date("Y-m-d");
//$hasta=date("Y-m-t",strtotime(date("Y-m-d")." -1 month"));
//$filtroHasta="";
if(isset($_GET["h"]) and $_GET["h"]!=""){
  $hasta=$_GET["h"];
}
//$filtroHasta=" AND DATE(vd.fecha_hora_pago)<='".$hasta."'";
$filtroHasta=" AND DATE(fecha_hora_pago)<='".$hasta."'";

$id_proveedor=0;
$filtroProveedor="";
if(isset($_GET["p"]) and $_GET["p"]!=0){
  $id_proveedor=$_GET["p"];
  $filtroProveedor=" AND pr.id=".$id_proveedor;
}
$id_almacen=0;
$filtroAlmacen="";
if(isset($_GET["a"]) and $_GET["a"]!=0){
  $id_almacen=$_GET["a"];
  $filtroAlmacen=" AND a.id=".$id_almacen;
}

$id_categoria=0;
$filtroCategoria="";
if(isset($_GET["c"]) and $_GET["c"]!=0){
  $id_categoria=$_GET["c"];
  $filtroCategoria=" AND c.id=".$id_categoria;
}
include 'database.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_tables.php');?>
    <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select-1.13.14/dist/css/bootstrap-select.min.css">
    <style>
      .select2-container{
        border: 1px solid #ccc;
        border-radius: 5px;
      }
      .multiselect{
        color:#212529 !important;
        background-color:#fff;
        border-color:#ccc;
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
                      <li class="breadcrumb-item">Productos Vendidos aptos para devolucion</li>
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
                    <h5>
                      Productos Vendidos aptos para devolucion
                      <button title="Continuar con la devolucion" class="btn btn-link btn-lg p-0 px-2 border" style="color:#05093f" id="btnContinuarDevolucion"><i class='fa fa-arrow-circle-right' aria-hidden='true'></i></button>
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <table class="table">
                        <tr>
                          <td class="text-right border-0 p-1">Desde: </td>
                          <td class="border-0 p-1"><input type="date" name="desde" id="desde" value="<?=$desde?>" class="form-control form-control-sm filtraTabla"></td>
                          <!-- <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Proveedores:</td> -->
                          <td rowspan="2" style="vertical-align: middle;width:18%" class="border-0 p-1">
                            <label style="margin-left: .5rem;" for="id_proveedor">Proveedores:</label><br>
                            <select id="id_proveedor" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-live-search="true" data-selected-text-format="count > 1" data-actions-box="true" multiple><?php
                              $pdo = Database::connect();
                              $whereAlmacen="";
                              if ($_SESSION['user']['id_perfil'] == 2) {
                                $whereAlmacen= " AND pr.id_almacen = ".$_SESSION['user']['id_almacen']; 
                              }
                              $sql = "SELECT pr.id, CONCAT(pr.apellido, ' ', pr.nombre) AS proveedor, pr.id_almacen
                              FROM proveedores pr 
                              WHERE EXISTS (
                                SELECT 1
                                FROM ventas_detalle vd 
                                INNER JOIN ventas v ON vd.id_venta = v.id 
                                INNER JOIN productos p ON vd.id_producto = p.id 
                                WHERE p.id_proveedor = pr.id AND v.anulada = 0 AND vd.pagado = 0 $whereAlmacen
                              )
                              OR EXISTS (
                                SELECT 1
                                FROM canjes_detalle cd 
                                INNER JOIN canjes c ON cd.id_canje = c.id 
                                INNER JOIN productos p ON cd.id_producto = p.id 
                                WHERE p.id_proveedor = pr.id AND c.anulado = 0 AND cd.pagado = 0 $whereAlmacen
                              )";
                              foreach ($pdo->query($sql) as $row) {
                                $selected="";
                                if($row["id"]==$id_proveedor){
                                  $selected="selected";
                                }?>
                                <option value="<?=$row["id"]?>" <?=$selected?>><?="(".$row["id"].") ".$row["proveedor"]?></option><?php
                              }
                              Database::disconnect();?>
                            </select>
                          </td>
                          <td rowspan="2" style="vertical-align: middle;width:18%" class="border-0 p-1">
                            <label style="margin-left: .5rem;" for="id_categoria">Categoria:</label><br>
                            <!-- <select name="id_categoria" id="id_categoria" class="js-example-basic-single w-100 filtraTabla">
                              <option value="0">- Seleccione -</option><?php
                              /*$pdo = Database::connect();
                              $sql = "SELECT id,categoria FROM categorias WHERE 1";
                              //echo $sql;
                              foreach ($pdo->query($sql) as $row) {
                                $selected="";
                                if($row["id"]==$id_categoria){
                                  $selected="selected";
                                }?>
                                <option value="<?=$row["id"]?>" <?=$selected?>><?=$row["categoria"]?></option><?php
                              }
                              Database::disconnect();*/?>
                            </select> -->
                            <select id="id_categoria" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-live-search="true" data-selected-text-format="count > 1" data-actions-box="true" multiple><?php
                              $pdo = Database::connect();
                              $sql = "SELECT id,categoria FROM categorias WHERE 1";
                              foreach ($pdo->query($sql) as $row) {
                                $selected="";
                                if($row["id"]==$id_categoria){
                                  $selected="selected";
                                }?>
                                <option value="<?=$row["id"]?>" <?=$selected?>><?=$row["categoria"]?></option><?php
                              }
                              Database::disconnect();?>
                            </select>
                          </td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1"><?php
                            if ($_SESSION['user']['id_perfil'] == 1) {?>
                              <label style="margin-left: .5rem;" for="id_almacen">Almacen:</label><br>
                              <select id="id_almacen" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect">
                                <option value="0">- Todos -</option><?php
                                $pdo = Database::connect();
                                $sql = " SELECT id, almacen FROM almacenes";
                                foreach ($pdo->query($sql) as $row) {
                                  $selected="";
                                  if($row["id"]==$id_almacen){
                                    $selected="selected";
                                  }?>
                                  <option value="<?=$row["id"]?>" <?=$selected?>><?=$row["almacen"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select><?php
                            }else{?>
                              <input type="hidden" id="id_almacen" value="<?=$_SESSION['user']['id_almacen']?>"><?php
                            }?>
                          </td>
                          
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                            <label class="d-block" for="checkbox-ventas">
                              <input class="checkbox_animated filtraTabla" value="Ventas" checked required id="checkbox-ventas" type="checkbox" name="tipo_venta[]">
                              <label for="checkbox-ventas">Ventas</label>
                            </label>

                            <label class="d-block" for="checkbox-canjes">
                              <input class="checkbox_animated filtraTabla" value="Canjes" checked required id="checkbox-canjes" type="checkbox" name="tipo_venta[]">
                              <label for="checkbox-canjes">Canjes</label>
                            </label>
                          </td>
                          <td colspan="2" class="text-center border-0 p-1"><button class="btn btn-primary btnFiltrar">Filtrar</button></td>
                          <!-- <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Total a devolver: </td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1" id="total_a_devolver">$ 0,00</td> -->
                        </tr>
                        <tr>
                          <td class="text-right border-0 p-1">Hasta: </td>
                          <td class="border-0 p-1"><input type="date" name="hasta" id="hasta" value="<?=$hasta?>" class="form-control form-control-sm filtraTabla"></td>
                          <td style="vertical-align: middle;" class="text-right border-0 p-1">Total a devolver: </td>
                          <td style="vertical-align: middle;" class="border-0 p-1" id="total_a_devolver">$ 0,00</td>
                        </tr>
                      </table>
                    </div>
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th></th>
                            <th>ID</th>
                            <!-- <th>Operacion</th> -->
                            <th>Fecha/Hora</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Proveedor</th>
                            <th>Precio</th>
                            <th>Almacen</th>
                            <th>Cantidad</th>
                            <!-- <th>Categoría</th> -->
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
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

    <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/dist/js/bootstrap-select.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/js/i18n/defaults-es_ES.js"></script>
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

      var filasSeleccionadas = [];
      var totalDevolver=0;

      $(document).ready(function() {
        getVentas();
        //$(".filtraTabla").on("change",getVentas);
        $(".btnFiltrar").on("click",getVentas);
        
        /*$(".check-row").on("click",function(){
          console.log(this);
          calcular_total_seleccionado()
        })*/
      });

      $("#btnContinuarDevolucion").on("click",function(e){
        e.preventDefault();
        if(filasSeleccionadas.length<=0){
        //if ($('.customer-selector:checked').length < 1) {
          alert("Debe seleccionar un producto como mínimo");
        } else {
          var arr = [];
          if($('#selectAll').prop("checked")){
            let idProductosFiltrados=$("#idProductosFiltrados");
            filasSeleccionadas=idProductosFiltrados.val();
            console.log(filasSeleccionadas);

            $("#formIdProductosFiltrados").submit();
          }else{
            $('.customer-selector:checked').each(function (i,o) { arr.push($(o).val()); });
            // Obtener los valores de la propiedad 'id' en un nuevo array
            let ids = filasSeleccionadas.map(elemento => elemento.id);

            // Utilizar join() en el nuevo array
            ids = ids.join(",");
            //filasSeleccionadas=filasSeleccionadas.join(",")
            console.log(ids);
            //window.open("etiquetarMasivo.php?id=" + arr.join(","), '_blank');
            //window.open("nuevaDevolucion.php?id=" + ids, '_blank');
            window.open("nuevaDevolucion.php?id=" + ids, '_top');
            /*console.log(arr);
            console.log(arr.join(","));*/
          }
          
        }
      })

      /*function calcular_total_seleccionado(){
        let total_seleccionado=0;
        $(".check-row").each(function (index, element) {
          if(this.checked){
            total_seleccionado+=parseFloat($(this).parent().parent().find(".deuda").html());
          }
        });
        $("#total_a_devolver").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_seleccionado))
      }*/

      function getVentas(){
        let desde=$("#desde").val();
        let hasta=$("#hasta").val();
        let id_almacen=$("#id_almacen").val();
        let proveedor=$("#id_proveedor").val();
        let id_categoria=$("#id_categoria").val();
        let ventas=$("#checkbox-ventas").prop("checked")
        let canjes=$("#checkbox-canjes").prop("checked")
        //console.log("Desde: " + desde + ", Hasta: " + hasta + ", Almacen: " + id_almacen + ", Proveedor: " + proveedor);
        let id_perfil="<?=$_SESSION["user"]["id_perfil"]?>";

        let table=$('#dataTables-example666')
        table.DataTable().destroy();
        table.DataTable({ 
          processing: true,
          ajax:{url:'ajaxProductosVendidosDevolucion.php?desde='+desde+'&hasta='+hasta+'&id_almacen='+id_almacen+'&proveedor='+proveedor+'&id_categoria='+id_categoria+'&ventas='+ventas+'&canjes='+canjes,
          'dataSrc': ''},
				  stateSave: true,
				  responsive: false,
          language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
            "infoEmpty": "Mostrando 0 to 0 of 0 Registros",
            "infoFiltered": "(Filtrado de _MAX_ total registros)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No hay resultados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
          },
          "columns":[
            {"data": "input"},
            {"data": "id"},
            //{"data": "tipo"},
            {render: function(data, type, row, meta) {
              if(type=="display"){
                return row.fecha_hora_formatted;
              }else{
                return row.fecha_hora;
                //return moment(full.fecha_hora_subida).format('DD MMM YYYY HH:mm');
              }
            }},
            {"data": "codigo"},
            {
              render: function(data, type, row, meta) {
                return row.descripcion;
              },
              className: 'w-25',
            },
            {"data": "proveedor"},
            {
              render: function(data, type, row, meta) {
                if(type=="display"){
                  return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.subtotal);
                }else{
                  return row.subtotal;
                  //return moment(full.fecha_hora_subida).format('DD MMM YYYY HH:mm');
                }
              },
              className: 'dt-body-right text-right',
            },
            {"data": "almacen"},
            //{"data": "pagado"},
            /*{"data": "caja_egreso"},
            {"data": "forma_pago"},*/
            {"data": "cantidad"},
            //{"data": "categoria"}
          ],
          columnDefs: [
            { targets: [0], order: false},
            { targets: [1], type: 'datetime'},
          ],
          drawCallback: function(settings, json){
            $('[title]').tooltip();
          }
        })
      };

      $(document).on('change', '.check-row', function() {
        let id=this.value
        let monto=parseInt(this.dataset.subtotal)
        if(this.checked){
          totalDevolver+=monto
        }else{
          totalDevolver-=monto
        }
        
        $("#total_a_devolver").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(totalDevolver))

        if ($(this).is(':checked')) {
          filasSeleccionadas.push({ id: id, monto: monto });
        } else {
          var index = -1;
          for (var i = 0; i < filasSeleccionadas.length; i++) {
            if (filasSeleccionadas[i].id === id) {
              index = i;
              break;
            }
          }
          if (index > -1) {
            filasSeleccionadas.splice(index, 1);
          }
        }
        console.log(filasSeleccionadas);
      });

      $('#dataTables-example666').on('draw.dt', function() {
        $('#selectAll').prop("checked",false)
        //$('.check-row').prop('checked', false);
        //filasSeleccionadas = [];
        var checkboxes = $('.check-row');
        checkboxes.each(function() {
          var valor = $(this).val();
          //if (filasSeleccionadas.includes(valor)) {
          if (filasSeleccionadas.some(elemento => elemento.id === valor)) {
            $(this).prop('checked', true);
          } else {
            $(this).prop('checked', false);
          }
        });
      });
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>