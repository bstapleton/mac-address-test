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

            // The identifier doesn't exist, so make it and assign to the organisation
            if (!$identifier) {
                Identifier::create([
                    'assignment' => $data[1],
                    'organisation_id' => $organisation->id,
                ]);

                $newCount++;
                $skippedCount--;
            }

            // The identifier exists, but has been re-assigned
            if ($identifier && $identifier->organisation_id !== $organisation->id) {
                $x = $identifier->organisation_id;
                $identifier->update([
                    'organisation_id' => $organisation->id
                ]);
                $y = $identifier->organisation_id;

                $this->info('Identifier ' . $data[1] . ' re-assigned from ' . $x . ' to ' . $y);
                $updatedCount++;
                $skippedCount--;
            }
        }

        $this->info('Chunk processed. ' . $newCount . ' added, ' . $updatedCount . ' updated, ' . $skippedCount . ' unchanged.');
    }
}
