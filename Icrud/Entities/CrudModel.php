<?php

namespace Modules\Core\Icrud\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\Traits\AuditTrait;
use Modules\Core\Icrud\Traits\hasEventsWithBindings;
use Modules\Isite\Traits\RevisionableTrait;
use Modules\Core\Icrud\Traits\SingleFlaggable;
use Modules\Core\Icrud\Traits\HasUniqueFields;

class CrudModel extends Model
{
  use AuditTrait, hasEventsWithBindings, RevisionableTrait, SingleFlaggable, HasUniqueFields;

  function getFillables(){
    return $this->fillable;
  }
}
