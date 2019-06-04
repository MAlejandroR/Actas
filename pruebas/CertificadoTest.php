<?php
require_once ('../vendor/autoload.php');
use PHPUnit\Framework\TestCase;
use Enlaces\Certificado;


class CertificadoTest extends TestCase
{
    public function test_get_file_cetificado()
    {
        Certificado::set_file_certificado("fichero.php");
        $this->assertSame(true, Certificado::get_file_certificado());

        Certificado::set_file_certificado("");
        $this->assertSame(false, Certificado::get_file_certificado());


    }
}
?>