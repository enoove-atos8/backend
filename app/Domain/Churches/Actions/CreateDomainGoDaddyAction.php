<?php

namespace Domain\Churches\Actions;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\App;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateDomainGoDaddyAction
{
    private string $env;
    private string|null $aws_host;
    private string $prodApiPathGodaddy;
    private string $domain;
    private string $godaddyProductionKey;
    private string $godaddyProductionSecret;
    const GODADDY_DOMAIN_RESOURCE = '/domains';
    const GODADDY_RECORD_RESOURCE = '/records';


    public function __construct()
    {
        $this->env = App::environment() == 'development' ? 'development' : 'local';

        $this->aws_host = $this->env == 'development' ?
            config('external-env.aws.dev.host') : null;

        $this->domain = $this->env == 'development' ?
            config('external-env.app.domain.dev') :
            config('external-env.app.domain.local');

        $this->prodApiPathGodaddy = config('external-env.godaddy.base_url');
        $this->godaddyProductionKey = config('external-env.godaddy.key');
        $this->godaddyProductionSecret = config('external-env.godaddy.secret');
    }

    /**
     * @throws GeneralExceptions
     */
    public function __invoke(string $tenant, $envProd = true): bool
    {
        $domain = config('external-env.app.domain.' . App::environment());
        $host = config('external-env.aws.' . App::environment() . '.host' );

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
                throw new GeneralExceptions('Houve um problema ao criar o site no serviço de hospedagem', $response->getStatusCode());
        }
        else
        {
            return true;
        }
    }
}
