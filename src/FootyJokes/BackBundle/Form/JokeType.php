<?php

namespace FootyJokes\BackBundle\Form;

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
                'label' => 'Date',
                'data' => new \DateTime(),
                'attr' => array(
                    'class' => 'datepicker',
                ),
            ))
            ->add('title', 'text', array(
                'label' => 'Title',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('file', 'file', array(
                'label' => 'File',
                'required' => false,
                'attr' => array(
                ),
            ))
            ->add('url', 'text', array(
                'label' => 'URL',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('visible', 'checkbox', array(
                'label' => 'Visible ?',
                'required' => false,
                'attr' => array(
                ),
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'joke';
    }
}
