<?php

namespace App\Console\Commands;

use App\Models\Identifier;
use App\Models\Organisation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportOuis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-ouis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import or update OUI assignments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $file = fopen(config('app.data_sources.oui'), 'r');
        } catch (\Exception $e) {
            $this->error('Unable to get file.');
            Log::error('OUI import failed due to the file being unavailable at the specified location.');
        }

        if ($file) {
            fgetcsv($file, 4096, ',', '"', '\\'); // Cheese it to skip the header row

            $chunkSize = 1000;
            while (!feof($file)) {
                $chunkData = [];

                for ($i = 0; $i < $chunkSize; $i++) {
                    $data = fgetcsv($file, 4096, ',', '"', '\\');
                    if ($data === false) {
                        break;
                    }

                    $chunkData[] = $data;
                }

                $this->processChunk($chunkData);
            }
        }
    }

    public function processChunk(array $chunkData): void
    {
        $newCount = 0;
        $updatedCount = 0;
        $skippedCount = count($chunkData);

        foreach ($chunkData as $data) {
            $organisation = Organisation::where('name', $data[2])->first();

            // New organisation, add it
            if (!$organisation) {
                $organisation = Organisation::create([
                    'name' => $data[2],
                    'address' => $data[3],
                ]);
            }

            // Organisation's address has changed, so update it
            if ($organisation && $organisation->address !== $data[3]) {
                $organisation->update([
                    'address' => $data[3],
                ]);
            }

            $identifier = Identifier::where('assignment', $data[1])->first();

            if (!$identifier) {
                // The identifier doesn't exist, so make it and assign to the organisation
                $newIdentifier = Identifier::create([
                    'assignment' => $data[1],
                ]);

                $newIdentifier->organisations()->attach($organisation);

                $newCount++;
                $skippedCount--;
            } elseif (!in_array($organisation->id, $identifier->organisations()->pluck('organisation_id')->toArray())) {
                // The identifier exists and is being attached to a new organisation as well
                $identifier->organisations()->attach($organisation);

                $updatedCount++;
                $skippedCount--;
            }
        }

        $this->info('Chunk processed. ' . $newCount . ' added, ' . $updatedCount . ' updated, ' . $skippedCount . ' unchanged.');
    }
}
