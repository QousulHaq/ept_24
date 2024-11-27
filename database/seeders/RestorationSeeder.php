<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\Finder\Finder;

class RestorationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \JsonException
     */
    public function run(): void
    {
        $this->call(BouncerTableSeeder::class);
        $this->call(UsersTableSeeder::class);

        Artisan::call('patcher:install');

        $this->restoreFromCsv();
    }

    public function restoreFromCsv(): void
    {
        $finder = new Finder();

        $finder->files()->in(__DIR__.'/data/restore');
        $finder->sortByName();

        $tables = collect();

        foreach ($finder as $file) {
            $this->command->newLine();
            $this->command->getOutput()->info('Restoring from CSV : '.$file->getRealPath());

            Schema::disableForeignKeyConstraints();

            try {
                $records = $this->loadFiles($file->getRealPath());

                $tableName = Str::after($file->getFilenameWithoutExtension(), 'ept_');

                $tables->push($tableName);

                $this->command->withProgressBar($records, function ($record) use ($tableName) {
                    // exorcise the empty value into null
                    foreach ($record as $key => $value) {
                        if (empty($value) && ! is_numeric($value)) {
                            unset($record[$key]);
                        }
                    }

                    // translate boolean value for mysql to integer
                    if (DB::getDefaultConnection() === 'mysql') {
                        foreach ($record as $key => $value) {
                            if (in_array($value, ['true', 'false'], true)) {
                                $record[$key] = $value === 'true' ? 1 : 0;
                            }
                        }
                    }

                    DB::table($tableName)->insert($record);
                });
            } catch (Exception) {
                $this->command->alert('Failed to restore CSV : '.$file->getRealPath());
            } finally {
                Schema::enableForeignKeyConstraints();
            }
        }

    }

    /**
     * @param $filePath
     * @return \Illuminate\Support\Collection
     * @throws \League\Csv\Exception
     */
    protected function loadFiles($filePath): Collection
    {
        $collection = new Collection();

        $csv = Reader::createFromPath($filePath);
        $csv->setHeaderOffset(0);

        foreach ((new Statement())->process($csv) as $item) {
            $collection->add($item);
        }

        return $collection;
    }
}
