<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Validator\Constraints\Choice;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [           
            TextField::new('username')->setRequired(true),
            Field::new('password')->setColumns(6)
            ->hideOnIndex()
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([               
                'type' => PasswordType::class,
                'data' => '',
                'invalid_message' => 'The password fields must match.',
                'attr' => ['class' => 'password-field col-md-6 col-xxl-5'],
                'first_options'  => ['label' => 'Password','attr' => ['class' => 'password-field col-md-6 col-xxl-5']],
                'second_options' => ['label' => 'Repeat Password'],
            ])           
            ->setRequired(false)
            ->setDefaultColumns('col-md-6 col-xxl-5')
            ->setCssClass('col-md-6 col-xxl-5')
            ->setCustomOption(TextField::OPTION_MAX_LENGTH, null)
            ->setCustomOption(TextField::OPTION_RENDER_AS_HTML, false)
            ->setCustomOption(TextField::OPTION_STRIP_TAGS, false),
            ChoiceField::new('roles')
                ->setChoices([
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                    'Team' => 'ROLE_TEAM',
                ])
               ->allowMultipleChoices()
                ->setRequired(true)
                ->setHelp('Select the roles for this user.')
                ->setLabel('Roles'),
           
        ];
    }
    
}
