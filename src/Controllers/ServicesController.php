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
            $isRunning = Build::isRunning();
            $count = Build::getOrderCount();
            if (Build::isRunning()) {
                // Add running job count.
                $count++;
            }
            $runningTime = intval(Build::getRunningTime());
            $runningTime = $runningTime > 0 ? date('Y-m-d H:i:s', $runningTime) : '';
            $json = json_encode([
                'count' => $count,
                'isRunning' => $isRunning,
                'runningTime' => $runningTime
            ]);
        }

        print($json);
        exit;
    }
}