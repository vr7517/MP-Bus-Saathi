<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            ['code' => 'BPL', 'name_en' => 'Bhopal', 'name_hi' => 'भोपाल', 'division' => 'Bhopal', 'lat' => 23.2599, 'lng' => 77.4126],
            ['code' => 'IND', 'name_en' => 'Indore', 'name_hi' => 'इंदौर', 'division' => 'Indore', 'lat' => 22.7196, 'lng' => 75.8577],
            ['code' => 'JBL', 'name_en' => 'Jabalpur', 'name_hi' => 'जबलपुर', 'division' => 'Jabalpur', 'lat' => 23.1815, 'lng' => 79.9864],
            ['code' => 'GWL', 'name_en' => 'Gwalior', 'name_hi' => 'ग्वालियर', 'division' => 'Gwalior', 'lat' => 26.2183, 'lng' => 78.1828],
            ['code' => 'UJN', 'name_en' => 'Ujjain', 'name_hi' => 'उज्जैन', 'division' => 'Ujjain', 'lat' => 23.1765, 'lng' => 75.7885],
            ['code' => 'SGR', 'name_en' => 'Sagar', 'name_hi' => 'सागर', 'division' => 'Sagar', 'lat' => 23.8388, 'lng' => 78.7378],
            ['code' => 'REW', 'name_en' => 'Rewa', 'name_hi' => 'रीवा', 'division' => 'Rewa', 'lat' => 24.5362, 'lng' => 81.3037],
            ['code' => 'STN', 'name_en' => 'Satna', 'name_hi' => 'सतना', 'division' => 'Rewa', 'lat' => 24.6005, 'lng' => 80.8322],
            ['code' => 'RTM', 'name_en' => 'Ratlam', 'name_hi' => 'रतलाम', 'division' => 'Ujjain', 'lat' => 23.3315, 'lng' => 75.0367],
            ['code' => 'CHH', 'name_en' => 'Chhindwara', 'name_hi' => 'छिंदवाड़ा', 'division' => 'Jabalpur', 'lat' => 22.0574, 'lng' => 78.9382],
            ['code' => 'NRM', 'name_en' => 'Narmadapuram', 'name_hi' => 'नर्मदापुरम', 'division' => 'Narmadapuram', 'lat' => 22.7519, 'lng' => 77.7289],
            ['code' => 'DEW', 'name_en' => 'Dewas', 'name_hi' => 'देवास', 'division' => 'Ujjain', 'lat' => 22.9676, 'lng' => 76.0534],
            ['code' => 'SEH', 'name_en' => 'Sehore', 'name_hi' => 'सीहोर', 'division' => 'Bhopal', 'lat' => 23.2005, 'lng' => 77.0833],
            ['code' => 'VID', 'name_en' => 'Vidisha', 'name_hi' => 'विदिशा', 'division' => 'Bhopal', 'lat' => 23.5251, 'lng' => 77.8081],
            ['code' => 'RAI', 'name_en' => 'Raisen', 'name_hi' => 'रायसेन', 'division' => 'Bhopal', 'lat' => 23.3315, 'lng' => 77.7816],
            ['code' => 'RAJ', 'name_en' => 'Rajgarh', 'name_hi' => 'राजगढ़', 'division' => 'Bhopal', 'lat' => 24.0064, 'lng' => 76.7299],
            ['code' => 'BET', 'name_en' => 'Betul', 'name_hi' => 'बैतूल', 'division' => 'Narmadapuram', 'lat' => 21.9015, 'lng' => 77.9018],
            ['code' => 'HAR', 'name_en' => 'Harda', 'name_hi' => 'हरदा', 'division' => 'Narmadapuram', 'lat' => 22.3444, 'lng' => 77.0989],
            ['code' => 'KHA', 'name_en' => 'Khandwa', 'name_hi' => 'खंडवा (पूर्वी निमाड़)', 'division' => 'Indore', 'lat' => 21.8314, 'lng' => 76.3498],
            ['code' => 'KHR', 'name_en' => 'Khargone', 'name_hi' => 'खरगोन (पश्चिमी निमाड़)', 'division' => 'Indore', 'lat' => 21.8236, 'lng' => 75.6174],
            ['code' => 'BAR', 'name_en' => 'Barwani', 'name_hi' => 'बड़वानी', 'division' => 'Indore', 'lat' => 22.0366, 'lng' => 74.9034],
            ['code' => 'BUR', 'name_en' => 'Burhanpur', 'name_hi' => 'बुरहानपुर', 'division' => 'Indore', 'lat' => 21.3145, 'lng' => 76.2299],
            ['code' => 'DHA', 'name_en' => 'Dhar', 'name_hi' => 'धार', 'division' => 'Indore', 'lat' => 22.5986, 'lng' => 75.2974],
            ['code' => 'JHA', 'name_en' => 'Jhabua', 'name_hi' => 'झाबुआ', 'division' => 'Indore', 'lat' => 22.7699, 'lng' => 74.5947],
            ['code' => 'ALI', 'name_en' => 'Alirajpur', 'name_hi' => 'आलीराजपुर', 'division' => 'Indore', 'lat' => 22.3044, 'lng' => 74.3541],
            ['code' => 'MAN', 'name_en' => 'Mandsaur', 'name_hi' => 'मंदसौर', 'division' => 'Ujjain', 'lat' => 24.0722, 'lng' => 75.0694],
            ['code' => 'NEE', 'name_en' => 'Neemuch', 'name_hi' => 'नीमच', 'division' => 'Ujjain', 'lat' => 24.4764, 'lng' => 74.8727],
            ['code' => 'AGA', 'name_en' => 'Agar Malwa', 'name_hi' => 'आगर मालवा', 'division' => 'Ujjain', 'lat' => 23.7144, 'lng' => 76.0189],
            ['code' => 'SHA', 'name_en' => 'Shajapur', 'name_hi' => 'शाजापुर', 'division' => 'Ujjain', 'lat' => 23.4288, 'lng' => 76.2764],
            ['code' => 'GUN', 'name_en' => 'Guna', 'name_hi' => 'गुना', 'division' => 'Gwalior', 'lat' => 24.6475, 'lng' => 77.3116],
            ['code' => 'ASH', 'name_en' => 'Ashoknagar', 'name_hi' => 'अशोकनगर', 'division' => 'Gwalior', 'lat' => 24.5772, 'lng' => 77.7289],
            ['code' => 'SHI', 'name_en' => 'Shivpuri', 'name_hi' => 'शिवपुरी', 'division' => 'Gwalior', 'lat' => 25.4244, 'lng' => 77.6616],
            ['code' => 'DAT', 'name_en' => 'Datia', 'name_hi' => 'दतिया', 'division' => 'Gwalior', 'lat' => 25.6698, 'lng' => 78.4619],
            ['code' => 'SHE', 'name_en' => 'Sheopur', 'name_hi' => 'श्योपुर', 'division' => 'Chambal', 'lat' => 25.6687, 'lng' => 76.6961],
            ['code' => 'MOR', 'name_en' => 'Morena', 'name_hi' => 'मुरैना', 'division' => 'Chambal', 'lat' => 26.4947, 'lng' => 77.9944],
            ['code' => 'BHI', 'name_en' => 'Bhind', 'name_hi' => 'भिंड', 'division' => 'Chambal', 'lat' => 26.5644, 'lng' => 78.7885],
            ['code' => 'TIK', 'name_en' => 'Tikamgarh', 'name_hi' => 'टीकमगढ़', 'division' => 'Sagar', 'lat' => 24.7454, 'lng' => 78.8322],
            ['code' => 'NIW', 'name_en' => 'Niwari', 'name_hi' => 'निवाड़ी', 'division' => 'Sagar', 'lat' => 25.3614, 'lng' => 78.6756],
            ['code' => 'CHT', 'name_en' => 'Chhatarpur', 'name_hi' => 'छतरपुर', 'division' => 'Sagar', 'lat' => 24.9164, 'lng' => 79.5946],
            ['code' => 'DAM', 'name_en' => 'Damoh', 'name_hi' => 'दमोह', 'division' => 'Sagar', 'lat' => 23.8323, 'lng' => 79.4422],
            ['code' => 'PAN', 'name_en' => 'Panna', 'name_hi' => 'पन्ना', 'division' => 'Sagar', 'lat' => 24.7214, 'lng' => 80.1989],
            ['code' => 'KAT', 'name_en' => 'Katni', 'name_hi' => 'कटनी', 'division' => 'Jabalpur', 'lat' => 23.8343, 'lng' => 80.3957],
            ['code' => 'NAR', 'name_en' => 'Narsinghpur', 'name_hi' => 'नरसिंहपुर', 'division' => 'Jabalpur', 'lat' => 22.9467, 'lng' => 79.1989],
            ['code' => 'SEO', 'name_en' => 'Seoni', 'name_hi' => 'सिवनी', 'division' => 'Jabalpur', 'lat' => 22.0864, 'lng' => 79.5489],
            ['code' => 'BAL', 'name_en' => 'Balaghat', 'name_hi' => 'बालाघाट', 'division' => 'Jabalpur', 'lat' => 21.8144, 'lng' => 80.1856],
            ['code' => 'MDL', 'name_en' => 'Mandla', 'name_hi' => 'मंडला', 'division' => 'Jabalpur', 'lat' => 22.5989, 'lng' => 80.3714],
            ['code' => 'DIN', 'name_en' => 'Dindori', 'name_hi' => 'डिंडोरी', 'division' => 'Jabalpur', 'lat' => 22.9514, 'lng' => 81.0822],
            ['code' => 'SHD', 'name_en' => 'Shahdol', 'name_hi' => 'शहडोल', 'division' => 'Shahdol', 'lat' => 23.2989, 'lng' => 81.3544],
            ['code' => 'UMA', 'name_en' => 'Umaria', 'name_hi' => 'उमरिया', 'division' => 'Shahdol', 'lat' => 23.5244, 'lng' => 80.8356],
            ['code' => 'ANU', 'name_en' => 'Anuppur', 'name_hi' => 'अनूपपुर', 'division' => 'Shahdol', 'lat' => 23.1044, 'lng' => 81.6914],
            ['code' => 'SID', 'name_en' => 'Sidhi', 'name_hi' => 'सीधी', 'division' => 'Rewa', 'lat' => 24.4044, 'lng' => 81.8789],
            ['code' => 'SNG', 'name_en' => 'Singrauli', 'name_hi' => 'सिंगरौली', 'division' => 'Rewa', 'lat' => 24.1997, 'lng' => 82.6645],
            ['code' => 'MAI', 'name_en' => 'Maihar', 'name_hi' => 'मैहर', 'division' => 'Rewa', 'lat' => 24.2694, 'lng' => 80.7589],
            ['code' => 'MAU', 'name_en' => 'Mauganj', 'name_hi' => 'मऊगंज', 'division' => 'Rewa', 'lat' => 24.6856, 'lng' => 81.8689],
            ['code' => 'PAN_D', 'name_en' => 'Pandhurna', 'name_hi' => 'पांढुर्णा', 'division' => 'Jabalpur', 'lat' => 21.5989, 'lng' => 78.5289],
        ];

        foreach ($districts as $d) {
            District::updateOrCreate(
                ['code' => $d['code']],
                [
                    'name_en' => $d['name_en'],
                    'name_hi' => $d['name_hi'],
                    'division' => $d['division'],
                    'latitude' => $d['lat'],
                    'longitude' => $d['lng'],
                    'is_active' => true,
                ]
            );
        }
    }
}
