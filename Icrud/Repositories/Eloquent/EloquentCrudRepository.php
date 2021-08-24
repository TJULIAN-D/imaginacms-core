<?php

namespace Modules\Core\Icrud\Repositories\Eloquent;

/**
 * Class EloquentCrudRepository
 *
 * @package Modules\Core\Repositories\Eloquent
 */
abstract class EloquentCrudRepository
{
  /**
   * Method to include relations to query
   * @param $query
   * @param $relations
   */
  public function includeToQuery($query, $relations)
  {
    //request all categories instances in the "relations" attribute in the entity model
    if (in_array('*', $relations)) $relations = $this->model->getRelations() ?? [];
    //Instance relations in query
    $query->with($relations);
    //Response
    return $query;
  }

  /**
   * Method to filter query
   * @param $query
   * @param $filter
   */
  public function filterQuery($query, $filter)
  {
    return $query;
  }

  /**
   * Method to order Query
   *
   * @param $query
   * @param $filter
   */
  public function orderQuery($query, $order)
  {
    $orderField = $order->field ?? 'created_at';//Default field
    $orderWay = $order->way ?? 'desc';//Default way

    //Set order to query
    if (in_array($orderField, ($this->model->translatedAttributes ?? []))) {
      $query->orderByTranslation($orderField, $orderWay);
    } else $query->orderBy($orderField, $orderWay);

    //Return query with filters
    return $query;
  }

  /**
   * Method to request all data from model
   *
   * @param false $params
   * @return mixed
   */
  public function getItemsBy($params)
  {
    //Instance Query
    $query = $this->model->query();

    //Include relationships
    if (isset($params->include)) $query = $this->includeToQuery($query, $params->include);

    //Filter Query
    if (isset($params->filter)) {
      //Short data filter
      $filter = $params->filter;

      //Set fiter order to params.order: TODO: to keep and don't break old version api
      if (isset($filter->order) && isset($params->order) && !$params->order) $params->order = $filter->order;

      //Filter by date
      if (isset($filter->date)) {
        //Filter from a date
        if (isset($filter->date->from)) $query->whereDate(($filter->date->field ?? 'created_at'), '>=', $filter->date->from);
        //Filter to a date
        if (isset($filter->date->to)) $query->whereDate(($filter->date->field ?? 'created_at'), '<=', $filter->date->to);
      }

      //Filter by id
      if (isset($filter->byId)) {
        if (is_array($filter->byId)) $query->whereIn("{$this->model->getTable()}.id", $filter->byId);
        else $query->where("{$this->model->getTable()}.id", $filter->byId);
      }

      //Add model filters
      $query = $this->filterQuery($query, $filter);
    }

    //Order Query
    $query = $this->orderQuery($query, $params->order ?? true);

    //Response as query
    if (isset($params->returnAsQuery) && $params->returnAsQuery) $response = $query;
    //Response paginate
    else if (isset($params->page) && $params->page) $response = $query->paginate($params->take);
    //Response complete
    else {
      if (isset($params->take) && $params->take) $query->take($params->take);//Take
      $response = $query->get();
    }

    //Response
    return $response;
  }

  /**
   * Method to get model by criteria
   *
   * @param $criteria
   * @param $params
   * @return mixed
   */
  public function getItem($criteria, $params)
  {
    //Instance Query
    $query = $this->model->query();

    //Include relationships
    if (isset($params->include)) $query = $this->includeToQuery($query, $params->include);

    //Check field name to criteria
    if (isset($params->filter->field)) $field = $params->filter->field;

    //Request
    $response = $query->where($field ?? 'id', $criteria)->first();

    //Response
    return $response;
  }

  /**
   * Method to update model by criteria
   *
   * @param $criteria
   * @param $data
   * @param $params
   * @return mixed
   */
  public function updateBy($criteria, $data, $params)
  {
    //Instance Query
    $query = $this->model->query();

    //Check field name to criteria
    if (isset($params->filter->field)) $field = $params->filter->field;

    //get model
    $model = $query->where($field ?? 'id', $criteria)->first();

    //Update Model
    if ($model) $model->update((array)$data);

    //Response
    return $model;
  }

  /**
   * Method to delete model by criteria
   *
   * @param $criteria
   * @param $params
   * @return mixed
   */
  public function deleteBy($criteria, $params)
  {
    //Instance Query
    $query = $this->model->query();

    //Check field name to criteria
    if (isset($params->filter->field)) $field = $params->filter->field;

    //get model
    $model = $query->where($field ?? 'id', $criteria)->first();

    //Delete Model
    if ($model) $model->delete();

    //Response
    return $model;
  }
}
