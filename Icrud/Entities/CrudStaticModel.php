<?php

namespace Modules\Core\Icrud\Entities;

class CrudStaticModel
{

  protected $records = [];

  public function lists()
  {
    return $this->records;
  }

  public function index()
  {
    //Instance response
    $response = [];
    //AMp status
    foreach ($this->records as $key => $item) {
      $itemData = ['id' => $key, 'title' => $item];
      if(is_array($item)) $itemData = array_merge($itemData, $item);
      array_push($response, $itemData);
    }
    //Repsonse
    return collect($response);
  }

  public function show($statusId)
  {
    $response = null;
    //Define the response
    if (isset($this->records[$statusId])) {
      $value = $this->records[$statusId];
      $response = ['id' => $statusId, 'title' => $value];
      if(is_array($value)) $response = array_merge($response, $value);
    }
    //Response
    return $response;
  }
}
