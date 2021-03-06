<?php


namespace Tiny\View;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager;
use Tiny\View\Exception;

class View
{

    const EVENT_CALL_VIEW_HELPER = 'view.call.helper.';

    /**
     * @var array
     */
    private array $variables;

    /**
     * @var string|null
     */
    private ?string $templatePath;

    /**
     * @var string|null
     */
    private ?string $layoutPath;

    /**
     * @var string
     */
    private ?string $content;

    /**
     * @var EventManager\EventManager|null
     */
    private ?EventManager\EventManager $eventManager = null;

    /**
     * View constructor.
     *
     * @param  array  $variables
     * @param  string|null  $templatePath
     * @param  string|null  $layoutPath
     */
    public function __construct(
        array $variables = [],
        string $templatePath = null,
        string $layoutPath = null
    ) {
        $this->variables = $variables;
        $this->templatePath = $templatePath;
        $this->layoutPath = $layoutPath;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return string|null
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param  string  $path
     *
     * @return $this
     */
    public function setTemplatePath(string $path): self
    {
        $this->templatePath = $path;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLayoutPath()
    {
        return $this->layoutPath;
    }

    /**
     * @param  string  $path
     *
     * @return $this
     */
    public function setLayoutPath(string $path): self
    {
        $this->layoutPath = $path;

        return $this;
    }

    /**
     * @param  EventManager\EventManager  $eventManager
     */
    public function setEventManager(EventManager\EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @return EventManager\EventManager|null
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * @param  string  $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if (isset($this->variables[$name])) {
            return $this->variables[$name];
        }
    }

    /**
     * @param  string  $name
     * @param  mixed   $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $eventName = self::EVENT_CALL_VIEW_HELPER . $name;

        // we use the event manager as a proxy for helpers calling
        if (!$this->eventManager
            || !$this->eventManager->isEventHasSubscribers(
                $eventName
            )
        ) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'The method "%s()" is unsupported.',
                    $name
                )
            );
        }

        $callEvent = new EventManager\Event(null, [
            'arguments' => $arguments
        ]);

        // we only need to get data from a first listener (all other should be skipped)
        $callEvent->setStopped(true);

        $this->eventManager->trigger(
            $eventName,
            $callEvent
        );

        return $callEvent->getData();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (!$this->templatePath) {
            throw new Exception\InvalidArgumentException(
                'Template file path is empty.'
            );
        }

        $this->content = $this->render($this->templatePath);

        if ($this->layoutPath) {
            $this->content = $this->render($this->layoutPath);
        }

        return $this->content;
    }

    /**
     * @param  string  $filePath
     *
     * @return string
     */
    private function render(string $filePath): string
    {
        ob_start();
        require $filePath;

        return ob_get_clean() ?? '';
    }

}
