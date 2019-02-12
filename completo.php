<?php

/**
 * Created by PhpStorm.
 * User: manuel
 * Date: 17/11/18
 * Time: 10:33
 */

require 'vendor/autoload.php';
require 'funciones3.php';
spl_autoload_register(function($clase){
    require "$clase.php";
} );

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Fill;


$file = "./ficheros/origen.xls";
$file_actilla = "./ficheros/origen_actilla.xls";

//__$sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
//preparo el fichero para trabajar con él
//__$worksheet = $sheet->getActiveSheet();
//De la actilla extraeremos de lo que está matriculado cada alumno
$sheet_actilla = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_actilla);
$sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
$worksheet=$sheet->getActiveSheet();
//preparo el fichero para trabajar con él
$worksheet_actilla = $sheet_actilla->getActiveSheet();

$modulos=[];
$alumnos = get_alumnos_notas($worksheet, $modulos);
$matriculas = get_alumnos_matriculas($worksheet_actilla);
echo "<h1>Metriculas</h1>";
var_dump($matriculas);

add_matriculas_alumnos($alumnos, $matriculas);
Alumnos::nombre_apellido($matriculas);
Alumnos::apellido_nombre($alumnos);

echo "<h1> Nombres</h1>";
var_dump(Alumnos::get_apellido_nombre());
echo "<h1> Apellido</h1>";
var_dump(Alumnos::get_nombre_apellido());
/*
echo "<h1>Array alumnos</h1>";                   
var_dump($alumnos);
*/
echo "<h1> Array matriculas</h1>";
var_dump($matriculas);
Modulos::siglas(next($matriculas));

echo "<h1> Modulos: código</h1>";
var_dump(Modulos::get_codigo());
echo "<h1> Modulos; nombre</h1>";
var_dump(Modulos::get_nombre());
echo "<h1> Modulos; siglas</h1>";
var_dump(Modulos::get_siglas());




//Ya tenemos los alumnos, ahora vamos a crear la excell

$spreadsheet = new Spreadsheet();  /*----Spreadsheet object-----*/
$spreadsheet->setActiveSheetIndex(0);
$ws= $spreadsheet->getActiveSheet();

echo "<h1>Modulos</h1>";
var_dump($modulos);

$n=1;
foreach ($modulos as $codigo=>$nombre){
    $n++;
    $ws->getCellByColumnAndRow($n,1)->setValue($codigo);
}
/*
echo "<h1>Alumnos</h1>";
var_dump($alumnos);
*/
echo "<h1>Combinando módulos </h1>";
Modulos::combinar();
$n=1;
foreach ($alumnos as $alumno=>$notas){
    $n++;
    $ws->getCellByColumnAndRow(1,$n)->setValue($alumno);
    foreach ($notas as $cod_modulo=>$nota) {

        $pos=search_index($cod_modulo,$modulos);
        $ws->getCellByColumnAndRow(($pos+2),$n)->setValue($nota);
        $styleArray=[
            'borders'=>[
                'top'=>['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,],
                'bottom'=>['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,],
                'left'=>['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,],
                'right'=>['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
            ]
        ];
        if (((int)$nota >=5) || ($nota =="CV-5")) {
            //En este caso quiero cambiar el backgroun color
            $styleArray = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    //'rotation' => 90,
                    'startColor' => [
                        'argb' => 'FFFF0000',
                    ],
                    'endColor' => [
                        'argb' => 'FFFF0000',
                    ],
                ],
            ];
        }
        $ws->getStyleByColumnAndRow(($pos + 2), $n)
            ->applyFromArray($styleArray);
            /*
                [
                    'fill' =>[
                        'type' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'AC983C']
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '0000FF']

                    ]
                ]
            );
            */
    }



}

//Ponemos anchura para todas las columnas
//Para ello en cada columna referenciada por la letra A.. establecemos una dimensión autosize

$n=1;
$columns = count ($modulos)+1;
for ($n = 0; $n <$columns;$n++) {
    $cod = ord("A");
    $cod+=$n;
    $char = chr($cod);

    $ws->getColumnDimension($char)->setAutoSize(true);
}
$filas = $worksheet->getHighestRow(); // e.g. 10
for ($n = 1; $n <=$filas;$n++) {
    //medida facilitada en pixeles
    $ws->getRowDimension($n,true)->setRowHeight(18);
}



$ws->setTitle('Notas Alumnos');
$writer = IOFactory::createWriter($spreadsheet, 'Xls');

//Obejetivo es visualizarlo en el html en lugar de descargar
//Dejar la descarga como opción de botón
//Descarga como pdf o xml


//$writer->save('./ficheros/mi_destino.xls');

/*
// Redirect output to a client’s web browser (Xls)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="salida.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
*/
/*__
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="salida.xls"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xls');
$writer->save("php://output");

echo "hola";

*/





?>


