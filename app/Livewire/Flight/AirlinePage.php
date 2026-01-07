<?php

namespace App\Livewire\Flight;

use App\Models\Airline;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class AirlinePage extends Component
{
    use WithFileUploads, LivewireAlert, WithPagination;

        // Form properties
        public $airline_code = '';
        public $airline_name = '';
        public $logo;
        public $e_airline_code;
        public $e_airline_name;
        public $e_markup;
        public $e_markdown;
        public $e_markprice;
        public $e_markstatus;
        public $e_markprice_economy;
        public $e_markstatus_economy;
        public $e_markprice_premium_economy;
        public $e_markstatus_premium_economy;
        public $e_markprice_first;
        public $e_markstatus_first;
        public $e_markprice_business;
        public $e_markstatus_business;

        public $e_logo;
          public $airline;
          public $search = '';


            // Validation rules
    protected $rules = [
        'airline_code' => 'required|string|max:10|unique:airlines,airline_code',
        'airline_name' => 'required|string|max:100',
        'logo' => 'nullable|image|max:10240', // 10MB max
    ];
    
    protected $messages = [
        'airline_code.required' => 'Please enter an airline code',
        'airline_code.unique' => 'This airline code is already in use',
        'airline_name.required' => 'Please enter the airline name',
        'logo.image' => 'The file must be an image',
        'logo.max' => 'The logo cannot be larger than 10MB',
    ];

    public function save()
    {
        $this->validate();
    
        try {
            // Handle logo upload
            $logoPath = null;
            if ($this->logo) {
                $filename = 'airline_' . time() . '.' . $this->logo->getClientOriginalExtension();
                $logoPath = $this->logo->storeAs('airlines/logos', $filename, 'public');
            }
    
            // Create new airline using mass assignment
            Airline::create([
                'airline_code' => $this->airline_code,
                'airline_name' => $this->airline_name,
                'logo' => $logoPath,
            ]);
    
            // Reset form fields
            $this->reset(['airline_code', 'airline_name', 'logo']);
    
            // Show success message
            $this->alert('success', 'Airline created successfully!', [
                'position' => 'top-right',
                'timer' => 3000,
                'toast' => true,
            ]);
    
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to create airline: ' . $e->getMessage(), [
                'position' => 'top-right',
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }
    


    public function selectAirline($airlineId)
    {
       
            $this->airline = Airline::findOrFail($airlineId);


        $this->e_airline_code = $this->airline->airline_code;
        $this->e_airline_name = $this->airline->airline_name;
        $this->e_markup = $this->airline->markup;
        $this->e_markdown = $this->airline->markdown;
        $this->e_markprice = $this->airline->markprice;
        $this->e_markstatus = $this->airline->markstatus ?? null;
        $this->e_markprice_economy = $this->airline->markprice_economy;
        $this->e_markstatus_economy = $this->airline->markstatus_economy ?? null;
        $this->e_markprice_premium_economy = $this->airline->markprice_premium_economy;
        $this->e_markstatus_premium_economy = $this->airline->markstatus_premium_economy ?? null;
        $this->e_markprice_business = $this->airline->markprice_business;
        $this->e_markstatus_business = $this->airline->markstatus_business ?? null;
        $this->e_markprice_first = $this->airline->markprice_first;
        $this->e_markstatus_first = $this->airline->markstatus_first ?? null;
        $this->e_logo = $this->airline->logo;
       

    
    }
    public function editAirline()
    {
        try {
            // Validate based on whether e_logo is a file upload or string
            if (is_object($this->e_logo)) {
                $this->validate([
                    'e_airline_code' => 'sometimes|string|max:10',
                    'e_airline_name' => 'sometimes|string|max:100',
                    // 'e_markup' => 'sometimes|integer',
                    'e_logo' => 'nullable|image|max:10240',
                ]);
                
                // Handle new logo upload
                $logoPath = null;
                if ($this->e_logo) {
                    // Delete old logo if exists
                    if ($this->airline->logo && Storage::disk('public')->exists($this->airline->logo)) {
                        Storage::disk('public')->delete($this->airline->logo);
                    }
                    
                    // Store new logo with original filename
                    $originalFilename = $this->e_logo->getClientOriginalName();
                    $logoPath = $this->e_logo->storeAs('airlines/logos', $originalFilename, 'public');
                }
            } else {
                $this->validate([
                    'e_airline_code' => 'sometimes|string|max:10',
                    'e_airline_name' => 'sometimes|string|max:100',
                    // 'e_markup' => 'sometimes|integer',
                ]);
                
                // Keep existing logo or set to null if removed
                $logoPath = $this->e_logo;
            }

            // return dd($this->e_markstatus ?? null);
            
            // Update airline details
            // return dd([$this->e_markprice_economy, $this->e_markstatus_economy, $this->e_markprice_premium_economy, $this->e_markstatus_premium_economy,
            // $this->e_markprice_business, $this->e_markstatus_business, $this->e_markprice_first, $this->e_markstatus_first]);
            
            $this->airline->update([
                'airline_code' => $this->e_airline_code,
                'airline_name' => $this->e_airline_name,
                'markup' => $this->e_markup,
                'markdown' => $this->e_markdown,
                'markprice' => $this->e_markprice,
                'markstatus' => $this->e_markstatus == "null" ? null : $this->e_markstatus,
                'markprice_economy' => $this->e_markprice_economy,
                'markstatus_economy' => $this->e_markstatus_economy == "null" ? null : $this->e_markstatus_economy, 
                'markprice_premium_economy' => $this->e_markprice_premium_economy,
                'markstatus_premium_economy' => $this->e_markstatus_premium_economy == "null" ? null : $this->e_markstatus_premium_economy, 
                'markprice_business' => $this->e_markprice_business,
                'markstatus_business' => $this->e_markstatus_business == "null" ? null : $this->e_markstatus_business, 
                'markprice_first' => $this->e_markprice_first,
                'markstatus_first' => $this->e_markstatus_first == "null" ? null : $this->e_markstatus_first, 
                'logo' => $logoPath,
            ]);
            
            // Success alert
            $this->alert('success', 'Airline updated successfully!', [
                'position' => 'top-right',
                'timer' => 3000,
                'toast' => true,
            ]);
            
            // Reset form fields
            $this->reset(['e_logo']);
            
        } catch (\Exception $e) {
            // Error alert
            $this->alert('error', 'Failed to update airline: ' . $e->getMessage(), [
                'position' => 'top-right',
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }
    // public function editAirline()
    // {
    //     try {
 
    
    //         // Validate edited fields
    //         $this->validate([
    //             'e_airline_code' => 'sometimes|string|max:10',
    //             'e_airline_name' => 'sometimes|string|max:100',
    //             'e_markup' => 'sometimes|integer',
    //             'e_logo' => 'nullable|image|max:10240',
    //         ]);
    
    //         // Handle logo upload
    //         $logoPath = $this->e_logo; // Keep existing logo unless changed
    //         if ($this->e_logo) {
    //             // Delete old logo if exists
    //             if ($this->e_logo && Storage::disk('public')->exists($this->e_logo)) {
    //                 Storage::disk('public')->delete($this->e_logo);
    //             }
    
    //             // Store new logo
    //             $filename = 'airline_' . time() . '.' . $this->e_logo->getClientOriginalExtension();
    //             $logoPath = $this->e_logo->storeAs('airlines/logos', $filename, 'public');
    //         }
    
    //         // Update airline details
    //         $this->airline->update([
    //             'airline_code' => $this->e_airline_code,
    //             'airline_name' => $this->e_airline_name,
    //             'markup' => $this->e_markup,
    //             'logo' => $logoPath,
    //         ]);
    
    //         // Success alert
    //         $this->alert('success', 'Airline updated successfully!', [
    //             'position' => 'top-right',
    //             'timer' => 3000,
    //             'toast' => true,
    //         ]);
    

    
    //     } catch (\Exception $e) {
    //         // Error alert
    //         $this->alert('error', 'Failed to update airline: ' . $e->getMessage(), [
    //             'position' => 'top-right',
    //             'timer' => 3000,
    //             'toast' => true,
    //         ]);
    //     }
    // }
    
    
    
    public function render()
    {
        $airlines = Airline::where('airline_name', 'like', '%' . $this->search . '%')
        ->orWhere('airline_code', 'like', '%' . $this->search . '%')
        ->paginate(12);
        // dd($airlines );
        return view('livewire.flight.airline-page', ['airlines' => $airlines]);
    }
}
