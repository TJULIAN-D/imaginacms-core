<?php

namespace Modules\Core\Icrud\Traits;


use http\Params;

trait SingleFlaggable
{
  private $params;

  public static function bootSingleFlaggable()
  {
    //Listen event after create model
    static::createdWithBindings(function ($model) {
      //Instance the params
      $model->instanceParams();
      $model->setSingleFlag();
    });
    //Listen event after update model
    static::updatedWithBindings(function ($model) {
      //Instance the params
      $model->instanceParams();
      $model->setSingleFlag();
    });

    static::deleted(function ($model) {
      //Instance the params
      $model->instanceParams();
      $model->setWhenDeleteSingleFlag();
    });
  }

  /**
   * Instance the params
   *
   * @return void
   */
  private function instanceParams()
  {
    $this->params = (object)[
      'singleFlagName' => $this->singleFlagName ?? 'default',
      'singleFlaggableCombination' => $this->singleFlaggableCombination ?? [],
      'singleFlagTrueValue' => $this->singleFlagTrueValue ?? 1,
      'singleFlagFalseValue' => $this->singleFlagFalseValue ?? 0,
      'isEnableSingleFlaggable' => $this->isEnableSingleFlaggable ?? true
    ];
  }

  /**
   * Validates if the flag can be managed
   *
   * @param $data
   * @return bool
   * @throws \Exception
   */
  public function canManageFlag($data)
  {
    //Check if flag name exists in data
    if(!isset($data[$this->params->singleFlagName])) return false;
    //Check if the flag name is equal to true value
    if($data[$this->params->singleFlagName] != $this->params->singleFlagTrueValue) return false;
    //Validate that all singleFlaggableCombination are in data
    $missingColumns = array_diff($this->params->singleFlaggableCombination, array_keys($data));
    if (!empty($missingColumns)){
      $errorMsg = trans("core::common.columnsNotFound", ['columns' => implode(', ', $missingColumns)]);
      throw new \Exception($errorMsg, 500);
    }

    //Default response
    return true;
  }

  /**
   * Turn to false value all records less the current
   *
   * @param $model
   * @return void
   */
  public function setSingleFlag()
  {
    //Returns if not enabled
    if(!$this->params->isEnableSingleFlaggable) return;
    $data = $this->toArray();

    //Validates if the flag can be managed
    if ($this->canManageFlag($data)) {
      $query = static::where('id', '!=', $data["id"]);

      // Including contitions for each combination
      foreach ($this->params->singleFlaggableCombination as $columnName){
        $query->where($columnName, $data[$columnName]);
      }

      //Set false value
      $query->update([$this->params->singleFlagName => $this->params->singleFlagFalseValue]);
    }
  }

  /**
   * Turn to true value first records less the current
   *
   * @return void
   * @throws \Exception
   */
  private function setWhenDeleteSingleFlag()
  {
    //Returns if not enabled
    if(!$this->params->isEnableSingleFlaggable) return;
    //Get the this like array
    $data = $this->toArray();

    //Validates if the flag can be managed
    if ($this->canManageFlag($data)) {
      $query = static::where('id', '!=', $data['id']);

      // Including contitions for each combination
      foreach ($this->params->singleFlaggableCombination as $columnName){
        $query->where($columnName, $data[$columnName]);
      }

      //Update the first record it finds
      $query->take(1)->update([$this->params->singleFlagName => $this->params->singleFlagTrueValue]);
      //remove default value to deleted record
      $this->update([$this->params->singleFlagName => $this->params->singleFlagFalseValue]);
    }
  }

}
