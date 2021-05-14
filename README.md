# VNET-php
Script PHP para la generación de los archivos .txt necesarios para el cobro mediante débito automático en VNET Argentina (VISA crédito, VISA débito, Mastercard)
El script llama a un PHP que contiene la estructura de conexion de la base de datos. Dicho script debe tener la estructura siguiente:
```
<?php

    function ConnBD() {
       $ConnBDConn = new mysqli('HOST', 'UsuarioBD', 'PasswordBD', 'NombreBD');
       if (!$ConnBDConn) {
         throw new Exception('Could not connect to database server');
       } else {
         return ConnBDConn;
       }
    }
?>
```
Al finalizar el script se puede añadir un envio automatico de los archivos via email mediante una secuencia similar a la siguiente:

```
include("/home/CallBack/classes/class.phpmailer.php");
include("/home/CallBack/classes/class.smtp.php");
$mail = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->SMTPSecure = "ssl";
$mail->Host = "SMTPHOST";
$mail->Port = PORT;
$mail->Username = "USUARIO";
$mail->Password = "PASSWORD";

$mail->From = "DESDE";
$mail->FromName = "DEBITOS";
$mail->Subject = "[DEBITOS] - DEBITOS AUTOMATICOS";
$CuerpoEmail .="<br><br>Por medio del presente se envian los pagos por debito automatico que se deben cobrar.";
$mail->Body = $CuerpoEmail;
$mail->AddAddress("DESTINATARIO","Administracion");
$mail->IsHTML(true);
$mail->AddAttachment("./DEBLIMC.txt");
$mail->AddAttachment("./DEBLIQD.txt");
$mail->AddAttachment("./DEBLIQC.txt");
$mail->AddAttachment("./DEBLIAMX.txt");

$mail->Send();
```
