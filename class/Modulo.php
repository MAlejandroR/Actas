<?php
/**
 * Created by PhpStorm.
 * User: manuel
 * Date: 21/11/18
 * Time: 22:39
 */
namespace Enlaces;
require 'vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Fill;


/**
 * Class Modulo
 * @package Enlaces
 * contiene información de los módulos de un ciclo
 * Con nombres, códigos, siglas y también una combinación (merge)
 * La combinación es por código
 *              $merge [$codigo][$nombre]= $sigla
 *              $merge [$codigo][$sigla]= $nombre
 */
class Modulo
{
    private $nombre = []; //"Desarrollo de aplicaciones en entorno servidor"
    private $codigos = []; //
    private $siglas = [];//DWES
    private $merge = [];
    private $inicializado = false;


    public function getInicializado()
    {
        return $this->inicializado;
    }

    public function setInicializado($estado)
    {
        $this->inicializado = $estado;
    }


    public function saluda()
    {
        echo "<h1>Hola</h1>";
    }

    public function getMerge()
    {
        return $this->merge;
    }
    public function getNombre()
    {
        return $this->nombre;
    }




    /**
     *Este método pretende combinar la información de los arrays en uno relacionado
     * $nombre es un array con los nombres completos el "Desarrollo de aplicaciones en entorno servidor"
     */
// Duplicado en setSiglas mirar de quitar y no usar
//A partir de la actilla obtendré las siglas de los módulos
    /**
     * @param $codigos array asociativo codigo_modulo=>nombre_modulo
     * @param $siglas array asociativo codigo_modulo=>sigla
     */
    public function setMerge($siglas_asignadas)
    {

        foreach ($this->codigos as $codigo => $nombre) {

            //$this->merge["'$codigo'"]=[];//Para evitar warning.
            $this->merge[$codigo]['nombre'] = $nombre;
            $this->merge[$codigo]['nombre'] = $nombre;
            $this->merge[$codigo]['sigla'] = $siglas_asignadas[$codigo];
        }


    }


    /**
     * @param $worksheet la excell para sacar las siglas de los módulos
     * Este método extrae las siglas de los módulos de una excell
     */
    public function get_siglas_modulos($worksheet)
    {
        $n = 1;

        $fila = 5; //La información de los módulos está en la fila 5
        //La primera columna es la 4.
        $col = 4;
        $valor = (string)$worksheet->getCellByColumnAndRow($col, $fila)->getValue();
        $n = 1;
        //Le
        while ($valor != "Nº MNS") {
            //IMPORTANTE solo quiero el string
            $valor = (string)$worksheet->getCellByColumnAndRow($col + $n, $fila)->getValue();
            $n++;
            if ($valor != "") {
                //quitamos los 3 últimos caracteres para quedarnos
                //solo con el nombre
                $s[] = substr($valor, 0, (strlen($valor) - 4));
            }
        }
        $this->siglas = $s;
    }

    /**
     * @param $worksheet
     *
     * Este método extrae los códigos de los módulos y los pone
     * ojo puede haber módulos cuyos códigos no aparezcan aquí
     */
    function get_codigo_nombre_modulos($worksheet)
    {
        $n = 1;
        $alumnos = [];

        //Numero de filas de la excell;
        $filas = $worksheet->getHighestRow();
        $modulos = [];

        for ($f = 0; $f < $filas; $f++) {
            //Obtengo el valor de las celdas de la columna B
            $valor = (string)$worksheet->getCellByColumnAndRow(2, $f)->getValue();
            //Si tiene la palabra CERTIFICA es que ahí está el nombre
            $f++;
            if ($valor === "Código (1)") {
                $modulo = (string)$worksheet->getCellByColumnAndRow(2, $f)->getValue();
                $nombre_modulo = (string)$worksheet->getCellByColumnAndRow(7, $f)->getValue();
                $f++; //Avanzamos f
                //ila;

                while (is_numeric(substr($modulo, 1))) {//Estos son los códigos

                    if (!array_key_exists($modulo, $modulos))
                        $modulos[$modulo] = $nombre_modulo;
                    $f++;
                    $modulo = (string)$worksheet->getCellByColumnAndRow(2, $f)->getValue();
                    $nombre_modulo = (string)$worksheet->getCellByColumnAndRow(7, $f)->getValue();
                }
            }

        }//End for;
        $this->codigos = $modulos;
    }

