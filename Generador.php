// Script generado por JJV (github/jjvillar269)
// 
//
// Edicion necesaria:
//    Lineas 15,16,17: Ingresar los Numeros de establecimiento VISA, MASTER y AMEX, en caso de corresponder
//    Linea 18: Ingresar la ruta al script de conexion a la base de datos
//    Linea 61: Agregar la estructura de busqueda del tipo de tarjeta
//    Linea 63: Agregar la estructura de busqueda del numero de la tarjeta
//
//
//  IMPORTANTE: NO me hago responsable de cobros indebidos ni con errores. Usar bajo responsabilidad.
//

<?php
DEFINE("EstVISA","XXXXXX");           // Nro de Establecimiento VISA
DEFINE("EstMASTER","XXXXXX");         // Nro de Establecimiento Master
DEFINE("EstAMX","XXXXXX");            // Nro de Establecimiento Amex
require_once('/folder_to/Database.php');

//CONECTO A BASE DE DATOS Y BUSCO CLIENTES CON DEBITO
$conn = ConnBD();

// Definicion de variables temporales
        $fecha=date('Ymd');
        $hora=date('Hi');

// Definicion de Contadores y Totales
$CANTC=$CANTD=$CANTMC=$CANTAMX=0;
$MONTOC=$MONTOD=$MONTOMC=$MONTOAMX=0;

// Elimino los archivos existentes
unlink("DEBLIQC.txt");
unlink("DEBLIQD.txt");
unlink("DEBLIMC.txt");
unlink("DEBLIAMX.txt");

$VISAC = fopen("DEBLIQC.txt", "c");
$VISAD = fopen("DEBLIQD.txt", "c");
$MASTER = fopen("DEBLIMC.txt", "c");
$AMEX = fopen("DEBLIAMX.txt", "c");

fwrite($VISAC,"0DEBLIQC 00".EstVISA."900000\t".$fecha.$hora."0" . PHP_EOL);
fwrite($VISAD,"0DEBLIQD 00".EstVISA."900000\t".$fecha.$hora."0" . PHP_EOL);
fwrite($MASTER,"0DEBLIMC 00".EstMASTER."900000\t".$fecha.$hora."0" . PHP_EOL);
fwrite($AMEX,"0DEBLIAMX 00".EstAMX."900000\t".$fecha.$hora."0" . PHP_EOL);

$BuscoDebitos=$conn->query("SELECT ID from TABLE where paymentmethod='DEBITO'");
while($row = mysqli_fetch_array($BuscoDebitos))
{
        $UserID = $row[0];
        $Monto = $conn->query("SELECT total,status,id from tblinvoices where userid='".$UserID."' ORDER BY id DESC limit 1");
        $MontoT = mysqli_fetch_array($Monto);
                $MontoCobro=$MontoT[0];
                $MONTO=str_replace('.','',$MontoT[0]);

                $Estado=$MontoT[1];
                $NroFACT=$MontoT[2];

        if($Estado=="Unpaid" && $MontoCobro>0)
        {
                $Tarjeta= BUSCO TIPO DE TARJETA;

                $TarjetaNRO=BUSCO NUMERO DE TARJETA;

                $MONTO=FORMATEO($MONTO,15);
                $IDENTIFICADOR=FORMATEO($UserID,15);
                $COMPROBANTE=FORMATEO($NroFACT,8);

                $LINEA="1".$TarjetaNRO."\t".$COMPROBANTE.$fecha."0005".$MONTO.$IDENTIFICADOR."E\t\t\t*";

                switch($Tarjeta)
                {
                        case 'VISA-DEBITO':
                                fwrite($VISAD, $LINEA . PHP_EOL);
                                $MONTOD=$MONTOD+$MontoCobro;
                                $CANTD++;
                                break;

                        case 'VISA-CREDITO':
                                fwrite($VISAC, $LINEA . PHP_EOL);
                                $MONTOC=$MONTOC+$MontoCobro;
                                $CANTC++;
                                break;

                        case 'MASTERCARD':
                                fwrite($MASTER, $LINEA . PHP_EOL);
                                $MONTOMC=$MONTOMC+$MontoCobro;
                                $CANTMC++;
                                break;
                        case 'AMEX':
                                fwrite($AMEX, $LINEA . PHP_EOL);
                                $MONTOAMX=$MONTOAMX+$MontoCobro;
                                $CANTAMX++;
                                break;
                }
        }
}
$CANTD=FORMATEO($CANTD,6);
$CANTC=FORMATEO($CANTC,6);
$CANTMC=FORMATEO($CANTMC,6);
$CANTAMX=FORMATEO($CANTAMX,6);

$MONTOD=str_replace('.','',$MONTOD);
$MONTOC=str_replace('.','',$MONTOC);
$MONTOMC=str_replace('.','',$MONTOMC);
$MONTOAMX=str_replace('.','',$MONTOAMX);
        $MONTOD=FORMATEO($MONTOD,15);
        $MONTOC=FORMATEO($MONTOC,15);
        $MONTOMC=FORMATEO($MONTOMC,15);
        $MONTOAMX=FORMATEO($MONTOAMX,15);

fwrite($VISAC,"9DEBLIQC 00".EstVISA."900000   ".$fecha.$hora."0".$CANTC.$MONTOC."\t\t\t\t");
fwrite($VISAD,"9DEBLIQD 00".EstVISA."900000   ".$fecha.$hora."0".$CANTD.$MONTOD."\t\t\t\t");
fwrite($MASTER,"9DEBLIMC 00".EstMASTER."900000   ".$fecha.$hora."0".$CANTMC.$MONTOMC."\t\t\t\t*");
fwrite($AMEX,"9DEBLIAMX 00".EstAMX."900000   ".$fecha.$hora."0".$CANTAMX.$MONTOAMX."\t\t\t\t*");
fclose($VISAD);
fclose($VISAC);
fclose($MASTER);
fclose($AMEX);

function FORMATEO($cadena,$cantidad)
{
        do
	{
                $cadena="0".$cadena;
        } while(strlen($cadena)<$cantidad);
        return $cadena;
}
?>
