<?php
// src/Form/TagType.php
namespace Owp\OwpEntry\Form;

use Owp\OwpCore\Entity\Base;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EntityType;

class ClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('base', EntityType::class, [
                'class' => Base::class,
                'property'      => 'id',
                'property_path' => '[id]',
                'query_builder' => function (BaseRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.club = :club')
                        ->setParameter('club', '2904BR')
                        ->orderBy('u.club', 'ASC');
                },
                'multiple'      => true,
                'expanded'      => true
            ])
        ;
    }
}
