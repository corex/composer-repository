<?php

namespace CoRex\Composer\Repository\Base;

use CoRex\Composer\Repository\Interfaces\ControllerInterface;
use CoRex\Site\View;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController implements ControllerInterface
{
    /** @var Request */
    private $request;

    /**
     * BaseController.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get.
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    protected function get($name, $default = null)
    {
        return $this->request->get($name, $default);
    }

    /**
     * Get int.
     *
     * @param string $name
     * @param int $default
     * @return int
     */
    protected function getInt($name, $default = 0)
    {
        return intval($this->get($name, $default));
    }

    /**
     * View.
     *
     * @param string $name
     * @param array $variables
     * @return \CoRex\Template\Helpers\Engine
     * @throws \Exception
     */
    protected function view($name, array $variables = [])
    {
        return View::load($name)->variables($variables);
    }
}