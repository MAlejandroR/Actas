<?php
require_once ('../vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Enlaces\Ficheros;


class CertificadoTest extends PHPUnit_Framework_TestCase
{
    public function test_get_file_cetificado()
    {
        $fichero = new Ficheros();

        $this->assertSame(true, $fichero->isset_certificado());

        $fichero->set_certificado("");
        $this->assertSame(fºalse, $fichero->isset_certificado());

        $fichero->set_certificado("fichero.txt");
        $this->assertSame(true, $fichero->isset_certificado());

        $fichero->set_actilla("");
        $this->assertSame(false, $fichero->isset_certificado());

        $fichero->set_actilla("fichero.txt");
        $this->assertSame(true, $fichero->isset_certificado());

    }
    public function test_set_title_ciclo()
    {

        $this->assertSame(true, true);


    }
}
?>
