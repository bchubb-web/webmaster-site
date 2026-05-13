<?php

declare(strict_types=1);

use App\Shared\Component\Header;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\UX\TwigComponent\ComponentFactory;
use Symfony\UX\TwigComponent\ComponentTemplateFinder;
use Symfony\UX\TwigComponent\ComponentTemplateFinderInterface;
use Symfony\UX\TwigComponent\Twig\ComponentExtension;
use Symfony\UX\TwigComponent\Twig\ComponentLexer;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

return new class extends AbstractServiceProvider
{
    public function provides(string $id): bool
    {
        return in_array($id, [
            ComponentFactory::class
        ]);
    }
    public function register(): void
    {
        $this->getContainer()
            ->addShared(ComponentFactory::class)
            ->addArguments([
                ComponentTemplateFinderInterface::class,
                ServiceLocator::class,
                PropertyAccessorInterface::class,
                EventDispatcherInterface::class,
                [
                    Header::class => [
                        'key'=>'header',
                        'template'=>'header.twig'
                    ],
                ],
                [
                    'header' => Header::class
                ],
                Environment::class,
            ])
        ;
    }
};