    /**
     * @param $col son letras de columnas de excell
     * Devuelve el entero A..Z AB AC .. 1..
     *
     */
    public function translate_letter_to_number($col)
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

    /*
     * /var/www/excell/completo.php:62:

array (size=15)
  0 => string 'BD' (length=2)
  1 => string 'ED' (length=2)
  2 => string 'FOL' (length=3)
  3 => string 'IN1' (length=3)
  4 => string 'LMS' (length=3)
  5 => string 'PRO' (length=3)
  6 => string 'SI' (length=2)
  7 => string 'DAW' (length=3)
  8 => string 'DIW' (length=3)
  9 => string 'DWC' (length=3)
  10 => string 'DWS' (length=3)
  11 => string 'EIE' (length=3)
  12 => string 'IN2' (length=3)
  13 => string 'PRY' (length=3)
  14 => string 'FCT' (length=3)
    * 3 0 1 2 4 5 6 
    ============================
    'A052' => string 'Lengua Extranjera profesional: Inglés 1 (1º)' (length=46)
  '0484' => string 'Bases de datos (1º)' (length=20)
  '0487' => string 'Entornos de desarrollo (1º)' (length=28)
  '0617' => string 'Formación y orientación laboral (1º)' (length=39)
  '0373' => string 'Lenguajes de marcas y sistemas de gestión de información (1º)' (length=64)
  '0485' => string 'Programación (1º)' (length=19)
  '0483' => string 'Sistemas informáticos (1º)' (length=28)
  '0612' => string 'Desarrollo web en entorno cliente (2º)' (length=39)
  '0613' => string 'Desarrollo web en entorno servidor (2º)' (length=40)
  '0614' => string 'Despliegue de aplicaciones web (2º)' (length=36)
  '0615' => string 'Diseño de interfaces Web (2º)' (length=31)
  '0618' => string 'Empresa e iniciativa emprendedora (2º)' (length=39)
  'A053' => string 'Lengua Extranjera profesional: Inglés 2 (2º)' (length=46)
  '0619' => string 'Formación en centros de trabajo (2º)' (length=38)
     */

    public function get_codigo_modulos(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet)
    {
        return $this->siglas;
    }
    /*
     public function combinar(){
        $this->combinacion=array_combine($this->codigo,$this->nombre);
        foreach ($this->combinacion as $cod=>$nombre) {
            unset($this->combinacion[$cod]);
            $this->combinacion[$cod]['nombre']=$nombre;
            $siglas =self::first_letter($nombre);
            $this->combinacion[$cod]['siglas']=$siglas           ;
        }
        echo"<h1>Combinación</h1>";
        var_dump($this->combinacion);
    }
*/
    //Busco en nombre que tenga esas siglas

    public function combinar()
    {
        $this->combinacion = array_combine($this->codigo, $this->nombre);
        foreach ($this->combinacion as $cod => $nombre) {
            unset($this->combinacion[$cod]);
            $this->combinacion[$cod]['nombre'] = $nombre;
            $siglas = self::first_letter($nombre);
            $this->combinacion[$cod]['siglas'] = $siglas;
        }
        echo "<h1>Combinación</h1>";

    }

    private function first_letter($nombre)
    {
        $palabras = explode(" ", $nombre);
        $sigla = "";
        foreach ($palabras as $palabra) {
            $sigla .= strtoupper($palabra[0]);
        }
        return $sigla;
    }

    /*
         private function inserta_sigla ($sigla){
            if ($sigla=='IN1')
                $cod = self::busca_siglas("Inglés 1");
            if ($sigla=='IN2')
                $cod = self::busca_siglas("Inglés 2");
            foreach ($this->combinacion as $cod=>$nom) {
                $l = strlen($nom);
                //Me quedo con la primera letra de cada palabra
                $letra ="";
                for ($n=0; $n>l; $n++) {
                    if ($nom[$n]==" ")
                        $letra.=$nom[$n+1];
                }
                $l = strlen($sigla);
                //Me quedo con la primera letra de cada palabra
                $check=true;
                for ($n=0; $n>l; $n++) {
                    if (strpos($sigla[$n], $letra)===false)
                        $ckeck=false;
                }
                if (check == true)
                    $this->combinacion[$cod]['siglas']=$sigla;
           }






            }


    */
/*
    //Pasamos unas siglas de un módulo
    Retorna el código de ese modulo según tengamos almacenado en el atributo merge
null si no lo localiza
*/
    public function codigo2siglas($sigla)
    {
        foreach ( $this->merge as $codigo => $datos) {
            if ($datos['sigla']==$sigla)
                return $codigo;
        }
        return null;

    }

