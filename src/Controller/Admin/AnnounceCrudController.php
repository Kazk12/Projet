<?php

namespace App\Controller\Admin;

use App\Entity\Announce;
use App\Entity\Comment;
use App\Entity\Warning;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class AnnounceCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    
    public static function getEntityFqcn(): string
    {
        return Announce::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('book.title', 'Livre')
                ->onlyOnIndex(),
            TextareaField::new('content', 'Description'),
            ImageField::new('imageUrl', 'Image')
                ->setBasePath('images/announces')
                ->hideOnForm(),
            AssociationField::new('user', 'Utilisateur')
                ->formatValue(function ($value, $entity) {
                    return $entity->getUser() ? $entity->getUser()->getPseudo() : '';
                }),
            AssociationField::new('book', 'Livre')
                ->formatValue(function ($value, $entity) {
                    return $entity->getBook() ? $entity->getBook()->getTitle() : '';
                })
                ->hideOnIndex(),
            DateTimeField::new('createdAt', 'Créé le')
                ->setFormat('dd/MM/yyyy HH:mm'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $deleteWithWarning = Action::new('deleteWithWarning', 'Supprimer avec avertissement', 'fa fa-trash')
            ->linkToCrudAction('deleteWithWarningAction')
            ->addCssClass('btn btn-danger')
            ->displayAsLink();

        return $actions
            ->add(Crud::PAGE_INDEX, $deleteWithWarning)
            ->add(Crud::PAGE_DETAIL, $deleteWithWarning)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    /**
     * Action pour supprimer une annonce et créer un avertissement pour l'utilisateur
     * en supprimant d'abord tous les commentaires liés à l'annonce
     */
    public function deleteWithWarningAction(): Response
    {
        try {
            $announceId = $this->getContext()->getRequest()->query->get('entityId');
            $announce = $this->entityManager->getRepository(Announce::class)->find($announceId);
            
            if ($announce === null) {
                $this->addFlash('danger', 'L\'annonce demandée n\'existe pas.');
                return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
            }

            // Création de l'avertissement
            $warning = new Warning();
            $warning->setCreatedAt(new \DateTimeImmutable());
            $warning->setUser($announce->getUser());
            $warning->setCreatedBy($this->getUser());

            // Enregistrement de l'avertissement
            $this->entityManager->persist($warning);
            
            // Suppression de l'annonce
            $this->entityManager->remove($announce);
            
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'annonce et ses commentaires ont été supprimés, et un avertissement a été créé pour l\'utilisateur.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue: ' . $e->getMessage());
        }

        return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Annonce')
            ->setEntityLabelInPlural('Annonces')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des annonces')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détail de l\'annonce')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->showEntityActionsInlined();
    }
}