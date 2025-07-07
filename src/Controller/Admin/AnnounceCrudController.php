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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;

class AnnounceCrudController extends AbstractCrudController
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
        return Announce::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('user', 'Utilisateur')
            ->formatValue(function ($value, $entity) {
                return $entity->getUser() ? $entity->getUser()->getPseudo() : '';
            }),
            TextField::new('book.title', 'Livre')
                ->onlyOnIndex(),
            TextareaField::new('content', 'Description'),
            ImageField::new('imageUrl', 'Image')
                ->setBasePath('images/announces')
                ->hideOnForm(),
           
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
            ->linkToCrudAction('showWarningForm')
            ->addCssClass('btn btn-danger')
            ->displayAsLink();

        return $actions
            ->add(Crud::PAGE_INDEX, $deleteWithWarning)
            ->add(Crud::PAGE_DETAIL, $deleteWithWarning)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    /**
     * Affiche le formulaire pour saisir le message d'avertissement
     */
    public function showWarningForm(Request $request): Response
    {
        $announceId = $request->query->get('entityId');
        $announce = $this->entityManager->getRepository(Announce::class)->find($announceId);
        
        if ($announce === null) {
            $this->addFlash('danger', 'L\'annonce demandée n\'existe pas.');
            return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
        }

        // Création du formulaire
        $form = $this->formFactory->createBuilder()
            ->add('message', TextareaType::class, [
                'label' => 'Message d\'avertissement',
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Saisissez le motif de suppression de l\'annonce...'
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
            return $this->processWarningCreation($announce, $form->get('message')->getData());
        }

        return $this->render('admin/announce/warning_form.html.twig', [
            'announce' => $announce,
            'form' => $form->createView(),
            'cancel_url' => $this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl()
        ]);
    }

    /**
     * Traite la création de l'avertissement et la suppression de l'annonce
     */
    private function processWarningCreation(Announce $announce, string $message): Response
    {
        try {
            // Création de l'avertissement
            $warning = new Warning();
            $warning->setCreatedAt(new \DateTimeImmutable());
            $warning->setUser($announce->getUser());
            $warning->setCreatedBy($this->getUser());
            $warning->setMessage($message); // Ajout du message personnalisé

            // Enregistrement de l'avertissement
            $this->entityManager->persist($warning);
            
            // Suppression de l'annonce (les commentaires seront supprimés automatiquement grâce à orphanRemoval=true)
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