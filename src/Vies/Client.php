<?php

namespace Ddeboer\Vatin\Vies;

use SoapFault;
use Ddeboer\Vatin\Exception\ViesException;

/**
 * A client for the VIES SOAP web service
 */
class Client
{
    /**
     * URL to WSDL
     *
     * @var string
     */
    private $wsdl = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /**
     * SOAP client
     *
     * @var \SoapClient
     */
    private $soapClient;

    /**
     * SOAP classmap
     *
     * @var array
     */
    private $classmap = [
        'checkVatResponse' => 'Ddeboer\Vatin\Vies\Response\CheckVatResponse'
    ];


    public function __construct(?string $wsdl = null)
    {
        if ($wsdl) {
            $this->wsdl = $wsdl;
        }
    }

    /**
     * Check VAT
     *
     * @throws ViesException
     */
    public function checkVat(string $countryCode, string $vatNumber): Response\CheckVatResponse
    {
        try {
            return $this->getSoapClient()->checkVat(
                [
                    'countryCode' => $countryCode,
                    'vatNumber' => $vatNumber
                ]
            );
        } catch (SoapFault $e) {
            throw new ViesException('Error communicating with VIES service', 0, $e);
        }
    }


    private function getSoapClient(): \SoapClient
    {
        if (null === $this->soapClient) {
            $this->soapClient = new \SoapClient(
                $this->wsdl,
                [
                    'classmap' => $this->classmap,
                    'user_agent' => 'Mozilla', // the request fails unless a (dummy) user agent is specified
                    'exceptions' => true,
                ]
            );
        }

        return $this->soapClient;
    }
}
