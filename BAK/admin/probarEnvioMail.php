<?php 
  require("admin/config.php");
	require("admin/database.php");
	
	require("admin/PHPMailer/class.phpmailer.php");
	require("admin/PHPMailer/class.smtp.php");
    
	$message = "
	<html>
	<head>
	<title>Solicitud de Turno MiRoperito</title>
	</head>
	<body>
	<table width='50%' border='0' align='center' cellpadding='0' cellspacing='0'>
	<tr>
	<td colspan='2' align='center' valign='top'><img style=' margin-top: 15px;max-width: 100%; ' src='https://miroperito.ar/images/logo/Logo-Mi-roperito.png' ></td>
	</tr>
	<tr>
	<td width='50%' align='right'>&nbsp;</td>
	<td align='left'>&nbsp;</td>
	</tr>
	<tr>
	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Sucursal:</td>
	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>sucursal</td>
	</tr>
	<tr>
	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Cantidad:</td>
	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>cantidad</td>
	</tr>
	<tr>
	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Fecha:</td>
	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>fecha</td>
	</tr>
	<tr>
	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Hora:</td>
	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>hora</td>
	</tr>
	<tr>
	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>DNI:</td>
	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>dni</td>
	</tr>
	<tr>
	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Nombre:</td>
	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>nombre</td>
	</tr>
	<tr>
	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>E-Mail:</td>
	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>email</td>
	</tr>
	<tr>
	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Teléfono:</td>
	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>telefono</td>
	</tr>
	</table>
	</body>
	</html>
	";
	
	//$smtpHost = "c1971287.ferozo.com";
  //$smtpHost = "hosting3.tecnosoul.com.ar";
  $smtpHost = "mail.miroperito.ar";
	$smtpUsuario = "avisos@miroperito.ar";
	$smtpClave = "zR*eHJJ3zK";

	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
  $mail->SMTPDebug = 3;
  $mail->SMTPDebug = SMTP::DEBUG_CLIENT;
	
  //$mail->SMTPSecure = 'ssl';
  //$mail->Port = 465;

  $mail->SMTPSecure = 'tls';
  $mail->Port = 587;
	
  $mail->IsHTML(true); 
	$mail->CharSet = "utf-8";
	$mail->Host = $smtpHost; 
	$mail->Username = $smtpUsuario; 
	$mail->Password = $smtpClave;
	$mail->From = "axelbritzius@gmail.com";
	$mail->FromName = "Axel Britzius";
	$mail->AddAddress("axelbritzius@gmail.com");
	$mensaje = $message;
	$mail->Subject = "Solicitud de Turno MiRoperito"; 
	$mensajeHtml = nl2br($mensaje);
	$mail->Body = "{$mensajeHtml} <br /><br />"; 
	$mail->AltBody = "{$mensaje} \n\n"; 
		
	$ok=$mail->Send();
  var_dump($ok);

  //COMENTADO PARA SUBIR A PRODUCCION
  /*include_once("admin/funciones.php");
  require_once("admin/WABADatosApi.php");*/
  //global $API_URL;

  //confirmacion_turno: Gracias por acercarte a ofrecer tu ropa en MiRoperito. Tenés turno para el día {{fecha}} a las {{hora}}hs en {{direccion}}, {{almacen}}. Te esperamos!

  //recordatorio_turno: Hola! Desde MiRoperito queremos recordarte acerca del turno que tenés reservado para hoy a las {{hora}}hs en {{direccion}}, {{almacen}}. Si no podés asistir a tu turno y deseas cancelarlo, responde CANCELAR. Muchas gracias!

  /*$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sqlZon = "SELECT almacen,direccion FROM almacenes WHERE id = $sucursal";
  $q = $pdo->prepare($sqlZon);
  $q->execute();
  $fila = $q->fetch(PDO::FETCH_ASSOC);
  $almacen=$fila['almacen'];*/

  //COMENTADO PARA SUBIR A PRODUCCION

  /*$url=$API_URL.$FROM_PHONE_NUMBER_ID."/messages";
  $tipoPeticion="POST";
  $opcionales=[
    "header"=>[
      'Content-Type: application/json',
      'Authorization: Bearer '.$ACCESS_TOKEN
    ],
    "parametros"=>[
      "messaging_product" => "whatsapp",
      "recipient_type"    => "individual",
      "to"                => $telefono,
      "type"              => "template",
      "template"          =>[
        //"name"=>"hello_world",
        "name"=>"confirmacion_turno",
        "language"=>[
          //"code"=> "en_US"
          "code"=> "es_AR"
        ],
        "components"=>[
          [
            "type"=> "body",
            "parameters"=>[
              [
                "type"=> "text",//fecha
                "text"=> date("d-m-Y",strtotime($fecha)),
              ],[
                "type"=> "text",//hora
                "text"=> $hora,
              ],[
                "type"=> "text",//direccion
                "text"=> $direccion,
              ],[
                "type"=> "text",//almacen
                "text"=> $almacen,
              ]
            ]
          ]
        ]
      ]
    ]
  ];*/
  //var_dump($opcionales);

  /*$json='{
    "to": "recipient_wa_id",
    "type": "template",
    "template": {
          "namespace": "your-namespace",
          "name": "your-template-name",
          "language": {
              "code": "your-language-and-locale-code",
              "policy": "deterministic"
          },
          "components": [{
              "type": "body",
              "parameters": [
                  {
                      "type": "text",
                      "text": "your-text-string"
                  },
                  {
                      "type": "currency",
                      "currency": {
                          "fallback_value": "$100.99",
                          "code": "USD",
                          "amount_1000": 100990
                      }
                  },
                  {
                      "type": "date_time",
                      "date_time" : {
                          "fallback_value": "February 25, 1977",
                          "day_of_week": 5,
                          "day_of_month": 25,
                          "year": 1977,
                          "month": 2,
                          "hour": 15,
                          "minute": 33
                      }
                  },
                  {
                  "type": "date_time",
                      "date_time" : {
                      "fallback_value": "February 25, 1977",
                      "timestamp": 1485470276
                      }
                  }
              ]
          }]
      }
  }';
  var_dump(json_decode($json,true));*/


  //COMENTADO PARA SUBIR A PRODUCCION

  /*$response=curl($url, $tipoPeticion, $ACCESS_TOKEN, $opcionales);
  var_dump($response);

  $response=curl($url, $tipoPeticion, $ACCESS_TOKEN, $opcionales);*/
?>