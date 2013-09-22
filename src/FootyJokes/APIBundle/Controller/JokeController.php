<?php

namespace FootyJokes\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FootyJokes\APIBundle\Entity\Joke;
use FootyJokes\APIBundle\Form\JokeType;

class JokeController extends Controller
{
    public function getAction($from, $maxResults)
    {
        if ($from < 0) {
            $from = 0;
        }
        if ($maxResults <= 0) {
            $maxResults = $this->container->getParameter('jokes_default_max_results');
        }
        
        $jokes = $this->getDoctrine()
                ->getRepository('FootyJokesAPIBundle:Joke')
                ->getVisible($from, $maxResults);
        
        $response = new Response(json_encode(array('jokes' => $jokes)));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    
    public function randomAction()
    {        
        $joke = $this->getDoctrine()
                ->getRepository('FootyJokesAPIBundle:Joke')
                ->getRandom();
        
        $response = new Response(json_encode(array('joke' => $joke)));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() != "POST") {
            throw new NotFoundHttpException();
        }
        
        $form = $this->createForm(new JokeType());
        $form->bind($request);
        
        if (!$form->isValid()) {
            $response = new Response(json_encode(array('joke' => 'null', 'error' => 'Invalid args')));
            $response->headers->set('Content-Type', 'application/json');
            
            return $response;
        }
        
        $joke = $form->getData();
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($joke);
        $em->flush();
        
        $response = new Response(json_encode(array('joke' => $joke->getId())));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
