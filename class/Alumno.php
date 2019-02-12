<?php
/**
 * Created by PhpStorm.
 * User: manuel
 * Date: 21/11/18
 * Time: 22:19
 */

namespace Enlaces;


/**
 * Class Alumno Mantiene un array con todos los alumnos de un ciclo
 * Los podemos tener por nombre o por apellido
 * @package Enlaces
 */
class Alumno
{
    private $alumnos_nombre_apellido = [];
    private $alumnos_apellido_nombre = [];
    private $notas_certificado = [];
    private $notas_actilla = [];
    private $expediente = [];//Combinar o merge notas de certificado y de actilla y matrículas


    /**
     * @param $datos los alumnos listados por nombre
     */
    public function nombre_apellido($datos)
    {
        foreach ($datos as $indice => $dato)
            $this->alumnos_nombre_apellido[] = $indice;
    }


    public function getExpediente()
    {
        return $this->expediente;
    }

    /**
     * Problema:
     * en certificado
     * private 'notas_certificado' =>
     * array (size=82)
     * 'MOISÉS ÁLVAREZ DOCÓN' =>
     * array (size=7)
     * 'A052' => string '6' (length=1)
     * '0484' => string 'RC' (length=2)
     * '0373' => string 'NE' (length=2)
     * '0483' => string '1' (length=1)
     * en actilla
     *
     */

    public function setExpediente($modulo)

    {


        //Trabajo sobre notas_actilla
        //RF1 buscar alumno de notas_actilla 
        //RF2Recorriendo el alumno en notas_certificado recojo cada código de módulo y busco sus siglas
        //RF3 Aporto la nota en notas_actilla.

        //Recorro cada alumno con sus notas de actilla y las quiero modificar
        //Actulizando la nota con su n        $this->expediente[$alumno_apellido_nombre][$sigla_modulo] = $nota;sota de expediente (si la tiene)
        //var_dump($this);
        foreach ($this->notas_actilla as $alumno_apellido_nombre => $notas_actilla_alumno) {
            /*
                $alumno_apellido_nombre = next($this->notas_actilla);
            $alumno_apellido_nombre = next($this->notas_actilla);
            $alumno_apellido_nombre = key($this->notas_actilla);

           
    */
            $alumno_apellido_nombre = trim(str_replace("(Rep)", "", $alumno_apellido_nombre));
            $alumno_certificado = $this->buscar($alumno_apellido_nombre);

            
            //Notas de certificado de un alumno de la actilla
            $notas_certificado_alumno = $this->notas_certificado[$alumno_certificado];

            foreach ($notas_actilla_alumno as $sigla_modulo => $nota) {
                //var_dump($nota);


                $codigo_modulo = $modulo->codigo2siglas($sigla_modulo);
                if (isset($notas_certificado_alumno[$codigo_modulo])) {
                    $n = $notas_certificado_alumno[$codigo_modulo];

                    if (($n >= 5) OR ($n == "CV-5") OR ($n == "CV5") OR ($n == "CV")OR ($n=="EX"))
                        $this->expediente[$alumno_apellido_nombre][$sigla_modulo] = $n;
                    else
                        $this->expediente[$alumno_apellido_nombre][$sigla_modulo] = $nota;
                }
                else//Si bi está en certificado pongo la nota del actilla
                    $this->expediente[$alumno_apellido_nombre][$sigla_modulo] = $nota;


            }

            //var_dump($this->expediente[$alumno_apellido_nombre]);


        }

        //var_dump($this->expediente);
    }

    private
    function buscar($alumno_apellido_nombre)
    {
        $nom = $this->ordena_nombre($alumno_apellido_nombre);
        //En este caso hacemos un intento quitando el segundo apellido
        if (is_null($this->notas_certificado[$nom])) {
            $nombre = explode(" ", $nom);
            $len = sizeof($nombre);
            $nom = "";
            for ($n = 0; $n < $len - 1; $n++) {
                $nom .= $nombre[$n] . " ";
            }
            $nom = trim($nom);
        }
        //vOLVEMOS A INSISTIR POR UN CASO DEL TIPO
      
        //
        if (is_null($this->notas_certificado[$nom])) {
            $nombre = explode(" ", $nom);
            $len = sizeof($nombre);
            $nom = "";
            for ($n = 0; $n < $len - 1; $n++) {
                $nom .= $nombre[$n] . " ";
            }
            $nom = trim($nom);
        }
        return $nom;
    }

