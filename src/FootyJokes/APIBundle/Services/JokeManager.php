<?php

namespace FootyJokes\APIBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use FootyJokes\APIBundle\Entity\Joke;

/**
 * Description of TwitterManager
 *
 * @author Vincent
 */
class JokeManager
{

    protected $em;
    protected $container;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }


    /**
     * Get $screenName timeline
     * @param array $rowJoke
     * @return int Added joke id
     */
    public function add($rowJoke)
    {
        $joke = new Joke();
        
        $joke->setDate($rowJoke['date']);
        $joke->setTitle($rowJoke['title']);
        $joke->setVisible($rowJoke['visible']);
        
        if (!empty($rowJoke['url'])) {
            $filename = sha1(uniqid(mt_rand(), true));
            $path = '/tmp/' . $filename;
            
            $ch = curl_init($rowJoke['url']);
            $fp = fopen($path, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            
            $joke->tmpPath = $path;
        }
        else {
            $joke->file = $rowJoke['file'];            
        }
        
        $this->em->persist($joke);
        $this->em->flush();
        
        return $joke->getId();
    }

}