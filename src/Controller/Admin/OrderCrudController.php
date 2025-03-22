<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
//use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
//use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;  //Symfony 5
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator; //Symfony 6

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $adminUrlGenerator; //$crudUrlGenerator (symfony 5)

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions {
        // $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        // $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');
        
        $show = Action::new('Afficher')->linkToCrudAction('show');

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            //->add(Crud::PAGE_INDEX, Action::DETAIL);
            ->add(Crud::PAGE_INDEX, $show);

            //->add('detail', $updatePreparation)
            //->add('detail', $updateDelivery)
            //->add('index', 'detail');
    }

    public function show(AdminContext $context) {
        $order = $context->getEntity()->getInstance();

        return $this->render('admin/order.html.twig', [
            'order' => $order
        ]);
    }

    public function updatePreparation(AdminContext $context)
    {
        //die('ok');
        $order = $context->getEntity()->getInstance();
        //dd($order);
        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:green'><b>La commande <b>".$order->getReference()."</b>b> est bien <u>en cours de préparation</u>.</b></span>");

        //Symfony 6 (redirection)
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context)
    {
        //die('ok');
        $order = $context->getEntity()->getInstance();
        //dd($order);
        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:orange'><b>La commande <b>".$order->getReference()."</b>b> est bien <u>en cours de livraison</u>.</b></span>");

        //Symfony 6 (redirection)
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud {   
        return $crud
                ->setEntityLabelInSingular('Commande')
                ->setEntityLabelInPlural('Commandes')
                ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt', 'date'),
            DateTimeField::new('createdAt', 'Passée le'),
            NumberField::new('State')->setLabel('Statut')->setTemplatePath('admin/state.html.twig'),
            AssociationField::new('user'),
            //TextField::new('user.getFullName', 'Utilisateur'),
            TextField::new('carrierName', 'Transporteur'),
            TextEditorField::new('delivery','Adresse de livraison')->onlyOnDetail(),
            MoneyField::new('totalWt', 'Total produit')->setCurrency('EUR'),
            MoneyField::new('carrierPrice', 'frais de port')->setCurrency('EUR'),
            //BooleanField::new('isPaid', 'Payée'),
            ChoiceField::new('state')->setChoices([
                'Non payé' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }

}
