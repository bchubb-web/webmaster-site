<?php

declare(strict_types=1);

namespace App\Shared\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'authors')]
class Author
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    public ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    public string $username;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $displayName = null;

    #[ORM\Column(type: Types::STRING)]
    public string $email;

    // property hook to md5 hash on set
    #[ORM\Column(type: Types::STRING)]
    public string $password {
        get {
            return $this->password;
        }
        set (string $value) {
            $this->password = md5($value);
        }
    }

    public function __construct()
    {
    }
}

