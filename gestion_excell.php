<?php
//Inicializo variables para evitar warning.
require_once "vendor/autoload.php";

require_once "funciones.php"; //Esto habrá que quitarlo si no hay funciones


use  PhpOffice\PhpSpreadsheet\Spreadsheet;
use Enlaces\Alumno;
use Enlaces\Certificado;
use Enlaces\Modulo;

<<<<<<< HEAD
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
=======
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
>>>>>>> 01f5fcc47ac63bb33d873be9bb751e3a21e37f61

/*
/*
use App\Alumno;
use App\Certificado;
use App\Modulo;


spl_autoload_register(function ($clase) {
    require_once "./class/$clase.php";
});
*/
session_start();


//Variables para vue mostrar/ocultar formularios
$msj = null;

//Cuidado, necesito el string true o false para pasarlo directamente a javascript
/*si tengo que asignar siglas a nombre de módulos
  Si no hay que asignarlo es por que ya lo están
  En este caso visualizaré esas parejas mostrando el botón para volver a asignar
*/
$siglas_asignadas = "false";
$ficheros = "true";


//Variables para mantener la información para otras páginas
$alumno_obj = isset($_SESSION['alumno_obj']) ? unserialize($_SESSION['alumno_obj']) : new Alumno();
$certificado_obj = isset($_SESSION['certificado_obj ']) ? unserialize($_SESSION['certificado_obj']) : new Certificado();
$modulo_obj = isset($_SESSION['modulo_obj']) ? unserialize($_SESSION['modulo_obj']) : new Modulo();


//Si he hecho un submit lo gestionamos

