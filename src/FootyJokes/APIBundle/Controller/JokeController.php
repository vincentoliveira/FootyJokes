<?php

namespace FootyJokes\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FootyJokes\APIBundle\Entity\Joke;
use FootyJokes\APIBundle\Form\JokeType;

class JokeController extends Controller
{
    /**
     * Get paginated joke list
     * @param int $from
     * @param type $maxResults
     * @return json Joke list
     */
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
    
    /**
     * Get a random joke
     * @return json joke
     */
    public function randomAction()
    {        
        $joke = $this->getDoctrine()
                ->getRepository('FootyJokesAPIBundle:Joke')
                ->getRandom();
        
        $response = new Response(json_encode(array('joke' => $joke)));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    
    /*
     * Add a joke
     * @return json id of added joke
     */
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
    
    /*
     * Add a joke from url
     * POST PARAM : json description of joke
     * {"date":"dd/mm/yyyy","title":"xxx","url:"http://xxx.xx","visible":true}
     * @return json id of added joke
     */
    public function addFromUrlAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() != "POST") {
            throw new NotFoundHttpException();
        }
        
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['date']) OR
                !isset($data['title']) OR
                !isset($data['visible']) OR
                !isset($data['url'])) {
                throw new \Exception("Invalid args.", 1);
            }
            
            $data['date'] = date_create_from_format('d/m/Y', $data['date']);
            
            $manager = $this->container->get('footy_jokes.joke_manager');
            $joke = $manager->add($data);
            
            $response = new Response(json_encode(array('joke' => $joke)));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (\Exception $e) {
            $err = array(
                'errno' => $e->getCode(),
                'errMsg' => $e->getMessage(),
            );
            $response = new Response(json_encode(array('error' => $err)));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
    }
}
