<?php

namespace Webmaster\View;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use DebugBar\DataCollector\TimeDataCollector;

class SimpleRenderer
{
    public function __construct(
        private readonly Environment $twig,
        private readonly LoaderInterface $loader,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly TimeDataCollector $timeline,
    ) {
    }
    public function render(string $template, array $data = []): string
    {
        $this->timeline->startMeasure("rendering-$template", "Rendering $template");
        if (!$this->loader->exists($template)) {
            throw new \RuntimeException("Template '$template' not found.");
        }

        $output = $this->twig->render($template, $data);

        $this->timeline->stopMeasure("rendering-$template");

        return $output;
    }

    public function stream(string $template, array $data = []): StreamInterface
    {
        return $this->streamFactory->createStream(
            $this->render($template, $data)
        );
    }

    public function response(int $status, string $template, array $data = []): ResponseInterface
    {
        return $this
            ->responseFactory
            ->createResponse($status)
            ->withBody($this->stream($template, $data))
        ;
    }
}
