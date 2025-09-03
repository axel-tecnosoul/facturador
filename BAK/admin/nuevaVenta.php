<?php
ini_set("display_errors",1);
ini_set("display_startup_errors",1);
error_reporting(E_ALL);
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
require 'funciones.php';

if ( !empty($_POST)) {
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $modoDebug=0;

  if ($modoDebug==1) {
    $pdo->beginTransaction();
    var_dump($_POST);
  }

  $nombre_cliente=($_POST['nombre_cliente']) ?: "";
  $dni=($_POST['dni']) ?: "";
  $direccion=($_POST['direccion']) ?: "";
  $email=($_POST['email']) ?: "";
  $telefono=($_POST['telefono']) ?: "";
  $tipo_comprobante=$_POST["tipo_comprobante"];
  $id_almacen=$_POST['id_almacen'];
  $id_usuario=$_SESSION['user']['id'];
  $id_forma_pago=$_POST['id_forma_pago'];
  $modalidad_venta=$_POST["modalidad_venta"];

  $total=0;
  foreach ($_POST['id_stock'] as $key => $id_stock) {
    $total+=($_POST['cantidad'][$key]*$_POST['precio'][$key]);
  }

  // Verificar si ya existe un registro similar
  //$sqlCheck = "SELECT COUNT(*) FROM ventas WHERE DATE_FORMAT(fecha_hora, '%Y-%m-%d %H:%i') = DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i') AND nombre_cliente = ? AND dni = ? AND direccion = ? AND email = ? AND telefono = ? AND id_almacen = ? AND total = ? AND tipo_comprobante = ? AND id_usuario = ? AND id_forma_pago = ? AND modalidad_venta = ?";
  $sqlCheck = "SELECT COUNT(*) FROM ventas WHERE fecha_hora >= NOW() - INTERVAL 60 SECOND AND nombre_cliente = ? AND dni = ? AND direccion = ? AND email = ? AND telefono = ? AND id_almacen = ? AND total = ? AND tipo_comprobante = ? AND id_usuario = ? AND id_forma_pago = ? AND modalidad_venta = ?";
  $qCheck = $pdo->prepare($sqlCheck);
  $qCheck->execute(array($nombre_cliente,$dni,$direccion,$email,$telefono,$id_almacen,$total,$tipo_comprobante,$id_usuario,$id_forma_pago,$modalidad_venta));
  $count = $qCheck->fetchColumn();

  if ($count == 0) {
  
    $sql = "INSERT INTO ventas(fecha_hora, nombre_cliente, dni, direccion, email, telefono, id_almacen, total, tipo_comprobante, id_usuario, id_forma_pago, modalidad_venta) VALUES (now(),?,?,?,?,?,?,?,?,?,?,?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($nombre_cliente,$dni,$direccion,$email,$telefono,$id_almacen,$total,$tipo_comprobante,$id_usuario,$id_forma_pago,$modalidad_venta));
    $idVenta = $pdo->lastInsertId();

    if ($modoDebug==1) {
      $q->debugDumpParams();
      echo "<br><br>Afe: ".$q->rowCount();
      echo "<br><br>";
    }
    
    $cantPrendas = count($_POST["id_producto"]);

    $minimo_compra="";
    $monto_fijo="";
    $porcentaje="";
    $minimo_cantidad_prendas="";
    if (!empty($_POST['id_descuento'])) {
      $sql2 = "SELECT minimo_compra, minimo_cantidad_prendas, monto_fijo, porcentaje FROM descuentos WHERE id = ? ";
      $q2 = $pdo->prepare($sql2);
      $q2->execute(array($_POST['id_descuento']));
      $data2 = $q2->fetch(PDO::FETCH_ASSOC);

      $minimo_compra=$data2['minimo_compra'];
      //$monto_fijo=$data2['monto_fijo'];
      $porcentaje=$data2['porcentaje'];
      $minimo_cantidad_prendas=$data2['minimo_cantidad_prendas'];

      if ($modoDebug==1) {
        $q2->debugDumpParams();
        echo "<br><br>Afe: ".$q2->rowCount();
        echo "<br><br>";
      }

    }

    //var_dump($total);

    $totalConDescuento = 0;
    foreach ($_POST['id_stock'] as $key => $id_stock) {
      
      //$idProducto = $row[8];
      $cantidad = $_POST['cantidad'][$key];
      $cantidadAnterior = $_POST['stock'][$key];
      $precio = $_POST['precio'][$key];
      $modalidad = $_POST["id_modalidad"][$key];
      $idProveedor = $_POST["id_proveedor"][$key];
      $id_producto = $_POST["id_producto"][$key];

      /*if ($_POST['id_forma_pago'] == 1) {
        $precio = $precio*0.9; //10% off por pago en efectivo
      }*/
      $subtotal = $cantidad * $precio;
      //var_dump($subtotal);
    
      if ($minimo_compra!="" and $total>$minimo_compra and $minimo_cantidad_prendas!="" and $cantPrendas>=$minimo_cantidad_prendas) {
        //$totalConDescuento = $totalConDescuento - $monto_fijo;
        $subtotal-=(($subtotal*$porcentaje)/100);
        //var_dump($subtotal);
      }
      
      $totalConDescuento += $subtotal;
      //var_dump($totalConDescuento);
      //$deuda_proveedor=0;

      $deuda_proveedor=calcularDeudaProveedor($_POST['id_forma_pago'],$modalidad,$subtotal);

      $pagado = 0;
      if($modalidad==1){
        $pagado = 1;
      }elseif($modalidad==50){
        /*$sql = "UPDATE proveedores set credito = credito + ? where id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($deuda_proveedor,$idProveedor));*/
      }
      
      $sql = "INSERT INTO ventas_detalle (id_venta, id_producto, cantidad, precio, subtotal, id_modalidad, deuda_proveedor, pagado) VALUES (?,?,?,?,?,?,?,?)";
      $q = $pdo->prepare($sql);
      //$q->execute(array($idVenta,$id_producto,$cantidad,$precio,$subtotal,$modalidad,$pagado));
      $q->execute(array($idVenta,$id_producto,$cantidad,$_POST['precio'][$key],$subtotal,$modalidad,$deuda_proveedor,$pagado));

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }
      
      $sql3 = "UPDATE stock set cantidad = cantidad - ? where id = ?";
      $q3 = $pdo->prepare($sql3);
      $q3->execute(array($cantidad,$id_stock));

      if ($modoDebug==1) {
        $q3->debugDumpParams();
        echo "<br><br>Afe: ".$q3->rowCount();
        echo "<br><br>";
      }
      
      if ($cantidadAnterior == $cantidad) {
        $sql3 = "DELETE from stock where id = ?";
        $q3 = $pdo->prepare($sql3);
        $q3->execute(array($id_stock));

        if ($modoDebug==1) {
          $q3->debugDumpParams();
          echo "<br><br>Afe: ".$q3->rowCount();
          echo "<br><br>";
        }
      }
      $cantPrendas++;
    }

    $id_descuento=NULL;
    if(isset($_POST['id_descuento']) and $_POST['id_descuento']!=""){
      $id_descuento=$_POST['id_descuento'];
    }

    $sql = "UPDATE ventas set total = ?, id_descuento_aplicado = ?, total_con_descuento = ? WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($total,$id_descuento,$totalConDescuento,$idVenta));

    if ($modoDebug==1) {
      $q->debugDumpParams();
      echo "<br><br>Afe: ".$q->rowCount();
      echo "<br><br>";
    }
    
    if($tipo_comprobante!="R"){

      include './../external/afip/Afip.php';

      /*$cuit=30717754200;
      $produccion=true;
      if ($modoDebug==1) {
        $cuit=20351290340;
        $produccion=false;
      }

      //$afip = new Afip(array('CUIT' => 20351290340,$production=true));
      $afip = new Afip(array('CUIT' => $cuit,'production'=>$produccion));*/

      include 'config_facturacion_electronica.php';//poner $homologacion=1 para facturar en modo homologacion. Retorna $aInitializeAFIP.
      $afip = new Afip($aInitializeAFIP);

      $sql4 = "SELECT punto_venta FROM almacenes WHERE id = ? ";
      $q4 = $pdo->prepare($sql4);
      $q4->execute(array($_POST['id_almacen']));
      $data4 = $q4->fetch(PDO::FETCH_ASSOC);

      if ($modoDebug==1) {
        $q4->debugDumpParams();
        echo "<br><br>Afe: ".$q4->rowCount();
        echo "<br><br>";
      }
      $punto_venta=$data4["punto_venta"];

      $server_status = $afip->ElectronicBilling->GetServerStatus();
      /*echo 'Este es el estado del servidor:';
      var_dump($server_status);*/

      $ImpTotal=$totalConDescuento;
      //$total=121;
      if($tipo_comprobante=="A"){
        $tipo_comprobante=1;//1 -> Factura A
        //$DocTipo=80;
        //$DocNro=$_POST["dni"];

        $ImpNeto=$ImpTotal/1.21;
        $ImpIVA=$ImpTotal-$ImpNeto;
      }elseif($tipo_comprobante=="B"){
        $tipo_comprobante=6;//6 -> Factura B
        //$DocTipo=99;
        //$DocNro=0;
        
        $ImpNeto=$ImpTotal/1.21;
        $ImpIVA=$ImpTotal-$ImpNeto;
      }
      $DocTipo=99;
      $DocNro=0;
      //$_POST["dni"]="33216897";
      //$_POST["cuit"]="27332168970";
      if(isset($_POST["dni"]) and $_POST["dni"]!=""){
        $DocNro=$_POST["dni"];
        $DocTipo=96;
      }
      if(isset($_POST["cuit"]) and $_POST["cuit"]!=""){
        $DocNro=$_POST["cuit"];
        $DocTipo=80;
      }
      
      $ImpNeto=number_format($ImpNeto,2,".","");
      $ImpIVA=number_format($ImpIVA,2,".","");
      

      $data = array(
        'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
        'PtoVta' 	=> $punto_venta,  // Punto de venta
        'CbteTipo' 	=> $tipo_comprobante,  // Tipo de comprobante (ver tipos disponibles) 
        'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
        'DocTipo' 	=> $DocTipo, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles). Para comprobantes clase A y M el campo DocTipo debe ser igual a 80 (CUIT)
        'DocNro' 	=> $DocNro,  // Número de documento del comprador (0 consumidor final)
        'CbteDesde' 	=> 2,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
        'CbteHasta' 	=> 2,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
        'CbteFch' 	=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
        'ImpTotal' 	=> $ImpTotal,//121, // Importe total del comprobante
        'ImpTotConc' 	=> 0,   // Importe neto no gravado
        'ImpNeto' 	=> $ImpNeto,//100, // Importe neto gravado
        'ImpOpEx' 	=> 0,   // Importe exento de IVA
        'ImpIVA' 	=> $ImpIVA,//21,  //Importe total de IVA
        'ImpTrib' 	=> 0,   //Importe total de tributos
        'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
        'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
        'Iva' 		=> array( // (Opcional) Alícuotas asociadas al comprobante
          array(
            'Id' 		=> 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
            'BaseImp' 	=> $ImpNeto,//100, // Base imponible -> ES IGUAL A ImpNeto?
            'Importe' 	=> $ImpIVA,//21 // Importe -> ES IGUAL A ImpIVA?
          )
        ), 
      );

      if ($modoDebug==1) {
        var_dump($data);
        
      }
      
      //$res = $afip->ElectronicBilling->CreateVoucher($data);
      $res = $afip->ElectronicBilling->CreateNextVoucher($data);
      
      $estado="E";
      if(isset($res['CAE'])){
        $estado="A";
        $CAE=$res['CAE'];//CAE asignado el comprobante
        $CAEFchVto=$res['CAEFchVto'];//Fecha de vencimiento del CAE (yyyy-mm-dd)
        $voucher_number=$res['voucher_number'];//Número asignado al comprobante
        //var_dump($res);
      }
      
      if ($modoDebug==1) {
        var_dump($res);
        var_dump($CAE);
        var_dump($CAEFchVto);
        var_dump($voucher_number);
      }

      $sql = "UPDATE ventas SET tipo_doc = ?, estado = ?, punto_venta = ?, numero_comprobante = ?, cae = ?, fecha_vencimiento_cae = ? WHERE id = ?";
      $q = $pdo->prepare($sql);
      $q->execute(array($DocTipo,$estado,$punto_venta,$voucher_number,$CAE,$CAEFchVto,$idVenta));

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }
    
    }

  }

  if ($modoDebug==1) {
    $pdo->rollBack();
    die();
  }
  Database::disconnect();
  
  header("Location: listarVentas.php");
}
$id_perfil=$_SESSION["user"]["id_perfil"];?>
<!DOCTYPE html>
<html lang="en">
  <head><?php
    include('head_forms.php');?>
    <link rel="stylesheet" type="text/css" href="assets/css/datatables.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
    <style>
      .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0;}
    </style>
  </head>
  <body class="light-only">
    <!-- Loader ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper"><?php
      include('header.php');?>
      <!-- Page Header Start-->
      <div class="page-body-wrapper"><?php
        include('menu.php');?>
        <!-- Page Sidebar Start-->
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
                      <li class="breadcrumb-item">Nueva Venta</li>
                    </ol>
                  </div>
                </div>
                <!-- Bookmark Start-->
                <div class="col-2">
                  <div class="bookmark pull-right">
                    <ul>
                      <li><a target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?php echo date('d-m-Y');?>"><i data-feather="calendar"></i></a></li>
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
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Nueva Venta</h5>
                  </div>
                  <form class="form theme-form" role="form" method="post" action="nuevaVenta.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Modalidad de venta</label>
                            <div class="col-sm-9">
                              <label class="d-block" for="edo-ani">
                                <input class="radio_animated" value="Presencial" checked required id="edo-ani" type="radio" name="modalidad_venta"><label for="edo-ani">Presencial</label>
                              </label>
                              <label class="d-block" for="edo-ani1">
                                <input class="radio_animated" value="Online" required id="edo-ani1" type="radio" name="modalidad_venta"><label for="edo-ani1">Online</label>
                              </label>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Almacen</label>
                            <div class="col-sm-9">
                              <select name="id_almacen" id="id_almacen" class="js-example-basic-single col-sm-12" required="required" onChange="jsListarProductos(this.value);">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT id, almacen, punto_venta FROM almacenes WHERE activo = 1";
                                if ($_SESSION['user']['id_perfil'] != 1) {
                                  $sqlZon .= " and id = ".$_SESSION['user']['id_almacen']; 
                                }
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."' data-punto_venta='".$fila['punto_venta']."'";
                                  echo ">".$fila['almacen']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Productos en Stock</label>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-12" id="tabla_productos">
                              <table class="table table-sm display" id="dataTables-example666">
                                <thead>
                                  <tr>
                                    <!-- <th>ID</th> -->
                                    <th>Proveedor</th>
                                    <th>Código</th>
                                    <th>Categoría</th>
                                    <th>Descripción</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th>Accion</th>
                                    <!-- <th class="d-none">Precio</th> -->
                                  </tr>
                                </thead>
                                <tbody></tbody>
                              </table>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Productos a vender</label>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-12">
                              <table class="table" id="productos_vender">
                                <thead>
                                  <tr>
                                    <!-- <th>ID</th> -->
                                    <th>Proveedor</th>
                                    <th>Código</th>
                                    <th>Categoría</th>
                                    <th>Descripción</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <!-- <th class="d-none">Precio</th> -->
                                    <th>Eliminar</th>
                                  </tr>
                                </thead>
                                <tbody></tbody>
                              </table>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Total productos</label>
                            <div class="col-sm-9"><label id="total_productos">$ 0</label></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tipo de comprobante</label>
                            <div class="col-sm-9">
                              <select name="tipo_comprobante" id="tipo_comprobante" class="js-example-basic-single col-sm-12" required="required">
                                <option value="">Seleccione...</option>
                                <!-- <option value="A" class="cbte_only_punto_venta" disabled>Factura A</option> -->
                                <option value="B" class="cbte_only_punto_venta">Factura B</option>
                                <option value="R">Recibo</option>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Forma de Pago</label>
                            <div class="col-sm-9">
                              <select name="id_forma_pago" id="id_forma_pago" class="js-example-basic-single col-sm-12" required>
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT id, forma_pago FROM forma_pago WHERE activo = 1 ORDER BY forma_pago";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  echo ">".$fila['forma_pago']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Descuentos Vigentes</label>
                            <div class="col-sm-9">
                              <select name="id_descuento" id="id_descuento" disabled class="js-example-basic-single col-sm-12">
                                
                                <option value="">Seleccione...</option><?php
                                /*
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT d.id, d.descripcion, d.minimo_compra, d.minimo_cantidad_prendas, d.monto_fijo, d.porcentaje, dfp.id_forma_pago, f.forma_pago FROM descuentos_x_formapago dfp INNER JOIN descuentos d on d.id = dfp.id_descuento INNER JOIN forma_pago f on f.id = dfp.id_forma_pago WHERE vigencia_desde <= now() and vigencia_hasta >= now() ";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  $detalle="";
                                  if($fila['porcentaje']>0){
                                    $detalle.=" (".$fila['porcentaje']."%)";
                                  }
                                  /*if($fila['monto_fijo']>0){
                                    $detalle.=" ($".number_format($fila['monto_fijo'],0,",",".").")";
                                  }*/
                                  /*if($fila['minimo_cantidad_prendas']>0){
                                    $detalle.=" Cantidad prendas minimo: ".$fila['minimo_cantidad_prendas'];
                                  }
                                  if($fila['minimo_compra']>0){
                                    $detalle.=" Compra minima: $".number_format($fila['minimo_compra'],0,",",".");
                                  }
                                  echo "<option value='".$fila['id']."' data-porcentaje='".$fila['porcentaje'].$detalle."</option>";
                                }
                                Database::disconnect();*/?>
                                
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Total a pagar</label>
                            <div class="col-sm-9"><label id="total_a_pagar">$ 0</label></div><?php
                            $sql4 = "SELECT valor FROM parametros WHERE id = 6 ";
                            $q4 = $pdo->prepare($sql4);
                            $q4->execute();
                            $data4 = $q4->fetch(PDO::FETCH_ASSOC);?>
                            <input type="hidden" name="monto_maximo_sin_informar_dni" id="monto_maximo_sin_informar_dni" value="<?=$data4["valor"]?>">
                            <input type="hidden" id="total_a_pagar_sin_formato">
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nombre y apellido</label>
                            <div class="col-sm-9"><input name="nombre_cliente" type="text" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div id="dni_group" class="form-group row">
                            <label class="col-sm-3 col-form-label">DNI</label>
                            <div class="col-sm-9"><input name="dni" id="dni" type="text" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div id="cuit_group" class="form-group row d-none">
                            <label class="col-sm-3 col-form-label">CUIT</label>
                            <div class="col-sm-9"><input name="cuit" id="cuit" disabled type="text" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Dirección</label>
                            <div class="col-sm-9"><input name="direccion" type="text" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">E-Mail</label>
                            <div class="col-sm-9"><input name="email" type="email" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Teléfono</label>
                            <div class="col-sm-9"><input name="telefono" type="text" maxlength="99" class="form-control" value=""></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit" id="formSubmit">Crear</button>
                        <a href="listarVentas.php" class="btn btn-light">Volver</a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start--><?php 
        include("footer.php"); ?>
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
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
	  <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
	
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

	<script>
	  $(document).ready(function() {
			jsListarProductos(0)

      $("#id_almacen").on("change",function(){

        $("#tipo_comprobante").val(null).trigger('change');

        let punto_venta=parseInt($(this).find(":checked").data("punto_venta"))
        let cbte_only_punto_venta=$(".cbte_only_punto_venta")
        
        if(isNaN(punto_venta)){
          cbte_only_punto_venta.prop("disabled","disabled");
        }else{
          cbte_only_punto_venta.prop("disabled",false);
        }
        $("#tipo_comprobante").select2("destroy").select2();

        changeTipoDNI();
      })
      
      $("#id_forma_pago").on("change",function(){

          var formaPagoId = $(this).val();
          
          if (formaPagoId) {
            $.ajax({
              url: 'obtener_descuentos.php', 
              method: 'POST',
              data: { forma_pago_id: formaPagoId }, 
              dataType: 'json', 
            }).done(function(data){
              
              var descuentosSelect = $('#id_descuento');
              descuentosSelect.html("");
              var descuentos = $(data);
              
              if (descuentos.length) {
                descuentosSelect.append('<option value="">Seleccione...</option>');
                console.log(descuentos);
                $.each(descuentos, function(i, descuento) {
                  let selected=""
                  if(descuento.id==1){
                    selected="selected"
                  }
                  descuentosSelect.append('<option '+selected+' data-minimo_cantidad_prendas="'+descuento.minimo_cantidad_prendas+'" data-minimo_compra="'+descuento.minimo_compra+'" data-porcentaje="'+descuento.porcentaje+'" value="'+descuento.id+'">'+descuento.nombre+'</option>');
                });
                  
                descuentosSelect.prop('disabled', false); 
              } else {
                descuentosSelect.append('<option value="">No se encontraron descuentos</option>');
                descuentosSelect.prop('disabled', true);
              }
              actualizarMontoTotal();

              //Descuento por defecto 10%
              
            }).fail(function(data){
              console.log(data);
              alert('Error al obtener las descuentos');
              actualizarMontoTotal();
            });
          } else {
            $('#id_descuento').empty().prop('disabled', true);
            actualizarMontoTotal();
          }
          
 
        /*let habilitarOFF=0;
        if(this.value){
          //id_descuento.value=0;
          id_descuento.prop("disabled",false);
          if(id_descuento.find("option").length==2){
            habilitarOFF=1;
          }
        }else{
          //id_descuento.value=0;
          id_descuento.prop("disabled",true);
        }

        if(habilitarOFF==1){
          id_descuento.val(1).trigger('change');
          //mostrarTotalDescuento()
          actualizarMontoTotal();
        }else{
          id_descuento.val(null).trigger('change');
        }*/
      })

      $("#id_descuento").on("change",function(){
        //mostrarTotalDescuento();
        actualizarMontoTotal();
      })

      $(document).on("click",".btnAnadir",function(){
        $(this).addClass("disabled");
        let prod_anadido=$("input[name='id_stock[]'][value='"+this.dataset.id_stock+"']");
        //console.log(prod_anadido)
        //console.log("cantidad encontrada");
        if(prod_anadido.length==0){//controlamos que el producto ya no haya sido añadido
          if(parseInt(this.dataset.cantidad)>0){//controlamos que haya stock del producto
            let fila=$(this).parent().parent();
            let clon=fila.clone();
            let precio=clon.find("input.precio");
            let id_perfil="<?=$id_perfil?>";
            //console.log(id_perfil);
            if(id_perfil!=1 && precio.val()==0){//controlamos que los usuarios NO adminsitradores no puedan añadir productos sin precio
              alert("No puede añadir un producto sin precio")
            }else{//los usuarios admin pueden añadir productos sin precio pero tienen que modifcarlo
              let btn=clon.find("button");
              //console.log(btn);
              btn.parent().html(`
                <input type='hidden' name='id_stock[]' value='${this.dataset.id_stock}'></input>
                <input type='number' name='cantidad[]' class='form-control form-control-sm cantidad mx-auto' style='width: 60px;' min='1' max='${this.dataset.cantidad}' value="1" required></input>
              `);
              clon.append(`
                <td class='text-center'>
                  <img src='img/icon_baja.png' data-id_stock='${this.dataset.id_stock}' class='btnEliminar' width='24' height='25' border='0' alt='Eliminar' title='Eliminar'>
                </td>
              `);
              if(id_perfil==1 && precio.val()==0){
                precio.attr("type","number").addClass("form-control form-control-sm mx-auto").attr("style","width: 60px;");//.attr("disabled",false)
                clon.find("label.precio").remove();
              }
              /*clon.find("input[name='precio[]']").attr("disabled",false);
              clon.find("input[name='stock[]']").attr("disabled",false);*/
              clon.find(".enviar_form").attr("disabled",false);

              $("#productos_vender tbody").append(clon[0]);

              actualizarMontoTotal();
            }
          }else{
            alert("No hay stock suficiente")
          }
        }else{
          alert("El producto ya fue añadido")
        }
      })

      $("#tipo_comprobante").on("change",function(){
        changeTipoDNI();
        checkTotalPagarDNI()
      })

      $("form").on("submit",function(e){
        e.preventDefault();
        let cant_productos=$('#productos_vender tbody tr').length;
        if(cant_productos){
          //console.log("submit");
          let precio_en_cero=0;
          $("input[type='number'].precio").each(function(){
            if(this.value==0){
              precio_en_cero=1;
            }
          });
          if(precio_en_cero==1){
            alert("Tiene productos sin precio")
          }else{
            let descuento=$('#id_descuento option:selected')
            console.log(descuento);
            let minimo_cantidad_prendas=descuento.data("minimo_cantidad_prendas")
            if(minimo_cantidad_prendas!=undefined && minimo_cantidad_prendas>cant_productos){
              alert("La cantidad de productos añadidos ("+cant_productos+") no alcanza para aplicar el descuento ("+minimo_cantidad_prendas+")")
              return false
            }
            let minimo_compra=descuento.data("minimo_compra")
            let total=calcularTotalCompra();
            if(minimo_compra!=undefined && minimo_compra>total){
              alert("El monto total ("+new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total)+") no alcanza para aplicar el descuento ("+new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(minimo_compra)+")")
              return false
            }
            //console.log("submit")
            $(this).find("#formSubmit").attr("disabled",true).addClass("disabled")
            this.submit();
          }
        }else{
          alert("Añada algún producto")
        }
      });

		});

    function changeTipoDNI(){
      console.log(changeTipoDNI);
      let tipo_comprobante=$("#tipo_comprobante").val()
      if(tipo_comprobante=="A"){
        $("#dni_group").addClass("d-none")
        $("#cuit_group").removeClass("d-none")
        $("#dni").attr("disabled",true)
        $("#cuit").attr("disabled",false).attr("required",true)
      }else{
        $("#dni_group").removeClass("d-none")
        $("#cuit_group").addClass("d-none")
        $("#dni").attr("disabled",false)
        $("#cuit").attr("disabled",true).attr("required",false)
      }
    }
		
    function jsListarProductos(val) {
      $("#dataTables-example666").dataTable().fnDestroy();
      $('#dataTables-example666').DataTable({
        "ajax" : "ajaxVentas.php?almacen="+val,//&id_vehiculo="+id_vehiculo+"
				stateSave: false,
				responsive: true,
        serverSide: true,
        processing: true,
        scrollY: false,
        "columns":[
          //{"data": "cb"},//"fecha_mostrar"},
          {render: function(data, type, row, meta) {
            return `(${row.id_proveedor}) ${row.proveedor}`;
          }},
          {"data": "codigo"},//"vehiculo.marca"},
          {"data": "categoria"},//"vehiculo.modelo"},
          //{"data": "descripcion"},//"vehiculo.patente"},
          {render: function(data, type, row, meta) {
            //return row.descripcion
            return `(${row.id_modalidad}%) ${row.descripcion}`;
          }},
          {
            "data": "cantidad",
            orderDataType: "num-fmt",
            className: 'dt-body-center text-center',
          },{
            render: function(data, type, row, meta) {
              return `
                <input type="hidden" disabled name="id_modalidad[]" class="enviar_form id_modalidad" value="${row.id_modalidad}">
                <input type="hidden" disabled name="id_proveedor[]" class="enviar_form id_proveedor" value="${row.id_proveedor}">
                <input type="hidden" disabled name="id_producto[]" class="enviar_form id_producto" value="${row.id_producto}">
                <input type="hidden" disabled name="stock[]" class="enviar_form stock" value="${row.cantidad}">
                <input type="hidden" disabled name="precio[]" class="enviar_form precio" value="${row.precio}">
                <label class="precio">`+new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.precio)+`</label>`;
            },
            className: 'dt-body-right text-right',
            orderDataType: "num-fmt"
          },{
            render: function(data, type, row, meta) {
              let disabled=""
              if(row.cantidad<1){
                disabled="disabled"
              }
              return `<button type="button" class="btn btn-success btn-sm btnAnadir" ${disabled} data-id_stock="${row.id_stock}" data-cantidad="${row.cantidad}">Añadir</button>`;
            }
          }/*,{
            "data": "precio",
            className: 'd-none precio'
          }*/
        ],
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
        initComplete: function(settings, json){
          $('[title]').tooltip();
        },
      })

      var table = $("#dataTables-example666").DataTable();
      table.on( 'draw', function () {
        let filtrado=table.rows({search:'applied'}).nodes()
        let search=$('input[type=search]')
        if(search.val()!='' && filtrado.length==1){
        //if(filtrado.length==1){
          $(filtrado[0]).find("button.btnAnadir").click();
          search.select();
          /*search.val('').change();
          table.search('').draw();*/
          //search.val('').trigger('change');
          //table.search('').columns().search('').draw();
          //table.rows().nodes().draw();
        }
      });
    }

    function calcularTotalCompra(){
      var total=0;
      $("#productos_vender tbody tr").each(function(){
        total+=parseInt($(this).find(".precio").val())*parseInt($(this).find(".cantidad").val());
      })
      if(isNaN(total)){total=0;}
      return total
    }

    function actualizarMontoTotal(){
      let total=calcularTotalCompra()
      $("#total_productos").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total))
      mostrarTotalDescuento(total)
    }

    function mostrarTotalDescuento(total){
      let porcentaje=$("#id_descuento option:selected").data("porcentaje");
      let totalConDescuento=total;
      if(porcentaje!=undefined){
        let descuento=porcentaje*total/100;
        totalConDescuento=total-descuento;
      }
      //console.log(parseInt(total)-parseInt(totalConDescuento))
      if(isNaN(totalConDescuento)){totalConDescuento=0;}
      $("#total_a_pagar").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(totalConDescuento))
      $("#total_a_pagar_sin_formato").val(totalConDescuento)
      checkTotalPagarDNI();
    }

    function checkTotalPagarDNI(){
      console.log("checkTotalPagarDNI");
      let tipo_comprobante=$("#tipo_comprobante").val()
      console.log(tipo_comprobante);
      let monto_maximo_sin_informar_dni=parseInt($("#monto_maximo_sin_informar_dni").val());
      console.log(monto_maximo_sin_informar_dni);
      let total_a_pagar_sin_formato=parseInt($("#total_a_pagar_sin_formato").val());
      console.log(total_a_pagar_sin_formato);
      console.log(total_a_pagar_sin_formato>monto_maximo_sin_informar_dni);
      if(total_a_pagar_sin_formato>monto_maximo_sin_informar_dni && tipo_comprobante=="B"){
        $("#dni").attr("disabled",false).attr("required",true)
      }else{
        $("#dni").attr("disabled",false).attr("required",false)
      }
    }

    $(document).on("keyup change",".cantidad, .precio",function(){
      actualizarMontoTotal()
    })

    $(document).on("click",".btnEliminar",function(){
      $(".btnAnadir[data-id_stock='"+this.dataset.id_stock+"']").removeClass("disabled");
      $(this).parent().parent().remove();
      actualizarMontoTotal()
    });

		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
		
  </body>
</html>