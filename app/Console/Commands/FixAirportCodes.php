<?php

namespace App\Console\Commands;

use App\Models\FlightSearch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixAirportCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:airport-codes {search_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix and debug airport codes in flight searches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $searchId = $this->argument('search_id');
        
        $query = FlightSearch::query();
        if ($searchId) {
            $query->where('search_id', $searchId);
        }
        
        $searches = $query->orderBy('created_at', 'desc')->limit(10)->get();
        
        if ($searches->isEmpty()) {
            $this->error('No flight searches found.');
            return 1;
        }
        
        $this->info('Found ' . $searches->count() . ' flight searches.');
        
        foreach ($searches as $search) {
            $this->info("\n" . str_repeat('-', 50));
            $this->info("Search ID: {$search->search_id}");
            $this->info("Created: {$search->created_at}");
            $this->info("From: {$search->from_city_name}");
            $this->info("To: {$search->to_city_name}");
            $this->info("From Code: " . ($search->from_code ?? 'NULL'));
            $this->info("To Code: " . ($search->to_code ?? 'NULL'));
            
            // Try to extract codes from city names if missing
            if (empty($search->from_code) || empty($search->to_code)) {
                $this->warn('Missing airport codes - attempting to fix...');
                
                // Method 1: Extract from city name strings
                $fromParts = explode('(', $search->from_city_name);
                $toParts = explode('(', $search->to_city_name);
                
                $fromCode = null;
                $toCode = null;
                
                if (count($fromParts) > 1) {
                    $fromCode = trim(str_replace(')', '', end($fromParts)));
                    if (strlen($fromCode) == 3) { // Valid IATA codes are 3 characters
                        $search->from_code = $fromCode;
                    }
                }
                
                if (count($toParts) > 1) {
                    $toCode = trim(str_replace(')', '', end($toParts)));
                    if (strlen($toCode) == 3) { // Valid IATA codes are 3 characters
                        $search->to_code = $toCode;
                    }
                }
                
                // Method 2: Try to get from flight offers
                if ((empty($search->from_code) || empty($search->to_code)) && !empty($search->flight_offers_path)) {
                    $this->info('Trying to extract codes from flight offers...');
                    
                    if (Storage::exists($search->flight_offers_path)) {
                        $flightOffersContent = Storage::get($search->flight_offers_path);
                        $flightOffers = json_decode($flightOffersContent, true);
                        
                        if (!empty($flightOffers) && isset($flightOffers[0]['itineraries'][0]['segments'][0])) {
                            $firstSegment = $flightOffers[0]['itineraries'][0]['segments'][0];
                            $lastSegment = end($flightOffers[0]['itineraries'][0]['segments']);
                            
                            if (empty($search->from_code)) {
                                $search->from_code = $firstSegment['departure']['iataCode'] ?? null;
                            }
                            
                            if (empty($search->to_code)) {
                                $search->to_code = $lastSegment['arrival']['iataCode'] ?? null;
                            }
                        }
                    } else {
                        $this->error('Flight offers file not found: ' . $search->flight_offers_path);
                    }
                }
                
                // Save the changes
                if ($search->isDirty()) {
                    $search->save();
                    $this->info('Updated codes:');
                    $this->info("From Code: {$search->from_code}");
                    $this->info("To Code: {$search->to_code}");
                } else {
                    $this->warn('Could not extract codes automatically.');
                }
            }
        }
        
        return 0;
    }
}
