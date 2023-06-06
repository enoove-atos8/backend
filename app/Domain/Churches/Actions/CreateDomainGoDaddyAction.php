<?php

namespace Domain\Churches\Actions;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Infrastructure\Exceptions\GeneralExceptions;
use Illuminate\Support\Facades\Http;

class CreateDomainGoDaddyAction
{
    const AWS_HOST = '3.14.69.129';
    const GODADDY_PRODUCTION_API_HOST = 'https://api.godaddy.com/v1';
    const GODADDY_DOMAIN_RESOURCE = '/domains';
    const DOMAIN = '/atos242.com';
    const GODADDY_RECORD_RESOURCE = '/records';
    const GODADDY_PRODUCTION_KEY = 'fY15ZyEcodfB_Ru5nBs24fYs1Z1khY2mDbL';
    const GODADDY_PRODUCTION_SECRET = 'Lh8hMSKX34noLu7SgrzWuR';


    /**
     * @throws GeneralExceptions
     */
    public function __invoke(string $subDomain, $envProd = true): bool
    {
        if($envProd)
        {
            $client = new Client();
            $body = '[
              {
                "data": "' . self::AWS_HOST . '",
                "name": "' . $subDomain . '",
                "ttl": 600,
                "type": "A"
              }
            ]';

            $headers = [
                'Authorization' =>  'sso-key ' . self::GODADDY_PRODUCTION_KEY . ':' . self::GODADDY_PRODUCTION_SECRET,
                'Content-Type' =>  'application/json',
            ];

            $endpoint = self::GODADDY_PRODUCTION_API_HOST . self::GODADDY_DOMAIN_RESOURCE . self::DOMAIN . self::GODADDY_RECORD_RESOURCE . '/?domain='. self::DOMAIN;
            $request = new Request('PATCH', $endpoint, $headers, $body);
            $response = $client->sendAsync($request)->wait();

            if($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)
                return true;
            else
                throw new GeneralExceptions('Houve um problema ao criar o site no serviço de hospedagem', $response->getStatusCode());
        }
        else
        {
            // TODO: Implementar código para criar uma entrada no arquivo hosts
        }
    }
}
