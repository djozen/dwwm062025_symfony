<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextEditorField::new('content')
                ->setRequired(false)
                ->setHelp('You can use Markdown syntax for formatting.')
                ->setLabel('Content'),
           ImageField::new('image')
                ->setBasePath('/uploads/articles')
                ->setUploadDir('public/uploads/articles')
                ->setRequired(false)
                ->setHelp('Upload an image for the article.'),           
        ];
    }

}
