<?php
namespace Enlaces;


/**
 * Class Ficheros
 * @package Enlaces
 * Referenica los ficheros de actilla, certificado y título de ciclo
 */
Class Ficheros
{


    private $actilla = "actilla.xls";
    private $certificado = "certificado.xls";


    //Atributo para saber si existen o el fichero de certificado para generar informe
    private $title_ciclo = null;

    private $dir_base; //Directorio donde están los ficheros actilla y acta.
    // Estarán bajo el directorio ficheros del directorio raiz del proyecto
    //Atributo actualizado en constructor
    //Termina en barra /

    /**
     * Ficheros constructor
     */
    public function __construct(){

        $actualDir = dirname(__FILE__);
        $baseDir = dirname($actualDir);
        $this->dir_base = "$baseDir/ficheros/";
    }

    /**
     * @return bool si existen el fichero de certificado y de actilla devuelve true
     * false en cualquier otro caso
     */
    public function isset_certificado()
    {
        $dir = $this->dir_base;

        $f1 = $dir . $this->certificado;

        $f2 = $dir . $this->actilla;


        if (file_exists($f1) && (file_exists($f2))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string el nombre del directorio donde están los ficheros
     * MRM_PEND Intención de cambiar el / por la constantie __DIR_SEPARATATOR__
     */
    private function get_directory()
    {
        $actualDir = dirname(__FILE__);
        $baseDir = dirname($actualDir);
        return ("$baseDir/ficheros/");
    }

    public function get_certificado()
    {
        return $this->certificado;
    }

    public function getCertificado()
    {
        return $this->certificado;
    }

    public function set_certificado($file_certificado)
    {
        var_dump($this);
        $this->certificado = $file_certificado;
    }

    public function get_actilla()
    {
        return $this->actilla;
    }

    public function getActilla()
    {
        return $this->actilla;
    }


    //Getter and setter methods

    public function set_actilla($file_actilla)
    {

        $this->actilla = $file_actilla;
    }

    /**
     * @param $file_certificado $file que viene del formulario
     * @param $file_actilla $file que viene del formulario
     * copia estos ficheros a la carpeta de ficheros con el nombre especificado de certificado y actilla
     */
    function copiar_ficheros($actilla, $certificado)
    {

        $origen = $certificado['tmp_name'];
        $destino = "./ficheros/certificado.xls";
        $a = move_uploaded_file($origen, $destino);
        $origen = $actilla['tmp_name'];
        $destino = "./ficheros/actilla.xls";
        $b = move_uploaded_file($origen, $destino);
        if ($a and $b) {
            $this->estado = true;
        } else
            echo "<h1>No se han podido copiar los fichero {$actilla['name']} y {$certificado['name']}";


    }

    /**
     * método que mira a ver si existen ficheros de ceritificados y actillas
     * En caso de que existan asiganará al atributo $ciclo el nombre de ciclo y a $estado true
     * Si no existe asigará a $estado false
     */
    public function obtener_datos(){

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
    }

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
        while (($pos1 < 1) && ($f < $filas)) {
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

    public function getTitleCiclo()
    {
        return $this->title_ciclo;
    }


}//End class


?>
