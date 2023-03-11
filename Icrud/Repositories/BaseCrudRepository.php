<?php

namespace Modules\Core\Icrud\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Repositories\BaseRepository;

/**
 * Interface Core Crud Repository
 * @package Modules\Core\Repositories
 */
interface BaseCrudRepository extends BaseRepository
{
  /**
   * @param $params
   * @return mixed
   */
  public function getItemsBy($params);

  /**
   * @param $criteria
   * @param $params
   * @return mixed
   */
  public function getItem($criteria, $params = false);

  /**
   * @param $data
   * @return mixed
   */
  public function create($data);

  /**
   * @param $criteria
   * @param $data
   * @param $params
   * @return mixed
   */
  public function updateBy($criteria, $data, $params);

  /**
  /**
   * Bulk order
   *
   * @param $data
   * @param $params
   * @return mixed
   */
  public function bulkOrder($data, $params);

  /**
   * @param $criteria
   * @param $params
   * @return mixed
   */
  public function deleteBy($criteria, $params);

  /**
   * @param $criteria
   * @param $params
   * @return mixed
   */
  public function restoreBy($criteria, $params);

  /**
   * @param $query
   * @param $filter
   * @return mixed
   */
  public function filterQuery($query, $filter);

  /**
   * @param $model
   * @param $data
   * @return mixed
   */
  public function syncModelRelations($model, $data);
}
