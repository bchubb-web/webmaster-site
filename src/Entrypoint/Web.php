<?php

declare(strict_types=1);

namespace Webmaster\Entrypoint;

use Middlewares\Debugbar;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Webmaster\Http\Routing\Router;
use Webmaster\Http\Dispatcher;
use Relay\RelayBuilder;
use DebugBar\DataCollector\TimeDataCollector;

class Web extends AbstractEntrypoint
{
    public function __construct(
        private ServerRequestInterface $request,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly RelayBuilder $relayBuilder,
        private readonly Dispatcher $dispatcher,
        private readonly Router $router,
        private readonly TimeDataCollector $timeline,
    ) {
    }

    public function handle(): int
    {
        $this->timeline->addMeasure('Framework boot', $_SERVER['REQUEST_TIME_FLOAT'], microtime(true));
        $this->handleRedirects();

        $this->request = $this->router->match($this->request);

        $queue = [
            $this->container->get(Debugbar::class),
            $this->dispatcher,
        ];
        $relay = $this->relayBuilder->newInstance($queue);

        $response = $relay->handle($this->request);

        $this->emit($response);

        return 0;
    }

    protected function handleRedirects(): void
    {
        $redirectsFile = $this->getRedirectFilePath();

        if (file_exists($redirectsFile)) {
            $redirects = parse_ini_file($redirectsFile, true);

            if (array_key_exists($this->request->getUri()->getPath(), $redirects)) {
                $target = $redirects[$this->request->getUri()->getPath()];

                $response = $this->responseFactory
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
