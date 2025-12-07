<?php
namespace App\Controller\Admin;

use App\Entity\Maquette;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use App\Repository\ImageRepository;


class MaquetteCrudController extends AbstractCrudController
{

    public function __construct(private ImageRepository $imageRepository ){

    }

    public static function getEntityFqcn(): string
    {
        return Maquette::class;
    }

    public function configureFields(string $pageName): iterable
    {
       
       

        return [
            IdField::new('id')
            ->setLabel('Identifiant')
            ->setDisabled(true) // visible mais non modifiable
            ->onlyOnIndex(), 
            TextField::new('title')
            ->setLabel('Titre h1'),
            TextField::new('slug')
            ->setLabel('Slug pour affichage dans l\'url'),
            TextField::new('subtitle')
            ->setLabel('Titre h3'),
            TextareaField::new('description'),
            TextareaField::new('shortDescription'),
            UrlField::new('url'),
            AssociationField::new('categoriesSite')
            ->setLabel('Catégories du site')
            ->setFormTypeOptions([
                'by_reference' => false,   
                'multiple' => true,        
            ]),
            TextField::new('imageLabel')
            ->setLabel('Sélection des images')
            ->setFormTypeOption('mapped', false)
            ->setFormTypeOption('disabled', true)
            ->setFormTypeOption('attr', [
                'class' => 'image-selector-label',
                'style' => 'border:none;background:none;color:#333;font-weight:bold;padding:0;margin-bottom:0'
            ])
            ->hideOnDetail()
            ->hideOnIndex(),
             Field::new('imagePreview')
             ->setLabel('Sélection des images')
            ->setFormType(HiddenType::class)
            ->setFormTypeOption('attr', ['class' => 'image-selector-input'])
            ->addJsFiles('js/admin/admin-gallery.js'),
           
        ];
    }
}
?>