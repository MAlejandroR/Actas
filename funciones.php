<?php

require_once "vendor/autoload.php";

use  PhpOffice\PhpSpreadsheet\Spreadsheet;
use Enlaces\Modulo;


/**
 * @param $file_certificado nombre del fichero de certificado de notas (el fichero grande)
 * @param $file_actilla nombre del fichero de la actilla (el fichero sencillo)
 * @return null|string un msj
 */      
function obtener_datos(){

    $file_certificado = "./ficheros/certificado.xls";
    $file_actilla = "./ficheros/actilla.xls";
    $msj = null;
    $error = null;

    if (!file_exists($file_certificado) || (!file_exists($file_actilla))) {
        $msj = "Aporte los ficheros de certificados y actillas para seguir";
    } else {
        $msj="Actualmente hay ficheros de los ciclos";
        if (!isset($_GET['siglas']) && (!isset($_POST['siglas']))) {
            header("Location:cargar_modulos.php");
            exit();
        }
    }
    return $msj;
}


function actua()
{


    switch ($_POST['submit']) {
        case "establecer":
            $first = false;
            $siglas = $_POST['siglas'];
            Modulos::setMerge($codigos, $siglas);
            $_SESSION['modulos'] = serialize(Modulos::getMerge());
            var_dump(Modulos::getMerge());
            break;
        case  "generar":
            $fichero = $_FILES['certificados'];
            $origen = $fichero['tmp_name'];
            //Borro destino por si existía
            // ??rmdir("./ficheros/origen.xls");
            //Mirar posibles problemas de concurrencia ????
            $destino = "./ficheros/origen.xls";
            move_uploaded_file($origen, $destino);
            header("Location:genera_xls.php");
            exit();
            break;
        case "visualiza":
            header("Location:mostrar_pantalla.php");
            exit();

    }
}

/**
 * @param $codigos array asociativo con codigo u nombre de módulo
 * '0484' => 'Bases de datos (1º)'},
 * @return string un array de objetos ej js para trabajarlo con javascript
 * {codigo:'0484', nombre:'Bases de datos (1º)'},
 * {codigo:'0485', nombre:'Programación (1º)'},
 *     ......
 *
 */
function genera_array_objetos_codigos($codigos)
{

    $txt = "[";
    foreach ($codigos as $codigo => $nombre) {
        $txt .= "\n\t\t\t\t\t\t{codigo:'$codigo', nombre:'$nombre'},";
    }
    //quitamos el último carácter
    $txt = substr($txt, 0, strlen($txt) - 1);
    $txt .= "\n\t\t\t\t\t]\n";
    return $txt;


}


/**
 * @param $siglas array con las siglas de los módulos
 * ['ED', 'FOL', 'IN1', 'LMS', 'PRO', 'SI', 'DAW', ...]
 * @return string un array en  js para trabajarlo con javascript
 * {'ED', 'FOL', 'IN1', 'LMS', 'PRO', 'SI', 'DAW'*     ......}
 *
 */
function genera_array_siglas($siglas)
{
    $txt = "[";
    foreach ($siglas as $sigla) {
        $txt .= "\n\t\t\t\t\t\t{name:'$sigla'}, ";
    }
    //quitamos el último carácter
    $txt = substr($txt, 0, strlen($txt) - 1);
    $txt .= "\n\t\t\t\t\t]\n";
    return $txt;

}


/**
 * Created by PhpStorm.
 * User: manuel
 * Date: 18/11/18
 * Time: 15:07
 */


