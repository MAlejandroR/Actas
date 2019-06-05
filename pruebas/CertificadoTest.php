<?php
require_once ('../vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Enlaces\Certificado;


class CertificadoTest extends PHPUnit_Framework_TestCase
{
    public function test_get_file_cetificado()
    {
        $this->assertSame(false, Certificado::get_file_certificado());

        Certificado::set_file_certificado("fichero.php");
        $this->assertSame("fichero.php", Certificado::get_file_certificado());

        Certificado::set_file_certificado(null);
        $this->assertSame(null, Certificado::get_file_certificado());


    }
}
?>
