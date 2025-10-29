<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Maharashtra' => ['Mumbai', 'Pune', 'Nagpur', 'Nashik', 'Aurangabad', 'Solapur', 'Amravati', 'Kolhapur', 'Sangli', 'Malegaon'],
            'Karnataka' => ['Bangalore', 'Mysore', 'Hubli', 'Mangalore', 'Belgaum', 'Gulbarga', 'Davanagere', 'Bellary', 'Bijapur', 'Shimoga'],
            'Tamil Nadu' => ['Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem', 'Tirunelveli', 'Tiruppur', 'Erode', 'Vellore', 'Thoothukudi'],
            'Gujarat' => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar', 'Jamnagar', 'Gandhinagar', 'Nadiad', 'Anand', 'Morbi'],
            'Uttar Pradesh' => ['Lucknow', 'Kanpur', 'Agra', 'Varanasi', 'Meerut', 'Allahabad', 'Bareilly', 'Ghaziabad', 'Noida', 'Aligarh'],
            'West Bengal' => ['Kolkata', 'Howrah', 'Durgapur', 'Asansol', 'Siliguri', 'Bardhaman', 'Malda', 'Baharampur', 'Habra', 'Kharagpur'],
            'Rajasthan' => ['Jaipur', 'Jodhpur', 'Udaipur', 'Kota', 'Bikaner', 'Ajmer', 'Bharatpur', 'Alwar', 'Sikar', 'Pali'],
            'Madhya Pradesh' => ['Bhopal', 'Indore', 'Gwalior', 'Jabalpur', 'Ujjain', 'Sagar', 'Dewas', 'Satna', 'Ratlam', 'Murwara'],
            'Andhra Pradesh' => ['Hyderabad', 'Visakhapatnam', 'Vijayawada', 'Guntur', 'Nellore', 'Kurnool', 'Tirupati', 'Kadapa', 'Anantapur', 'Chittoor'],
            'Telangana' => ['Hyderabad', 'Warangal', 'Nizamabad', 'Khammam', 'Karimnagar', 'Ramagundam', 'Mahbubnagar', 'Nalgonda', 'Adilabad', 'Suryapet'],
            'Kerala' => ['Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Thrissur', 'Palakkad', 'Kollam', 'Malappuram', 'Kannur', 'Kasaragod', 'Alappuzha'],
            'Punjab' => ['Chandigarh', 'Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala', 'Bathinda', 'Mohali', 'Firozpur', 'Batala', 'Pathankot'],
            'Haryana' => ['Chandigarh', 'Faridabad', 'Gurgaon', 'Panipat', 'Ambala', 'Yamunanagar', 'Rohtak', 'Hisar', 'Karnal', 'Sonipat'],
            'Bihar' => ['Patna', 'Gaya', 'Bhagalpur', 'Muzaffarpur', 'Darbhanga', 'Purnia', 'Arrah', 'Begusarai', 'Katihar', 'Munger'],
            'Odisha' => ['Bhubaneswar', 'Cuttack', 'Rourkela', 'Berhampur', 'Sambalpur', 'Puri', 'Balasore', 'Bhadrak', 'Baripada', 'Bargarh'],
            'Assam' => ['Guwahati', 'Silchar', 'Dibrugarh', 'Jorhat', 'Tezpur', 'Nagaon', 'Tinsukia', 'Dhubri', 'Sivasagar', 'Goalpara'],
            'Jharkhand' => ['Ranchi', 'Jamshedpur', 'Dhanbad', 'Bokaro', 'Deoghar', 'Phusro', 'Hazaribagh', 'Giridih', 'Ramgarh', 'Medininagar'],
            'Chhattisgarh' => ['Raipur', 'Bhilai', 'Bilaspur', 'Korba', 'Rajnandgaon', 'Durg', 'Raigarh', 'Jagdalpur', 'Ambikapur', 'Chirmiri'],
            'Himachal Pradesh' => ['Shimla', 'Dharamshala', 'Solan', 'Mandi', 'Palampur', 'Baddi', 'Chamba', 'Una', 'Nahan', 'Kullu'],
            'Uttarakhand' => ['Dehradun', 'Haridwar', 'Rishikesh', 'Roorkee', 'Kashipur', 'Rudrapur', 'Haldwani', 'Ramnagar', 'Pithoragarh', 'Srinagar'],
            'Goa' => ['Panaji', 'Margao', 'Vasco da Gama', 'Mapusa', 'Ponda', 'Mormugao', 'Curchorem', 'Sanquelim', 'Bicholim', 'Valpoi'],
            'Delhi' => ['New Delhi', 'Central Delhi', 'North Delhi', 'South Delhi', 'East Delhi', 'West Delhi', 'North East Delhi', 'North West Delhi', 'South East Delhi', 'South West Delhi'],
        ];

        foreach ($cities as $stateName => $cityNames) {
            $state = State::where('name', $stateName)->first();
            
            if ($state) {
                foreach ($cityNames as $cityName) {
                    City::create([
                        'name' => $cityName,
                        'state_id' => $state->id,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}