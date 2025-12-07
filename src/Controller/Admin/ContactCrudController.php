<?php
// src/Controller/Admin/ContactCrudController.php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name')->setCssClass('col-2');
        yield TextField::new('firstName')->setCssClass('col-2');
        yield EmailField::new('email')->setCssClass('col-3');
        yield TelephoneField::new('phone')->setCssClass('col-2');
        yield TextareaField::new('message')->setCssClass('col-5'); // Le message prend plus de place
        yield DateTimeField::new('createdAt')->setCssClass('col-2');
        
    }
}
?>