<?php


namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface; // Optionnel : pour logger les erreurs d'API
use Symfony\Component\DependencyInjection\Attribute\AsService; //  Importez l'attribut AsService
use Symfony\Component\DependencyInjection\Attribute\Autowire; //  Importez l'attribut Autowire

class IpGeolocationService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private LoggerInterface $logger; // Pour les logs

    // Symfony injectera HttpClientInterface et la clé API (définie dans .env)
    public function __construct(HttpClientInterface $httpClient,  #[Autowire(env: 'APIBUNDLE_IP_LOOKUP_API_KEY')] string $apibundleIpLookupApiKey, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apibundleIpLookupApiKey;
        $this->logger = $logger;
    }

    public function geolocate(string $ipAddress): ?array
    {
        // Vérifier si l'IP est une IP locale (ne pas appeler l'API pour 127.0.0.1, etc.)
        if (in_array($ipAddress, ['127.0.0.1', '::1']) || str_starts_with($ipAddress, '10.') || str_starts_with($ipAddress, '172.16.') || str_starts_with($ipAddress, '192.168.')) {
            return [
                'country' => 'Local',
                'city' => 'Local',
                // Ajoutez d'autres champs si nécessaire
            ];
        }

        try {
            $response = $this->httpClient->request(
                'GET',
                'https://api.apibundle.io/ip-lookup',
                [
                    'query' => [
                        'apikey' => $this->apiKey,
                        'ip' => $ipAddress,
                    ],
                ]
            );

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                $this->logger->error(sprintf('API Bundle IP Lookup failed for IP %s with status code %d and content: %s', $ipAddress, $statusCode, $response->getContent(false)));
                return null;
            }

            $data = $response->toArray();

            // Assurez-vous que l'API retourne bien 'country' et 'city' au niveau racine
            // Si l'API retourne des données imbriquées, ajustez ceci (ex: $data['location']['country'])
            return [
                'country' => $data['country_name'] ?? null, // Utilisez null coalescing pour gérer les clés manquantes
                'city' => $data['city'] ?? null,
                // Ajoutez d'autres champs de l'API si vous les avez ajoutés à vos entités
                // 'region' => $data['region_name'] ?? null,
                // 'latitude' => $data['latitude'] ?? null,
                // 'longitude' => $data['longitude'] ?? null,
            ];

        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error calling API Bundle IP Lookup for IP %s: %s', $ipAddress, $e->getMessage()));
            return null;
        }
    }
}