<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Warning;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class CommentCrudController extends AbstractCrudController
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
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('user', 'Utilisateur')
                ->setFormTypeOption('choice_label', 'pseudo')
                ->formatValue(function ($value, $entity) {
                    return $entity->getUser() ? $entity->getUser()->getPseudo() : '';
                }),
            TextareaField::new('content', 'Contenu'),
            AssociationField::new('announce', 'Annonce')
                ->setFormTypeOption('choice_label', 'title'),
            DateTimeField::new('createdAt', 'Créé le')
                ->setFormat('dd/MM/yyyy HH:mm'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $createWarning = Action::new('createWarning', 'Signaler et supprimer', 'fa fa-exclamation-triangle')
            ->linkToCrudAction('createWarningAction')
            ->addCssClass('btn btn-danger')
            ->displayAsLink();

        return $actions
            ->add(Crud::PAGE_INDEX, $createWarning)
            ->add(Crud::PAGE_DETAIL, $createWarning)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    /**
     * Action pour supprimer un commentaire et créer un avertissement pour l'utilisateur
     */
    public function createWarningAction(AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager): Response
    {
        $commentId = $this->getContext()->getRequest()->query->get('entityId');
        $comment = $entityManager->getRepository(Comment::class)->find($commentId);
        
        if ($comment === null) {
            $this->addFlash('danger', 'Le commentaire demandé n\'existe pas.');
            return $this->redirect($adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
        }

        try {
            // Création de l'avertissement
            $warning = new Warning();
            $warning->setCreatedAt(new \DateTimeImmutable());
            $warning->setUser($comment->getUser());
            $warning->setCreatedBy($this->getUser());

            // Enregistrement de l'avertissement
            $entityManager->persist($warning);
            
            // Suppression du commentaire
            $entityManager->remove($comment);
            
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé et un avertissement a été créé pour l\'utilisateur.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue: ' . $e->getMessage());
        }

        return $this->redirect($adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commentaire')
            ->setEntityLabelInPlural('Commentaires')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des commentaires')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détail du commentaire')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->showEntityActionsInlined();
    }
}