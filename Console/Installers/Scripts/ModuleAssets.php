<?php

namespace Modules\Core\Console\Installers\Scripts;

use Illuminate\Console\Command;
use Modules\Core\Console\Installers\SetupScript;

class ModuleAssets implements SetupScript
{
  /**
   * Fire the install script
   *
   * @return mixed
   */
  public function fire(Command $command)
  {
    if ($command->option('verbose')) {
      $command->blockMessage('Module assets', 'Publishing module assets ...', 'comment');
    }

    $modules = config('asgard.core.config.CoreModules');
    foreach ($modules as $module) {
      if ($command->option('verbose')) {
        $command->call('module:publish', ['module' => $module]);

        continue;
      }
      $command->callSilent('module:publish', ['module' => $module]);
    }
  }
}