/**
 * Los nombres de los alumnos están en una celda en la columna B
 * La celda tiene siempre este formato
 *
 * --------------------------------------------------------------------------------------
 * Dña. CARMEN PARICIO HERNANDEZ, Secretaria del Centro Público Integrado de Formación Profesional LOS ENLACES
 * con código de centro 50010314, dirección Cl. Jarque De Moncayo, 10, 50012 Zaragoza (Zaragoza), teléfono 976300804 y
 * correo electrónico cpilosenlaces@educa.aragon.es
 *
 * CERTIFICA:
 *
 * Que JOSÉ ANTONIO ANDREU RUBIO con DNI 76920305W, nacido el 11 de febrero de 1986, está matriculado en el centro
 * LOS ENLACES en DESARROLLO DE APLICACIONES WEB DE CICLOS FORMATIVOS DE FORMACIÓN
 * PROFESIONAL DE GRADO SUPERIOR, regulado por el Real Decreto 686/2010 (BOE 12/06/2010) y por Orden de
 * 08/07/2011 (BOA 28/07/2011), y que:"
 * --------------------------------------------------------------------------------------
 *Nos interesa el nombre, en este caso JOSÉ ANTONIO ANDREU RUBIO
 * Si hacemos un explode (" ", $texto), en nombre estará a partir de la posición 34
 * Podrá ser 34,35,36 y a veces 37 (esto cuando la posición 37 no sea la palabra "con"
 *
 * Después siempre ocurre que 11 filas después están los módulos
 * Estos son códigos
 * en la misma fila tenemos en la columna G el nombre del módulo
 *                          en la columna H la nota de ese módulo
 * Ejemplos:
 * ----------------------------------------------------------------------------
 * 0487    Entornos de desarrollo (1º)                                    7
 * 0617    Formación y orientación laboral (1º)                            CV-5
 * 0373    Lenguajes de marcas y sistemas de gestión de información (1º)    8
 * 0483    Sistemas informáticos (1º)                                        7
 * ----------------------------------------------------------------------------
 *
 * La  primera fila que no tiene código empieza siempre por Y
 *
 * ----------------------------------------------------------------------------
 * Y, con fecha 15 de noviembre de 2018, ha hecho la solicitud para traslado de expediente u otros efectos.
 * ----------------------------------------------------------------------------
 *
 */


/**
 *
 * recorremos la excell por la columna B
 * Si está por ejemplo la palabra CERTIFICA (uso este criterio aleatoriamente)
 * tomo el contenido de esa celda
 * hago el explode y me quedo con las posiciones 34,35 y 36 lo asigno a nombre
 * en caso de que haya 37 (que sea diferente a Y), lo tomo también como parte del nombre
 * Creo un array con ese índice (el del nombre)
 * Ahora se trata de agregar en esa posición todas las notas de ese alumno/a
 * Para ello actulizo la posición y leo hasta encontrar una fila que empiece por Y
 * Anoto código y notaç
 * Además creo un array con los nombres de los códigos de los módulos
 */

function get_alumnos_notas($worksheet, &$modulos)
{
    $n = 1;
    $alumnos = [];

//Numero de filas de la excell;
    $filas = $worksheet->getHighestRow();

    for ($f = 0; $f < $filas; $f++) {
        //Obtengo el valor de las celdas de la columna B
        $valor = $worksheet->getCellByColumnAndRow(2, $f)->getValue();
        //Si tiene la palabra CERTIFICA es que ahí está el nombre
        if (strpos($valor, "CERTIFICA") > 1) {
            $datos = explode(" ", $valor);
            
            $nombre = $datos[34] . " " . $datos[35] . " " . $datos[36];
            $n++;
            if ($datos[37] != "con")
                $nombre .= " " . $datos[37];
            $nombre = strtoupper($nombre);

            //11 filas después comienzan los módulos
            $f += 11;
            $modulo = $worksheet->getCellByColumnAndRow(2, $f)->getValue();
            while (is_string($modulo)) {

                $nombre_modulo = $worksheet->getCellByColumnAndRow(7, $f)->getValue();
                if (!isset($modulos[$modulo]))
                    //Lo convierto a string por que es un objeto
                    //que debe tener implementado el tostring y así tengo
                    //el valor que quiero leer
                    $modulos[$modulo] = (string)$nombre_modulo;
                $nota = $worksheet->getCellByColumnAndRow(12, $f)->getValue();
                $alumnos[$nombre][$modulo] = $nota;
                //echo "Módulo $modulo-$nombre: $nota <br />";
                $f++;
                $modulo = $worksheet->getCellByColumnAndRow(2, $f)->getValue();

            }

        }

    }//End for;
    //$modulo_obj->setCodigo($modulos);
    //$modulo_obj->setNombre($modulos);
    return $alumnos;
}

/**
 * @param $indice es un valor que quiero buscar como indice
 * @param $array array donde voy a buscar el índice
 * @return int posición de ese índice en el array si existe, -1 si no existe
 */
function search_index($indice, $array)
{
    $pos = 0;
    $retorno = -1;
    foreach ($array as $ind => $cont) {
        if ($indice === $ind) {
            $retorno = $pos;
        }
        $pos++;
    }
    return $retorno;
}

//====================================================

/**
 * @param $worksheet
 * @return array
 */
