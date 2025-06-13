<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AirlineSeeder extends Seeder
{
    public function run()
    {
        $airlines = [
            ['airport_code' => 'ATL', 'airport' => 'Hartsfield-Jackson Atlanta International Airport', 'city_country' => 'Atlanta, USA', 'city' => 'Atlanta', 'country' => 'USA'],
            ['airport_code' => 'PEK', 'airport' => 'Beijing Capital International Airport', 'city_country' => 'Beijing, China', 'city' => 'Beijing', 'country' => 'China'],
            ['airport_code' => 'LAX', 'airport' => 'Los Angeles International Airport', 'city_country' => 'Los Angeles, USA', 'city' => 'Los Angeles', 'country' => 'USA'],
            ['airport_code' => 'DXB', 'airport' => 'Dubai International Airport', 'city_country' => 'Dubai, UAE', 'city' => 'Dubai', 'country' => 'UAE'],
            ['airport_code' => 'HND', 'airport' => 'Tokyo Haneda Airport', 'city_country' => 'Tokyo, Japan', 'city' => 'Tokyo', 'country' => 'Japan'],
            ['airport_code' => 'ORD', 'airport' => 'O’Hare International Airport', 'city_country' => 'Chicago, USA', 'city' => 'Chicago', 'country' => 'USA'],
            ['airport_code' => 'LHR', 'airport' => 'London Heathrow Airport', 'city_country' => 'London, UK', 'city' => 'London', 'country' => 'UK'],
            ['airport_code' => 'PVG', 'airport' => 'Shanghai Pudong International Airport', 'city_country' => 'Shanghai, China', 'city' => 'Shanghai', 'country' => 'China'],
            ['airport_code' => 'CDG', 'airport' => 'Paris Charles de Gaulle Airport', 'city_country' => 'Paris, France', 'city' => 'Paris', 'country' => 'France'],
            ['airport_code' => 'DFW', 'airport' => 'Dallas/Fort Worth International Airport', 'city_country' => 'Dallas, USA', 'city' => 'Dallas', 'country' => 'USA'],
            ['airport_code' => 'AMS', 'airport' => 'Amsterdam Schiphol Airport', 'city_country' => 'Amsterdam, Netherlands', 'city' => 'Amsterdam', 'country' => 'Netherlands'],
            ['airport_code' => 'FRA', 'airport' => 'Frankfurt Airport', 'city_country' => 'Frankfurt, Germany', 'city' => 'Frankfurt', 'country' => 'Germany'],
            ['airport_code' => 'IST', 'airport' => 'Istanbul Airport', 'city_country' => 'Istanbul, Turkey', 'city' => 'Istanbul', 'country' => 'Turkey'],
            ['airport_code' => 'SIN', 'airport' => 'Singapore Changi Airport', 'city_country' => 'Singapore, Singapore', 'city' => 'Singapore', 'country' => 'Singapore'],
            ['airport_code' => 'ICN', 'airport' => 'Incheon International Airport', 'city_country' => 'Seoul, South Korea', 'city' => 'Seoul', 'country' => 'South Korea'],
            ['airport_code' => 'JFK', 'airport' => 'John F. Kennedy International Airport', 'city_country' => 'New York, USA', 'city' => 'New York', 'country' => 'USA'],
            ['airport_code' => 'KUL', 'airport' => 'Kuala Lumpur International Airport', 'city_country' => 'Kuala Lumpur, Malaysia', 'city' => 'Kuala Lumpur', 'country' => 'Malaysia'],
            ['airport_code' => 'BKK', 'airport' => 'Suvarnabhumi Airport', 'city_country' => 'Bangkok, Thailand', 'city' => 'Bangkok', 'country' => 'Thailand'],
            ['airport_code' => 'SYD', 'airport' => 'Sydney Kingsford Smith Airport', 'city_country' => 'Sydney, Australia', 'city' => 'Sydney', 'country' => 'Australia'],
            ['airport_code' => 'MUC', 'airport' => 'Munich Airport', 'city_country' => 'Munich, Germany', 'city' => 'Munich', 'country' => 'Germany'],
            // More entries below
            ['airport_code' => 'HKG', 'airport' => 'Hong Kong International Airport', 'city_country' => 'Hong Kong, China', 'city' => 'Hong Kong', 'country' => 'China'],
            ['airport_code' => 'GRU', 'airport' => 'São Paulo/Guarulhos–Governador André Franco Montoro International Airport', 'city_country' => 'São Paulo, Brazil', 'city' => 'São Paulo', 'country' => 'Brazil'],
            ['airport_code' => 'YYZ', 'airport' => 'Toronto Pearson International Airport', 'city_country' => 'Toronto, Canada', 'city' => 'Toronto', 'country' => 'Canada'],
            ['airport_code' => 'ZRH', 'airport' => 'Zürich Airport', 'city_country' => 'Zürich, Switzerland', 'city' => 'Zürich', 'country' => 'Switzerland'],
            ['airport_code' => 'MAD', 'airport' => 'Adolfo Suárez Madrid–Barajas Airport', 'city_country' => 'Madrid, Spain', 'city' => 'Madrid', 'country' => 'Spain'],
            ['airport_code' => 'BCN', 'airport' => 'Barcelona–El Prat Airport', 'city_country' => 'Barcelona, Spain', 'city' => 'Barcelona', 'country' => 'Spain'],
            ['airport_code' => 'FCO', 'airport' => 'Leonardo da Vinci–Fiumicino Airport', 'city_country' => 'Rome, Italy', 'city' => 'Rome', 'country' => 'Italy'],
            ['airport_code' => 'JNB', 'airport' => 'O. R. Tambo International Airport', 'city_country' => 'Johannesburg, South Africa', 'city' => 'Johannesburg', 'country' => 'South Africa'],
            ['airport_code' => 'LIS', 'airport' => 'Lisbon Portela Airport', 'city_country' => 'Lisbon, Portugal', 'city' => 'Lisbon', 'country' => 'Portugal'],
            ['airport_code' => 'CPT', 'airport' => 'Cape Town International Airport', 'city_country' => 'Cape Town, South Africa', 'city' => 'Cape Town', 'country' => 'South Africa'],
            ['airport_code' => 'BOM', 'airport' => 'Chhatrapati Shivaji Maharaj International Airport', 'city_country' => 'Mumbai, India', 'city' => 'Mumbai', 'country' => 'India'],
            ['airport_code' => 'DEL', 'airport' => 'Indira Gandhi International Airport', 'city_country' => 'New Delhi, India', 'city' => 'New Delhi', 'country' => 'India'],
            ['airport_code' => 'SVO', 'airport' => 'Sheremetyevo International Airport', 'city_country' => 'Moscow, Russia', 'city' => 'Moscow', 'country' => 'Russia'],
            ['airport_code' => 'VIE', 'airport' => 'Vienna International Airport', 'city_country' => 'Vienna, Austria', 'city' => 'Vienna', 'country' => 'Austria'],
            ['airport_code' => 'OSL', 'airport' => 'Oslo Gardermoen Airport', 'city_country' => 'Oslo, Norway', 'city' => 'Oslo', 'country' => 'Norway'],
            ['airport_code' => 'ARN', 'airport' => 'Stockholm Arlanda Airport', 'city_country' => 'Stockholm, Sweden', 'city' => 'Stockholm', 'country' => 'Sweden'],
            ['airport_code' => 'HEL', 'airport' => 'Helsinki-Vantaa Airport', 'city_country' => 'Helsinki, Finland', 'city' => 'Helsinki', 'country' => 'Finland'],
            ['airport_code' => 'CPH', 'airport' => 'Copenhagen Airport', 'city_country' => 'Copenhagen, Denmark', 'city' => 'Copenhagen', 'country' => 'Denmark'],
            ['airport_code' => 'DME', 'airport' => 'Domodedovo International Airport', 'city_country' => 'Moscow, Russia', 'city' => 'Moscow', 'country' => 'Russia'],
            ['airport_code' => 'KIX', 'airport' => 'Kansai International Airport', 'city_country' => 'Osaka, Japan', 'city' => 'Osaka', 'country' => 'Japan'],
            ['airport_code' => 'PER', 'airport' => 'Perth Airport', 'city_country' => 'Perth, Australia', 'city' => 'Perth', 'country' => 'Australia'],
            ['airport_code' => 'YVR', 'airport' => 'Vancouver International Airport', 'city_country' => 'Vancouver, Canada', 'city' => 'Vancouver', 'country' => 'Canada'],
            ['airport_code' => 'MEX', 'airport' => 'Mexico City International Airport', 'city_country' => 'Mexico City, Mexico', 'city' => 'Mexico City', 'country' => 'Mexico'],
            ['airport_code' => 'GRZ', 'airport' => 'Graz International Airport', 'city_country' => 'Graz, Austria', 'city' => 'Graz', 'country' => 'Austria'],

             // Nigerian Airlines
             ['airport_code' => 'LOS', 'airport' => 'Murtala Muhammed International Airport', 'city_country' => 'Lagos, Nigeria', 'city' => 'Lagos', 'country' => 'Nigeria'],
             ['airport_code' => 'PHC', 'airport' => 'Port Harcourt International Airport', 'city_country' => 'Port Harcourt, Nigeria', 'city' => 'Port Harcourt', 'country' => 'Nigeria'],
             ['airport_code' => 'KAN', 'airport' => 'Mallam Aminu Kano International Airport', 'city_country' => 'Kano, Nigeria', 'city' => 'Kano', 'country' => 'Nigeria'],
             ['airport_code' => 'ENU', 'airport' => 'Enugu International Airport', 'city_country' => 'Enugu, Nigeria', 'city' => 'Enugu', 'country' => 'Nigeria'],
             ['airport_code' => 'ABV', 'airport' => 'Nnamdi Azikiwe International Airport', 'city_country' => 'Abuja, Nigeria', 'city' => 'Abuja', 'country' => 'Nigeria'],
             
         
        ];

        DB::table('airlines')->insert($airlines);
    }
}
