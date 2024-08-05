<?php

namespace Modules\Core\Icrud\Traits;

trait HasCacheClearable
{
  public static function bootHasCacheClearable()
  {
    static::created(function ($model) {
      $model->initCacheClearable();
    });
    static::saved(function ($model) {
      if ($model->wasRecentlyCreated) return; //Validate saved only for updated model
      $model->initCacheClearable();
    });

    static::deleting(function ($model) {
      $model->initCacheClearable();
    });
  }

  /**
   * Return the needed data by cache provider from model
   *
   * @param $type
   * @return mixed|null
   */
  public function initCacheClearableData($type)
  {
    $response = null;
    if (method_exists($this, 'getCacheClearableData')) {
      $cacheClearableData = $this->getCacheClearableData();
      $response = $cacheClearableData[$type] ?? null;
    }
    return $response;
  }

  /**
   * Call the cache providers to clear model cache
   *
   * @return void
   */
  public function initCacheClearable()
  {
    $this->clearCacheCDN();
  }

  /**
   *Clear the cache of CDN provider
   *
   * @return void
   */
  public function clearCacheCDN()
  {
    //Get the keys
    $cdnProvider = env("CDN_PROVIDER", null);
    $cdnUrl = env("CDN_URL", null);
    $cdnAccessKey = env("CDN_ACCESS_KEY", null);

    //Get the model URLs to purge
    $urlsToPurge = $this->initCacheClearableData('cdn');

    if ($cdnUrl && $cdnProvider && $cdnAccessKey && is_array($urlsToPurge)) {
      switch ($cdnProvider) {
        case "bunny":
          $client = new \GuzzleHttp\Client();

          foreach ($urlsToPurge as $url) {
            try {
              $requestUrl = "$cdnUrl/purge?async=true&url=$url";
              $response = $client->request('GET', $requestUrl, [
                'headers' => [
                  'AccessKey' => $cdnAccessKey,
                  'accept' => 'application/json',
                ],
              ]);
              \Log::info("Trait|HasCacheClearable|CDN:: cache cleared for URL: $requestUrl");
            } catch (\Exception $e) {
              \Log::info("Trait|HasCacheClearable|CDN:: error clearing for url: $requestUrl");
            }
          }
          break;
      }
    }
  }
}
