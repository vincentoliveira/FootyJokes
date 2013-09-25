<?php

namespace FootyJokes\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FootyJokesFrontBundle:Default:index.html.twig', array('name' => 'world'));
    }
}
