<?php

namespace Modules\Core\Icrud\Routing;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Http\Request;

class RouterGenerator
{
  private $router;

  public function __construct(Router $router)
  {
    $this->router = $router;
  }

  /**
   * Generate CRUD API routes
   *
   * @param array $params [module,prefix,controller]
   */
  public function apiCrud($params)
  {
    //Instance CRUD routes
    $crudRoutes = [
      (object)[//Route create
        'method' => 'post',
        'path' => '/',
        'actions' => [
          'as' => "api.{$params['module']}.{$params['prefix']}.create",
          'uses' => $params['controller'] . "@create",
          'middleware' => ['auth:api']
        ]
      ],
      (object)[//Route index
        'method' => 'get',
        'path' => '/',
        'actions' => [
          'as' => "api.{$params['module']}.{$params['prefix']}.getItemsBy",
          'uses' => $params['controller'] . "@index",
          'middleware' => ['auth:api']
        ]
      ],
      (object)[//Route show
        'method' => 'get',
        'path' => '/{criteria}',
        'actions' => [
          'as' => "api.{$params['module']}.{$params['prefix']}.getItem",
          'uses' => $params['controller'] . "@show",
          'middleware' => ['auth:api']
        ]
      ],
      (object)[//Route Update
        'method' => 'put',
        'path' => '/{criteria}',
        'actions' => [
          'as' => "api.{$params['module']}.{$params['prefix']}.update",
          'uses' => $params['controller'] . "@update",
          'middleware' => ['auth:api']
        ]
      ],
      (object)[//Route delete
        'method' => 'delete',
        'path' => '/{criteria}',
        'actions' => [
          'as' => "api.{$params['module']}.{$params['prefix']}.delete",
          'uses' => $params['controller'] . "@delete",
          'middleware' => ['auth:api']
        ]
      ]
    ];

    //Generate routes
    $this->router->group(['prefix' => $params['prefix']], function (Router $router) use ($crudRoutes) {
      foreach ($crudRoutes as $route) {
        $router->match($route->method, $route->path, $route->actions);
      }
    });
  }
}
