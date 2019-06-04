<?php

/**
 * Created by PhpStorm.
 * User: manuel
 * Date: 17/11/18
 * Time: 10:33
 */

require 'vendor/autoload.php';
require 'funciones.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Enlaces\Alumno;
use Enlaces\Certificado;
use Enlaces\Modulo;
use Enlaces\Excell;


session_start();



//echo "<h1>Antes de leer serializable </h1>";
$certificado_obj = unserialize($_SESSION['certificado_obj']);
if (($certificado_obj) == false) header("Location:index.php");

$modulos_obj = unserialize($_SESSION['modulo_obj']);
//if (($modulos_obj)==false) header("Location:index.php");

//Array del tipo
// $modulo[codgio][nombre]=sigla
// $modulo[codgio][sigla]=modulo
$alumnos_obj = unserialize($_SESSION['alumno_obj']);
if (($alumnoss_obj) == false)
    $alumnos_obj = new Alumno();


$modulos = [];
$siglas = $modulos_obj->getMerge();


$file = $certificado_obj->getCertificado();
$file_actilla = $certificado_obj->getActilla();
$ciclo = $certificado_obj->getTitleCiclo();


//Leemos la xls de la que vamos a sacar la información
$sheet = PhpOffice\PhpSpreadsheet\IOFactory::load($file);
//preparo el fichero para trabajar con él
$worksheet = $sheet->getActiveSheet();


//Esta es la información que necesito para generar la xls
//Esta la saco del certificado, falta agregar la de la actilla
$alumnos_obj->setNotasCertificado($worksheet, $modulos);


$sheet_actilla=IOFactory::load($file_actilla);
$worksheet_actilla=$sheet_actilla->getActiveSheet();


$alumnos_obj->setNotasActilla($worksheet_actilla);

$alumnos_obj->setExpediente($modulos_obj);
//var_dump($alumnos_obj);
$titulo = $certificado_obj->getTitleCiclo();
Excell::genera_xls($alumnos_obj, $modulos_obj, $titulo);
//var_dump($modulos_obj);

//var_dump($modulos_obj);
//var_dump($alumnos_obj);

?>


