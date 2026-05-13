<?php

declare(strict_types=1);

use App\Articles\Domain\Entity\Article;
use App\Articles\ViewArticle;
use App\Authors\Infra\Http\SignUpHandler as AuthorSignUp;
use App\Articles\Infra\Http\ArticleController;
use App\Application\Controllers\SitemapRequestHandler;
use App\Application\Controllers\TestController;
use App\Homepage\RequestHandler;
use Doctrine\ORM\EntityManager;
use Webmaster\Http\Routing\RouteBuilder;

return function (RouteBuilder $root): RouteBuilder {
    return $root->
};
