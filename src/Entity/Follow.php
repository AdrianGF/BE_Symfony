<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FollowRepository")
 */
class Follow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $follwer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $followed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFollower(): ?int
    {
        return $this->follwer;
    }

    public function setFollower(?int $follwer): self
    {
        $this->follwer = $follwer;

        return $this;
    }

    public function getFollowed(): ?int
    {
        return $this->followed;
    }

    public function setFollowed(?int $followed): self
    {
        $this->followed = $followed;

        return $this;
    }


    public function getUnfollow(): ?int
    {
        return $this->id;
    }

    public function setUnfollow(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

}
