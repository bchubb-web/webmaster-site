<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'threads')]
class Thread
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    public ?int $id = null;

    #[ORM\Column]
    public string $title;

    #[ORM\Column]
    public string $slug;

    #[ORM\Column]
    public ?\DateTime $created;

    public function __construct()
    {
        $this->created = new \DateTime('now');
    }
}

