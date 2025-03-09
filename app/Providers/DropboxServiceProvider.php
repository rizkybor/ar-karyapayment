<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Dropbox\Client as DropboxClient;

class DropboxServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('dropbox', function ($app, $config) {
            $adapter = new DropboxAdapter(new \Spatie\Dropbox\Client($config['access_token']));
            return new FilesystemAdapter(new Filesystem($adapter), $adapter);
        });
    }

    public function register()
    {
        //
    }
}