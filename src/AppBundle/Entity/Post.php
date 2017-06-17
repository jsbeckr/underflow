<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 */
class Post
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
     * Many Users have One Address.
     * @ManyToOne(targetEntity="Category")
     * @JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * Many Posts are favorited by many users
     * @ManyToMany(targetEntity="User")
     * @JoinTable(name="posts_users", joinColumns={@JoinColumn(name="post_id", referencedColumnName="id")}, inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id")})
     */
    private $favoritedUsers;

    /**
     *
     * @ManyToMany(targetEntity="Comment")
     * @JoinTable(name="posts_comments", joinColumns={@JoinColumn(name="post_id", referencedColumnName="id")}, inverseJoinColumns={@JoinColumn(name="comment_id", referencedColumnName="id")})
     */
    private $comments;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="creator", referencedColumnName="id")
     */
    private $creator;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="string", length=255)
     */
    private $image_url;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Post
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Post
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Post
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Post
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Post
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->favoritedUsers = new ArrayCollection();
    }

    /**
     * Add favoritedUser
     *
     * @param \AppBundle\Entity\User $favoritedUser
     *
     * @return Post
     */
    public function addFavoritedUser(\AppBundle\Entity\User $favoritedUser)
    {
        if($this->favoritedUsers->contains($favoritedUser))
        {
            return;
        }

        $this->favoritedUsers[] = $favoritedUser;
        $favoritedUser->removePost($this);

        return $this;
    }

    /**
     * Remove favoritedUser
     *
     * @param \AppBundle\Entity\User $favoritedUser
     */
    public function removeFavoritedUser(\AppBundle\Entity\User $favoritedUser)
    {
        if(!$this->favoritedUsers->contains($favoritedUser))
        {
            return;
        }

        $this->favoritedUsers->removeElement($favoritedUser);
        $favoritedUser->removePost($this);
    }

    /**
     * Get favoritedUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavoritedUsers()
    {
        return $this->favoritedUsers;
    }

    /**
     * Set creator
     *
     * @param \AppBundle\Entity\User $creator
     *
     * @return Post
     */
    public function setCreator(\AppBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \AppBundle\Entity\User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return Post
     */
    public function setImageUrl($imageUrl)
    {
        $this->image_url = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return Post
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