function get_alumnos_matriculas($worksheet)
{
    $n = 1;
    $alumnos = [];

//Numero de filas de la excell;
    $filas = $worksheet->getHighestRow();

    //Ojo devuelve letras de las columnas A B .. AA AB ...
    $col = $worksheet->getHighestColumn();

    //Con esto calculo en número de columnas (cambio la letra a número)
    $col = translate_letter_to_number($col);


    for ($f = 0; $f < $filas; $f++) {
        //Obtengo el valor de las celdas de la columna B = 2
        //En esta columna está la información
        $valor = $worksheet->getCellByColumnAndRow(2, $f)->getValue();

        if (is_numeric((int)$valor)) { //Esto es que esta fila nos interesa
            //sacamos el nombre de esta fila
            $nombre = $worksheet->getCellByColumnAndRow(3, $f)->getValue();
            if ((!is_null($nombre)) and (strpos($nombre, "Apellidos") === false))
                //En este caso tengo un alumno, miremos sus módulos matriculados
                $modulos = obtener_modulos_matriculados($worksheet, $f, $col);
            $alumnos[$nombre] = $modulos;
        }
    }//End for;
    return $alumnos;
}

//Retornará un array con todos los módulos matriculados de una fila

/**
 * @param $w Worksheet hoja excell
 * @param $f la fila del alumno
 * @param $col es el número de columnas donde están los módulos
 *
 * @return array con los módulos todos los módulos
 * true si está matriculado
 * false en caso de que no esté matriuclado
 *
 */
function obtener_modulos_matriculados(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $w, $f, $col)
{

    for ($i = 0; $i <= $col; $i++) {
        $modulo = (string)$w->getCellByColumnAndRow((4 + $i), 5);
        echo "<h1>Valor de módulo </h1>";
        var_dump($modulo);
        if ((!is_null($modulo)) && ($modulo != "Nº MNS") && $modulo != '') {
            $color = $w->getStyleByColumnAndRow(4 + $i, $f)->getFill();
            $c = $color->getEndColor()->getRGB();
            if ($c == "FFFFFF")
                $modulos[$modulo] = true;
            else
                $modulos[$modulo] = false;
            //echo "Color leído $c<br />";
        }
    }
    return $modulos;

}

/**
 * @param $col son letras de columnas de excell
 * Devuelve el entero A..Z AB AC .. 1..
 *
 */
function translate_letter_to_number($col)
{

    $letras = ord('Z') - ord('A') + 1; //son las letras del abecedario

    if (ord($col) <= 'Z')
        $retorno = ((ord($col) - ord('A')) + 1);
    else {
        $retorno1 = $letras + (ord($col[0]) - ord('A'));//Valor de la primera letra
        //echo "construyendo valor $retorno1<br />";
        $retorno2 = ord($col[1]) - ord('A');
        //echo "construyendo valor $col[1] - $retorno2<br />";
        $retorno = $retorno1 + $retorno2;
    }


    return $retorno;

}

/**
 * @param $alumnos
 * @param $matriculas
 * Esta función cogerá para cada alumno
 * En el array matriculas tengo si está o no matriculado en él
 * Si está matriculado pondré un color en ese alumno en el array alumno en el módulo correspondinete
 * Problemas con los índices
 * $matriculas
 *   'Andreu Rubio, José Antonio' =>
 * array (size=16)
 * 'BD
 * 1º' => boolean true
 * '' => boolean true
 * 'ED
 * 1º' => boolean false
 * 'FOL
 * 1º' => boolean true
 * ..........................................
 * $alumnos
 * array (size=82)
 * 'MOISÉS ÁLVAREZ DOCÓN' =>
 * array (size=7)
 * 'A052' => string '6' (length=1)
 * '0484' => string 'RC' (length=2)
 * '0487' => string 'RC' (length=2)
 * '0617' => string 'RC' (length=2)
 * '0373' => string 'NE' (length=2)
 * .........................
 */

function add_matriculas_alumnos(&$alumnos, $matriculas)
{
    //Primero establecer una relación entre los índices de los arrays
    /*
    'Andreu Rubio, José Antonio' =>
    'JOSÉ ANTONIO ANDREU RUBIO' =>
*/


}

function actualiza_codigos()
{
//Leo los valores previamente establecidos
//Si no no podría estar aquí, tendría que estar buscándolos

    //Leo por get o por post
    $siglas = isset($_GET["siglas"]) ? unserialize($_GET["siglas"]) : null;
    $codigos = isset($_GET["codigos"]) ? unserialize($_GET["codigos"]) : null;

    if ((!isset($_GET['siglas']) && (!isset($_POST['siglas']))) || $actualizar) {
        header("Location:cargar_modulos.php");
        exit();
    }
}




?>



