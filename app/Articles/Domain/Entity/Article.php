<?php

declare(strict_types=1);

namespace App\Articles\Domain\Entity;

use App\Shared\Domain\Entity\Author;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'articles')]
class Article
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    public ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    public string $title;

    #[ORM\ManyToOne(targetEntity: Author::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Author $author = null;

    #[ORM\Column(type: Types::TEXT)]
    public string $content;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $created;

    #[ORM\Column(type: Types::INTEGER)]
    public int $likes = 0;

    /*#[ORM\OneToMany(targetEntity: 'Comment', mappedBy: 'article')]
    public $comments;*/

    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        //$this->comments = new \Doctrine\Common\Collections\ArrayCollection();
    }
}

