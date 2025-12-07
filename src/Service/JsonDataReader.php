<?php
// src/Service/JsonDataReader.php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JsonDataReader
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function getData(string $filename): array
    {
        $projectDir = $this->params->get('kernel.project_dir');
        $filePath = $projectDir . '/src/Data/' . $filename;

        if (!file_exists($filePath)) {
            return [];
        }

        $jsonData = file_get_contents($filePath);

        if($jsonData === false || $jsonData === null || $jsonData === ''){
            return [];
        }

        $data=json_decode($jsonData,true);
        
        if(is_array($data))
            {
                return $data;
            }

        return $data;
    }
}
?>