<?php

namespace Modules\Core\Icrud\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Iblog\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Media\Transformers\NewTransformers\MediaTransformer;
use  Modules\Isite\Transformers\RevisionTransformer;

class CrudResource extends JsonResource
{
  /**
   * Attribute to exclude relations from transformed data
   * @var array
   */
  protected $excludeRelations = [];

  /**
   * Method to merge values to response
   *
   * @return array
   */
  public function modelAttributes($request)
  {
    return [];
  }

  /**
   * Transform the resource into an array.
   * @param $request
   * @return array
   */
  public function toArray($request)
  {
    $response = []; //Default Response
    $translatableAttributes = $this->translatedAttributes ?? [];//Get translatable attributes
    $attributes = method_exists($this->resource, "getFillables") ?
      $this->resource->getFillables() : [];//Get all fillable attributes, just for model that extends CrudModel

    $attributes = array_merge($attributes, array_keys($this->getAttributes())); //get Attributes add extras non fillables attributes

    $filter = json_decode($request->filter);//Get request Filters
    $languages = \LaravelLocalization::getSupportedLocales();// Get site languages
    $excludeRelations = array_merge(['translations'], $this->excludeRelations);//No self-load this relations

    //Add attributes
    foreach ($attributes as $fieldName) {
      $response[snakeToCamel($fieldName)] = $this->when(
        (isset($this[$fieldName]) || is_null($this[$fieldName])),
        $this[$fieldName]
      );
    }

    //Add translatable attributes
    foreach ($translatableAttributes as $fieldName) {
      $response[snakeToCamel($fieldName)] = $this->when(
        (isset($this[$fieldName]) || is_null($this[$fieldName])),
        $this[$fieldName]
      );
    }

    // Add translations
    if (isset($filter->allTranslations) && $filter->allTranslations) {
      foreach ($languages as $lang => $value) {
        foreach ($translatableAttributes as $fieldName) {
          $response[$lang][snakeToCamel($fieldName)] = $this->hasTranslation($lang) ? $this->translate($lang)[$fieldName] : '';
        }
      }
    }

    //Transform relations.
    foreach ($this->getRelations() as $relationName => $relation) {
      if (!in_array($relationName, $excludeRelations)) {
        //Transform relation
        $response[$relationName] = $this->transformData($relation);
        //Format fields relation
        if (($relationName == 'fields') && method_exists($this->resource, 'formatFillableToModel')) {
          //Get fillable data
          $fillableData = json_decode(json_encode($response[$relationName]));
          //Merge fillable to main level of response
          $response = array_merge_recursive($response, $this->formatFillableToModel($fillableData));
        }
        //Format files relations
        if (($relationName == 'files') && method_exists($this->resource, 'mediaFiles')) {
          //Add files relations
          $response["files"] = MediaTransformer::collection($this->files);
          //Add media Files
          if (method_exists($this->resource, 'mediaFiles')) $response['mediaFiles'] = $this->mediaFiles();
          //Add media Fields to model
          if (method_exists($this->resource, 'getMediaFields')) {
            $response = array_merge($response, $this->getMediaFields());
          }
        }
        //Add Revision relation
        if (($relationName == 'revisions') && method_exists($this->resource, 'revisions')) {
          $response['revisions'] = RevisionTransformer::collection($this->whenLoaded('revisions'));
        }
        //Add Tags Relation | Return only the names of tags as array
        if (($relationName == 'tags') && method_exists($this->resource, 'getNameTags')) {
          $response['tags'] = $this->getNameTags();
        }
      }
    }


    //Add magic attributes
    foreach (get_class_methods($this->resource) as $methodName) {
      // if the method starts with get and ends with Attribute
      // excepting base method "getAttribute"
      if (Str::startsWith($methodName, "get") && Str::endsWith($methodName, "Attribute")
        && $methodName != "getAttribute") {

        //removing "get" and "Attribute" to get the real attribute name
        $attributeName = Str::replace(["get", "Attribute"], ["", ""], $methodName);

        //avoid the magic methods of the fillables
        if (!in_array(Str::snake($attributeName), $attributes)) {
          $response[Str::camel($attributeName)] = $this->{$methodName}();
        }
      }
    }


    //Add model extra attributes
    $response = array_merge($response, $this->modelAttributes($request));
    //Sort response
    ksort($response);

    //Response
    return $response;
  }

  /**
   * Transform data from a collection or model
   * @param $data
   */
  public static function transformData($data)
  {
    //Transform from a collections
    if (($data instanceof Collection) || ($data instanceof LengthAwarePaginator)) {
      return (isset($data->first()->transformer) && $data->first()->transformer) ?
        $data->first()->transformer::collection($data) : CrudResource::collection($data);
    } //Transform from model
    else {
      return (isset($data->transformer) && $data->transformer) ?
        new $data->transformer($data) : new CrudResource($data);
    }
  }
}
