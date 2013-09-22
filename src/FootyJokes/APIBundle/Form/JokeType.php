<?php

namespace FootyJokes\APIBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JokeType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array(
                'data' => new \DateTime()
            ))
            ->add('title', 'text', array(
            ))
            ->add('file', 'file', array(
            ))
            ->add('visible', 'checkbox', array(
                'required' => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FootyJokes\APIBundle\Entity\Joke'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'joke';
    }
}
