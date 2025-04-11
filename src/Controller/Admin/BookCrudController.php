<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class BookCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Manga/Anime')
            ->setEntityLabelInPlural('Mangas/Animes')
            ->setPageTitle('index', 'Liste des mangas et animes')
            ->setPageTitle('new', 'Ajouter un manga/anime')
            ->setPageTitle('edit', 'Modifier un manga/anime');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title', 'Titre');
        yield TextField::new('author', 'Auteur');
        yield DateTimeField::new('createdAt', 'Date de publication')
            ->setFormTypeOption('input', 'datetime_immutable');
        
        yield ImageField::new('img', 'Image')
            ->setBasePath('uploads/books')
            ->setUploadDir('public/uploads/books')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired($pageName === Crud::PAGE_NEW);

        // Gestion des genres via une collection
        yield AssociationField::new('typeBooks', 'Genres')
            ->setFormTypeOption('by_reference', false)
            ->hideOnIndex();
    }

    public function createEntity(string $entityFqcn)
    {
        $book = new Book();
        $book->setCreatedAt(new \DateTimeImmutable());
        
        return $book;
    }
}