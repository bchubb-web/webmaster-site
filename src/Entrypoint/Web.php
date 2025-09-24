<?php

declare(strict_types=1);

namespace Webmaster\Entrypoint;

use Middlewares\Debugbar;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Webmaster\Http\Routing\Router;
use Webmaster\Http\Dispatcher;
use Relay\RelayBuilder;

class Web implements EntrypointInterface
{
    public function __construct(
        private readonly \Webmaster\Core $core,
        private readonly ContainerInterface $container,
        private ServerRequestInterface $request,
        private readonly RelayBuilder $relayBuilder,
        private readonly Router $router,
    ) {
    }

    public function handle(): int
    {
        $this->handleRedirects();

        $matched = $this->router->match($this->request);
        $this->request = $this->request->withAttribute('matched', $matched);

        $dispatcher = $this->container->get(Dispatcher::class);
        $dispatcher->setMatched($matched);
        $queue = [
            $this->container->get(Debugbar::class),
            $dispatcher,
        ];
        $relay = $this->relayBuilder->newInstance($queue);

        $response = $relay->handle($this->request);

        $this->emit($response);

        return 0;
    }

    public function getCore(): \Webmaster\Core
    {
        return $this->core;
    }

    protected function handleRedirects(): void
    {
        $redirectsFile = $this->getRedirectFilePath();

        if (file_exists($redirectsFile)) {
            $redirects = parse_ini_file($redirectsFile, true);

            if (array_key_exists($this->request->getUri()->getPath(), $redirects)) {
                $target = $redirects[$this->request->getUri()->getPath()];

                $responseFactory = $this->container->get(\Psr\Http\Message\ResponseFactoryInterface::class);
                $response = $responseFactory
                    ->createResponse(301)
                    ->withHeader('Location', $target);

                $this->emit($response);
                exit(0);
            }
        }
    }

    protected function emit(ResponseInterface $response): void
    {
        // Send the response to the browser
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }
        echo $response->getBody();
    }

    protected function getRedirectFilePath(): string
    {
        return ROOT . '/config/redirects.ini';
    }
}
