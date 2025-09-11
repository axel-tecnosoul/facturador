<?php
ini_set("display_errors",1);
ini_set("display_startup_errors",1);
error_reporting(E_ALL);

require_once "config.php";
if(empty($_SESSION['user']['id_perfil'])){
  header("Location: index.php");
  die("Redirecting to index.php");
}
require_once 'database.php';

class ArcaClient {
    private $apiUrl;
    private $token;
    public function __construct(string $apiUrl, string $token) {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->token = $token;
    }
    public function enviarComprobante(array $data): array {
        $ch = curl_init($this->apiUrl . '/comprobantes');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new Exception($err);
        }
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status >= 400) {
            throw new Exception("HTTP $status: $response");
        }
        return json_decode($response, true);
    }
}

function informarFacturaARCA(int $idFactura): string {
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM facturas WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute([$idFactura]);
    $factura = $q->fetch(PDO::FETCH_ASSOC);
    if (!$factura) {
        Database::disconnect();
        return "Error: factura no encontrada";
    }

    $sql = "SELECT descripcion, precio, cantidad FROM factura_detalle WHERE id_factura = ?";
    $q = $pdo->prepare($sql);
    $q->execute([$idFactura]);
    $items = $q->fetchAll(PDO::FETCH_ASSOC);

    $payload = [
        'fecha' => $factura['fecha'],
        'total' => $factura['total'],
        'items' => $items
    ];

    $apiUrl = 'https://api.arca.gob.ar'; // URL base de ARCA
    $apiToken = 'TOKEN_ARCA'; // Reemplazar con token real

    try {
        $client = new ArcaClient($apiUrl, $apiToken);
        $res = $client->enviarComprobante($payload);
    } catch (Exception $e) {
        Database::disconnect();
        return "Error comunicando con ARCA: " . $e->getMessage();
    }

    if (isset($res['cae'])) {
        $sql = "UPDATE facturas SET estado = ?, cae = ?, fecha_vencimiento_cae = ?, numero_comprobante = ? WHERE id = ?";
        $q = $pdo->prepare($sql);
        $q->execute([
            'A',
            $res['cae'],
            $res['cae_vencimiento'] ?? null,
            $res['numero'] ?? null,
            $idFactura
        ]);
        Database::disconnect();
        return "Factura informada a ARCA correctamente";
    }

    Database::disconnect();
    return "Error: respuesta inválida de ARCA";
}

if (!debug_backtrace() && isset($_GET['id'])) {
    echo informarFacturaARCA((int)$_GET['id']);
}

?>
