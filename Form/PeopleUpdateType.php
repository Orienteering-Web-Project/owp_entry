<?php
// src/Form/TagType.php
namespace Owp\OwpEntry\Form;

use Owp\OwpEntry\Entity\People;
use Owp\OwpCore\Entity\Base;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Owp\OwpCore\Repository\BaseRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PeopleUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('base', EntityType::class, [
                'required' => false,
                'class' => Base::class,
                'query_builder' => function (BaseRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.club', 'ASC');
                },
                'group_by' => 'club'
            ])
            ->add('firstName', TextType::class, [
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'required' => false,
            ])
            ->add('comment', TextType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => People::class
        ]);
    }
}
