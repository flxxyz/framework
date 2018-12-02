<?php

namespace Col;


trait Helpers
{
    /**
     * 获取不带命名空间的类名
     * @param string $class_str
     * @return string
     */
    public function class_basename($class_str = '')
    {
        return basename(str_replace('\\', '/', $class_str));
    }
}