<?php
namespace Enlaces;


/**
 * Class Certificado
 * @package Enlaces
 * Referenica los ficheros de actilla, certificado, título de ciclo y estado (que sí que existen)
 */
Class Certificado
{


     private $certificado = "./ficheros/certificado.xls";
     private $actilla = "./ficheros/actilla.xls";
     private $title_ciclo=null;
    //Atributo para saber si existen o el fichero de certificado para generar informe
     private static $file_cetificado= false;


    /**
     * @param $file_certificado $file que viene del formulario
     * @param $file_actilla $file que viene del formulario
     * copia estos ficheros a la carpeta de ficheros con el nombre especificado de certificado y actilla
     */
 function copiar_ficheros($actilla,$certificado){
    
    $origen = $certificado['tmp_name'];
    $destino  ="./ficheros/certificado.xls";
    $a=move_uploaded_file($origen, $destino);
    $origen = $actilla['tmp_name'];
    $destino  ="./ficheros/actilla.xls";
    $b=move_uploaded_file($origen, $destino);
    if ($a and $b){
        $this->estado=true;
    }else
        echo "<h1>No se han podido copiar los fichero {$actilla['name']} y {$certificado['name']}";



}

    /**
     * método que mira a ver si existen ficheros de ceritificados y actillas
     * En caso de que existan asiganará al atributo $ciclo el nombre de ciclo y a $estado true
     * Si no existe asigará a $estado false
     */
     public function obtener_datos()
    {

        if (!file_exists($this->getCertificado()) || (!file_exists($this->getActilla()))) {
            $this->estado = false;
        } else {
            $this->estado = true;
            $this->title_ciclo = $this->getCiclo();
            if (is_null($this->title_ciclo)) {
                //Borramos los fichero
                unlink($this->certificado);
                unlink($this->actilla);
                $this->estado = false;
            }

        }
    }//End function obtener_datos


    /**
     * Busca el título del ciclo en el fichero excell del certificado
     * @return bool|string
     */
     private function getCiclo()
    {

        $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->getCertificado());
        //preparo el fichero para trabajar con él
        $worksheet = $sheet->getActiveSheet();

        $filas = $worksheet->getHighestRow();
        $f = 0;
        $valor = $worksheet->getCellByColumnAndRow(2, $f)->getValue();
        $pos1 = strpos($valor, "LOS ENLACES en");
        while (($pos1 < 1) && ($f<$filas)) {
            $valor = $worksheet->getCellByColumnAndRow(2, $f)->getValue();
            $pos1 = strpos($valor, "LOS ENLACES en");
            $f++;
        }
        if ($pos1 > 0) {
            $pos2 = strpos($valor, "DE CICLOS FORMATIVOS DE FORM");
            $len_title_ciclo = $pos2 - ($pos1 + strlen("LOS ENLACES en"));
            $titulo = substr($valor, $pos1 + strlen("LOS ENLACES en"), $len_title_ciclo);
            return $titulo;
        }
        return null;
    }


    //Getter and setter methods


     public function getTitleCiclo(){
        return $this->title_ciclo;
    }



    public static  function set_file_certificado($file){
         self::$file_cetificado = $file;
    }
    public static  function get_file_certificado(){
        return self::$file_cetificado;
    }

    public function getCertificado(){
         return $this->certificado;
    }
    public function getActilla(){
         return $this->actilla;
    }


}//End class


?>
