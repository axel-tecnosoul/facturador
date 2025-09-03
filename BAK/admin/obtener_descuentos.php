<?php
    require 'database.php';
    require("config.php");

    $forma_pago_id = $_POST['forma_pago_id'];
    $descuentos = [];
    $porcentaje = 0;
    //var_dump($forma_pago_id);

    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sqlZon = "SELECT d.id, d.descripcion, d.minimo_compra, d.minimo_cantidad_prendas, d.monto_fijo, d.porcentaje, dfp.id_forma_pago, f.forma_pago FROM descuentos_x_formapago dfp INNER JOIN descuentos d on d.id = dfp.id_descuento INNER JOIN forma_pago f on f.id = dfp.id_forma_pago WHERE dfp.id_forma_pago = $forma_pago_id and d.vigencia_desde <= curdate() and d.vigencia_hasta >= curdate() ";
    $q = $pdo->prepare($sqlZon);
    $q->execute();
    while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
        $detalle="";
        $porcentaje=0;
        if($fila['porcentaje']>0){
            $detalle.=" (".$fila['porcentaje']."%)";
            $porcentaje = $fila['porcentaje'];
        }
        if($fila['minimo_cantidad_prendas']>0){
            $detalle.=" Cantidad prendas minimo: ". $fila['minimo_cantidad_prendas'];
        }
        if($fila['minimo_compra']>0){
            $detalle.=" Compra minima: $".number_format($fila['minimo_compra'],0,",",".");
        }
        
        $descuentos[] = [
            'id' => $fila['id'],
            'nombre' => $fila['descripcion'] . $detalle,
            'porcentaje' => $porcentaje,
            'minimo_cantidad_prendas' => $fila['minimo_cantidad_prendas'],
            'minimo_compra' => $fila['minimo_compra'],
        ];
    }    
    Database::disconnect();
    
    $json = json_encode($descuentos);
    //Pasar los datos al ajax
    echo $json;
?>