<?php

declare(strict_types=1);

/*
 * This file is a part of Sculpin.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sculpin\Bundle\TextileBundle;

use Netcarver\Textile\Parser;
use Sculpin\Core\Converter\ConverterContextInterface;
use Sculpin\Core\Converter\ConverterInterface;
use Sculpin\Core\Event\SourceSetEvent;
use Sculpin\Core\Sculpin;
use Sculpin\Core\Source\SourceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Textile Converter.
 *
 * @author Beau Simensen <beau@dflydev.com>
 */
class TextileConverter implements ConverterInterface, EventSubscriberInterface
{
    /**
     * Textile parser
     *
     * @var Parser
     */
    protected $parser;

    /**
     * Extensions
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * Constructor.
     *
     * @param Parser $parser     Parser
     * @param array  $extensions Extensions
     */
    public function __construct(Parser $parser, array $extensions = [])
    {
        $this->parser = $parser;
        $this->extensions = $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(ConverterContextInterface $converterContext): void
    {
        $converterContext->setContent($this->parser->textileThis($converterContext->content()));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Sculpin::EVENT_BEFORE_RUN => 'beforeRun',
        ];
    }

    /**
     * Before run
     *
     * @param SourceSetEvent $sourceSetEvent Source Set Event
     */
    public function beforeRun(SourceSetEvent $sourceSetEvent): void
    {
        /** @var SourceInterface $source */
        foreach ($sourceSetEvent->updatedSources() as $source) {
            foreach ($this->extensions as $extension) {
                if (fnmatch("*.{$extension}", $source->filename())) {
                    $source->data()->append('converters', SculpinTextileBundle::CONVERTER_NAME);
                    break;
                }
            }
        }
    }
}
