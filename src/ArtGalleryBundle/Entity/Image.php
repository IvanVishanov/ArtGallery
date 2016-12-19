<?php

namespace ArtGalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="ArtGalleryBundle\Repository\ImageRepository")
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var UploadedFile
     *
     * @ORM\Column(name="image", type="string", length=255)
     *
     * @Assert\File(mimeTypes={ "image/x-icon" ,"image/png"})
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="author_id", type="integer")
     */
    private $authorId;



    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="ArtGalleryBundle\Entity\User",inversedBy="images")
     * @ORM\JoinColumn(name="author_id",referencedColumnName="id")
     */
    private $author;

    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Image
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return UploadedFile
     */
    public function getImage()
    {
        return $this->image;
    }



    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param boolean $approved
     */
    public function setApproved(bool $approved)
    {
        $this->approved = $approved;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     *  @return \ArtGalleryBundle\Entity\User $author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param \ArtGalleryBundle\Entity\User $author
     *
     * @return Image
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     */
    public function setAuthorId(int $authorId)
    {
        $this->authorId = $authorId;
    }


}

