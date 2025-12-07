<?php

namespace App\Twig;

use App\Repository\OptionsRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class VariableGlobalExtension extends AbstractExtension implements GlobalsInterface{

    public function __construct(private OptionsRepository $optionsRepository)
    {
        $this->optionsRepository = $optionsRepository;
    }

    public function getGlobals(): array
    {
        return [
            'favicon' => $this->optionsRepository->findValueByName('favicon'),
        ];
    }

}

?>