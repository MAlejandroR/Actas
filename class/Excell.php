<?php


namespace Enlaces;

use PhpOffice\PhpSpreadsheet\Spreadsheet as hoja_excell;
use PhpOffice\PhpSpreadsheet\IOFactory as Estilo;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Fill as Relleno;
use PhpOffice\PhpSpreadsheet\Style\Border as Borde;

use Enlaces\Alumno;
use Enlaces\Certificado;
use Enlaces\Modulo;


class Excell
{


    private static $spreadsheet;
    private static $ws;


    static public function genera_xls($alumnos, $modulos, $titulo)
    {

        self::$spreadsheet = new  hoja_excell();  /*----Spreadsheet object-----*/
        self::$spreadsheet->setActiveSheetIndex(0);
        self::$ws = self::$spreadsheet->getActiveSheet();


        $n = 2; //PAra dejar libres las dos primeras columas
        //var_dump($modulos);
        $style = ['font' => ['bold' => true, 'size' => 9], 'borders' => [
            'top' => ['borderStyle' => Borde::BORDER_THIN,],
            'bottom' => ['borderStyle' => Borde::BORDER_THIN,],
            'left' => ['borderStyle' => Borde::BORDER_THIN,],
            'right' => ['borderStyle' => Borde::BORDER_THIN,]
        ]];

        //Contamos cuantas columnas vamos a tener
        //número de módulos +1 de números +1 de nombres.
        $len_col = sizeof($modulos->getSiglas()) + 2;//

        $num_letra = ord('A') + $len_col - 1;//Ya que la A, ya la tengo
        $letra = chr($num_letra);
        self::$ws->mergeCells("A1:$letra" . "1");

        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 14,
            ],

            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => ['borderStyle' => Borde::BORDER_THIN,],
                'bottom' => ['borderStyle' => Borde::BORDER_THIN,],
                'left' => ['borderStyle' => Borde::BORDER_THIN,],
                'right' => ['borderStyle' => Borde::BORDER_THIN,]
            ],
            'fill' => [
                'fillType' => Relleno::FILL_SOLID,
//'rotation' => 90,
                'startColor' => [
                    'rgb' => "FF0000",
                ],
                'endColor' => [
                    'rgb' => "FF0000"
                ],
            ],
        ];
        self::$ws->getStyleByColumnAndRow(1, 1)
            ->applyFromArray($styleArray);

        self::$ws->getRowDimension('1')->setRowHeight(30);
        self::$ws->getCellByColumnAndRow(1, 1)->setValue($titulo);


        $fila = 2; //Para empezar en esta fila y dejar la primera para títutlo
        foreach ($modulos->getMerge() as $codigo => $datos) {
            $n++;
            $m[] = $datos['sigla']; //Para tener una referencia posicional de los códigos de módulos
            self::$ws->getStyleByColumnAndRow($n, $fila)->applyFromArray($style);
            self::$ws->getCellByColumnAndRow($n, $fila)->setValue($datos['sigla']);
        }
        self::$ws->getStyleByColumnAndRow($n + 1, $fila)->applyFromArray($style);
        self::$ws->getCellByColumnAndRow($n + 1, $fila)->setValue('PRY');//Agrego proyecto explícitamente
        $m[] = "PRY";
        $m[] = "FCT";
        //ya que este módulo no aparece en el certificado de dónde saco los módulos
        $n = $fila;
        //var_dump($alumnos->getExpediente());
        //var_dump($m);
        foreach ($alumnos->getExpediente() as $alumno => $notas) {
            //var_dump($notas);
            $n++;
            //self::$ws->getCellByColumnAndRow(1, $n)->setValue($datos['sigla']);
            self::$ws->getStyleByColumnAndRow(1, $n)->applyFromArray($style);
            self::$ws->getCellByColumnAndRow(1, $n)->setValue($n - 1);
            self::$ws->getStyleByColumnAndRow(2, $n)->applyFromArray($style);

            self::$ws->getCellByColumnAndRow(2, $n)->setValue($alumno);
            //var_dump($notas);
            //var_dump($m);

            foreach ($notas as $cod_modulo => $nota) {
                //$m = $modulos->getMerge();

                $pos = array_search($cod_modulo, $m);
                //ar_dump($cod_modulo);
                //ar_dump($m);
                //var_dump($pos);
                //var_dump($nota);

                //Establecemos el color en función del valor de nota

                switch ($nota) {
                    case "MATRICULADO":
                        $color = "FFFFFF";//$colores['matriculado'];//"FFfbaa"; //matriculado
                        break;
                    case false://Pendiente
                        $color = "9c9c9c";//$colores['pendiente'];//"636363FF";//no matriculado
                        break;
                    default://aprobado
                        $color = "00FF00";//$colores['aprobado'];//"FF9e81";//ya aprobado
                }
                $styleArray = [
                    'font' => [
//                        'bold' => true,
                        'size' => 9,
                    ],

                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                    'borders' => [
                        'top' => ['borderStyle' => Borde::BORDER_THIN,],
                        'bottom' => ['borderStyle' => Borde::BORDER_THIN,],
                        'left' => ['borderStyle' => Borde::BORDER_THIN,],
                        'right' => ['borderStyle' => Borde::BORDER_THIN,]
                    ],
                    'fill' => [
                        'fillType' => Relleno::FILL_SOLID,
//'rotation' => 90,
                        'startColor' => [
                            'rgb' => $color,
                        ],
                        'endColor' => [
                            'rgb' => $color,
                        ],
                    ],
                ];
                self::$ws->getStyleByColumnAndRow(($pos + 3), $n)
                    ->applyFromArray($styleArray);
                if (($nota != "MATRICULADO") && ($nota !== false))
                    self::$ws->getCellByColumnAndRow(($pos + 3), $n)->setValue($nota);
            }//End foreach notas
        }
