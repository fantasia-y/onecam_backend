<?php

namespace App\Form\Session;

use App\Entity\Session\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('validUntil', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'])
            ->add('maxParticipants')
            ->add('allowGuests');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class
        ]);
    }
}