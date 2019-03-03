<?php

namespace CoRex\Composer\Repository\Helpers;

use CoRex\Composer\Repository\Config;
use CoRex\Filesystem\Directory;
use CoRex\Filesystem\File;
use CoRex\Helpers\Arr;
use CoRex\Helpers\Str;

class Build
{
    /**
     * Place a build order.
     */
    public static function order()
    {
        File::put(self::filename('build-order-' . time()), time());
    }

    /**
     * Get order count.
     *
     * @return integer
     */
    public static function getOrderCount()
    {
        return count(self::getOrders());
    }

    /**
     * Get orders.
     *
     * @return integer[] Order timestamps.
     */
    public static function getOrders()
    {
        $path = Config::load()->getPath();
        $entries = Directory::entries($path, 'build-order-*', Directory::TYPE_FILE);
        $orders = [];
        foreach ($entries as $entry) {
            $orders[] = Str::last($entry['name'], '-');
        }
        sort($orders);
        return $orders;
    }

    /**
     * Get order.
     *
     * @return integer|null
     */
    public static function getOrder()
    {
        $orders = self::getOrders();
        if (count($orders) > 0) {
            $order = Arr::first($orders);
            self::removeOrder($order);
            return $order;
        }
        return null;
    }

    /**
     * Remove order.
     *
     * @param integer $order
     */
    public static function removeOrder($order)
    {
        File::delete(self::filename('build-order-' . $order));
    }

    /**
     * Remove all orders.
     */
    public static function removeAllOrders()
    {
        $orders = self::getOrders();
        if (count($orders) > 0) {
            foreach ($orders as $order) {
                self::removeOrder($order);
            }
        }
    }

    /**
     * Mark running.
     */
    public static function markRunning()
    {
        File::put(self::filename('build-running'), time());
    }

    /**
     * Mark running done.
     */
    public static function markRunningDone()
    {
        File::delete(self::filename('build-running'));
    }

    /**
     * Check if running.
     *
     * @return boolean
     */
    public static function isRunning()
    {
        return self::getRunningTime() !== '';
    }

    /**
     * Get building time.
     *
     * @return string|null
     */
    public static function getRunningTime()
    {
        return File::get(self::filename('build-running'));
    }

    /**
     * Build filename.
     *
     * @param string $additional
     * @return string
     */
    private static function filename($additional)
    {
        return Config::load()->getPath([$additional]);
    }
}