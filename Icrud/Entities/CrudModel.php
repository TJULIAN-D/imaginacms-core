<?php

namespace Modules\Core\Icrud\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\Traits\AuditTrait;
use Modules\Core\Icrud\Traits\hasEventsWithBindings;
use Modules\Isite\Traits\RevisionableTrait;
use Modules\Core\Icrud\Traits\SingleFlaggable;
use Modules\Core\Icrud\Traits\HasUniqueFields;
use Modules\Core\Icrud\Traits\HasCacheClearable;

class CrudModel extends Model
{
  use AuditTrait, hasEventsWithBindings, RevisionableTrait, SingleFlaggable, HasUniqueFields, HasCacheClearable;

  function getFillables()
  {
    return $this->fillable;
  }
}
