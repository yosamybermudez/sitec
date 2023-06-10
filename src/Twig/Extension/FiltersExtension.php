<?php
/**
 * Created by PhpStorm.
 * User: Samy
 * Date: 15/6/2019
 * Time: 17:26
 */

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use DateTime;

class FiltersExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('edad', array($this, 'edadFilter')),
            new TwigFilter('fecha_nacimiento', array($this, 'fechaNacimientoFilter')),
            new TwigFilter('fecha_es', array($this, 'fechaEsFilter')),
            new TwigFilter('fechahora_es', array($this, 'fechaHoraEsFilter')),
            new TwigFilter('empty_value', array($this, 'emptyValue')),
            new TwigFilter('empty_value_ne', array($this, 'emptyValueNE')),
            new TwigFilter('mes_es', array($this, 'mesFilter')),
            new TwigFilter('letras', array($this, 'letras')),
            new TwigFilter('safe_encrypt', array($this, 'safeEncrypt')),
            new TwigFilter('safe_decrypt', array($this, 'safeDecrypt')),
            new TwigFilter('obtener_anno', array($this, 'obtenerAnno')),
            new TwigFilter('traducirSiglas', array($this, 'traducirSiglas')),
        );
    }

    public function fechaNacimientoFilter($carneIdentidad){
        if(strpos($carneIdentidad,0,1) === '0'){
            $anno = '20'.substr($carneIdentidad,0,2);
        }
        else{
            $anno = '19'.substr($carneIdentidad,0,2);
        }
        $mes = substr($carneIdentidad,2,2);
        $dia = substr($carneIdentidad,4,2);
        return $dia.' de '.$this->mesFilter($mes).' del '.$anno;
    }

    public function fechaEsFilter($fecha){
        if(!is_a($fecha,\DateTime::class)){
            $fecha = new \DateTime($fecha);
        }

        return date('j',$fecha->getTimestamp()).' de '.$this->mesFilter(date('n',$fecha->getTimestamp())).' de '.date('Y',$fecha->getTimestamp());

    }

    public function traducirSiglas($texto) : string {
        switch ($texto){
            //Estados por lo que pasa la orden
            case 'ESP' : return 'En espera de ser atendido';
            case 'LE' : return 'Listo para entregar';
            case 'RA' : return 'Se revisará ahora';
            case 'EP' : return 'En espera de piezas';
            case 'N' : return 'Notificado';
            case 'OP' : return 'Otro problema';


            case 'R' : return 'Resuelto';
            case 'NR' : return 'No resuelto';
            case 'AOT' : return 'Asignar a otro técnico';
            case 'EC' : return 'Entregar al cliente';
            case 'C' : return 'Cobrado';
            case 'EEC' : return 'Equipo entregado al cliente';
            case 'TR' : return 'Técnico revisando';
            case 'PNE' : return 'Problema no encontrado';
            case 'NPN' : return 'No hay la pieza que se necesita';
            case 'NPR' : return 'No se puede reparar';
            case 'EME' : return 'Equipo en mal estado';
            case 'NED' : return 'No se encontró el defecto';
            case 'ET' : return 'Enviado al técnico';
            case 'DAT' : return 'Demora en la atención. El cliente se fue';
            case 'NQ' : return 'El técnico no quizo hacer el trabajo';
            case 'DF' : return 'Defectación';
            case 'MT' : return 'Mantenimiento';



            case 'EAC' : return 'Entregado al cliente';
            case 'CF' : return 'El cliente se fue';
            case 'NRT' : return 'No se realizará el trabajo';

            case 'ECT' : return 'En cola para el técnico';
            case 'AT' : return 'Asignado al técnico';

            case 'DT' : return 'Dejado en taller';
            case 'RES' : return 'Reservación';
            case 'DEC' : return 'Decomisado';
            case 'CANC' : return 'Reserva cancelada';


            case 'E' : return 'Entrada';
            case 'S' : return 'Salida';

            default :  return 'Sin traducción';
        }
    }

    public function fechaHoraEsFilter($fecha){
        $fechaA = $this->fechaEsFilter($fecha);
        return $fechaA . " a las " . date('h:i:s a', $fecha->getTimestamp());

    }

    public function emptyValue($var){
        if(is_null($var) || $var === '' || $var === 0){
            return '-';
        }else{
            return $var;
        }
    }

    public function emptyValueNE($var){
        if(is_null($var) || $var === '' || $var === 0){
            return 'N/E';
        }else{
            return $var;
        }
    }

    public function safeEncrypt(string $message){
        if(mb_strlen('aaa', '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES){
            throw new \RangeException('Key is not the correct size (must be 32 bytes).');
        }
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $cipher = base64_encode(
            $nonce.
            sodium_crypto_secretbox(
                $message,
                $nonce,
                $key
            )
        );
        sodium_memzero($message);
        sodium_memzero($key);
        return $cipher;
    }

    public function safeDecrypt(string $encrypted, string $key){
        $decoded = base64_decode($encrypted);
        $nonce = mb_substr($decoded, 0 , SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $plain = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            $key
        );
        if(!is_string($plain)){
            throw new Exception('Invalid MAC');
        }
        sodium_memzero($ciphertext);
        sodium_memzero($key);
        return $plain;
    }

    public function mesFilter($mes){
        switch ($mes){
            case '01': return "enero";
            case '02': return "febrero";
            case '03': return "marzo";
            case '04': return "abril";
            case '05': return "mayo";
            case '06': return "junio";
            case '07': return "julio";
            case '08': return "agosto";
            case '09': return "septiembre";
            case '10': return "octubre";
            case '11': return "noviembre";
            case '12': return "diciembre";
        }
    }

    public function edadFilter($carneIdentidad)
    {
        if(strpos($carneIdentidad,0,1) === '0' or strpos($carneIdentidad,0,1) === '1'){
            $anno = '20'.substr($carneIdentidad,0,2);
        }
        else{
            $anno = '19'.substr($carneIdentidad,0,2);
        }
        $mes = substr($carneIdentidad,2,2);
        $dia = substr($carneIdentidad,4,2);
        $fecha_nac = $anno.'-'.$mes.'-'.$dia;
        $hoy = new DateTime();
        $fecha = new DateTime($fecha_nac);
        $edad = $hoy->diff($fecha);
        return $edad->y;
        //$fecha_nac->setDate($anno::int,$mes::int,$dia::int);
    }

    function letras($xcifra)
    {
        $xarray = array(0 => "Cero",
            1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
            "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
            "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
            100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
//
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $xdecimales = "00";
        if (!($xpos_punto === false)) {
            if ($xpos_punto == 0) {
                $xcifra = "0" . $xcifra;
                $xpos_punto = strpos($xcifra, ".");
            }
            $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
            $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }

        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
        $xcadena = "";
        for ($xz = 0; $xz < 3; $xz++) {
            $xaux = substr($XAUX, $xz * 6, 6);
            $xi = 0;
            $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While
            while ($xexit) {
                if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                    break; // termina el ciclo
                }

                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
                for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                    switch ($xy) {
                        case 1: // checa las centenas
                            if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas

                            } else {
                                $key = (int) substr($xaux, 0, 3);
                                if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                    if (substr($xaux, 0, 3) == 100)
                                        $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                }
                                else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                    $key = (int) substr($xaux, 0, 1) * 100;
                                    $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                            break;
                        case 2: // checa las decenas (con la misma lógica que las centenas)
                            if (substr($xaux, 1, 2) < 10) {

                            } else {
                                $key = (int) substr($xaux, 1, 2);
                                if (TRUE === array_key_exists($key, $xarray)) {
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux);
                                    if (substr($xaux, 1, 2) == 20)
                                        $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3;
                                }
                                else {
                                    $key = (int) substr($xaux, 1, 1) * 10;
                                    $xseek = $xarray[$key];
                                    if (20 == substr($xaux, 1, 1) * 10)
                                        $xcadena = " " . $xcadena . " " . $xseek;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)
                            break;
                        case 3: // checa las unidades
                            if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada

                            } else {
                                $key = (int) substr($xaux, 2, 1);
                                $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                $xsub = $this->subfijo($xaux);
                                $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                            break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO

            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena.= " DE";

            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena.= " DE";

            // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
            if (trim($xaux) != "") {
                switch ($xz) {
                    case 0:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN BILLON ";
                        else
                            $xcadena.= " BILLONES ";
                        break;
                    case 1:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN MILLON";
                        else
                            $xcadena.= " MILLONES";
                        break;
                    case 2:
                        if ($xcifra < 1) {
                            $xcadena = "CERO CON $xdecimales/100";
                        }
                        if ($xcifra >= 1 && $xcifra < 2) {
                            $xcadena = "UNO CON $xdecimales/100";
                        }
                        if ($xcifra >= 2) {
                            $xcadena.= " CON $xdecimales/100"; //
                        }
                        break;
                } // endswitch ($xz)
            } // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para México se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR ($xz)
        return trim($xcadena);
    }

// END FUNCTION

    function subfijo($xx)
    { // esta función regresa un subfijo para la cifra
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen === 1 || $xstrlen === 2 || $xstrlen === 3)
            $xsub = "";
        //
        if ($xstrlen === 4 || $xstrlen === 5 || $xstrlen === 6)
            $xsub = "MIL";
        //
        return $xsub;
    }

    function obtenerAnno($fecha)
    { // esta función regresa un subfijo para la cifra
        $anno = date('Y', $fecha);

        return string($anno);
    }

}