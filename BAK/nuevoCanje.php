<?php
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
  
  $id_almacen=$_POST['id_almacen'];
  $id_proveedor_canje=$_POST['id_proveedor_canje'];
  $id_usuario=$_SESSION['user']['id'];
  $total_a_pagar = $_POST['total_input'];
  $credito_usar = $_POST['credito_usar'];
  $forma_pago = $_POST["id_forma_pago"];
  
  $sql = "INSERT INTO canjes (fecha_hora, id_proveedor, id_almacen, total, id_usuario) VALUES (now(),?,?,0,?)";
  $q = $pdo->prepare($sql);
  $q->execute(array($id_proveedor_canje,$id_almacen,$id_usuario));
  $idCanje = $pdo->lastInsertId();

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
  $id_descuento=NULL;
  //Descuentos
  if(isset($_POST['id_descuento']) and $_POST['id_descuento']!=""){
    $id_descuento=$_POST['id_descuento'];
    $sql2 = "SELECT minimo_compra, minimo_cantidad_prendas, monto_fijo, porcentaje FROM descuentos WHERE id = ? ";
    $q2 = $pdo->prepare($sql2);
    $q2->execute(array($id_descuento));
    $data2 = $q2->fetch(PDO::FETCH_ASSOC);
    
    $minimo_compra=$data2['minimo_compra'];
    //$monto_fijo=$data2['monto_fijo'];
    $porcentaje=$data2['porcentaje'];
    $minimo_cantidad_prendas=$data2['minimo_cantidad_prendas'];
    
    if ($modoDebug==1) {
      $q2->debugDumpParams();
      echo "<br><br>Afe: ".$q2->rowCount();
      echo "<br><br>porcentaje: ".$porcentaje;
      echo "<br>minimo_compra: ".$minimo_compra;
      echo "<br>minimo_cantidad_prendas: ".$minimo_cantidad_prendas;
      echo "<br>cantidad de prendas: ".$cantPrendas;
      echo "<br>";
    }
    
  }

  $total = 0;
  $totalConDescuento = 0;
  foreach ($_POST['id_stock'] as $key => $id_stock) {

    $cantidad = $_POST['cantidad'][$key];
    $cantidadAnterior = $_POST['stock'][$key];
    $precio = $_POST['precio'][$key];
    $modalidad = $_POST["id_modalidad"][$key];
    $idProveedor = $_POST["id_proveedor"][$key];
    $idProducto = $_POST["id_producto"][$key];

    $subtotal = $cantidad * $precio;
    $total += $subtotal;
    if ($modoDebug==1) {
      echo "<br><br>Total de productos (parcial): ".$total;
      echo "<br><br>Subtotal: ".$subtotal;
    }

    if ($minimo_compra!="" and $total>$minimo_compra and $minimo_cantidad_prendas!="" and $cantPrendas>=$minimo_cantidad_prendas) {
      //$totalConDescuento = $totalConDescuento - $monto_fijo;
      $subtotal-=(($subtotal*$porcentaje)/100);
      //var_dump("subtotal: " . $subtotal);
      if ($modoDebug==1) {
        //$q->debugDumpParams();
        echo "<br><br>Subtotal Con Descuento: ". $subtotal;
        echo "<br><br>";
      }
    }

    $totalConDescuento += $subtotal;

    $deuda_proveedor=calcularDeudaProveedor($forma_pago,$modalidad,$subtotal);

    $pagado = 0;
    //$credito = 0;
    if ($modalidad == 1) {
      $pagado = 1;
    } else if ($modalidad == 40) {
      //$pagado = 0;
    } else if ($modalidad == 50) {
      /*$credito = $subtotal/2;
      $sql = "UPDATE proveedores set credito = credito + ? where id = ?";
      $q = $pdo->prepare($sql);
      $q->execute(array($credito,$idProveedor));

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Proveedores: ".$q->rowCount();
        echo "<br><br>";
      }*/
    }

    $sql = "INSERT INTO canjes_detalle (id_canje, id_producto, cantidad, precio, subtotal, id_modalidad, deuda_proveedor, pagado) VALUES (?,?,?,?,?,?,?,?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($idCanje,$idProducto,$cantidad,$precio,$subtotal,$modalidad,$deuda_proveedor,$pagado));

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

  }
  
  /*$sql = "UPDATE canjes set total = ? WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($total,$idCanje));*/

  $id_venta = NULL;
  if($total_a_pagar > 0){
    //Datos proveedor 
    $sql3 = "SELECT email, dni, nombre, apellido, telefono, id_modalidad FROM proveedores WHERE id = ? ";
    $q3 = $pdo->prepare($sql3);
    $q3->execute(array($id_proveedor_canje));
    $data3 = $q3->fetch(PDO::FETCH_ASSOC);

    $nombre_cliente = $data3['nombre'] . ' ' . $data3['apellido'];
    $dni = $data3['dni'];
    $email = $data3['email'];
    $telefono = $data3['telefono'];
    $direccion = '';
    $tipo_comprobante = $_POST['tipo_comprobante'];
    $modalidad = 'Presencial';

    //Alta Nueva Venta
    $sql = "INSERT INTO ventas(fecha_hora, nombre_cliente, dni, direccion, email, telefono, id_almacen, tipo_comprobante, id_usuario, id_forma_pago, modalidad_venta, total, id_descuento_aplicado, total_con_descuento) VALUES (now(),?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($nombre_cliente,$dni,$direccion,$email,$telefono,$id_almacen,$tipo_comprobante,$id_usuario,$forma_pago,$modalidad,$total_a_pagar,$id_descuento,$total_a_pagar));
    $id_venta = $pdo->lastInsertId();

    if ($modoDebug==1) {
      $q->debugDumpParams();
      echo "<br><br>Afe: ".$q->rowCount();
      echo "<br><br>ID venta: ".$id_venta;
      echo "<br><br>";
    }
  
    /*$total = $total - $credito_usar;
    $sql = "UPDATE ventas set total = ?, id_descuento_aplicado = ?, total_con_descuento = ? WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($total,$id_descuento,$total_a_pagar,$id_venta));*/
    
    /*$sql = "UPDATE ventas set total = ?, total_con_descuento = ? WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($total_a_pagar,$total_a_pagar,$id_venta));

    if ($modoDebug==1) {
      $q->debugDumpParams();
      echo "<br><br>Afe: ".$q->rowCount();
      echo "<br><br>";
    }*/

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
      $q4->execute(array($id_almacen));
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
  
      $ImpTotal=$total_a_pagar;
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
      /*if(isset($_POST["dni"]) and $_POST["dni"]!=""){
        $DocNro=$_POST["dni"];
        $DocTipo=96;
      }
      if(isset($_POST["cuit"]) and $_POST["cuit"]!=""){
        $DocNro=$_POST["cuit"];
        $DocTipo=80;
      }*/

      if(!empty($dni) and $dni!=""){
        $DocNro=$dni;
        $DocTipo=96;
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
      $q->execute(array($DocTipo,$estado,$punto_venta,$voucher_number,$CAE,$CAEFchVto,$id_venta));
  
      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }
  
    }
  }

  $sql4 = "UPDATE canjes set total = ?, id_descuento_aplicado = ?, total_con_descuento = ?, credito_usado = ?, id_venta = ? WHERE id = ?";
  $q4 = $pdo->prepare($sql4);
  $q4->execute(array($total, $id_descuento, $totalConDescuento, $credito_usar, $id_venta, $idCanje));
  if ($modoDebug==1) {
    $q4->debugDumpParams();
    echo "<br><br>Afe: " . $q4->rowCount();
    echo "<br><br>";
  }

  $sql3 = "UPDATE proveedores set credito = credito - ? WHERE id = ?";
  $q3 = $pdo->prepare($sql3);
  $q3->execute(array($credito_usar,$id_proveedor_canje));
  if ($modoDebug==1) {
    $q3->debugDumpParams();
    echo "<br><br>Afe: ".$q3->rowCount();
    echo "<br><br>Total de credito ocupado: ".$credito_usar;
    echo "<br><br>ID proveedor: ".$id_proveedor_canje;
  }
  
  
  if ($modoDebug==1) {
    $pdo->rollBack();
    die();
  }
  Database::disconnect();
  
  header("Location: listarCanjes.php");
}?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
	<link rel="stylesheet" type="text/css" href="assets/css/select2.css">
	<link rel="stylesheet" type="text/css" href="assets/css/datatables.css">
  </head>
  <body class="light-only">
    <!-- Loader ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
	  <?php include('header.php');?>
	  
      <!-- Page Header Start-->
      <div class="page-body-wrapper">
		<?php include('menu.php');?>
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
                      <li class="breadcrumb-item">Nuevo Canje</li>
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
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Nuevo Canje</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="nuevoCanje.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Proveedor</label>
                            <div class="col-sm-9">
                              <select name="id_proveedor_canje" id="id_proveedor" class="js-example-basic-single col-sm-12" required="required">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT id, dni, nombre, apellido, credito FROM proveedores WHERE activo = 1 and credito>0";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'data-credito='".$fila['credito']."'";
                                  echo ">".$fila['nombre']." ".$fila['apellido']." (".$fila['dni'].")</option>";
                                }
                                Database::disconnect();?>
                              </select>
                              <!-- <input type="hidden" id="credito_numero" name="credito_numero" value=""> -->
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Credito Disponible</label>
                            <div class="col-sm-9"><label id="credito_disponible_formatted"><?= "$".number_format(0, 2, ',', '.');?></label></div>
                            <input type="hidden" id="credito_disponible" name="credito_disponible" value="">
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Almacen</label>
                            <div class="col-sm-9">
                              <select name="id_almacen" id="id_almacen" class="js-example-basic-single col-sm-12" required="required" onChange="jsListarProductos(this.value);">
                                <option value="">Seleccione...</option><?php 
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT id, almacen FROM almacenes WHERE activo = 1";
                                if ($_SESSION['user']['id_perfil'] == 2) {
                                  $sqlZon .= " and id = ".$_SESSION['user']['id_almacen']; 
                                }
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  echo ">".$fila['almacen']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Productos en stock</label>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-12">
                              <table class="display" id="dataTables-example666">
                                <thead>
                                  <tr>
                                  <th>ID</th>
                                  <th>Código</th>
                                  <th>Categoría</th>
                                  <th>Descripción</th>
                                  <th>Stock</th>
                                  <th>Precio</th>
                                  <th>Accion</th>
                                  </tr>
                                </thead>
                                <tbody></tbody>
                              </table>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Productos a canjear</label>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-12">
                              <table class="table" id="productos_canjear">
                                <thead>
                                  <tr>
                                    <th>ID</th>
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
                            <label class="col-sm-3 col-form-label">Total productos a canjear</label>
                            <div class="col-sm-9"><label id="total_productos_canjear">$ 0</label></div>
                          </div>
                          <!--<div class="form-group row">
                            <label class="col-sm-3 col-form-label">Forma de Pago</label>
                            <div class="col-sm-9">
                              <select name="id_forma_pago" id="id_forma_pago" class="js-example-basic-single col-sm-12" required disabled>
                              <option value="">Seleccione...</option><?php
                                /*$pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT id, forma_pago FROM forma_pago WHERE activo = 1 ORDER BY forma_pago";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  echo ">".$fila['forma_pago']."</option>";
                                }
                                Database::disconnect();*/?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Descuentos Vigentes</label>
                            <div class="col-sm-9">
                              <select name="id_descuento" id="id_descuento" class="js-example-basic-single col-sm-12" disabled>
                                <option value="">Seleccione...</option><?php
                                /*$pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT d.id as id_descuento, d.descripcion, d.minimo_compra, d.minimo_cantidad_prendas, d.monto_fijo, d.porcentaje, dfp.id_forma_pago, f.forma_pago FROM descuentos_x_formapago dfp INNER JOIN descuentos d on d.id = dfp.id_descuento INNER JOIN forma_pago f on f.id = dfp.id_forma_pago WHERE dfp.id_forma_pago = '1' AND vigencia_desde <= now() and vigencia_hasta >= now()";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id_descuento']. "' data-porcentaje='" . $fila['porcentaje'] ."'";
                                  echo ">".$fila['descripcion']."</option>";
                                }
                                Database::disconnect();*/?>
                                
                              </select>
                            </div>
                          </div>-->

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
                                  if($fila['id']==1){
                                    echo "selected";
                                  }else{
                                    echo "disabled";
                                  }
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
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT d.id as id_descuento, d.descripcion, d.minimo_compra, d.minimo_cantidad_prendas, d.monto_fijo, d.porcentaje, dfp.id_forma_pago, f.forma_pago FROM descuentos_x_formapago dfp INNER JOIN descuentos d on d.id = dfp.id_descuento INNER JOIN forma_pago f on f.id = dfp.id_forma_pago WHERE dfp.id_forma_pago = '1' AND vigencia_desde <= now() and vigencia_hasta >= now()";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id_descuento']. "' data-porcentaje='" . $fila['porcentaje'] ."'";
                                  echo ">".$fila['descripcion']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Total con descuento</label>
                            <div class="col-sm-9"><label id="total_con_descuento_formatted"><?= "$".number_format(0, 2, ',', '.');?></label></div>
                            <input type="hidden" id="total_con_descuento" name="credito_usar" value="">
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Credito a Usar</label>
                            <div class="col-sm-9"><label id="credito_usar_formatted"><?= "$".number_format(0, 2, ',', '.');?></label></div>
                            <input type="hidden" id="credito_usar" name="credito_usar" value="">
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Total a Pagar</label>
                            <div class="col-sm-9"><label id="total_compra"><?= "$".number_format(0, 2, ',', '.');?></label></div>
                            <input type="hidden" id="total_input" name="total_input" value="">
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tipo de comprobante</label>
                            <div class="col-sm-9">
                              <select name="tipo_comprobante" id="tipo_comprobante" class="js-example-basic-single col-sm-12" disabled>
                                <option value="">Seleccione...</option>
                                <!-- <option value="A" class="cbte_only_punto_venta" disabled>Factura A</option> -->
                                <option value="B" class="cbte_only_punto_venta">Factura B</option>
                                <option value="R">Recibo</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Crear</button>
						            <a href="listarCanjes.php" class="btn btn-light">Volver</a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
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
    <!-- <script src="assets/js/typeahead/handlebars.js"></script>
    <script src="assets/js/typeahead/typeahead.bundle.js"></script>
    <script src="assets/js/typeahead/typeahead.custom.js"></script> -->
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <!-- <script src="assets/js/typeahead-search/handlebars.js"></script>
    <script src="assets/js/typeahead-search/typeahead-custom.js"></script> -->
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
      $("form").on("submit",function(e){
        e.preventDefault();
        let cant_productos=$('#productos_canjear tbody tr').length;
        if(cant_productos){
          let total = $('#total_compra');
          total = total.text();
          // Eliminar el signo de moneda y el punto de separación de miles
          total = total.replace('$', '').replace(/\./g, '');
          //Eliminar los espacios
          total = total.replace(/\s/g, '');
          //Reemplazar la coma por un punto
          total = total.replace(/,/g, '.');
          total = parseInt(total);
          console.log("Total: " + total);
          if(total >= 0){
            $('#total_input').val(total);
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
              this.submit();
            }
          }
        }else{
          alert("Añada algún producto")
        }
      });
      
      function jsListarProductos(val) {
        $("#dataTables-example666").dataTable().fnDestroy();
        $('#dataTables-example666').DataTable({
          "ajax" : "ajaxVentas.php?almacen="+val,//&id_vehiculo="+id_vehiculo+"
          stateSave: true,
          responsive: true,
          serverSide: true,
          processing: true,
          scrollY: false,
          "columns":[
            {"data": "cb"},//"fecha_mostrar"},
            {"data": "codigo"},//"vehiculo.marca"},
            {"data": "categoria"},//"vehiculo.modelo"},
            {"data": "descripcion"},//"vehiculo.patente"},
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
                  <input type="hidden" disabled name="precio[]" class="enviar_form precio" value="${row.precio}">`+
                  new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.precio);
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
          initComplete: function(){
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
    
      $(document).ready(function() {
        jsListarProductos(0);
        //$('#id_forma_pago option:not(:contains("Efectivo"))').prop('disabled', true);
        get_descuentos();
      });

      $("#id_forma_pago").on("change",function(){
        get_descuentos();
      });

      function get_descuentos(){
        var formaPagoId = $("#id_forma_pago").val();
        
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
      }

      $("#id_descuento").change(function() {
        //mostrarTotalDescuento();
        actualizarMontoTotal();
      });

      $("#id_proveedor").on("change", function () {
        let option_elegido = $(this).find("option:selected");
        let credito = option_elegido.data("credito");

        $("#credito_disponible_formatted").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(credito));
        $("#credito_disponible").val(credito);
        $("#credito_usar_formatted").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(credito));
        $("#credito_usar").val(credito);
        actualizarMontoTotal();
      });

      $(document).on("click",".btnAnadir",function(){
        let prod_anadido=$("input[name='id_stock[]'][value='"+this.dataset.id_stock+"']");
        //console.log(prod_anadido)
        //console.log("cantidad encontrada");
        if(prod_anadido.length==0){
          if(parseInt(this.dataset.cantidad)>0){
            let fila=$(this).parent().parent();
            let clon=fila.clone();
            let btn=clon.find("button");
            //console.log(btn);
            btn.parent().html(`
              <input type='hidden' name='id_stock[]' value='${this.dataset.id_stock}'></input>
              <input type='number' name='cantidad[]' class='form-control form-control-sm cantidad mx-auto' style='width: 60px;' min='1' max='${this.dataset.cantidad}' value="1" required></input>
            `);
            clon.append(`
              <td class='text-center'>
                <img src='img/icon_baja.png' class='btnEliminar' width='24' height='25' border='0' alt='Eliminar' title='Eliminar'>
              </td>
            `);
            /*clon.find("input[name='precio[]']").attr("disabled",false);
            clon.find("input[name='stock[]']").attr("disabled",false);*/
            clon.find(".enviar_form").attr("disabled",false);

            $("#productos_canjear tbody").append(clon[0]);

            actualizarMontoTotal();
          }else{
            alert("No hay stock suficiente")
          }
        }else{
          alert("El producto ya fue añadido")
        }
      })
      
      
      function calcularTotalCompra(){
        let total=0;
        $("#productos_canjear tbody tr").each(function(){
          total+=parseInt($(this).find(".precio").val())*parseInt($(this).find(".cantidad").val());
        })
        if(isNaN(total)){total=0;}
        return total
      }

      function actualizarMontoTotal(){
        let total=calcularTotalCompra();
        $("#total_productos_canjear").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total));
        mostrarTotalDescuento(total)
      }

      function mostrarTotalDescuento(total){
        //let totalConDescuento = total;
        let totalFinal = 0;
        let porcentaje=$("#id_descuento option:selected").data("porcentaje");
        let subtotal=$("#total_productos_canjear").val();
        let credito_disponible=$("#credito_disponible");
        let credito=credito_disponible.val();

        let total_con_descuento=total;
        if(porcentaje!=undefined){
          let descuento=total*porcentaje/100;
          total_con_descuento=total-descuento;
        }

        $("#total_con_descuento_formatted").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_con_descuento));
        $("#total_con_descuento").val(total_con_descuento);

        calcularCreditoUsar()
      }

      function calcularCreditoUsar(){
        let total_con_descuento=$("#total_con_descuento").val();
        let credito_disponible=$("#credito_disponible").val();
        let credito_diferencia = credito_disponible - total_con_descuento;
        let credito_usar = credito_disponible - credito_diferencia;

        if(credito_usar >= credito_disponible){
          credito_usar = credito_disponible;
        }

        $("#credito_usar_formatted").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(credito_usar));
        $("#credito_usar").val(credito_usar);

        let total_a_pagar=total_con_descuento-credito_usar
        $("#total_compra").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_a_pagar));

        //si el total a pagar a pagar es mayor a 0 habilitamos las otras formas de pago y si es menor a 0 deshabilitamos todas y luego habilitamos solo efectivo y marcamos como selected
        let id_forma_pago=$('#id_forma_pago')
        let tipo_comprobante=$('#tipo_comprobante')
        if(total_a_pagar > 0){
          id_forma_pago.find('option').prop('disabled', false);
          tipo_comprobante.prop('disabled', false).prop('required', true);
        }else{
          id_forma_pago.find('option').prop('disabled', true);
          id_forma_pago.find('option[value="1"]').prop('disabled', false).prop('selected', true);
          tipo_comprobante.prop('disabled', true).prop('required', false);
        }
        id_forma_pago.select2("destroy").select2()
      }

      $(document).on("keyup change",".cantidad",function(){
        actualizarMontoTotal()
      })

      $(document).on("click",".btnEliminar",function(){
        $(this).parent().parent().remove();
        actualizarMontoTotal()
      })
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
  </body>
</html>