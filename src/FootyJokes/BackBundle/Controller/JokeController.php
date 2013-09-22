<?php

namespace FootyJokes\BackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use FootyJokes\APIBundle\Form\JokeType;
use FootyJokes\APIBundle\Form\EditJokeType;

class JokeController extends Controller
{
    /**
     * @Template()
     */
    public function addAction()
    {
        $form = $this->createForm(new JokeType());
        return array('form' => $form->createView());
    }
    
    /**
     * @Template()
     */
    public function editAction($id, $page = 1)
    {
        $joke = $this->getDoctrine()
                ->getRepository('FootyJokesAPIBundle:Joke')
                ->find($id)
        ;
        
        $form = $this->createForm(new EditJokeType(), $joke);
        
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
                 $joke = $form->getData();
        
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($joke);
                $em->flush();
                
                return $this->redirect($this->generateUrl('footy_jokes_back_joke_list', array('page' => $page)));
            }
        }
        
        return array('form' => $form->createView());
    }
    
    /**
     * @Template()
     */
    public function listAction($page = 1)
    {
        $maxResults = $this->container->getParameter('jokes_back_max_results');
        $first = ($page - 1) * $maxResults;
        
        $jokes = $this->getDoctrine()
                ->getRepository('FootyJokesAPIBundle:Joke')
                ->getAll($first, $maxResults);
        
        return array(
            'jokes' => $jokes,
            'page' => $page,
        );
    }
}
