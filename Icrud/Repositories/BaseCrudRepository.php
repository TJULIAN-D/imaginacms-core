<?php

namespace Modules\Core\Icrud\Repositories;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface Core Crud Repository
 * @package Modules\Core\Repositories
 */
interface BaseCrudRepository
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
  public function getItem($criteria, $params);

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
   * @param $criteria
   * @param $params
   * @return mixed
   */
  public function deleteBy($criteria, $params);
}
