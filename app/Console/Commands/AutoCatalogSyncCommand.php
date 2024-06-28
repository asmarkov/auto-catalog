<?php

namespace App\Console\Commands;

use App\Rules\StorageFileExistsRule;
use App\Rules\StorageFileExtensionRule;
use App\Services\CatalogSyncService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AutoCatalogSyncCommand extends Command implements Isolatable
{

    protected $signature = 'auto-catalog:sync {xml_file=public/data.xml}';

    protected $description = 'Синхронизация каталога авто из xml файла';

    public function handle(CatalogSyncService $sync_service)
    {
        Validator::make($this->arguments(), [
            'xml_file' => [
                'bail',
                new StorageFileExistsRule(),
                new StorageFileExtensionRule('text/xml')
            ]
        ])->validate();

        $sync_service->sync(Storage::path($this->argument('xml_file')));
    }
}
