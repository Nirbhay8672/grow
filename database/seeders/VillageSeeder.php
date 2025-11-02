<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Taluka;
use App\Models\Village;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        $villages = [
            // Maharashtra - Mumbai City
            'Mumbai City' => [
                'Colaba' => ['Colaba Village', 'Gateway Village', 'Navy Nagar'],
                'Fort' => ['Fort Area', 'Ballard Estate', 'Fort Market'],
                'Marine Lines' => ['Marine Drive', 'Kalba Devi', 'Dockyard'],
            ],
            
            // Maharashtra - Mumbai Suburban
            'Mumbai Suburban' => [
                'Andheri' => ['Andheri East', 'Andheri West', 'Saki Naka', 'Jogeshwari'],
                'Bandra' => ['Bandra East', 'Bandra West', 'Khar', 'Santacruz'],
                'Borivali' => ['Borivali East', 'Borivali West', 'Kandivali', 'Dahisar'],
            ],
            
            // Maharashtra - Pune
            'Pune' => [
                'Pune City' => ['Shivajinagar', 'Kothrud', 'Baner', 'Hinjewadi'],
                'Haveli' => ['Wagholi', 'Manjri', 'Kesnand', 'Phursungi'],
                'Mulshi' => ['Mulshi', 'Tamhini', 'Velhe', 'Pawana'],
            ],
            
            // Karnataka - Bengaluru Urban
            'Bengaluru Urban' => [
                'Bengaluru North' => ['Hebbal', 'Yelahanka', 'Peenya', 'Yeshwanthpur'],
                'Bengaluru South' => ['JP Nagar', 'BTM Layout', 'Koramangala', 'Indiranagar'],
                'Bengaluru East' => ['Whitefield', 'Marathahalli', 'Varthur', 'Bellandur'],
            ],
            
            // Karnataka - Mysuru
            'Mysuru' => [
                'Mysuru City' => ['Kuvempunagar', 'Vijayanagar', 'Gokulam', 'Krishnamurthypuram'],
                'Hunsur' => ['Hunsur', 'Periyapatna', 'Krishnarajanagara'],
                'Nanjangud' => ['Nanjangud', 'T Narasipura', 'Saragur'],
            ],
            
            // Tamil Nadu - Chennai
            'Chennai' => [
                'Egmore' => ['Egmore', 'Nungambakkam', 'Kodambakkam'],
                'Guindy' => ['Guindy', 'Velachery', 'Thiruvanmiyur'],
                'Mylapore' => ['Mylapore', 'T Nagar', 'Adyar'],
            ],
            
            // Tamil Nadu - Coimbatore
            'Coimbatore' => [
                'Coimbatore North' => ['Peelamedu', 'Ramanathapuram', 'Sitra'],
                'Pollachi' => ['Pollachi', 'Anamalai', 'Valparai'],
            ],
            
            // Gujarat - Ahmedabad
            'Ahmedabad' => [
                'Ahmedabad City' => ['Navrangpura', 'Satellite', 'Vastrapur'],
                'Daskroi' => ['Sarkhej', 'Jodhpur Gam', 'Sanand'],
                'Dholka' => ['Dholka', 'Bavla', 'Detroj'],
            ],
            
            // Gujarat - Surat
            'Surat' => [
                'Surat City' => ['Athwa', 'Adajan', 'Piplod'],
                'Choryasi' => ['Varachha', 'Kosad', 'Kadodara'],
            ],
            
            // Uttar Pradesh - Lucknow
            'Lucknow' => [
                'Lucknow' => ['Hazratganj', 'Aliganj', 'Gomti Nagar'],
                'Bakshi Ka Talab' => ['Bakshi Ka Talab', 'Malihabad'],
            ],
            
            // Uttar Pradesh - Kanpur Nagar
            'Kanpur Nagar' => [
                'Kanpur City' => ['Cantonment', 'Civil Lines', 'Panki'],
                'Bilhaur' => ['Bilhaur', 'Ghatampur'],
            ],
            
            // West Bengal - Kolkata
            'Kolkata' => [
                'Alipore' => ['Alipore', 'Ballygunge', 'New Alipore'],
                'Park Street' => ['Park Street', 'Elgin Road', 'Camac Street'],
            ],
            
            // Rajasthan - Jaipur
            'Jaipur' => [
                'Jaipur City' => ['Pink City', 'Bani Park', 'C-Scheme'],
                'Sanganer' => ['Sanganer', 'Bagru', 'Chomu'],
            ],
            
            // Rajasthan - Jodhpur
            'Jodhpur' => [
                'Jodhpur City' => ['Ratanada', 'Sardarpura', 'Basni'],
                'Bilara' => ['Bilara', 'Phalodi'],
            ],
            
            // Madhya Pradesh - Bhopal
            'Bhopal' => [
                'Bhopal' => ['MP Nagar', 'Arera Colony', 'Shahpura'],
            ],
            
            // Madhya Pradesh - Indore
            'Indore' => [
                'Indore' => ['Vijay Nagar', 'Scheme 54', 'Rau'],
            ],
            
            // Telangana - Hyderabad
            'Hyderabad' => [
                'Banjara Hills' => ['Banjara Hills', 'Jubilee Hills', 'Banjara Hills Road No 12'],
                'Secunderabad' => ['Secunderabad', 'Begumpet', 'Marredpally'],
            ],
            
            // Kerala - Thiruvananthapuram
            'Thiruvananthapuram' => [
                'Kowdiar' => ['Kowdiar', 'Vellayambalam', 'Sasthamangalam'],
            ],
            
            // Kerala - Kochi
            'Kochi' => [
                'Kakkanad' => ['Kakkanad', 'Edapally', 'Infopark'],
            ],
            
            // Punjab - Amritsar
            'Amritsar' => [
                'Amritsar-I' => ['Hall Bazaar', 'Ranjit Avenue', 'Civil Lines'],
            ],
            
            // Haryana - Gurugram
            'Gurugram' => [
                'Gurugram' => ['Sector 29', 'DLF Phase 1', 'Sohna Road'],
            ],
        ];

        foreach ($villages as $districtName => $talukas) {
            $district = District::where('name', $districtName)->first();
            
            if ($district) {
                foreach ($talukas as $talukaName => $villageNames) {
                    $taluka = Taluka::where('name', $talukaName)
                        ->where('district_id', $district->id)
                        ->first();
                    
                    if ($taluka) {
                        foreach ($villageNames as $villageName) {
                            // Check if village already exists to avoid duplicates
                            $existingVillage = Village::where('name', $villageName)
                                ->where('district_id', $district->id)
                                ->where('taluka_id', $taluka->id)
                                ->first();
                            
                            if (!$existingVillage) {
                                Village::create([
                                    'name' => $villageName,
                                    'district_id' => $district->id,
                                    'taluka_id' => $taluka->id,
                                    'is_active' => true,
                                    'user_id' => 1,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}

