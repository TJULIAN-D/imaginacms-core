<?php

namespace Modules\Core\Console\Installers\Scripts;

use Illuminate\Console\Command;
use Modules\Core\Console\Installers\SetupScript;

class PostInstallCommands implements SetupScript
{
  /**
   * @var array
   */
  protected $postCommands = [
    'key:generate' => [],
    'migrate' => [],
    'passport:install' => [],
    'module:publish-config' => [
      '-f',
    ],
    'module:publish' => [],
  ];

  /**
   * Fire the install script
   *
   * @return mixed
   * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
   */
  public function fire(Command $command)
  {
    if ($command->option('verbose')) {
      $command->blockMessage('Post Install Commands', 'Executing post install commands ...', 'comment');
    }

    foreach ($this->postCommands as $postCommand => $options) {
      if ($command->option('verbose')) {
        $command->call($postCommand, $options);

        continue;
      }
      $command->callSilent($postCommand, $options);
    }
    $routePublic = public_path();
    $routeIcms = str_replace('/public', '/', $routePublic);
    $route = $routeIcms . 'config/modules.php';
    $modulesActivator = (new \Illuminate\Filesystem\Filesystem)->get($route);
    $modulesActivator = str_replace('FileActivator::class', 'ModuleActivator::class', $modulesActivator);
    (new \Illuminate\Filesystem\Filesystem)->put($route, $modulesActivator);
  }
}
