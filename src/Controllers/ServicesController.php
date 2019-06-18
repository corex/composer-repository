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
        $response = json_encode([]);

        if ($service == 'order') {
            $response = 0; // Indicate 'not ordered' as default.
            if (!Build::isOrdered()) {
                Build::order();
                $response = 1; // Indicate 'ordered'.
            }
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

            $response = json_encode([
                'isOrdered' => $isOrdered,
                'orderTime' => $orderTime,
                'isRunning' => $isRunning,
                'runningTime' => $runningTime
            ]);
        }

        print($response);
        exit;
    }
}