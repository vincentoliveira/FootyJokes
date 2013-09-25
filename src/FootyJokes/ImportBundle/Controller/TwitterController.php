<?php

namespace FootyJokes\ImportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TwitterController extends Controller
{
    /**
     * @Template()
     * 
     * @param string $screenName
     */
    public function timelineAction($screenName)
    {
        $twitterManager = $this->container->get('footy_jokes_import.twitter');
        
        $timeline = $twitterManager->getTimeline($screenName);
        
        return array(
            'screenName' => $screenName,
            'timeline' => $timeline,
        );
    }
}
