<?php

namespace CoRex\Composer\Repository\Browser;

use CoRex\Helpers\Arr;
use CoRex\Support\System\Session;

class Breadcrumbs
{
    const SESSION_NAMESPACE = 'composer-repository';
    const HOME_TITLE = 'Packages';

    /**
     * Clear.
     */
    public static function clear()
    {
        self::setItems([]);
    }

    /**
     * Add.
     *
     * @param string $breadcrumb
     * @param array $params
     */
    public static function add($breadcrumb, array $params = [])
    {
        $items = self::getItems();

        // Make sure it is not added twice.
        $lastItem = Arr::last($items);
        if ($lastItem !== null && $breadcrumb == $lastItem['title']) {
            return;
        }

        $items[] = [
            'title' => $breadcrumb,
            'params' => $params
        ];
        self::setItems($items);
    }

    /**
     * Remove last.
     */
    public static function removeLast()
    {
        $items = self::getItems();
        if (count($items) > 0) {
            $items = Arr::removeLast($items);
            self::setItems($items);
        }
    }

    /**
     * Remove from.
     *
     * @param string $breadcrumb
     */
    public static function removeFrom($breadcrumb)
    {
        $items = self::getItems();
        $indexFound = -1;
        foreach ($items as $index => $item) {
            if ($breadcrumb == $item['title']) {
                $indexFound = $index;
                break;
            }
        }
        if ($indexFound > -1) {
            $items = self::getItems();
            $items = array_slice($items, 0, $indexFound);
            self::setItems($items);
        }
    }

    /**
     * Render.
     *
     * @return string
     */
    public static function render()
    {
        $items = self::getItems();
        $result = [];
        $result[] = '<nav aria-label="breadcrumb">';
        $result[] = '<ol class="breadcrumb">';
        $result[] = self::li(self::HOME_TITLE, [], count($items) == 0);
        $index = 0;
        foreach ($items as $item) {
            $title = $item['title'];
            $params = $item['params'];
            $result[] = self::li($title, $params, $index == count($items) - 1);
            $index++;
        }
        $result[] = '</ol>';
        $result[] = '</nav>';
        return implode('', $result);
    }

    /**
     * Display.
     */
    public static function display()
    {
        print(self::render());
    }

    /**
     * <li>.
     *
     * @param string $title
     * @param array $params
     * @param boolean $isLast
     * @return string
     */
    private static function li($title, array $params, $isLast)
    {
        if (!$isLast) {
            return '<li class="breadcrumb-item">' . Url::link($title, $params) . '</li>';
        } else {
            return '<li class="breadcrumb-item active" aria-current="page">' . $title . '</li>';
        }
    }

    /**
     * Get items.
     *
     * @return array
     */
    public static function getItems()
    {
        return Session::get('breadcrumbs', [], self::SESSION_NAMESPACE);
    }

    /**
     * Set items.
     *
     * @param array $items
     */
    public static function setItems(array $items)
    {
        Session::set('breadcrumbs', $items, self::SESSION_NAMESPACE);
    }
}