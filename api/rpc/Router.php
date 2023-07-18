<?php

namespace rpc;

class Router
{
    public $basePath = '..\\src\\';
    public $routes   = [

        'utils:test'        => 'Test::index',
        'notebook:index'    => 'Notebook::index',
        'notebooks:list'    => 'Auth\\User::index',


        'utils' => [
            'taskScheduler:tick'           => 'Tasks\\TaskScheduler::tick',
        ],
        'categories.get'    => 'Controllers\\JsonRpcTest::getResources',
        'categories.update' => 'Controllers\\JsonRpcTest::updateResources',
    ];

    public function route($method)
    {
        $routeTable = $this->flattenTree($this->routes);
        return $routeTable[$method] ?? false;
    }

    private function flattenTree($tree, $parent = '')
    {
        $result = array();

        foreach ($tree as $key => $value) {
            $node_id = ($parent ? $parent . (str_starts_with($key, ':') ? '' : '.') : '') . $key;

            if (is_array($value)) {
                $result = array_merge($result, self::flattenTree($value, $node_id));
            } else {
                // $result[] = [$node_id => $value];
                $result[$node_id] = $value;
            }
        }

        return $result;
    }
}