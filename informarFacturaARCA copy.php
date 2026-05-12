<?php
ini_set("display_errors",1);
ini_set("display_startup_errors",1);
error_reporting(E_ALL);

/**
 * Función para informar factura a ARCA
 * @param int $idFactura ID de la factura a informar
 * @return string Resultado de la operación
 */
function informarFacturaARCA($idFactura) {
    require_once("config.php");
    if(empty($_SESSION['user']['id_perfil'])){
      return "Error: Usuario no autenticado";
    }
    require_once 'database.php';

    // insert data
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $modoDebug=0;
    if ($modoDebug==1) {
        $pdo->beginTransaction();
        var_dump($idFactura);
    }
    
    $sql = "SELECT f.*, c.razon_social, c.cuit, c.email, c.telefono, c.direccion, c.pide_factura 
            FROM facturas f 
            INNER JOIN clientes c ON f.id_cliente = c.id 
            WHERE f.id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($idFactura));
    $data = $q->fetch(PDO::FETCH_ASSOC);

    if(!$data) {
        Database::disconnect();
        return "Error: Factura no encontrada";
    }

    // Para facturas generadas desde cobros, establecemos valores por defecto
    $punto_venta = 1; // Punto de venta por defecto para homologación

    require_once 'vendor/afip/Afip.php'; // SDK de AFIP/ARCA para facturación electrónica
    require_once 'config_facturacion_electronica.php';//poner $homologacion=1 para facturar en modo homologacion. Retorna $aInitializeAFIP.

    if($modoDebug==1){
        var_dump($aInitializeAFIP);
    }
    $afip = new Afip($aInitializeAFIP); // Instancia del SDK AFIP/ARCA

    try {
        $server_status = $afip->ElectronicBilling->GetServerStatus();
        /*echo 'Este es el estado del servidor ARCA:';
        var_dump($server_status);*/

        // Datos de la factura adaptados para tabla 'facturas'
        $ImpTotal = $data["total"];
        
        // Determinar tipo de comprobante (por defecto Factura C para monotributistas)
        $tipo_comprobante = "C"; // Factura C por defecto
        $es_monotributista = true; // Por defecto monotributista
        
        // TODO: Determinar el tipo según configuración del emisor
        if($tipo_comprobante=="A"){
            $tipo_comprobante=1;//1 -> Factura A
            $es_monotributista = false;
        }elseif($tipo_comprobante=="B"){
            $tipo_comprobante=6;//6 -> Factura B
            $es_monotributista = false;
        }elseif($tipo_comprobante=="C"){
            $tipo_comprobante=11;//11 -> Factura C (Monotributistas)
            $es_monotributista = true;
        }
        
        // Cálculo de IVA según condición fiscal
        if($es_monotributista || $tipo_comprobante==11){
            // Monotributistas: NO facturan IVA
            $ImpNeto=$ImpTotal;
            $ImpIVA=0;
        } else {
            // Responsables Inscriptos: calculan IVA
            $ImpNeto=$ImpTotal/1.21;
            $ImpIVA=$ImpTotal-$ImpNeto;
        }

        // Datos del cliente
        $DocNro = $data["cuit"] ?: "0"; // CUIT o 0 para consumidor final
        $DocTipo = $data["cuit"] ? 80 : 99; // 80=CUIT, 99=Consumidor Final
        
        $ImpNeto=number_format($ImpNeto,2,".","");
        $ImpIVA=number_format($ImpIVA,2,".","");
        

        $data_factura = array(
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
        );
        
        // Solo incluir alícuotas de IVA si NO es monotributista
        if(!$es_monotributista && $ImpIVA > 0){
            $data_factura['Iva'] = array( // (Opcional) Alícuotas asociadas al comprobante
                array(
                    'Id' 		=> 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                    'BaseImp' 	=> $ImpNeto,//100, // Base imponible
                    'Importe' 	=> $ImpIVA,//21 // Importe IVA
                )
            );
        }
        
        // Ejemplo: Agregar percepciones y retenciones
        // TODO: Obtener de la venta los tributos aplicados
        /*
        $data_factura['Tributos'] = array(
            array(
                'Id' => 99, // ID del tributo según AFIP
                'Desc' => 'Percepción IIBB Buenos Aires', 
                'BaseImp' => $ImpNeto,
                'Alic' => 2.50, // 2.5%
                'Importe' => $ImpNeto * 0.025
            ),
            array(
                'Id' => 1, // ID para retención Ganancias
                'Desc' => 'Retención Ganancias',
                'BaseImp' => $ImpNeto,
                'Alic' => 6.00, // 6%
                'Importe' => $ImpNeto * 0.06
            )
        );
        */

        if ($modoDebug==1) {
            var_dump($data_factura);
        }

        $res = $afip->ElectronicBilling->CreateNextVoucher($data_factura);
        
        if(isset($res['CAE'])){
            $estado="A";
            $CAE=$res['CAE'];//CAE asignado el comprobante
            $CAEFchVto=$res['CAEFchVto'];//Fecha de vencimiento del CAE (yyyy-mm-dd)
            $voucher_number=$res['voucher_number'];//Número asignado al comprobante

            if ($modoDebug==1) {
                var_dump($res);
                var_dump($CAE);
                var_dump($CAEFchVto);
                var_dump($voucher_number);
            }

            $sql = "UPDATE facturas SET estado = ?, numero_comprobante = ?, cae = ?, fecha_vencimiento_cae = ? WHERE id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($estado,$voucher_number,$CAE,$CAEFchVto,$idFactura));

            if ($modoDebug==1) {
                $q->debugDumpParams();
                echo "<br><br>Afe: ".$q->rowCount();
                echo "<br><br>";
            }

            if($q->rowCount()==1){
                $resultado = "Venta informada a ARCA correctamente";
            } else {
                $resultado = "Error: No se pudo actualizar la venta";
            }

        } else {
            $resultado = "Error: No se obtuvo CAE de ARCA";
        }

        if ($modoDebug==1) {
            $pdo->rollBack();
            Database::disconnect();
            return $resultado;
        }
        
        Database::disconnect();
        return $resultado;
        
    } catch (Exception $e) {
        Database::disconnect();
        return 'Error: ' . $e->getMessage();
    }
}

// Si se ejecuta directamente via GET (compatibilidad hacia atrás)
if (!debug_backtrace() && !empty($_GET['id'])) {
    require("config.php");
    if(empty($_SESSION['user']['id_perfil'])){
      header("Location: index.php");
      die("Redirecting to index.php"); 
    }
    
    $resultado = informarFacturaARCA((int)$_GET['id']);
    echo $resultado;
    
    if(stripos($resultado, 'Error') === false) {
        echo "<br><a href='verVenta.php?id=".$_GET["id"]."'>Volver a Ver venta</a>";
    } else {
        echo "<br><a href='listarVentas.php'>Volver al listado</a>";
    }
}
?>