<?php

namespace App\Livewire\Flight;

use App\Models\Airport;
use Livewire\Component;
use App\Imports\UsersImport;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Imports\AirportImport;
use Maatwebsite\Excel\Facades\Excel;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class AirportPage extends Component
{
    use WithFileUploads, LivewireAlert;
    use WithPagination;
    
public $file;
public $airport_code;
public $airport;
public $city_country;
public $city;
public $country;
public $airportresult;
public $search = '';

    // public function getPageName()
    // {
    //     return 'page'; // make sure Livewire uses ?page=
    // }


public function selectAirport($airlineId)
{
   
        $this->airportresult = Airport::findOrFail($airlineId);

    $this->airport_code = $this->airportresult->airport_code;
    $this->airport = $this->airportresult->airport;
    $this->city_country = $this->airportresult->city_country;
    $this->city = $this->airportresult->city;
    $this->country = $this->airportresult->country;

   


}


public function save()
{
    try {


        // Validate edited fields
        $this->validate([
            'airport_code' => 'sometimes|string|max:10',
    'airport' => 'sometimes|string',
    'city_country' => 'sometimes|string',
    'city' => 'sometimes|string',
    'country' => 'sometimes|string',
        ]);


        // Update airline details
        $this->airportresult->update([
            'airport_code' => $this->airport_code,
            'airport' => $this->airport,
            'city_country' => $this->city_country,
            'city' => $this->city,
            'country' => $this->country,
      
        ]);

        // Success alert
        $this->alert('success', 'Airport updated successfully!', [
            'position' => 'top-right',
            'timer' => 3000,
            'toast' => true,
        ]);



    } catch (\Exception $e) {
        // Error alert
        $this->alert('error', 'Failed to update Airport: ' . $e->getMessage(), [
            'position' => 'top-right',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
}

    public function import() 
    {
       
        $this->validate([
        'file' => 'required',
        ]);

        try {
            // Use Maatwebsite\Excel to import the file
            Excel::import(new AirportImport, $this->file);

            $this->reset('file'); // Clear the file input after successful upload
            $this->alert('success', 'CSV data imported successfully!', [
                'position' => 'top-end',
                'timer' => 5000,
                'toast' => true,
            ]);
        } catch (\Exception $e) {

            $this->alert('error', 'Error importing CSV: ' . $e->getMessage(), [
                'position' => 'top-end',
                'timer' => 5000,
                'toast' => true,
            ]);
        }
    }
  
    public function toggleVisibility($airportId)
    {
        try {
            $airport = Airport::findOrFail($airportId);
            
            // Toggle the visibility status
            $airport->update([
                'visible' => !$airport->visible
            ]);
            
            // Show success message
            $this->alert('success', 'Airport visibility updated!', [
                'position' => 'top-right',
                'timer' => 3000,
                'toast' => true,
            ]);
            
        } catch (\Exception $e) {
            // Show error message if something goes wrong
            $this->alert('error', 'Failed to update visibility: ' . $e->getMessage(), [
                'position' => 'top-right',
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }

       
    
    public function render()
    {
        $airports = Airport::where('airport_code', 'like', '%' . $this->search . '%')
        ->orWhere('airport', 'like', '%' . $this->search . '%')
        ->orWhere('city_country', 'like', '%' . $this->search . '%')
        ->orWhere('city', 'like', '%' . $this->search . '%')
        ->orWhere('country', 'like', '%' . $this->search . '%')
        ->paginate(12)->withQueryString();
        return view('livewire.flight.airport-page',
        [
            'airports'=> $airports
        ]
    );
    }
}
