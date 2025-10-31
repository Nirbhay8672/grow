<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Locality;
use App\Models\State;
use Illuminate\Database\Seeder;

class LocalitySeeder extends Seeder
{
    public function run(): void
    {
        $localities = [
            // Maharashtra
            'Maharashtra' => [
                'Mumbai' => [
                    ['name' => 'Colaba', 'zip_code' => '400001'],
                    ['name' => 'Fort', 'zip_code' => '400023'],
                    ['name' => 'Marine Lines', 'zip_code' => '400020'],
                    ['name' => 'Bandra West', 'zip_code' => '400050'],
                    ['name' => 'Andheri West', 'zip_code' => '400053'],
                    ['name' => 'Juhu', 'zip_code' => '400049'],
                    ['name' => 'Powai', 'zip_code' => '400076'],
                    ['name' => 'Borivali West', 'zip_code' => '400092'],
                    ['name' => 'Malad West', 'zip_code' => '400064'],
                    ['name' => 'Goregaon West', 'zip_code' => '400062'],
                ],
                'Pune' => [
                    ['name' => 'Koregaon Park', 'zip_code' => '411001'],
                    ['name' => 'Hinjewadi', 'zip_code' => '411057'],
                    ['name' => 'Baner', 'zip_code' => '411045'],
                    ['name' => 'Viman Nagar', 'zip_code' => '411014'],
                    ['name' => 'Wakad', 'zip_code' => '411057'],
                    ['name' => 'Aundh', 'zip_code' => '411007'],
                    ['name' => 'Kothrud', 'zip_code' => '411038'],
                    ['name' => 'Hadapsar', 'zip_code' => '411028'],
                    ['name' => 'Shivajinagar', 'zip_code' => '411005'],
                    ['name' => 'Pimpri Chinchwad', 'zip_code' => '411018'],
                ],
                'Nagpur' => [
                    ['name' => 'Civil Lines', 'zip_code' => '440001'],
                    ['name' => 'Sitabuldi', 'zip_code' => '440012'],
                    ['name' => 'Dhantoli', 'zip_code' => '440012'],
                    ['name' => 'Ramdaspeth', 'zip_code' => '440010'],
                    ['name' => 'Sadar', 'zip_code' => '440001'],
                ],
            ],

            // Karnataka
            'Karnataka' => [
                'Bangalore' => [
                    ['name' => 'Indiranagar', 'zip_code' => '560038'],
                    ['name' => 'Koramangala', 'zip_code' => '560095'],
                    ['name' => 'Whitefield', 'zip_code' => '560066'],
                    ['name' => 'Electronic City', 'zip_code' => '560100'],
                    ['name' => 'HSR Layout', 'zip_code' => '560102'],
                    ['name' => 'BTM Layout', 'zip_code' => '560076'],
                    ['name' => 'JP Nagar', 'zip_code' => '560078'],
                    ['name' => 'Marathahalli', 'zip_code' => '560037'],
                    ['name' => 'Bellandur', 'zip_code' => '560103'],
                    ['name' => 'Malleshwaram', 'zip_code' => '560003'],
                ],
                'Mysore' => [
                    ['name' => 'Gokulam', 'zip_code' => '570002'],
                    ['name' => 'Vijayanagar', 'zip_code' => '570017'],
                    ['name' => 'Kuvempunagar', 'zip_code' => '570023'],
                    ['name' => 'JP Nagar', 'zip_code' => '570008'],
                    ['name' => 'Yadavagiri', 'zip_code' => '570020'],
                ],
            ],

            // Tamil Nadu
            'Tamil Nadu' => [
                'Chennai' => [
                    ['name' => 'Adyar', 'zip_code' => '600020'],
                    ['name' => 'Anna Nagar', 'zip_code' => '600040'],
                    ['name' => 'T Nagar', 'zip_code' => '600017'],
                    ['name' => 'Velachery', 'zip_code' => '600042'],
                    ['name' => 'Chromepet', 'zip_code' => '600044'],
                    ['name' => 'Guindy', 'zip_code' => '600032'],
                    ['name' => 'Nungambakkam', 'zip_code' => '600034'],
                    ['name' => 'Mylapore', 'zip_code' => '600004'],
                ],
                'Coimbatore' => [
                    ['name' => 'RS Puram', 'zip_code' => '641002'],
                    ['name' => 'Race Course', 'zip_code' => '641018'],
                    ['name' => 'Sitra', 'zip_code' => '641014'],
                    ['name' => 'Peelamedu', 'zip_code' => '641004'],
                ],
            ],

            // Gujarat
            'Gujarat' => [
                'Ahmedabad' => [
                    ['name' => 'Navrangpura', 'zip_code' => '380009'],
                    ['name' => 'Satellite', 'zip_code' => '380015'],
                    ['name' => 'Vastrapur', 'zip_code' => '380052'],
                    ['name' => 'Bodakdev', 'zip_code' => '380054'],
                    ['name' => 'Prahladnagar', 'zip_code' => '380015'],
                    ['name' => 'Thaltej', 'zip_code' => '380054'],
                ],
                'Surat' => [
                    ['name' => 'Athwa', 'zip_code' => '395007'],
                    ['name' => 'Adajan', 'zip_code' => '395009'],
                    ['name' => 'Piplod', 'zip_code' => '395007'],
                    ['name' => 'Varachha', 'zip_code' => '395006'],
                ],
            ],

            // Uttar Pradesh
            'Uttar Pradesh' => [
                'Lucknow' => [
                    ['name' => 'Hazratganj', 'zip_code' => '226001'],
                    ['name' => 'Gomti Nagar', 'zip_code' => '226010'],
                    ['name' => 'Aliganj', 'zip_code' => '226024'],
                    ['name' => 'Indira Nagar', 'zip_code' => '226016'],
                ],
                'Noida' => [
                    ['name' => 'Sector 62', 'zip_code' => '201301'],
                    ['name' => 'Sector 51', 'zip_code' => '201301'],
                    ['name' => 'Sector 137', 'zip_code' => '201305'],
                    ['name' => 'Greater Noida', 'zip_code' => '201310'],
                ],
            ],

            // West Bengal
            'West Bengal' => [
                'Kolkata' => [
                    ['name' => 'Park Street', 'zip_code' => '700016'],
                    ['name' => 'Salt Lake', 'zip_code' => '700064'],
                    ['name' => 'New Town', 'zip_code' => '700156'],
                    ['name' => 'Ballygunge', 'zip_code' => '700019'],
                    ['name' => 'Alipore', 'zip_code' => '700027'],
                ],
                'Howrah' => [
                    ['name' => 'Howrah Maidan', 'zip_code' => '711101'],
                    ['name' => 'Shibpur', 'zip_code' => '711102'],
                ],
            ],

            // Rajasthan
            'Rajasthan' => [
                'Jaipur' => [
                    ['name' => 'Malviya Nagar', 'zip_code' => '302017'],
                    ['name' => 'Vaishali Nagar', 'zip_code' => '302021'],
                    ['name' => 'Mansarovar', 'zip_code' => '302020'],
                    ['name' => 'C-Scheme', 'zip_code' => '302001'],
                    ['name' => 'Bani Park', 'zip_code' => '302016'],
                ],
                'Jodhpur' => [
                    ['name' => 'Ratanada', 'zip_code' => '342001'],
                    ['name' => 'Sardarpura', 'zip_code' => '342003'],
                ],
            ],

            // Madhya Pradesh
            'Madhya Pradesh' => [
                'Bhopal' => [
                    ['name' => 'MP Nagar', 'zip_code' => '462011'],
                    ['name' => 'Arera Colony', 'zip_code' => '462016'],
                    ['name' => 'Shahpura', 'zip_code' => '462039'],
                ],
                'Indore' => [
                    ['name' => 'Vijay Nagar', 'zip_code' => '452010'],
                    ['name' => 'Scheme 54', 'zip_code' => '452010'],
                    ['name' => 'Rau', 'zip_code' => '453331'],
                ],
            ],

            // Andhra Pradesh
            'Andhra Pradesh' => [
                'Visakhapatnam' => [
                    ['name' => 'Dwaraka Nagar', 'zip_code' => '530016'],
                    ['name' => 'MVP Colony', 'zip_code' => '530017'],
                    ['name' => 'Seethammadhara', 'zip_code' => '530013'],
                ],
                'Vijayawada' => [
                    ['name' => 'Benz Circle', 'zip_code' => '520010'],
                    ['name' => 'Labbipet', 'zip_code' => '520010'],
                ],
            ],

            // Telangana
            'Telangana' => [
                'Hyderabad' => [
                    ['name' => 'Banjara Hills', 'zip_code' => '500034'],
                    ['name' => 'Jubilee Hills', 'zip_code' => '500033'],
                    ['name' => 'Hitech City', 'zip_code' => '500081'],
                    ['name' => 'Gachibowli', 'zip_code' => '500032'],
                    ['name' => 'Kondapur', 'zip_code' => '500084'],
                    ['name' => 'Madhapur', 'zip_code' => '500081'],
                    ['name' => 'Secunderabad', 'zip_code' => '500003'],
                    ['name' => 'Begumpet', 'zip_code' => '500016'],
                ],
            ],

            // Kerala
            'Kerala' => [
                'Thiruvananthapuram' => [
                    ['name' => 'Kowdiar', 'zip_code' => '695003'],
                    ['name' => 'Vellayambalam', 'zip_code' => '695010'],
                    ['name' => 'Peroorkada', 'zip_code' => '695005'],
                ],
                'Kochi' => [
                    ['name' => 'Marine Drive', 'zip_code' => '682031'],
                    ['name' => 'Kakkanad', 'zip_code' => '682030'],
                    ['name' => 'Edapally', 'zip_code' => '682024'],
                ],
            ],

            // Punjab
            'Punjab' => [
                'Amritsar' => [
                    ['name' => 'Hall Bazaar', 'zip_code' => '143001'],
                    ['name' => 'Ranjit Avenue', 'zip_code' => '143001'],
                ],
                'Ludhiana' => [
                    ['name' => 'Model Town', 'zip_code' => '141002'],
                    ['name' => 'Feroze Gandhi Market', 'zip_code' => '141001'],
                ],
            ],

            // Haryana
            'Haryana' => [
                'Gurgaon' => [
                    ['name' => 'Sector 29', 'zip_code' => '122001'],
                    ['name' => 'DLF Phase 1', 'zip_code' => '122002'],
                    ['name' => 'Sohna Road', 'zip_code' => '122018'],
                    ['name' => 'Sector 56', 'zip_code' => '122011'],
                ],
                'Faridabad' => [
                    ['name' => 'Sector 15', 'zip_code' => '121007'],
                    ['name' => 'NIT', 'zip_code' => '121001'],
                ],
            ],

            // Bihar
            'Bihar' => [
                'Patna' => [
                    ['name' => 'Boring Road', 'zip_code' => '800001'],
                    ['name' => 'Kankarbagh', 'zip_code' => '800020'],
                    ['name' => 'Exhibition Road', 'zip_code' => '800001'],
                ],
            ],

            // Odisha
            'Odisha' => [
                'Bhubaneswar' => [
                    ['name' => 'Nayapalli', 'zip_code' => '751012'],
                    ['name' => 'Patia', 'zip_code' => '751024'],
                    ['name' => 'Khandagiri', 'zip_code' => '751030'],
                ],
                'Cuttack' => [
                    ['name' => 'Tulasipur', 'zip_code' => '753008'],
                    ['name' => 'Cantonment Road', 'zip_code' => '753001'],
                ],
            ],

            // Delhi
            'Delhi' => [
                'New Delhi' => [
                    ['name' => 'Connaught Place', 'zip_code' => '110001'],
                    ['name' => 'Chanakyapuri', 'zip_code' => '110021'],
                    ['name' => 'Janpath', 'zip_code' => '110001'],
                ],
                'Central Delhi' => [
                    ['name' => 'Karol Bagh', 'zip_code' => '110005'],
                    ['name' => 'Darya Ganj', 'zip_code' => '110002'],
                ],
                'South Delhi' => [
                    ['name' => 'Greater Kailash', 'zip_code' => '110048'],
                    ['name' => 'Vasant Kunj', 'zip_code' => '110070'],
                    ['name' => 'Saket', 'zip_code' => '110017'],
                ],
                'North Delhi' => [
                    ['name' => 'Model Town', 'zip_code' => '110009'],
                    ['name' => 'Rohini', 'zip_code' => '110085'],
                ],
            ],
        ];

        foreach ($localities as $stateName => $cities) {
            $state = State::where('name', $stateName)->first();
            
            if ($state) {
                foreach ($cities as $cityName => $localityData) {
                    $city = City::where('name', $cityName)
                        ->where('state_id', $state->id)
                        ->first();
                    
                    if ($city) {
                        foreach ($localityData as $localityInfo) {
                            // Check if locality already exists to avoid duplicates
                            $existingLocality = Locality::where('name', $localityInfo['name'])
                                ->where('state_id', $state->id)
                                ->where('city_id', $city->id)
                                ->where('zip_code', $localityInfo['zip_code'])
                                ->first();
                            
                            if (!$existingLocality) {
                                // Also check if zip_code is unique globally
                                $existingZip = Locality::where('zip_code', $localityInfo['zip_code'])->first();
                                
                                if (!$existingZip) {
                                    Locality::create([
                                        'name' => $localityInfo['name'],
                                        'state_id' => $state->id,
                                        'city_id' => $city->id,
                                        'zip_code' => $localityInfo['zip_code'],
                                        'is_active' => true,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

