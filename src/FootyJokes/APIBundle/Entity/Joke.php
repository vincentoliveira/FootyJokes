<?php

namespace FootyJokes\APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FootyJokes\APIBundle\Helper\CommonHelper;

/**
 * Joke
 *
 * @ORM\Table(name="joke")
 * @ORM\Entity(repositoryClass="FootyJokes\APIBundle\Entity\Repository\JokeRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Joke
{
    const THUMB_HEIGHT = 300;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=31, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=127, nullable=false)
     */
    private $path;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", length=127, nullable=false)
     */
    private $visible;

    /*
     * @var File
     * @Assert\File(maxSize="2M")
     */
    public $file;
    
    /**
     * @var string 
     */
    public $tmpPath;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Joke
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Joke
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Joke
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Joke
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }
    
     public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }
    
     public function getThumbAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/min-'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'images';
    }
    
        /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->tmpPath) {
            $ext = basename(mime_content_type($this->tmpPath));
            $this->path = sha1(uniqid(mt_rand(), true)).'.'.$ext;
        }
        if (null !== $this->file) {
            $this->path = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            if (null === $this->tmpPath) {
                return;
            }
            $path = $this->getUploadRootDir() . '/' . $this->path;
            copy($this->tmpPath, $path);
        }
        else {
            $this->file->move($this->getUploadRootDir(), $this->path);
        }
        
        // generate thumbnail
        $thumbnailPath = $this->getUploadRootDir() . '/min-' . $this->path;
        CommonHelper::resizeImage($this->getAbsolutePath(), $thumbnailPath, self::THUMB_HEIGHT);

        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
        if ($file = $this->getThumbAbsolutePath()) {
            unlink($file);
        }
    }
}