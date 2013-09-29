<?php

namespace FootyJokes\BackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use FootyJokes\APIBundle\Entity\Joke;
use FootyJokes\BackBundle\Form\JokeType;
use FootyJokes\BackBundle\Form\EditJokeType;

class JokeController extends Controller
{
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
        $form = $this->createForm(new JokeType());
        
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                try {
                    $manager = $this->container->get('footy_jokes.joke_manager');
                    $joke = $manager->add($form->getData());
                    $this->container->get('session')->getFlashBag()->add(
                        'success',
                        'New joke has been added.'
                    );
                }
                catch (\Exception $e) {
                    $this->container->get('session')->getFlashBag()->add(
                        'error',
                        'An error has occured: '.$e->getMessage()
                    );
                }
            }
        }
        
        $maxResults = $this->container->getParameter('jokes_back_max_results');
        $first = ($page - 1) * $maxResults;
        
        $jokes = $this->getDoctrine()
                ->getRepository('FootyJokesAPIBundle:Joke')
                ->getAll($first, $maxResults);
        
        $maxPages = ceil($jokes['count'] / $maxResults);
        
        return array(
            'form' => $form->createView(),
            'jokes' => $jokes['jokes'],
            'maxPages' => $maxPages,
            'page' => $page,
        );
    }
}
