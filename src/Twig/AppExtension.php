<?php
// src/Twig/AppExtension.php

// ...
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;

class AppExtension extends AbstractExtension
{
   

    public function __construct(private EntrypointLookupInterface $entrypointLookup)
    {
       
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('encore_entry_exists', [$this, 'encoreEntryExists']),
        ];
    }

    public function encoreEntryExists(string $entryName): bool
    {
        return $this->entrypointLookup->entryExists($entryName);
    }
}
?>