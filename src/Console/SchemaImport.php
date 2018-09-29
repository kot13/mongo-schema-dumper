<?php

namespace Pmurkin\MongoSchemaDumper\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SchemaImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:import {--file=./schema.json : file with schema}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import schemas mongo databases from file';

    /**
     * SchemaImport constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $file = $this->option('file');

        if (!file_exists($file)) {
            $this->warn('File not found');
            return;
        }

        $json = file_get_contents($file);
        $databases = json_decode($json, true);
        foreach ($databases as $database => $collections) {
            $this->info('Import: ' . $database);
            $db = DB::getMongoClient()->selectDatabase($database);

            foreach ($collections as $name => $collection) {
                $this->info('Create: ' . $name);
                $db->createCollection($name, $collection['options']);

                if (isset($collection['indexes']) && !empty($collection['indexes'])) {
                    $db->selectCollection($name)->createIndexes($collection['indexes']);
                }

                if (isset($collection['data']) && !empty($collection['data'])) {
                    $db->selectCollection($name)->insertMany($collection['data']);
                }
            }
        }

        $this->info('Import is done');
    }
}
