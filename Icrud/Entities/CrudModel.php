<?php

namespace Modules\Core\Icrud\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\Traits\AuditTrait;
use Modules\Core\Icrud\Traits\hasEventsWithBindings;
use Modules\Isite\Traits\RevisionableTrait;
use Modules\Core\Icrud\Traits\SingleFlaggable;
use Modules\Core\Icrud\Traits\HasUniqueFields;
use Modules\Core\Icrud\Traits\HasCacheClearable;
use Modules\Core\Icrud\Repositories\Eloquent\CustomBuilder;
use Modules\Core\Icrud\Traits\HasOptionalTraits;

class CrudModel extends Model
{
  use AuditTrait, hasEventsWithBindings, RevisionableTrait, SingleFlaggable, HasUniqueFields,
    HasCacheClearable, HasOptionalTraits;

  function getFillables()
  {
    return $this->fillable;
  }

  /**
   * Use the custom query builder.
   *
   */
  public function newEloquentBuilder($query)
  {
    return new CustomBuilder($query);
  }

  /**
   * Filter valid relations for eager loading.
   *
   */
  public function filterValidRelations($relations)
  {
    $relations = is_array($relations) ? $relations : func_get_args();

    return array_filter($relations, function ($relation) use ($relations) {
      return !is_string($relation) || method_exists($this, $relation) ||
        in_array($relation, static::$optionalTraitsRelations); //This depent of HasOptionalTraits trait
    });
  }
}
