<?php

namespace Modules\Core\Icrud\Controllers;

use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

//Default transformer
use Modules\Core\Icrud\Transformers\CrudResource;

class BaseCrudController extends BaseApiController
{
  /**
   * Return request to create model
   *
   * @param $modelData
   * @return false
   */
  public function modelCreateRequest($modelData)
  {
    return false;
  }

  /**
   * Return request to create model
   *
   * @param $modelData
   * @return false
   */
  public function modelUpdateRequest($modelData)
  {
    return false;
  }

  /**
   * Return model collection transformer
   *
   * @param $data
   * @return mixed
   */
  public function modelCollectionTransformer($data)
  {
    return CrudResource::collection($data);
  }

  /**
   * Return model transformer
   *
   * @param $data
   * @return mixed
   */
  public function modelTransformer($data)
  {
    return new CrudResource($data);
  }

  /**
   * Controller to create model
   *
   * @param Request $request
   * @return mixed
   */
  public function create(Request $request)
  {
    \DB::beginTransaction();
    try {
      //Get model data
      $modelData = $request->input('attributes') ?? [];

      //Validate Request
      if ($this->modelCreateRequest($modelData))
        $this->validateRequestApi($this->modelCreateRequest($modelData));

      //Create model
      $model = $this->modelRepository->create($modelData);

      //Response
      $response = ["data" => $this->modelTransformer($model)];
      \DB::commit(); //Commit to Data Base
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }

  /**
   * Controller To request all model data
   *
   * @return mixed
   */
  public function index(Request $request)
  {
    try {
      //Get Parameters from request
      $params = $this->getParamsRequest($request);

      //Request data to Repository
      $models = $this->modelRepository->getItemsBy($params);

      //Response
      $response = ["data" => $this->modelCollectionTransformer($models)];

      //If request pagination add meta-page
      $params->page ? $response["meta"] = ["page" => $this->pageTransformer($dataEntity)] : false;
    } catch (\Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }

  /**
   * Controller to request model by criteria
   *
   * @param $criteria
   * @return mixed
   */
  public function show($criteria, Request $request)
  {
    try {
      //Get Parameters from request
      $params = $this->getParamsRequest($request);

      //Request data to Repository
      $model = $this->modelRepository->getItem($criteria, $params);

      //Throw exception if no found item
      if (!$model) throw new Exception('Item not found', 204);

      //Response
      $response = ["data" => $this->modelTransformer($model)];

      //If request pagination add meta-page
      $params->page ? $response["meta"] = ["page" => $this->pageTransformer($dataEntity)] : false;
    } catch (\Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }

  /**
   * Controller to update model by criteria
   *
   * @param $criteria
   * @param Request $request
   * @return mixed
   */
  public function update($criteria, Request $request)
  {
    \DB::beginTransaction(); //DB Transaction
    try {
      //Get model data
      $modelData = $request->input('attributes') ?? [];
      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);

      //Validate Request
      if ($this->modelUpdateRequest($modelData))
        $this->validateRequestApi($this->modelUpdateRequest($modelData));

      //Update model
      $model = $this->modelRepository->updateBy($criteria, $modelData, $params);

      //Throw exception if no found item
      if (!$model) throw new Exception('Item not found', 204);

      //Response
      $response = ["data" => $this->modelTransformer($model)];
      \DB::commit();//Commit to DataBase
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }

  /**
   * Controller to delete model by criteria
   *
   * @param $criteria
   * @return mixed
   */
  public function delete($criteria, Request $request)
  {
    \DB::beginTransaction();
    try {
      //Get params
      $params = $this->getParamsRequest($request);

      //Delete methomodel
      $this->modelRepository->deleteBy($criteria, $params);

      //Response
      $response = ["data" => "Item deleted"];
      \DB::commit();//Commit to Data Base
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }
}
