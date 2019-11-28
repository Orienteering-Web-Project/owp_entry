<?php
// src/Form/TagType.php
namespace Owp\OwpEntry\Form;

use Owp\OwpEntry\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TeamType extends AbstractType
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('peoples', CollectionType::class, [
                'entry_type' => PeopleAddType::class,
                'entry_options' => ['label' => false]
            ])
            ->add('label', TextType::class, [
                'required' => false,
            ])
            ->setAction($this->router->generate('owp_entry_add_submit', [
                'event' => $options['event'],
                'mode' => 'team'
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
            'club' => $_ENV['CLUB'],
            'event' => null
        ]);
    }
}