    /**
     * @param $nombre algo del tipo
     *       Rodríguez    Ruiz,  Fernando
     * @retuer $nom   el nombre ordenado en mayúsculas y con un caracter en blanco entre cada identificador del nombre
     *       FERNANDO RUIZ RODRIGUEZ
     *
     */
    private
    function ordena_nombre($nombre)
    {
        //Separo nombre y apellido por la coma
        $nom = explode(",", $nombre);
        //Ahora me quedo con cada indentificaor por orde
        $nombres = explode(" ", trim($nom[1]));
        $apellidos = explode(" ", trim($nom[0]));
        //paso a mayúsculas y quito espacios en blanco y concateno
        $nom = "";

        /*
                    //CUIDADO
                    //Si nombre manuel    alejandro romero
                    Como hay mas de un espacio, este se queda como nombre o apellido
                    Hay que quitarlo por eso el
                                  if(!empty ...
        */
        foreach ($nombres as $nombre) {
            if (!empty($nombre))
                $nom .= trim(mb_strtoupper($nombre, "utf-8")) . " ";
        }
        foreach ($apellidos as $apellido) {
            if (!empty($apellido))
                $nom .= trim(mb_strtoupper($apellido, "utf-8")) . " ";
        }

        return trim($nom);
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
     * Dña. xxxxxxxxxxxxxxxx, Secretaria del Centro Público Integrado de Formación Profesional LOS ENLACES
     * con código de centro 50010314, dirección Cl. Jarque De Moncayo, 10, 50012 Zaragoza (Zaragoza), teléfono 976300804 y
     * correo electrónico cpilosenlaces@educa.aragon.es
     *
     * CERTIFICA:
     *
     * Que xxxxxxxxxxxxxxxx con DNI xxxxxxxxxxxxx, nacido el xx de febrero de 1986, está matriculado en el centro
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
     * 0487    Entornos de desarrollo (1º)                                    9
     * 0617    Formación y orientación laboral (1º)                            CV-5
     * 0373    Lenguajes de marcas y sistemas de gestión de información (1º)    9
     * 0483    Sistemas informáticos (1º)                                        9
     * ----------------------------------------------------------------------------
     *
     * La  primera fila que no tiene código empieza siempre por Y
     *
     * ----------------------------------------------------------------------------
     * Y, con fecha XX de noviembre de 2018, ha hecho la solicitud para traslado de expediente u otros efectos.
     * ----------------------------------------------------------------------------
     *
     */

    /**
     * @param $datos array de alumnos listados por apellido
     */
    public
    function apellido_nombre($datos)
    {
        foreach ($datos as $indice => $dato)
            $this->alumnos_apellido_nombre[] = $indice;
    }

    public
    function get_apellido_nombre()
    {
        return $this->alumnos_apellido_nombre;
    }

//Retornará un array con todos los módulos matriculados de una fila

    public
    function getNotas()
    {
        return $this->notas;
    }

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
     * Anoto código y nota
     * Además creo un array con los nombres de los códigos de los módulos
     */

    public
    function setNotasCertificado($worksheet, $modulos)
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
        $this->notas_certificado = $alumnos;
    }


//Este método trata de combinar las notas extraídas de actilla y certificado
// Para cada alumno
//Con ello ya podré hacer el excell final

    /**
     * @param $worksheet hoja excell con la actilla
     * Este método sacará las notas de la actilla y las incorporará a cada alumno
     * De la actilla puedo tener
     * CV5 => Convalidado                             => Indicarlo
     * RC  => Renunica a convocatoria                 => No hacer nada
     * Blanco => Que está matriculado                  => Poner un color indicando matrículo
     * gris => O no está matriculado, o ya la tiene aprobada  => no hacer nada
     */
    public
    function setNotasActilla($worksheet)
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
                    $modulos = $this->obtener_modulos_matriculados($worksheet, $f, $col);

                $alumnos[$nombre] = $modulos;
            }
        }//End for;
        //MRM   Hay que hacer algo con estos datos !!!!
        //return $alumnos;
        //Quitamos los índices que no son de alumnos que son "" y "Apellidos, Nombre";
        unset ($alumnos['']);
        unset ($alumnos['Apellidos, Nombre']);
        $this->notas_actilla = $alumnos;
    }//End setMerge

    /**
     * @param $w Worksheet hoja excell
     * @param $f la fila del alumno
     * @param $col es el número de columnas donde están los módulos
     *
     * @return array con los módulos todos los módulos
     * si está matriculado ponemos "MATRICULADO"
     * si no lo está ponemos false
     * Si ya tiene nota (p.e. CV), se pone
     *
     */
    private
    function obtener_modulos_matriculados(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $w, $f, $col)
    {

        for ($i = 0; $i <= $col; $i++) {
            $modulo = (string)$w->getCellByColumnAndRow((4 + $i), 5);
            $m = explode("\n", $modulo);//Quito 1º y 2º que están en un salto de línea
            $modulo = $m[0];


            //     echo "<h1>Valor de módulo </h1>";
            //
            //var_dump($modulo);
            //BD,X,X,ED,X,X,FOL,X,X,IN1,X,X,LMS,X,X,X,PRO,X,X,SI,X,X,DAW,DIW,DWC,DWS,EIE,IN2,PRY,FCT,X,X,X,NºNMS,..9*X
            if ((!is_null($modulo)) && ($modulo != "Nº MNS") && $modulo != '') {
                $color = $w->getStyleByColumnAndRow(4 + $i, $f)->getFill();
                $valor = $w->getCellByColumnAndRow((4 + $i), $f)->getValue();
                //echo "<h1>$valor</h1>";
                $c = $color->getEndColor()->getRGB();
                if ($c == "FFFFFF")
                    if (is_null($valor)) //Esto es que está matriculado
                        $modulos[$modulo] = "MATRICULADO";
                    else
                        $modulos[$modulo] = $valor;
                else
                    $modulos[$modulo] = false; //No está matriculado
                //echo "Color leído $c<br />";
            }

        }
        //
        //var_dump($modulos);
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

    }//End function

}//End Class



