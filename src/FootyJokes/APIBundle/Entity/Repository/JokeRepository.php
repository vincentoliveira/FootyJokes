<?php

namespace FootyJokes\APIBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * JokeRepository
 */
class JokeRepository extends EntityRepository
{
    /**
     * Get visible jokes (paginated)
     * @param $first
     * @param $maxResults
     * @return array
     */
    public function getVisible($first, $maxResults)
    {
        $query = $this->createQueryBuilder('joke')
                ->where('joke.visible = true')
                ->andWhere('joke.date < :now')
                ->setParameter(':now', new \DateTime())
                ->orderBy('joke.date', 'DESC')
                ->addOrderBy('joke.id', 'DESC')
        ;
        
        if ($first > 0) {
            $query->setFirstResult($first);
        }
        if ($maxResults > 0) {
            $query->setMaxResults($maxResults);
        }
        
        return $query->getQuery()->getArrayResult();
    }
    
    /**
     * Get all jokes (paginated)
     * @param $first
     * @param $maxResults
     * @return array ['count'] => maxJokes, ['jokes'] => paginated jokes
     */
    public function getAll($first = 0, $maxResults = 0)
    {
        $query = $this->createQueryBuilder('joke');
        
        // count pictures
        $count = $query
                ->select('count(joke)')
                ->getQuery()
                ->getSingleScalarResult()
        ;
        
        // get paginated jokes
        $query->select('joke')
                ->orderBy('joke.date', 'DESC')
                ->addOrderBy('joke.id', 'DESC')
        ;

        if ($first > 0) {
            $query->setFirstResult($first);
        }
        if ($maxResults > 0) {
            $query->setMaxResults($maxResults);
        }
        
        $jokes = $query->getQuery()->getResult();
        
        return array(
            'count' => $count,
            'jokes' => $jokes,
        );
    }
    
    /**
     * Get a random visible joke
     * @return joke
     */
    public function getRandom()
    {
        $queryBuilder = $this->createQueryBuilder('joke')
                ->where('joke.visible = true')
        ;

        // count pictures
        $count = $queryBuilder
                ->select('count(joke)')
                ->getQuery()
                ->getSingleScalarResult()
        ;

        $seed = mt_rand(0, $count - 1);
        $queryBuilder->select('joke')
                ->setFirstResult($seed)
                ->setMaxResults(1)
        ;

        $joke = $queryBuilder
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
        
        if ($joke == null) {
            return null;
        }
        
        return array(
            'id' => $joke->getId(),
            'title' => $joke->getTitle(),
            'path' => $joke->getPath(),
        );
    }
}