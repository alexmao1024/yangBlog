<?php

namespace App\Utils;

use Doctrine\ORM\Mapping as ORM;
date_default_timezone_set('Asia/Shanghai');

trait DateTimeTrait
{

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $createAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updateAt;

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    #[ORM\PrePersist]
    public function setCreateAt(): self
    {
        $this->createAt = new \DateTime();

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdateAt(): self
    {
        $this->updateAt = new \DateTime();

        return $this;
    }
}