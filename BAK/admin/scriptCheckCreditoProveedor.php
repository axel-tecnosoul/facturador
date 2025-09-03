<?php
require("config.php");
require 'database.php';
require 'funciones.php';

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT id,credito,apellido,nombre,email,clave FROM proveedores";//WHERE p.id_proveedor=667;
$q = $pdo->prepare($sql);
$q->execute();
$canje_detalle = $q->fetchAll(PDO::FETCH_ASSOC);
//echo $q->rowCount()."<br>";
$aProveedor=[];
foreach ($canje_detalle as $data){
  $aProveedor[$data['id']]=[
    "proveedora"=>$data["apellido"]." ".$data["nombre"],
    "email"=>$data["email"],
    "clave"=>$data["clave"],
    "credito_actual"=>$data["credito"],
    "credito_generado_por_ventas"=>0,
    "credito_generado_por_canjes"=>0,
    "canjes_realizados"=>0,
  ];
}

//var_dump($aProveedor);

$sql = "SELECT SUM(vd.deuda_proveedor) AS credito,p.id_proveedor FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id INNER JOIN productos p ON vd.id_producto=p.id WHERE vd.id_modalidad=50 AND v.anulada=0 AND id_venta_cbte_relacionado IS NULL GROUP BY p.id_proveedor";//WHERE p.id_proveedor=667;
$q = $pdo->prepare($sql);
$q->execute();
$canje_detalle = $q->fetchAll(PDO::FETCH_ASSOC);
//echo $q->rowCount()."<br>";
foreach ($canje_detalle as $data){
  //var_dump($data);
  $aProveedor[$data['id_proveedor']]["credito_generado_por_ventas"]+=$data['credito'];
}

$sql = "SELECT SUM(cd.deuda_proveedor) AS credito,p.id_proveedor FROM canjes_detalle cd INNER JOIN canjes v ON cd.id_canje=v.id INNER JOIN productos p ON cd.id_producto=p.id WHERE cd.id_modalidad=50 AND anulado=0 GROUP BY p.id_proveedor";//WHERE p.id_proveedor=667;
$q = $pdo->prepare($sql);
$q->execute();
$canje_detalle = $q->fetchAll(PDO::FETCH_ASSOC);
//echo $q->rowCount()."<br>";
foreach ($canje_detalle as $data){
  $aProveedor[$data['id_proveedor']]["credito_generado_por_canjes"]+=$data['credito'];
}

$sql = "SELECT SUM(c.total_con_descuento) AS credito_usado,c.id_proveedor FROM canjes c WHERE anulado=0 GROUP BY c.id_proveedor";//WHERE p.id_proveedor=667;
$q = $pdo->prepare($sql);
$q->execute();
$canje_detalle = $q->fetchAll(PDO::FETCH_ASSOC);
//echo $q->rowCount()."<br>";
foreach ($canje_detalle as $data){
  $aProveedor[$data['id_proveedor']]["canjes_realizados"]+=$data['credito_usado'];
}

$count_total=$count_iguales=$count_mayores=$count_menores=0;

$pdo = Database::disconnect();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_tables.php');?>
    <style>
      .dataTables_scrollBody{
        max-height: 87vh !important;
      }
      .a_copiar:hover{
        cursor: pointer;
        text-decoration: underline;
      }
    </style>
  </head>
  <?php //include('header.php');?>

  <div style="height: 100%;">
    <div class="dt-ext table-responsive">
      <table class="display" id="dataTables-example666" style="height: 100%;">
        <thead>
          <tr>
            <th style="text-align:center">ID Proveedora</th>
            <th style="text-align:center">Apellido y Nombre</th>
            <th style="text-align:center">Email</th>
            <th style="text-align:center">Clave</th>
            <th style="text-align:center">Credito disponible</th>
            <th style="text-align:center">Credito generado por ventas</th>
            <th style="text-align:center">Credito generado por canjes</th>
            <th style="text-align:center">Canjes realizados</th>
          </tr>
        </thead>
        <tbody><?php
          foreach($aProveedor as $id_proveedor => $data){
            if(array_sum($data)>0){
              $count_total++;
              $credito_generado=$data["credito_generado_por_ventas"]+$data["credito_generado_por_canjes"];
              $credito=$data["canjes_realizados"]+$data["credito_actual"];
          
              if($credito_generado==$credito){
                $count_iguales++;
                //echo "EL CREDITO GENERADO ES IGUAL AL USADO Y/O DISPONIBLE<br>";
              }
              if($credito_generado>$credito){
                $count_mayores++;
                //echo "EL CREDITO GENERADO ES MAYOR AL USADO Y/O DISPONIBLE<br>";
              }
              if($credito_generado<$credito){
                $count_menores++;
                //echo "EL CREDITO GENERADO ES MENOR AL USADO Y/O DISPONIBLE<br>";
                //var_dump($data); 
              }
              echo '<tr>';
              echo '<td>'.$id_proveedor.'</td>';
              echo '<td>'.$data["proveedora"].'</td>';
              echo '<td title="Click para copiar" class="a_copiar">'.$data["email"].'</td>';
              echo '<td title="Click para copiar" class="a_copiar">'.$data["clave"].'</td>';
              echo '<td style="text-align:right">$'.number_format($data["credito_actual"],2).'</td>';
              echo '<td style="text-align:right">$'.number_format($data["credito_generado_por_ventas"],2).'</td>';
              echo '<td style="text-align:right">$'.number_format($data["credito_generado_por_canjes"],2).'</td>';
              echo '<td style="text-align:right">$'.number_format($data["canjes_realizados"],2).'</td>';
              echo '</tr>';
            }
          }
          
          //var_dump($count_total,$count_iguales,$count_mayores,$count_menores);
            /*if($row["modalidad_venta"]!=$row["modalidad_proveedor"]){
              //var_dump($row);
              echo '<tr>';
              echo '<td>';
              echo $row["id"];
              echo '</td>';
              echo '<td>'. $row["id_venta"] .'</td>';
              echo '<td>'. $row["nombre"] . ' ' . $row["apellido"] . '</td>';
              echo '<td>'. $row["modalidad_proveedor"] . '</td>';
              echo '<td>('.$row["codigo"].') '.$row["descripcion"].'</td>';
              echo '<td>'. $row["modalidad_venta"] . '</td>';
              echo '<td style="text-align:right">'. number_format($row["deuda_proveedor"],2) . '</td>';
              //echo '<td style="text-align:right">'. number_format($row["deuda_total"],2) . '</td>';
              echo '</tr>';
            }*/?>
        </tbody>
      </table>
    </div>
  </div>
</html>

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
  $(document).ready(function() {

    $('#dataTables-example666').DataTable({
      stateSave: true,
      //dom: '<"#total_pagos_seleccionados.mr-2 d-inline"l>frtip',
      dom: 'lrtip',
      //responsive: true,
      paginate: false,
      scrollY: '100vh',
      scrollCollapse: true,
      "columnDefs": [{
        "targets": [0],
        "searchable": false,
        "orderable": false,
      }],
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

    });

    $('.a_copiar').on("click",function(){
      //var contenido = document.getElementById('contenido-a-copiar');
      console.log(this);
      //var contenido = this.innerText;
      var contenido = this;
      console.log(contenido);
      var seleccion = window.getSelection();
      var rango = document.createRange();
      rango.selectNodeContents(contenido);
      seleccion.removeAllRanges();
      seleccion.addRange(rango);
      document.execCommand('copy');
      seleccion.removeAllRanges();
    })

});

</script>