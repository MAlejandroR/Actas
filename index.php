<?php
if (isset($_POST['submit'])){
    session_start();
    unset($_SESSION);
    session_destroy();
}

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
</head><body>
<h1>Vamos a generar un fichero xls con las notas de alumnos</h1>
    <!-- vista presentación aplicación controlado con variable bue "first"  -->
    <fieldset class="presentacion">
        <form action="gestion_excell.php" method="POST">
        <ul>
            <li>
                Esta aplicación permitirá mostrar un informe de matrícula por alumno
                de un determinado ciclo.
            </li>
            <li>
                Para ello se necesitan dos ficheros xls
                <ol>
                    <li>
                        Documento de actilla de notas (disponible para tutores en sigad)
                    </li>
                    <li>
                        Documento de certificado de notas (facilitado por jefatura)
                    </li>
                </ol>
            </li>
            <li>Además deberás de establecer las siglas para los módulos a partir de un formulario que se te facilitará</li>
        </ul>

           <input class=presentacion type="submit" value="Siguiente">

        </form>
    </fieldset>
<form action="index.php" method="POST">

    <input type="submit" value="Borrar variables de sesión " name="submit">
</form>


</body>
</html>
