<?php
require 'database.php';

try {
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener clientes con precio base y fecha base definidos
    $sql = "SELECT id, razon_social, precio_base, fecha_base FROM clientes WHERE precio_base IS NOT NULL AND fecha_base IS NOT NULL";
    foreach ($pdo->query($sql) as $cliente) {
        $precio_base = (float)$cliente['precio_base'];
        $fecha_base = $cliente['fecha_base'];
        $factor = 1.0;
        $fecha_inicio = new DateTime($fecha_base);
        $fecha_fin = new DateTime(); // Fecha actual

        if ($fecha_fin > $fecha_inicio) {
            $fecha_inicio->modify('first day of next month');
            while ($fecha_inicio <= $fecha_fin) {
                $periodo = $fecha_inicio->format('Y-m-01');
                $q = $pdo->prepare('SELECT porcentaje FROM ipc_historial WHERE periodo = ?');
                $q->execute(array($periodo));
                $ipc = $q->fetch(PDO::FETCH_ASSOC);
                if ($ipc) {
                    $factor *= (1 + $ipc['porcentaje']/100);
                }
                $fecha_inicio->modify('first day of next month');
            }
        }

        $precio_actual = round($precio_base * $factor, 2);
        $u = $pdo->prepare('UPDATE clientes SET precio_actual = ? WHERE id = ?');
        $u->execute(array($precio_actual, $cliente['id']));

        $message = sprintf("Cliente %s (ID %d) actualizado: %.2f\n", $cliente['razon_social'], $cliente['id'], $precio_actual);
        echo $message;
        file_put_contents('save_log.txt', $message, FILE_APPEND);
    }

    Database::disconnect();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
