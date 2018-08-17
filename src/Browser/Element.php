<?php

namespace CoRex\Composer\Repository\Browser;

use Highlight\Highlighter;

class Element
{
    /**
     * Title.
     *
     * @param string $title
     * @return string
     */
    public static function title($title)
    {
        return '<h5>' . $title . '</h5>';
    }

    /**
     * Error.
     *
     * @param string $message
     * @return string
     */
    public static function error($message)
    {
        return '<div class="alert alert-danger" role="alert">' . $message . '</div>';
    }

    /**
     * Properties.
     *
     * @param array $properties
     * @return string
     */
    public static function properties(array $properties)
    {
        $result = [];
        $result[] = '<table cellspacing="0" cellpadding="0" style="width: auto; border: none;">';
        $result[] = '<tbody>';
        foreach ($properties as $property => $value) {
            $result[] = '<tr>';
            $result[] = '<th>' . $property . '</th><td>' . $value . '</td>';
            $result[] = '</tr>';
        }
        $result[] = '</tbody>';
        $result[] = '</table>';
        return implode('', $result);
    }

    /**
     * Properties table.
     *
     * @param array $properties Properties (label -> text).
     * @return string
     */
    public static function propertiesTable(array $properties)
    {
        $result = [];
        $result[] = '<table class="table table-bordered" style="width: auto;">';
        $result[] = '<tbody>';
        foreach ($properties as $property => $value) {
            $result[] = '<tr>';
            $result[] = '<th>' . $property . '</th><td>' . $value . '</td>';
            $result[] = '</tr>';
        }
        $result[] = '</tbody>';
        $result[] = '</table>';
        return implode('', $result);
    }

    /**
     * Button.
     *
     * @param string $title
     * @param boolean $isActive
     * @param array $params
     * @return string
     */
    public static function button($title, $isActive = false, array $params = [])
    {
        $cssClasses = [
            'btn',
            'btn-outline-dark'
        ];
        if ($isActive) {
            $cssClasses[] = 'active';
        }
        $result = [];
        $result[] = '<button';
        $result[] = 'class="' . implode(' ', $cssClasses) . '"';
        if (count($params) > 0) {
            $url = Url::build($params);
            $result[] = 'onClick="location.href=\'' . $url . '\'; return false;"';
        }
        $result[] = '>';
        $result[] = $title;
        $result[] = '</button>';
        return implode(' ', $result);
    }

    /**
     * Dropdown.
     *
     * @param array $items
     * @param string $default
     * @param array $params
     * @param boolean $titleToValue
     * @param integer $limit
     * @return string
     */
    public static function dropdown(array $items, $default, array $params = [], $titleToValue = false, $limit = 0)
    {
        $attributes = [
            'class="btn btn-secondary btn-outline-dark dropdown-toggle"',
            'type="button"',
            'id="dropdownMenuButton"',
            'data-toggle="dropdown"',
            'aria-haspopup="true"',
            'aria-expanded="false"'
        ];
        $result = [];
        $result[] = '<div class="dropdown float-left">';
        $result[] = '<button ' . implode(' ', $attributes) . '>';
        $result[] = $default;
        $result[] = '</button>';
        $result[] = '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
        $counter = 0;
        foreach ($items as $value => $title) {
            if ($titleToValue) {
                $value = $title;
            }
            $params['version'] = $value;
            $url = Url::build($params);
            $result[] = '<a class="dropdown-item" href="' . $url . '">' . $title . '</a>';
            $counter++;
            if ($limit > 0 && $counter == $limit) {
                break;
            }
        }
        $result[] = '  </div>';
        $result[] = '</div>';
        return implode('', $result);
    }

    /**
     * Code.
     *
     * @param string $content
     * @return string
     */
    public static function code($content)
    {
        try {
            $highlighter = new Highlighter();
            $highlighter->setAutodetectLanguages(['markdown', 'php', 'javascript', 'json']);
            $highlighted = $highlighter->highlightAuto($content);
            $result = "<pre class=\"hljs {$highlighted->language}\">\n";
            $result .= $highlighted->value . "\n";
            $result .= "</pre>\n";
            return $result;
        } catch (\Exception $e) {
            return Element::error($e->getMessage());
        }
    }
}