<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Taluka;
use Illuminate\Database\Seeder;

class TalukaSeeder extends Seeder
{
    public function run(): void
    {
        $talukas = [
            // Maharashtra
            'Mumbai City' => ['Colaba', 'Fort', 'Marine Lines', 'Malabar Hill', 'Worli', 'Parel', 'Dadar', 'Matunga'],
            'Mumbai Suburban' => ['Andheri', 'Bandra', 'Borivali', 'Kurla', 'Malad', 'Goregaon', 'Jogeshwari', 'Kandivali'],
            'Pune' => ['Pune City', 'Haveli', 'Mulshi', 'Ambegaon', 'Junnar', 'Khed', 'Maval', 'Velhe'],
            'Nagpur' => ['Nagpur City', 'Kamptee', 'Hingna', 'Kalmeshwar', 'Katol', 'Narkhed', 'Parseoni', 'Saoner'],
            'Nashik' => ['Nashik City', 'Igatpuri', 'Sinnar', 'Niphad', 'Dindori', 'Peint', 'Yeola', 'Nandgaon'],
            
            // Karnataka
            'Bengaluru Urban' => ['Bengaluru North', 'Bengaluru South', 'Bengaluru East', 'Bengaluru West', 'Yelahanka', 'Anekal'],
            'Mysuru' => ['Mysuru City', 'Hunsur', 'Krishnarajanagara', 'Nanjangud', 'Periyapatna', 'Piriyapatna', 'T Narasipura'],
            'Belagavi' => ['Belagavi', 'Bailhongal', 'Chikodi', 'Gokak', 'Khanapur', 'Ramdurg', 'Saudatti'],
            'Dharwad' => ['Dharwad', 'Hubballi', 'Kalghatgi', 'Kundgol', 'Navalgund'],
            
            // Tamil Nadu
            'Chennai' => ['Egmore', 'Guindy', 'Mylapore', 'Tondiarpet', 'Velachery', 'Anna Nagar', 'Tambaram'],
            'Coimbatore' => ['Coimbatore North', 'Coimbatore South', 'Sulur', 'Pollachi', 'Mettupalayam', 'Udumalaipettai'],
            'Madurai' => ['Madurai North', 'Madurai South', 'Melur', 'Peraiyur', 'Thirumangalam', 'Usilampatti'],
            
            // Gujarat
            'Ahmedabad' => ['Ahmedabad City', 'Daskroi', 'Dholka', 'Bavla', 'Viramgam', 'Sanand', 'Detroj'],
            'Surat' => ['Surat City', 'Choryasi', 'Kamrej', 'Mangrol', 'Mandvi', 'Olpad', 'Palsana'],
            'Vadodara' => ['Vadodara City', 'Dabhoi', 'Padra', 'Savli', 'Vaghodia', 'Karjan', 'Sinor'],
            
            // Uttar Pradesh
            'Lucknow' => ['Lucknow', 'Bakshi Ka Talab', 'Malihabad', 'Mohaan', 'Gosainganj', 'Chinhat'],
            'Kanpur Nagar' => ['Kanpur City', 'Bilhaur', 'Ghatampur', 'Sarsaul', 'Akbarpur'],
            'Agra' => ['Agra', 'Fatehabad', 'Kheragarh', 'Kiraoli', 'Bah'],
            
            // West Bengal
            'Kolkata' => ['Alipore', 'Behala', 'Bhowanipore', 'Entally', 'Park Street', 'Tollygunge'],
            'North 24 Parganas' => ['Barasat', 'Barrackpore', 'Bidhannagar', 'Basirhat', 'Bongaon', 'Dum Dum'],
            'Howrah' => ['Howrah', 'Bally', 'Domjur', 'Jagatballavpur', 'Panchla', 'Sankrail'],
            
            // Rajasthan
            'Jaipur' => ['Jaipur City', 'Amber', 'Chomu', 'Phagi', 'Sanganer', 'Shahpura', 'Viranagar'],
            'Jodhpur' => ['Jodhpur City', 'Bilara', 'Luni', 'Osian', 'Phalodi', 'Shergarh', 'Bhopalgarh'],
            'Udaipur' => ['Udaipur', 'Girwa', 'Kherwara', 'Rishabhdeo', 'Salumber', 'Sarada', 'Vallabhnagar'],
            
            // Madhya Pradesh
            'Bhopal' => ['Bhopal', 'Huzur', 'Berasia', 'Phanda'],
            'Indore' => ['Indore', 'Depalpur', 'Hatod', 'Mhow', 'Sawer'],
            'Gwalior' => ['Gwalior', 'Bhitarwar', 'Dabra', 'Ghatigaon'],
            
            // Andhra Pradesh
            'Visakhapatnam' => ['Visakhapatnam', 'Anakapalle', 'Bheemunipatnam', 'Chodavaram', 'Gajuwaka', 'Paderu'],
            'Vijayawada' => ['Vijayawada', 'Gannavaram', 'Ibrahimpatnam', 'Kanuru', 'Tadigadapa'],
            'Guntur' => ['Guntur', 'Bapatla', 'Mangalagiri', 'Ponnur', 'Repalle', 'Tadepalle'],
            
            // Telangana
            'Hyderabad' => ['Charminar', 'Himayathnagar', 'Khairatabad', 'Secunderabad', 'Serilingampally', 'Shaikpet'],
            'Warangal Urban' => ['Warangal', 'Hanamkonda', 'Kazipet'],
            'Karimnagar' => ['Karimnagar', 'Huzurabad', 'Jagtial', 'Manthani', 'Sircilla'],
            
            // Kerala
            'Thiruvananthapuram' => ['Thiruvananthapuram', 'Neyyattinkara', 'Parassala', 'Kattakada', 'Chirayinkeezhu'],
            'Kochi' => ['Kochi', 'Kanayannur', 'Kothamangalam', 'Muvattupuzha', 'Paravur'],
            'Kozhikode' => ['Kozhikode', 'Koyilandy', 'Thamarassery', 'Vadakara'],
            
            // Punjab
            'Amritsar' => ['Amritsar-I', 'Amritsar-II', 'Ajnala', 'Attari', 'Baba Bakala', 'Majitha'],
            'Ludhiana' => ['Ludhiana East', 'Ludhiana West', 'Jagraon', 'Khanna', 'Payal', 'Raikot'],
            'Jalandhar' => ['Jalandhar-I', 'Jalandhar-II', 'Adampur', 'Bhogpur', 'Nakodar', 'Phillaur'],
            
            // Haryana
            'Gurugram' => ['Gurugram', 'Badshahpur', 'Farrukhnagar', 'Pataudi', 'Sohna'],
            'Faridabad' => ['Faridabad', 'Ballabgarh', 'Tigaon'],
            'Panipat' => ['Panipat', 'Israna', 'Samalkha'],
            
            // Bihar
            'Patna' => ['Patna', 'Barh', 'Danapur', 'Dinapur-Cum-Khagaul', 'Masaurhi', 'Paliganj'],
            'Gaya' => ['Gaya', 'Atri', 'Bodh Gaya', 'Fatehpur', 'Konch', 'Nagar Nausa'],
            'Muzaffarpur' => ['Muzaffarpur', 'Bochaha', 'Kanti', 'Kurhani', 'Marwan', 'Minapur'],
            
            // Odisha
            'Bhubaneswar' => ['Bhubaneswar', 'Jatni', 'Khurda'],
            'Cuttack' => ['Cuttack', 'Banki', 'Baramba', 'Kandarpur', 'Niali', 'Tigiria'],
            'Rourkela' => ['Rourkela', 'Bisra', 'Bondamunda', 'Lathikata'],
            
            // Assam
            'Kamrup Metropolitan' => ['Guwahati', 'Dispur', 'Sonapur'],
            'Jorhat' => ['Jorhat', 'Teok', 'Titabor'],
            'Dibrugarh' => ['Dibrugarh', 'Chabua', 'Moran'],
            
            // Jharkhand
            'Ranchi' => ['Ranchi', 'Angara', 'Bero', 'Burmu', 'Kanke', 'Lapung', 'Namkum'],
            'Jamshedpur' => ['Jamshedpur', 'Golmuri-Cum-Jugsalai', 'Potka'],
            'Dhanbad' => ['Dhanbad', 'Baghmara', 'Govindpur', 'Jharia', 'Nirsa'],
            
            // Chhattisgarh
            'Raipur' => ['Raipur', 'Abhanpur', 'Arang', 'Tilda', 'Pallari'],
            'Bhilai' => ['Bhilai', 'Bhatapara', 'Baloda Bazar'],
            'Bilaspur' => ['Bilaspur', 'Lormi', 'Takhatpur'],
            
            // Himachal Pradesh
            'Shimla' => ['Shimla', 'Jubbal', 'Kotkhai', 'Rohru', 'Theog'],
            'Kangra' => ['Kangra', 'Baijnath', 'Dehra', 'Jawali', 'Nurpur'],
            'Mandi' => ['Mandi', 'Bali Chowki', 'Sarkaghat', 'Sundernagar'],
            
            // Uttarakhand
            'Dehradun' => ['Dehradun', 'Chakrata', 'Kalsi', 'Rishikesh', 'Vikasnagar'],
            'Haridwar' => ['Haridwar', 'Bahadrabad', 'Laksar', 'Narsan', 'Roorkee'],
            'Nainital' => ['Nainital', 'Haldwani', 'Kaladhungi', 'Kosya Kutauli'],
            
            // Goa
            'North Goa' => ['Bardez', 'Bicholim', 'Pernem', 'Satari', 'Tiswadi'],
            'South Goa' => ['Canacona', 'Mormugao', 'Quepem', 'Sanguem', 'Salcete'],
            
            // Delhi
            'Central Delhi' => ['Karol Bagh', 'Parliament Street', 'Darya Ganj'],
            'New Delhi' => ['Connaught Place', 'Parliament Street', 'Chanakyapuri'],
            'North Delhi' => ['Civil Lines', 'Model Town', 'Narela', 'Saraswati Vihar'],
        ];

        foreach ($talukas as $districtName => $talukaNames) {
            $district = District::where('name', $districtName)->first();
            
            if ($district) {
                foreach ($talukaNames as $talukaName) {
                    // Check if taluka already exists to avoid duplicates
                    $existingTaluka = Taluka::where('name', $talukaName)
                        ->where('district_id', $district->id)
                        ->first();
                    
                    if (!$existingTaluka) {
                        Taluka::create([
                            'name' => $talukaName,
                            'district_id' => $district->id,
                            'is_active' => true,
                            'user_id' => 1,
                        ]);
                    }
                }
            }
        }
    }
}

