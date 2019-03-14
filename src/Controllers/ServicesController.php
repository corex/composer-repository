<?php

namespace CoRex\Composer\Repository\Controllers;

use CoRex\Composer\Repository\Base\BaseController;
use CoRex\Composer\Repository\Helpers\Build;

class ServicesController extends BaseController
{
    /**
     * Render.
     *
     * @throws \Exception
     */
    public function render()
    {
        $service = $this->get('service');
        $json = json_encode([]);

        if ($service == 'order') {
            Build::order();
        }

        if ($service == 'getOrderStatus') {
            // Get order information.
            $isOrdered = Build::isOrdered();
            $orderTime = intval(Build::getOrderTime());
            $orderTime = $orderTime > 0 ? date('Y-m-d H:i:s', $orderTime) : '';

            // Get running information.
            $isRunning = Build::isRunning();
            $runningTime = intval(Build::getRunningTime());
            $runningTime = $runningTime > 0 ? date('Y-m-d H:i:s', $runningTime) : '';

            $json = json_encode([
                'isOrdered' => $isOrdered,
                'orderTime' => $orderTime,
                'isRunning' => $isRunning,
                'runningTime' => $runningTime
            ]);
        }

        print($json);
        exit;
    }
}