if (isset($_POST['submit'])) {
    switch ($_POST['submit']) {
        case "Cargar ficheros": //Quiero cargar ficheros de actillas y certificados
            $actilla = $_FILES['actilla'];
<<<<<<< HEAD
            var_dump($actilla);
            $certificado = $_FILES['certificado'];
            var_dump($certificado);
            //Solo si he aportado los dos ficheros
            if (!(empty($actilla['name'])) || !(empty($certificado['name']))) {
                $alumno_obj =  new Alumno();
                $certificado_obj =  new Certificado();
                $modulo_obj =  new Modulo();

=======
            $certificado = $_FILES['certificado'];
            //Solo si he aportado los dos ficheros
            if (!(empty($actilla['name'])) || !(empty($certificado['name']))) {
>>>>>>> 01f5fcc47ac63bb33d873be9bb751e3a21e37f61
                $certificado_obj->copiar_ficheros($actilla, $certificado);
                $actualizar = true;//Para cambiar las siglas
            }
            $ficheros = "true"; //Para Vue
            $modulo_obj->setInicializado(false); //Posiblemente he cambiado de ciclo
            break;
        case "Asignar siglas":
            //Los datos me vienen por post
            $siglas = $_POST["siglas"];
            $siglas_asignadas = "true";//Para cambiar las siglas
            $modulo_obj->setMerge($siglas);
            break;
        case 'generar':
            //Ya la tengo cargada en variable de sesión, pero lo vuelvo a hacer  ???
            $_SESSION['certificado_obj'] = serialize($certificado_obj);
            header("Location:genera_xls.php");
            break;
        case 'visualizar':
            echo "<h1>Visualizar el informe</h1>";
            $_SESSION['modulo'] = serialize($modulo_obj);
            header("Location:mostrar_pantalla.php");
            break;
    }
}


//Necesario para mostrar información
$certificado_obj->obtener_datos();

//Submimos el fichero de certificado de notas xls al servidor


//Necesitamos esta información
if ($certificado_obj->getEstado()) {//Si tengo los ficheros para hacer el informe
    if (is_null($certificado_obj->getTitleCiclo())) {
        $certificado_obj->setTitleCiclo();
    }
    $title = $certificado_obj->getTitleCiclo();
    $msj = <<<FIN
    <h3>Ficheros cargados corresponden al ciclo $title</h3>
    Pata obtener de otro ciclo carga los ficheros correspondientes<br />
    <button @click="btn_ficheros()" class=presentacion>{{ msj_ficheros }}</button>
FIN;
    if (!$modulo_obj->getInicializado()) {
        //Mostramos las siglas para los módulos
        $modulo_obj->setSiglas($certificado_obj); //ok
        $modulo_obj->setCodigos($certificado_obj); //ok
        $modulo_obj->setInicializado(true);
    }
    $siglas = $modulo_obj->getSiglas();
    $codigos = $modulo_obj->getCodigos();
    $ficheros = "false"; //Oculto formulario cargar ficheros
    if (sizeof($modulo_obj->getMerge()) != 0)
        $siglas_asignadas = "true";

    else
        $siglas_asigandas = "false"; //Mostrar para asignar códigos a módulos

    if ($siglas_asignadas === "true") //Si ya están asignado lo oculto
        //obtener_datos();
        $selecciona_codigos = "false";


} else { //No tengo los ficheros para generar la información
    //Activamos este formulario
    $ficheros = "true";
    //anulamos para que no se vean estos formularios
    $siglas_asignadas = "false";
    $selecciona_codigos = "false";

    //Preparamos el mensaje a mostrar
    $msj = <<<FIN
    <h3>Actulamente no hay ficheros cargados para hacer el informe,</h3>
    <h4>o los cargados no son correctos.</h4>
             Para generar el informe se necesitan dos ficheros xls:
                <ol>
                    <li>
                        Documento de actilla de notas (disponible para tutores en sigad).
                    </li>
                    <li>
                        Documento de certificado de notas (facilitado por jefatura).
                    </li>
                </ol>
            Por favor, aporta estos ficheros en el menú inferior, para poder seguir.
FIN;

}
/*
//Leo los valores previamente establecidos
//Si no no podría estar aquí, tendría que estar buscándolos
$siglas = isset($_GET["siglas"])? unserialize($_GET["siglas"]): null;
$codigos= isset($_GET["codigos"])? unserialize($_GET["codigos"]): null;
echo "first-$first-";
isset($_POST['submit']) ? actua():null;
*/

//Debemos guardar en variables de sesión para generar y visualizar
$_SESSION['modulo_obj'] = serialize($modulo_obj);
$_SESSION['certificado_obj'] = serialize($certificado_obj);
$_SESSION['alumno_obj'] = serialize($alumno_obj);


?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./css/estilo.css" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <!--
            <title>Title</title>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
                  integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ"
                  crossorigin="anonymous">
                  -->
</head>
<body>

<div id="app">


    <fieldset class="presentacion2">
        <legend>Notas importantes</legend>
        <?php echo $msj ?>
    </fieldset>


    <!--Formulario para aportar nuevos ficheros-->
    <template v-if="ficheros">
        <fieldset>
            <legend>Ficheros de certificados y actillas</legend>
            <form action="gestion_excell.php" method="POST" enctype="multipart/form-data">
                <label for="actilla">Excel Actilla de grupo</label>
                <input type="file" name="actilla" id="actilla">
                <br/>
                <label for="certificado">Excel Certificados de alumnos grupo</label>
                <input type="file" name="certificado" id="certificado">
                <br/>
                <br/>
                <hr/>
                <input type="submit" class="" value="Cargar ficheros" name="submit">
            </form>
        </fieldset>
    </template>
    <template v-if="siglas_asignadas">
        <fieldset>
            <legend>Siglas asignadas</legend>
            <?php
            if ($siglas_asignadas === "true") {
                foreach ($modulo_obj->getMerge() as $codigo => $modulo) {
                    echo $modulo['nombre'] . " -- " . $modulo['sigla'] . "<br />";
                }
            }
            ?>
            Si quieres cambiarlas debes presionar el botón correspondiente
            <button @click="btn_asignacion()" class=presentacion>Reasignar siglas de módulos</button>


    </template>

    <!-- Formulario para vincular siglas con módulos -->
    <template v-else="siglas_asignadas">
        <fieldset>
            <legend>Selecciona códigos</legend>
            <form action="gestion_excell.php" method="POST">
                <?php
                if (isset($codigos)):
                    $n = 0;
                    foreach ($codigos as $codigo => $nombre):
                        echo "$nombre";
                        $n++;
                        ?>
                        <select @click="valida(<?php echo $n ?>)" v-if="keys[<?php echo($n - 1) ?>]"
                                name="<?php echo "siglas[$codigo]" ?>"
                                v-model="keys[<?php echo $n ?>]">
                            <option v-for="sigla in siglas">
                                {{ sigla.name }}
                            </option>
                        </select>
                        <br/>
                    <?php endforeach;
                endif; ?>
                <!--son las 10 siglas-->
                <!--                <input type="hidden" value='<?php //echo serialize($siglas) ?>' name =siglas>
                <input type="hidden" value='<?php echo serialize($codigos) ?>' name=codigos>-->
                <input type="submit" value="Asignar siglas" name="submit">

            </form>
        </fieldset>

    </template>
    <fieldset>
        <legend>Selecciona Las acciones para generar el informe</legend>
        <form action="gestion_excell.php" method="POST">
            <fieldset>
                <legend>Color fondo de celdas</legend>
                Color Matriculado &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="color" disabled value="#ffffff" name='c_matricula' id="">
                <br/>
                Color aprobado&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="color" disabled   value="#00FF00" name='c_aprobado' id="">
                <br/>
                Color No matriculado&nbsp;&nbsp;&nbsp;<input type="color"   value="#9c9c9c" disabled name='c_pendiente' id="">
                <br/>
            </fieldset>
            <br/>
            <label for="submit">Generamos el informe descargando una xls</label>
            <input type="submit" class="" value="generar" name="submit">
            <br />
            <label for="submit">Mostrar el informe en una tabla en la pantalla </label>
            <input type="submit" value="visualiza" name="submit">
        </form>
    </fieldset>


    <!--Formulario para asignar siglas -->
</div>

<script>
    var app;
    app = new Vue({
        el: '#app',
        data: {
            ficheros: <?php echo $ficheros ?>,
            siglas_modulos: true,
            selecciona_codigos: "",
            siglas_asignadas: <?php echo $siglas_asignadas ?>,
            msj_ficheros: 'Mostrar formulario',
            siglas:<?php echo isset($siglas) ? genera_array_siglas($siglas) : "false" ?>,
            codigos:<?php echo isset($siglas) ? genera_array_objetos_codigos($codigos) : "false"?>,
            modulos_establecidos: false,

            keys: [true,<?php if (isset($codigos))
                for ($a = 1; $a <= count($codigos); $a++) {
                    echo "''";
                    if ($a < count($codigos))
                        echo ",";
                }
                ?>],
            imagen: [
                'http://blog.nubecolectiva.com/wp-content/uploads/2018/10/img_destacada_blog_devs-9-950x305.png'
            ],
            isLoad: false,
            /* crearImagen() {
                 this.cargarImagen()
             },*/
        },
        methods: {
            btn_ficheros() {
                if (this.ficheros === false) {
                    this.ficheros = true;
                    this.msj_ficheros = "Ocultar formulario";
                } else {
                    this.ficheros = false;
                    this.msj_ficheros = "Mostrar formulario";

                }
            },//end function
            valida: function (n, event) {
//                alert ("Leído "+eval("this.key"+n));
                var c;
                for (c = 0; c < n; c++) {
                    var a = this.keys[c]
                    var b = this.keys[n]
                    if (a == b) {
                        alert("Este valor" + b + " ya ha sido usado");
                        this.keys[n] = null;
                    }
                }//End for
                if (n == this.keys.length)
                    this.modulos_establecidos = true;
            },//end funciton
            btn_asignacion: function () {
                this.siglas_asignadas = false;
                this.selecciona_codigos = true;
            },

            esperarTiempo() {
                setTimeout(function () {
                    this.isLoad = true
                }.bind(this), 3000);
            },
        },
        //end methods
    });
</script>
</body>
</html>


