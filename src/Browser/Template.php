<?php

namespace CoRex\Composer\Repository\Browser;

use CoRex\Composer\Repository\Path;
use CoRex\Filesystem\File;

class Template
{
    private $path;
    private $templateName;
    private $template;
    private $data;

    /**
     * Template constructor.
     *
     * @param string $templateName
     */
    public function __construct($templateName)
    {
        $this->path = Path::packageCurrent(['templates']);
        $this->templateName = rtrim($templateName, '.html');
        $filename = $this->path . '/' . $this->templateName . '.php';
        if (!File::exist($filename)) {
            die('Internal error. Template not found.');
        }

        // Require template.
        ob_start();
        require($filename);
        $this->template = ob_get_clean();

        $this->data = [];
    }

    /**
     * Load.
     *
     * @param string $template
     * @return static
     */
    public static function load($template)
    {
        return new static($template);
    }

    /**
     * Set.
     *
     * @param string $section
     * @param string $content
     * @param boolean $overwrite
     */
    public function set($section, $content, $overwrite = false)
    {
        if (!array_key_exists($section, $this->data) || $overwrite) {
            $this->data[$section] = '';
        }
        $this->data[$section] .= (string)$content;
    }

    /**
     * Render.
     *
     * @return string
     */
    public function render()
    {
        $template = $this->template;

        // Render sections.
        if (count($this->data) > 0) {
            foreach ($this->data as $section => $content) {
                $template = str_replace('{' . $section . '}', $content, $template);
            }
        }

        return (string)$template;
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}