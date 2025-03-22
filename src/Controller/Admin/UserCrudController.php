<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInSingular('Utilisateur')
        ->setEntityLabelInPlural('Utilisateurs');
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstname')->setLabel('Prénom'),
            TextField::new('lastname')->SetLabel('Nom'),
            TextField::new('email')->SetLabel('Email')->onlyOnIndex()
            //TextEditorField::new('description'),
        ];
    }    
}
