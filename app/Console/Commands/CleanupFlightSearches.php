<?php

namespace App\Console\Commands;

use App\Models\FlightSearch;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CleanupFlightSearches extends Command
{
    protected $signature = 'flight:cleanup-searches';
    protected $description = 'Clean up expired flight search records and their associated files';

    public function handle()
    {
        // Find expired records
        $expiredSearches = FlightSearch::where('expires_at', '<=', Carbon::now())->get();
        
        // Delete associated files first
        foreach ($expiredSearches as $search) {
            if (!empty($search->flight_offers_path) && Storage::exists($search->flight_offers_path)) {
                Storage::delete($search->flight_offers_path);
            }
            
            if (!empty($search->dictionaries_path) && Storage::exists($search->dictionaries_path)) {
                Storage::delete($search->dictionaries_path);
            }
        }
        
        // Now delete the records
        $count = $expiredSearches->count();
        FlightSearch::where('expires_at', '<=', Carbon::now())->delete();
        
        $this->info("Deleted {$count} expired flight search records and their associated files.");
    }
} 