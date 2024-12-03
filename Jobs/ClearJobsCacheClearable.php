<?php

namespace Modules\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Modules\Core\Jobs\ClearAllResponseCache;
use Modules\Core\Jobs\ClearCacheByRoutes;

class ClearJobsCacheClearable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * vars
     */
    private $log;
   
    /**
     * 
     */
    public function __construct()
    {
        $this->log = 'Core: Jobs||ClearJobsCacheClearable|';
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        
        $deleted = \DB::table('jobs') ->where('queue', 'cacheByRoutes') ->delete();
        \Log::info($this->log."Deleted: ".$deleted);

        \Log::info($this->log."ClearAllResponseCache");
        ClearAllResponseCache::dispatch(['force' => true]);

        //Clean Homepage
        $url = url('/');
        $client = new \GuzzleHttp\Client();
        $promise = $client->get($url, ['headers' => ['icache-bypass' => 1]]);
        \Log::info('Route Update Cache: '. $url);
            
    }

   
}
