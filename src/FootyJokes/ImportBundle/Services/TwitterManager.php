<?php

namespace FootyJokes\ImportBundle\Services;

use Symfony\Component\DependencyInjection\Container;

/**
 * Description of TwitterManager
 *
 * @author Vincent
 */
class TwitterManager
{

    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }


    /**
     * Get $screenName timeline
     * @param string $screenName
     * @return array List of tweets
     */
    public function getUserTimeline($screenName)
    {
        $twtterData = $this->container->getParameter('fos_twitter');

        $connection = new \TwitterOAuth(
                $twtterData['consumer_key'], 
                $twtterData['consumer_secret'], 
                $twtterData['oauth_token'], 
                $twtterData['oauth_secret']
        );
        
        $parameters = array();
        $parameters['screen_name'] = $screenName;
        $timelineArray = $connection->get($twtterData['usertimeline_url'], $parameters);
        
        $timeline = array();
        foreach ($timelineArray as $tweet) {
            $tweetData = array();
            $tweetData['text'] = $tweet->text;
            $tweetData['title'] = $this->getTitleFromTweet($tweet->text);
            
            $media = isset($tweet->entities->media) ? $tweet->entities->media : null;
            if (!empty($media)) {
                $tweetData['image'] = $media[0]->media_url;
            }
            else {
                $tweetData['image'] = null;                
            }
            
            $timeline[] = $tweetData;
        }
        
        return $timeline;
    }


    /**
     * Get $screenName home timeline
     * @param string $screenName
     * @return array List of tweets
     */
    public function getHomeTimeline($screenName)
    {
        $twtterData = $this->container->getParameter('fos_twitter');

        $connection = new \TwitterOAuth(
                $twtterData['consumer_key'], 
                $twtterData['consumer_secret'], 
                $twtterData['oauth_token'], 
                $twtterData['oauth_secret']
        );
        
        $parameters = array();
        $parameters['screen_name'] = $screenName;
        $timelineArray = $connection->get($twtterData['hometimeline_url'], $parameters);
        
        $timeline = array();
        foreach ($timelineArray as $tweet) {
            $tweetData = array();
            $tweetData['text'] = $tweet->text;
            $tweetData['title'] = $this->getTitleFromTweet($tweet->text);
            
            $media = isset($tweet->entities->media) ? $tweet->entities->media : null;
            if (!empty($media)) {
                $tweetData['image'] = $media[0]->media_url;
            }
            else {
                $tweetData['image'] = null;                
            }
            
            $timeline[] = $tweetData;
        }
        
        return $timeline;
    }

    /**
     * Remove url from tweet to get title
     * 
     * @param string $tweet
     * @return string
     */
    protected function getTitleFromTweet($tweet)
    {
        $pattern = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i";
        $replacement = "";
        return preg_replace($pattern, $replacement, $tweet);
    }

}