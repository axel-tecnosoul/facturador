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

//$_GET["id"]=52;

if ( !empty($_GET)) {
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $modoDebug=0;

  if ($modoDebug==1) {
    $pdo->beginTransaction();
    var_dump($_GET);
  }

  $id=$_GET["id"];

  //obtenemos los datos de la factura para insertarlos en la nota de credito
  $sql = "SELECT nombre_cliente, dni, direccion, email, telefono, id_almacen, total, total_con_descuento, id_forma_pago, punto_venta, numero_comprobante, tipo_comprobante, fecha_hora, modalidad_venta FROM ventas WHERE id = ? ";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  $data = $q->fetch(PDO::FETCH_ASSOC);

  // Verificar si ya existe un registro similar
  $sqlCheck = "SELECT COUNT(*) FROM ventas WHERE fecha_hora >= NOW() - INTERVAL 60 SECOND AND nombre_cliente = ? AND dni = ? AND direccion = ? AND email = ? AND telefono = ? AND id_almacen = ? AND total = ? AND total_con_descuento = ? AND id_usuario = ? AND id_forma_pago = ? AND id_venta_cbte_relacionado = ? AND modalidad_venta = ?";
  $qCheck = $pdo->prepare($sqlCheck);
  $qCheck->execute(array($data["nombre_cliente"],$data["dni"],$data["direccion"],$data["email"],$data["telefono"],$data["id_almacen"],$data["total"],$data["total_con_descuento"],$_SESSION['user']['id'],$data['id_forma_pago'],$id,$data['modalidad_venta']));
  $count = $qCheck->fetchColumn();

  if ($count == 0) {

    //guardamos la nota de credito con los mismos datos de la factura y establemecemos el id_venta_cbte_relacionado
    $sql = "INSERT INTO ventas (fecha_hora, nombre_cliente, dni, direccion, email, telefono, id_almacen, total, total_con_descuento, id_usuario,id_forma_pago,id_venta_cbte_relacionado, modalidad_venta) VALUES (now(),?,?,?,?,?,?,?,?,?,?,?,?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($data["nombre_cliente"],$data["dni"],$data["direccion"],$data["email"],$data["telefono"],$data["id_almacen"],$data["total"],$data["total_con_descuento"],$_SESSION['user']['id'],$data['id_forma_pago'],$id,$data['modalidad_venta']));
    $idVentaCbteRelacionado = $pdo->lastInsertId();

    if ($modoDebug==1) {
      $q->debugDumpParams();
      echo "<br><br>Afe: ".$q->rowCount();
      echo "<br><br>";
    }

    //actualizamos la venta para informar que la factura tiene una nota de credito relacionada
    $sql = "UPDATE ventas set id_venta_cbte_relacionado = ? where id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($idVentaCbteRelacionado,$id));

    if ($modoDebug==1) {
      $q->debugDumpParams();
      echo "<br><br>Afe: ".$q->rowCount();
      echo "<br><br>";
    }

    //seleccionamos todos los productos de la factura
    $sql = " SELECT vd.id_producto, vd.cantidad, vd.precio, vd.subtotal, vd.id_modalidad, vd.deuda_proveedor, vd.pagado, p.id_proveedor, v.id_almacen from ventas_detalle vd inner join ventas v on v.id = vd.id_venta inner join productos p on p.id = vd.id_producto where vd.id_venta = ".$id;
    foreach ($pdo->query($sql) as $row) {

      $pagado=0;
      if ($row["id_modalidad"]==50 and $row["pagado"]==1) {
        //si la modalidad es por credito (al 50%) disminuimos el credito de la proveedora por que se devolvió su producto
        $credito = $row["deuda_proveedor"];
        $sql = "UPDATE proveedores set credito = credito - ? where id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($credito,$row["id_proveedor"]));
        $pagado=1;
      }

      //replicamos el detalle de los productos de la factura a la nota de credito
      $sqlA = "INSERT INTO ventas_detalle (id_venta, id_producto, cantidad, precio, subtotal, id_modalidad, deuda_proveedor, pagado) VALUES (?,?,?,?,?,?,?,?)";
      $q = $pdo->prepare($sqlA);
      //$q->execute(array($idVentaCbteRelacionado,$row["id_producto"],$row["cantidad"],$row["precio"],$row["subtotal"],$row["id_modalidad"],$row["deuda_proveedor"],$row["pagado"]));
      $q->execute(array($idVentaCbteRelacionado,$row["id_producto"],$row["cantidad"],$row["precio"],$row["subtotal"],$row["id_modalidad"],$row["deuda_proveedor"],$pagado));

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }

      //seleccionamos el id del stock para registrar la devolucion de los productos
      $sql2 = "SELECT id FROM stock WHERE id_producto = ? and id_almacen = ?";
      $q2 = $pdo->prepare($sql2);
      $q2->execute(array($row["id_producto"],$row["id_almacen"]));
      $data2 = $q2->fetch(PDO::FETCH_ASSOC);
      if (!empty($data2)) {
        //si lo encuentra aumentamos la cantidad
        $sql3 = "UPDATE stock set cantidad = cantidad + ? where id = ?";
        $q3 = $pdo->prepare($sql3);
        $q3->execute(array($row["cantidad"],$data2['id']));

        if ($modoDebug==1) {
          $q3->debugDumpParams();
          echo "<br><br>Afe: ".$q3->rowCount();
          echo "<br><br>";
        }

      } else {
        //si no lo encunetra (se habia eliminado por ser el/los ultimos) los volvemos a insertar
        $sql3 = "INSERT INTO stock(id_producto, id_almacen, cantidad, id_modalidad) VALUES (?,?,?,?)";
        $q3 = $pdo->prepare($sql3);
        $q3->execute(array($row["id_producto"],$row["id_almacen"],$row["cantidad"],$row["id_modalidad"]));

        if ($modoDebug==1) {
          $q3->debugDumpParams();
          echo "<br><br>Afe: ".$q3->rowCount();
          echo "<br><br>";
        }
      }
    
    }
    
    include './../external/afip/Afip.php';
    
    include 'config_facturacion_electronica.php';//poner $homologacion=1 para facturar en modo homologacion. Retorna $aInitializeAFIP.
    $afip = new Afip($aInitializeAFIP);

    $punto_venta=$data["punto_venta"];

    $server_status = $afip->ElectronicBilling->GetServerStatus();
    /*echo 'Este es el estado del servidor:';
    var_dump($server_status);*/

    $ImpTotal=$data["total_con_descuento"];
    $CbteAsoc=$data["numero_comprobante"];
    //$total=121;
    if($data["tipo_comprobante"]=="A"){
      $tipo_comprobante_asociado=1;//1 -> Factura A
      $tipo_comprobante=3;//3 -> Nota de Crédito A
      $tipo_de_nota="NCA";
      $DocTipo=80;
      $DocNro=$_POST["dni"];

      $ImpNeto=$ImpTotal/1.21;
      $ImpIVA=$ImpTotal-$ImpNeto;
    }elseif($data["tipo_comprobante"]=="B"){
      $tipo_comprobante_asociado=6;//6 -> Factura B
      $tipo_comprobante=8;//8 -> Nota de Crédito B
      $tipo_de_nota="NCB";
      $DocTipo=99;
      $DocNro=0;
      
      $ImpNeto=$ImpTotal/1.21;
      $ImpIVA=$ImpTotal-$ImpNeto;
    }
    $ImpNeto=number_format($ImpNeto,2,".","");
    $ImpIVA=number_format($ImpIVA,2,".","");
    

    $dataNC = array(
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
      'CbtesAsoc' 		=> array( // (Solo para notas de credito o debito) Array de comprobantes asociados
        array(
          'Tipo' 		=> $tipo_comprobante_asociado, // tipo de factura
          'PtoVta' 	=> $punto_venta,//punto de venta de la factura
          'Nro' 	=> $CbteAsoc,//nro de comprobante de la factura
        )
      ), 
    );

    //var_dump($dataNC);
    
    //$res = $afip->ElectronicBilling->CreateVoucher($dataNC);
    $res = $afip->ElectronicBilling->CreateNextVoucher($dataNC);
    
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

    //actualizamos la nota de credito con los datos devueltos por AFIP
    $sql = "UPDATE ventas SET tipo_comprobante = ?, tipo_doc = ?, estado = ?, punto_venta = ?, numero_comprobante = ?, cae = ?, fecha_vencimiento_cae = ? WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($tipo_de_nota,$DocTipo,$estado,$punto_venta,$voucher_number,$CAE,$CAEFchVto,$idVentaCbteRelacionado));

    if ($modoDebug==1) {
      $q->debugDumpParams();
      echo "<br><br>Afe: ".$q->rowCount();
      echo "<br><br>";
    }
  }

  if ($modoDebug==1) {
    $pdo->rollBack();
    die();
  }

  Database::disconnect();
  
}

header("Location: listarVentas.php");