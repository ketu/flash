<?php
/**
 * User: ketu.lai <ketu.lai@gmail.com>
 * Date: 2017/5/22 9:37
 */

namespace Veda\Flash;
use Pimple\Container as BaseContainer;

class Container extends BaseContainer
{
    public function get($id)
    {
        return $this->offsetGet($id);
    }
}