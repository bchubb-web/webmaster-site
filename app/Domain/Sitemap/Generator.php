<?php

declare(strict_types=1);

namespace App\Domain\Sitemap;

use Symfony\Component\Routing\Generator\CompiledUrlGenerator;

class Generator
{
    private \SimpleXMLElement $template;

    public function __construct(
        private readonly CompiledUrlGenerator $urlGenerator,
    ) {
    }

    public function read(): self
    {
        if (!file_exists(ROOT . '/config/sitemap.xml')) {
            throw new \RuntimeException('Sitemap config file not found');
        }

        // read from config/sitemap.xml
        $sitemap = simplexml_load_file(ROOT . '/config/sitemap.xml');

        if (!$sitemap) {
            throw new \RuntimeException('Failed to load sitemap XML');
        }

        $this->template = $sitemap;

        return $this;
    }


    public function build(): \SimpleXMLElement
    {
        // handle differently based on the root node
        return match ($this->template->getName()) {
            'urlset' => $this->template,
            'routes' => $this->transformRoutes($this->newUrlset(), $this->template),
            'sitemap' => $this->buildSitemap(),
            default => throw new \RuntimeException('Invalid sitemap root element'),
        };
    }

    protected function transformRoutes(\SimpleXMLElement $root, \SimpleXMLElement $routes): \SimpleXMLElement
    {
        // iterate over routes
        foreach ($routes->route as $route) {
            $name = (string) $route['name'];
            $loc = $this->urlGenerator->generate($name);
            $url = $root->addChild('url');
            $url->addChild('loc', htmlspecialchars($loc, ENT_QUOTES | ENT_XML1, 'UTF-8'));
        }

        
        return $root;
    }

    private function buildSitemap(): \SimpleXMLElement
    {
        if ($this->template->urlset) {
            $urlset = $this->template->urlset;
        } else {
            $urlset = $this->newUrlset();
        }

        if (isset($this->template->routes)) {
            $urlset = $this->transformRoutes($urlset, $this->template->routes);
        }

        return $urlset;
    }

    private function newUrlset(): \SimpleXMLElement
    {
        return new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
    }
}