    public function setCodigo($datos)
    {
        foreach ($datos as $indice => $dato)
            $this->codigo[] = $indice;
    }


  /**MRM quito por duplicado no se muy bien cual hay que quitar
    public function setSiglas($datos)
    {
        foreach ($datos as $indice => $dato) {
            $this->siglas[] = trim(substr($indice, 0, -3));
        }
    }
   *
   */

    public function setNombre($datos)
    {

        foreach ($datos as $indice => $dato)
            $this->nombre[] = $dato;
    }

    public function get_codigos()
    {
        return $this->codigos;
    }

    public function getCodigos()
    {
        return $this->codigos;
    }

    public function setCodigos($certificado)
    {
        $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($certificado->getCertificado());
        //preparo el fichero para trabajar con él
        $worksheet = $sheet->getActiveSheet();


        $n = 1;
        $alumnos = [];

        //Numero de filas de la excell;
        $filas = $worksheet->getHighestRow();
        $modulos = [];
        for ($f = 0; $f < $filas; $f++) {
            //Obtengo el valor de las celdas de la columna B
            $valor = (string)$worksheet->getCellByColumnAndRow(2, $f)->getValue();
            //Si tiene la palabra CERTIFICA es que ahí está el nombre
            $f++;
            if ($valor === "Código (1)") {
                $modulo = (string)$worksheet->getCellByColumnAndRow(2, $f)->getValue();
                $nombre_modulo = (string)$worksheet->getCellByColumnAndRow(7, $f)->getValue();
                $f++; //Avanzamos f
                //ila;

                while (is_numeric(substr($modulo, 1))) {//Estos son los códigos
                    if (!array_key_exists($modulo, $modulos))
                        $modulos[$modulo] = $nombre_modulo;
                    $f++;
                    $modulo = (string)$worksheet->getCellByColumnAndRow(2, $f)->getValue();
                    $nombre_modulo = (string)$worksheet->getCellByColumnAndRow(7, $f)->getValue();
                }
            }

        }//End for;
        $this->codigos = $modulos;


    }

    public function get_nombre()
    {
        return $this->nombre;
    }

    //Obtiene el atributo de siglas un array indexado por código con las siglas
    //A patir de  la actilla voy a obtener las siglas

    public function getSiglas()
    {
        return $this->siglas;
    }

    //Obtiene el atributo de siglas un array indexado por código con los nombres
    //A partir del certificado voy a obtener los códigos de los módulos y sus nombres

    public function setSiglas($certificado)
    {
        {
            $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($certificado->getActilla());
            //preparo el fichero para trabajar con él
            $worksheet = $sheet->getActiveSheet();
            $n = 1;

            $fila = 5; //La información de los módulos está en la fila 5
            //La primera columna es la 4.
            $col = 4;
            $valor = (string)$worksheet->getCellByColumnAndRow($col, $fila)->getValue();
            $n = 1;

            while ($valor != "Nº MNS") {
                //IMPORTANTE solo quiero el string
                if ($valor != "") {
                    //quitamos los 3 últimos caracteres para quedarnos
                    //solo con el nombre
                    $s[] = substr($valor, 0, (strlen($valor) - 4));
                }
                $valor = (string)$worksheet->getCellByColumnAndRow($col + $n, $fila)->getValue();
                $n++;
            }
            $this->siglas = $s;

        }
    }

    private function reordena_siglas()
    {
    }
    //$codigo_modulo = $modulo->sigla2cod($sigla_modulo);

    //
    /**
     * @param $sigla una sigla de módulo pe. BD
     * @return string el código de esa sigla p3 402
     */
    public function  sigla2cod($sigla){
        foreach ($this->merge as $cod => $datos){
            if ($datos['sigla'] == $sigla)
                return $cod;
        }
        return false;
    }


}