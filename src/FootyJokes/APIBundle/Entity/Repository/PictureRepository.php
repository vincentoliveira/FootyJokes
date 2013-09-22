<?php

namespace Pictures\CommonBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PictureRepository
 */
class PictureRepository extends EntityRepository
{
    /*
     * Get a random picture
     * @param $statusId
     * @return \Picture
     */
    public function getRandom($statusId)
    {
        $queryBuilder = $this->createQueryBuilder('picture')
                ->where('picture.status = :statusId')
                ->setParameter(':statusId', $statusId)
        ;

        // count pictures
        $count = $queryBuilder
                ->select('count(picture)')
                ->getQuery()
                ->getSingleScalarResult()
        ;

        $seed = mt_rand(0, $count - 1);
        $queryBuilder->select('picture')
                ->setFirstResult($seed)
                ->setMaxResults(1)
        ;

        return $queryBuilder
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
    }
    
    
    /*
     * Paginated search by status
     * @param int|array $statusId
     * @param int $first
     * @param int $number
     * @return array count and pictures
     */
    public function searchByStatus($search, $statusIds, $first, $maxResults)
    {
        if (!is_array($statusIds)) {
            $statusIds = array($statusIds);
        }
        
        $queryBuilder = $this->createQueryBuilder('picture')
                ->select('picture.id, picture.token, picture.title')
                ->leftJoin('picture.author', 'author')
                ->leftJoin('picture.status', 'status')
                ->setFirstResult($first)
                ->setMaxResults($maxResults)
                ->addOrderBy('picture.date', 'DESC')
        ;
        
        if (!empty($search)) {
            $queryBuilder->where('picture.title LIKE :search)')
                    ->setParameter(':search', '%'.$search.'%');
        }
        if (!empty($statusIds)) {
            $queryBuilder->where('status IN (:statusIds)')
                    ->setParameter(':statusIds', $statusIds);
        }

        return $queryBuilder
                ->getQuery()
                ->getArrayResult()
        ;
    }


    /*
     * Paginated search 
     * @param $filters
     * @param $first
     * @param $number
     * @return array count and pictures
     */
    public function search($filters, $first, $maxResults)
    {
        $queryBuilder = $this->createQueryBuilder('picture')
                ->leftJoin('picture.author', 'author')
                ->leftJoin('picture.status', 'status')
        ;

        $i = 1;
        foreach ($filters as $key => $value) {
            $queryBuilder->andWhere($key . ' = ?' . $i)
                    ->setParameter($i, $value);
            $i++;
        }

        $count = $queryBuilder
                ->select('count(picture)')
                ->getQuery()
                ->getSingleScalarResult()
        ;


        $pictures = $queryBuilder
                ->select('picture, author, status')
                ->setFirstResult($first)
                ->setMaxResults($maxResults)
                ->addOrderBy('picture.date', 'DESC')
                ->getQuery()
                ->getResult()
        ;

        return array(
            'count' => $count,
            'pictures' => $pictures,
        );
    }


}