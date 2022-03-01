<?php

namespace NotaFiscalSP\Entities;

use Exception;
use NotaFiscalSP\Constants\Params;
use NotaFiscalSP\Helpers\Certificate;
use NotaFiscalSP\Helpers\General;
use NotaFiscalSP\Responses\CnpjInformationResponse;

/**
 * Class BaseInformation
 * @package NotaFiscalSP\Entities
 */
class BaseInformation
{
    /**
     * @var
     *  Todos Processos exigem o CNPJ ou CPF como uma identificação
     */
    private $cnpj;

    /**
     * @var
     *  Todos Processos exigem o CNPJ ou CPF como uma identificação
     */
    private $cpf;

    /**
     * @var
     *  Inscrição Municipal da Empresa é informada na Nota Fiscal Obrigatóriamente
     */
    private $im;
    /**
     * @var
     *  Para Realizar o acesso a API e Assinar é obrigatório o Certifiado digital da empresa
     */
    private $certificate;
    /**
     * @var
     */
    private $xmlPath;
    /**
     * @var
     */
    private $xml;
    /**
     * @var
     */
    private $certificatePass;
    /**
     * @var
     */
    private $certificatePath;

    /**
     * @return mixed
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @param mixed $cpf
     */
    public function setCpf($cpf)
    {
        $this->cpf = General::onlyNumbers($cpf);
    }

    /**
     * @return mixed
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @param mixed $xml
     */
    public function setXml($file)
    {
        $signed = Certificate::signXmlWithCertificate($this->getCertificate(), $file);

        $tempNam = @tempnam('/tmp', 'xml');
        $filename = $tempNam . '.xml';
        $fp = fopen($filename, 'w');
        fwrite($fp, $signed);

        $this->setXmlPath($filename);
        $this->xml = $signed;

        fclose($fp);
    }

    /**
     * @return mixed
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @param $options
     * @return false|string
     * @throws Exception
     */
    public function setCertificate($options)
    {
        if (strpos($options[Params::CERTIFICATE_PATH], '.pfx')) {
            $certificate = Certificate::pfx2pem($options[Params::CERTIFICATE_PATH], $options[Params::CERTIFICATE_PASS]);
            $tempNam = @tempnam(Params::CERTIFICATE_PATH, 'cert');
            $filename = $tempNam . '.pem';
            $fp = fopen($filename, 'w');
            fwrite($fp, $certificate);
            $this->setCertificatePath($filename);
            fclose($fp);
        } else {
            $this->setCertificatePath($options[Params::CERTIFICATE_PATH]);
            $certificate = file_get_contents($options[Params::CERTIFICATE_PATH]);
        }

        return $this->certificate = $certificate;
    }

    /**
     * @return mixed
     */
    public function getXmlPath()
    {
        return $this->xmlPath;
    }

    /**
     * @param mixed $xmlPath
     */
    public function setXmlPath($xmlPath)
    {
        $this->xmlPath = trim($xmlPath);
    }

    /**
     * @return mixed
     */
    public function getCertificatePath()
    {
        return $this->certificatePath;
    }

    /**
     * @param mixed $certificatePath
     */
    public function setCertificatePath($certificatePath)
    {
        $this->certificatePath = trim($certificatePath);
    }

    /**
     * @return mixed
     */
    public function getCertificatePass()
    {
        return $this->certificatePass;
    }

    /**
     * @param mixed $certificatePass
     */
    public function setCertificatePass($certificatePass)
    {
        $this->certificatePass = trim($certificatePass);
    }

    /**
     * @return mixed
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * @param mixed $cnpj
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = General::onlyNumbers($cnpj);
    }

    /**
     * @return mixed
     */
    public function getIm()
    {
        return $this->im;
    }

    /**
     * @param mixed $im
     */
    public function setIm($im)
    {
        if ($im instanceof CnpjInformationResponse) {
            $this->im = $im->getIm();
        } else {
            $this->im = General::onlyNumbers($im);
        }
    }
}