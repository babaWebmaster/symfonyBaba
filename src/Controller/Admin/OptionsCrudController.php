<?php

namespace App\Controller\Admin;

use App\Entity\Options;
use App\Enum\OptionType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class OptionsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Options::class;
    }

    
    public function configureFields(string $pageName): iterable 
    {
        yield TextField::new('name')->setLabel('Nom de l’option');
        yield ChoiceField::new('optionType')
            ->setChoices([
                'Texte' => OptionType::STRING,
                'Texte long' => OptionType::TEXT,
                'Image' => OptionType::IMAGE,
                'Nombre' => OptionType::INTEGER,
                'Booléen' => OptionType::BOOLEAN,
            ])
            ->setLabel('Type');

        // Champ dynamique pour value
        yield $this->getValueField();
    }

    private function getValueField()
    {
        $options = $this->getContext()->getEntity()->getInstance();

        if ($options instanceof Options) {
            return match($options->getOptionType()) {
                OptionType::IMAGE   => ImageField::new('value')
                    ->setUploadDir('public/uploads/images/options/')
                    ->setBasePath('uploads/images/options')
                    ->setLabel('Valeur (image)')
                    ->setRequired(false),
                OptionType::BOOLEAN => BooleanField::new('value')->setLabel('Valeur (booléen)'),
                OptionType::INTEGER => IntegerField::new('value')->setLabel('Valeur (nombre)'),
                OptionType::TEXT    => TextareaField::new('value')->setLabel('Valeur (texte long)'),
                default             => TextField::new('value')->setLabel('Valeur (texte)'),
            };
        }

        // Par défaut si pas encore instancié
        return TextField::new('value')->setLabel('Valeur');
    }
    
}
