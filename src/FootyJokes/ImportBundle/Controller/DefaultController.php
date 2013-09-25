<?php

namespace FootyJokes\ImportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FootyJokesImportBundle:Default:index.html.twig', array('name' => 'world'));
    }
}
