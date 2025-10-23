<?php

namespace App\Authors\Domain;

use App\Shared\Domain\Entity\Author;

class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
}
