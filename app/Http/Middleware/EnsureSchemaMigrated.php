<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchemaMigrated
{
    public function __construct(protected Application $app) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->app->environment('production') && config('site.auto_migrate')) {
            if (! Schema::hasTable('migrations')) {
                Artisan::call('migrate', ['--force' => true]);
            } else {
                $rows = DB::table('migrations')->count();
                $files = count(glob($this->app->databasePath('migrations/*.php')));

                if ($files > $rows) {
                    Artisan::call('migrate', ['--force' => true]);
                }
            }
        }

        return $next($request);
    }
}