//Ponemos anchura para todas las columnas
//Para ello en cada columna referenciada por la letra A.. establecemos una dimensión autosize
        $n = 1;
        $columns = count($modulos->getCodigos()) + 3;//Para coger fct y proyectos

        for ($n = 0; $n < $columns; $n++) {
            $cod = ord("A");
            $cod += $n;
            $char = chr($cod);

            $w = 4.2;
            switch ($n) {
                case 0:
                    $w = 3;
                    break;
                case 1:
                    $w = 24;
                    break;
            }

            self::$ws->getColumnDimension($char)->setWidth($w);
        }
        $filas = self::$ws->getHighestRow(); // e.g. 10
        for ($n = 2; $n <= $filas; $n++) {
//medida facilitada en pixeles
            self::$ws->getRowDimension($n, true)->setRowHeight(18);
        }

        //Contamos cuantos módulos tenemos alumnos hay matriculados de cada móduloç


        for ($f = 3; $f <= $filas; $f++) {
            //Intentar contar número de módulos matriculados
            for ($c = 3; $c <= $columns; $c++) {
                $color = self::$ws->getStyleByColumnAndRow($c, $f)->getFill();
                $col = $color->getEndColor()->getRGB();
                if ($col == "FFFFFF") //Porque está matriculado en ese módulo
                    $num_modulos_matriculados[$c]++;
                //if color FFFFFF Matriculado
            }
        }
//        var_dump($num_modulos_matriculados);


        $color = "FF0000";

        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 14,
            ],

            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'top' => ['borderStyle' => Borde::BORDER_THIN,],
                'bottom' => ['borderStyle' => Borde::BORDER_THIN,],
                'left' => ['borderStyle' => Borde::BORDER_THIN,],
                'right' => ['borderStyle' => Borde::BORDER_THIN,]
            ],
            'fill' => [
                'fillType' => Relleno::FILL_SOLID,
//'rotation' => 90,
                'startColor' => [
                    'rgb' => $color,
                ],
                'endColor' => [
                    'rgb' => $color,
                ],
            ],
        ];
        self::$ws->getRowDimension($filas+1, true)->setRowHeight(18);

        self::$ws->mergeCells("A".($filas+1).":B".($filas+1));
        self::$ws->getStyleByColumnAndRow(1, ($filas + 1))->applyFromArray($styleArray);
        self::$ws->getCellByColumnAndRow(1, ($filas + 1))->SetValue("Total Matrículas");


        foreach ($num_modulos_matriculados as $num_modulo => $num_matriculas) {

            self::$ws->getStyleByColumnAndRow($num_modulo, ($filas + 1))->applyFromArray($styleArray);
            self::$ws->getCellByColumnAndRow($num_modulo, ($filas + 1))->setValue($num_matriculas);
        }

        self::$ws->freezePane("A3");


        self::$ws->setTitle("Acta notas");

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xls"');
        $writer = Estilo::createWriter(self::$spreadsheet, 'Xls');
        ob_end_clean();
        ob_start();
        $writer->save('php://output');


    }

}
