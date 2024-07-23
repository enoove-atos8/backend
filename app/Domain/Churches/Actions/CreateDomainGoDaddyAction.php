<?php

namespace Domain\Churches\Actions;

use App\Domain\Churches\Constants\ReturnMessages;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\App;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateDomainGoDaddyAction
{
    private string $env;
    private string $prodApiPathGodaddy;
    private string $godaddyProductionKey;
    private string $godaddyProductionSecret;
    const GODADDY_DOMAIN_RESOURCE = '/domains';
    const GODADDY_RECORD_RESOURCE = '/records';


    public function __construct()
    {
        $this->env = App::environment();
        $this->prodApiPathGodaddy = config('godaddy.url.prod.base');
        $this->godaddyProductionKey = config('godaddy.credentials.key');
        $this->godaddyProductionSecret = config('godaddy.credentials.secret');
    }

    /**
     * @throws GeneralExceptions
     */
    public function __invoke(string $tenant, $envProd = true): bool
    {
        $domain = config('domain.' . $this->env);
        $host = config('env.environments.' . $this->env . '.host' );

        if(App::environment() !== 'local')
        {
            $client = new Client();
            $body = '[
              {
                "data": "' . $host . '",
                "name": "' . $tenant . '",
                "ttl": 600,
                "type": "A"
              }
            ]';

            $headers = [
                'Authorization' =>  'sso-key ' . $this->godaddyProductionKey . ':' . $this->godaddyProductionSecret,
                'Content-Type' =>  'application/json',
            ];

            $endpoint = $this->prodApiPathGodaddy . self::GODADDY_DOMAIN_RESOURCE . '/' . $domain . self::GODADDY_RECORD_RESOURCE . '/?domain='. $domain;
            $request = new Request('PATCH', $endpoint, $headers, $body);
            $response = $client->sendAsync($request)->wait();

            if($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)
                return true;
            else
                throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_DOMAIN, $response->getStatusCode());
        }
        else
        {
            return true;
        }
    }
}
