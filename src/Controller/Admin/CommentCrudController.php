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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;

class CommentCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;
    private AdminUrlGenerator $adminUrlGenerator;
    private FormFactoryInterface $formFactory;

    public function __construct(
        EntityManagerInterface $entityManager, 
        AdminUrlGenerator $adminUrlGenerator,
        FormFactoryInterface $formFactory
    ) {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->formFactory = $formFactory;
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
            ->linkToCrudAction('showWarningForm')
            ->addCssClass('btn btn-danger')
            ->displayAsLink();

        return $actions
            ->add(Crud::PAGE_INDEX, $createWarning)
            ->add(Crud::PAGE_DETAIL, $createWarning)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    /**
     * Affiche le formulaire pour saisir le message d'avertissement
     */
    public function showWarningForm(Request $request): Response
    {
        $commentId = $request->query->get('entityId');
        $comment = $this->entityManager->getRepository(Comment::class)->find($commentId);
        
        if ($comment === null) {
            $this->addFlash('danger', 'Le commentaire demandé n\'existe pas.');
            return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
        }

        // Création du formulaire
        $form = $this->formFactory->createBuilder()
            ->add('message', TextareaType::class, [
                'label' => 'Message d\'avertissement',
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Saisissez le motif de suppression du commentaire...'
                ],
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Confirmer la suppression',
                'attr' => ['class' => 'btn btn-danger']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processWarningCreation($comment, $form->get('message')->getData());
        }

        return $this->render('admin/comment/warning_form.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'cancel_url' => $this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl()
        ]);
    }

    /**
     * Traite la création de l'avertissement et la suppression du commentaire
     */
    private function processWarningCreation(Comment $comment, string $message): Response
    {
        try {
            // Création de l'avertissement
            $warning = new Warning();
            $warning->setCreatedAt(new \DateTimeImmutable());
            $warning->setUser($comment->getUser());
            $warning->setCreatedBy($this->getUser());
            $warning->setMessage($message); // Ajout du message personnalisé

            // Enregistrement de l'avertissement
            $this->entityManager->persist($warning);
            
            // Suppression du commentaire
            $this->entityManager->remove($comment);
            
            $this->entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé et un avertissement a été créé pour l\'utilisateur.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue: ' . $e->getMessage());
        }

        return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
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