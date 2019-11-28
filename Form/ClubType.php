<?php
// src/Form/TagType.php
namespace Owp\OwpEntry\Form;

use Owp\OwpCore\Entity\Base;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Owp\OwpCore\Repository\BaseRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ClubType extends AbstractType
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('base', EntityType::class, [
                'class' => Base::class,
                'property_path' => '[id]',
                'query_builder' => function (BaseRepository $er) use($options) {
                    return $er->createQueryBuilder('u')
                        ->where('u.club = :club')
                        ->setParameter('club', $options['club'])
                        ->addOrderBy('u.lastName', 'ASC')
                        ->addOrderBy('u.firstName', 'ASC');
                },
                'multiple'      => true,
                'expanded'      => true
            ])
            ->setAction($this->router->generate('owp_entry_add_submit', [
                'event' => $options['event'],
                'mode' => 'club',
                'option' => $options['club']
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'club' => $_ENV['CLUB'],
            'event' => null
        ));
    }
}
