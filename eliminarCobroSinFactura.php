<?php
function eliminarCobroSinFactura($pdo, $idCobro) {
    $pdo->beginTransaction();
    try {
        $q = $pdo->prepare('SELECT id,id_factura FROM cobros WHERE id=? FOR UPDATE');
        $q->execute(array($idCobro));
        $cobro = $q->fetch(PDO::FETCH_ASSOC);
        if (!$cobro) throw new RuntimeException('El cobro ya no existe.');
        if (!empty($cobro['id_factura'])) throw new RuntimeException('No se puede eliminar un cobro con factura asociada.');
        $q = $pdo->prepare('SELECT id FROM pagos_colaboradores WHERE id_cobro=? LIMIT 1 FOR UPDATE');
        $q->execute(array($idCobro));
        if ($q->fetchColumn()) throw new RuntimeException('No se puede eliminar: el cobro tiene pagos a colaboradores.');
        $pdo->prepare('DELETE FROM cobro_detalle WHERE id_cobro=?')->execute(array($idCobro));
        $pdo->prepare('DELETE FROM cobros WHERE id=?')->execute(array($idCobro));
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
}
