<?php

namespace Domain\CentralDomain\Churches\Church\Actions;

use Domain\CentralDomain\Churches\Church\Constants\ReturnMessages;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\App;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateSubDomainAction
{
    private string $urlBaseApi;
    private string $cloudFlareEmail;
    private string $cloudFlareKey;
    private string $cloudFlareZoneId;
    private string $publicIP;
    const CLOUDFLARE_DNS_RECORDS_RESOURCE = '/dns_records';

    public function __construct()
    {
        $this->urlBaseApi = config('services-hosts.cloudflare.url.base');
        $this->cloudFlareEmail = config('services-hosts.cloudflare.credentials.email');
        $this->cloudFlareKey = config('services-hosts.cloudflare.credentials.key');
        $this->cloudFlareZoneId = config('services-hosts.cloudflare.credentials.zone_id');
        $this->publicIP = config('services-hosts.environments.stage.host');
    }

    /**
     * @throws GeneralExceptions
     * @throws Exception
     */
    public function __invoke(string $tenant, $envProd = true): bool
    {
        $env = App::environment();
        $uniqueId = $this->generateUniqueId(32);

        if($env !== 'local')
        {
            $client = new Client();

            $headers = [
                'X-Auth-Email'  =>  $this->cloudFlareEmail,
                'X-Auth-Key'    =>  $this->cloudFlareKey,
                'Content-Type'  =>  'application/json',
            ];

            $body = [
                'content' => $this->publicIP,
                'name' => $tenant,
                'proxied' => true,
                'type' => 'A',
                'comment' => 'Client created by atos8.com platform',
                'id' => $uniqueId,
                'ttl' => 120,
            ];

            $endpoint = $this->urlBaseApi . $this->cloudFlareZoneId . self::CLOUDFLARE_DNS_RECORDS_RESOURCE;

            try {
                $response = $client->post($endpoint, [
                    'headers' => $headers,
                    'json' => $body,
                    'timeout' => 30,
                ]);

                if($response->getStatusCode() == 200)
                {
                    return true;
                }
                else
                {
                    throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_DOMAIN, $response->getStatusCode());
                }
            }
            catch (GuzzleException $e)
            {
                throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_DOMAIN, $e->getCode());
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * @throws Exception
     */
    function generateUniqueId($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
