<?php

namespace Domain\Churches\Actions;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateDomainGoDaddyAction
{
    private string $aws_host;
    private string $prodApiPathGodaddy;
    private string $domain;
    private string $godaddyProductionKey;
    private string $godaddyProductionSecret;
    const GODADDY_DOMAIN_RESOURCE = '/domains';
    const GODADDY_RECORD_RESOURCE = '/records';


    public function __construct()
    {
        $this->aws_host = config('api-resources.aws.host');
        $this->domain = config('api-resources.godaddy.domain');
        $this->prodApiPathGodaddy = config('api-resources.godaddy.base_url');
        $this->godaddyProductionKey = config('api-resources.godaddy.key');
        $this->godaddyProductionSecret = config('api-resources.godaddy.secret');
    }

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
                "data": "' . $this->aws_host . '",
                "name": "' . $subDomain . '",
                "ttl": 600,
                "type": "A"
              }
            ]';

            $headers = [
                'Authorization' =>  'sso-key ' . $this->godaddyProductionKey . ':' . $this->godaddyProductionSecret,
                'Content-Type' =>  'application/json',
            ];

            $endpoint = $this->prodApiPathGodaddy . self::GODADDY_DOMAIN_RESOURCE . '/' . $this->domain . self::GODADDY_RECORD_RESOURCE . '/?domain='. $this->domain;
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
