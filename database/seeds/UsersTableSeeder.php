<?php
use Illuminate\Database\Seeder;
use App\User;
use App\TmsOrganizationAddresses;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        User::create([
//            'email' => 'admin@gmail.com',
//            'name' => 'Jane Doe',
//            'role' => 'admin',
//            'password' => bcrypt('admin@123')
//        ]);
//
//        User::create([
//            'email' => 'shane@laraspace.in',
//            'name' => 'Shane White',
//            'role' => 'user',
//            'password' => bcrypt('hank@123')
//        ]);
//
//        User::create([
//            'email' => 'adam@laraspace.in',
//            'name' => 'Adam David',
//            'role' => 'user',
//            'password' => bcrypt('jesse@123')
//        ]);


        //seeds tms_organization_addresses
        #region PHH
        TmsOrganizationAddresses::create([
            'organization_id' => '',
            'country' => 'VIETNAM',
            'name' => 'PHH GROUP',
            'address' => 'C/o ATS Hotel, Suites 326 & 327 33b Pham Ngu Lao Street, Hanoi City - Vietnam',
            'tel' => '+84-24 39 33 13 62',
            'fax' => '+84-24 39 33 13 07'
        ]);
        #endregion

        #region EASIA
        //CAMBODIA
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'CAMBODIA',
            'name' => 'SIEM REAP OFFICE',
            'address' => 'Charming City, No. R32 -R34, Roluos Street, Trorpeangses Village, Sangkat Koukchork, Siem Reap',
            'tel' => '',
            'fax' => ''
        ]);
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'CAMBODIA',
            'name' => 'PHNOM PENH OFFICE',
            'address' => 'Rooms 501 & 502, #60 Monivong Blvd Phnom Penh, Cambodia',
            'tel' => '',
            'fax' => ''
        ]);
        //LAOS
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'LAOS',
            'name' => 'LUANG PRABANG OFFICE',
            'address' => 'Ban Visoun, Unit 07 - P.O. BOX 095 Blvd Luang Prabang Lao P.D.R.',
            'tel' => '',
            'fax' => ''
        ]);
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'LAOS',
            'name' => 'VIENTIANE OFFICE',
            'address' => 'Ban Sisavat Kang, Chanta Boury District, Vientiane Capital Laos P.D.R',
            'tel' => '',
            'fax' => ''
        ]);
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'LAOS',
            'name' => 'PAKSE OFFICE',
            'address' => 'Ban Houyangkham, Road no:13south, Pakse district, Champasak province Lao PDR',
            'tel' => '',
            'fax' => ''
        ]);
        //MYANMAR
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'MYANMAR',
            'name' => 'YANGON OFFICE',
            'address' => 'No. 19(B)3-4, Kanbawza Street, Bahan Township, Yangon, Myanmar',
            'tel' => '',
            'fax' => ''
        ]);
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'MYANMAR',
            'name' => 'MANDALAY OFFICE',
            'address' => 'Block(16), Daw Na Bwar Qtr., Yadanar Moe Lane, Cnr. of 60th & 18th Street, Aung Myae Tharzan Township Mandalay, Myanmar',
            'tel' => '',
            'fax' => ''
        ]);
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'MYANMAR',
            'name' => 'BAGAN OFFICE',
            'address' => 'No. 4/C, Taik Kone Quarter, Nyaung U Township Myanmar',
            'tel' => '',
            'fax' => ''
        ]);
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'MYANMAR',
            'name' => 'INLE OFFICE',
            'address' => 'Thousand Island Hotel, 1st Floor, Room no. 104, Phaung Daw Side Street and Corner of Kan Nar Street, Nyaung Shwe Township, Southern Shan State, Myanmar',
            'tel' => '',
            'fax' => ''
        ]);
        //THAILAND
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'THAILAND',
            'name' => 'BANGKOK OFFICE',
            'address' => '140 One Pacific Place Building, 9th Floor, Unit 908, Sukhumvit Road, Sub District Klongtoey, District Klongtoey, Bangkok 10110 Thailand.',
            'tel' => '',
            'fax' => ''
        ]);
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'THAILAND',
            'name' => 'CHIANG MAI OFFICE',
            'address' => '174/3 Sakulchai Building, 7th Floor, Chang Klan Road T.Chang Klan A.Muang, Chiang Mai, Thailand',
            'tel' => '',
            'fax' => ''
        ]);
        //VIETNAM
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'VIETNAM',
            'name' => 'VIETNAM HEAD OFFICE',
            'address' => 'ATS Hotel, Suites 326 & 327, 33b Pham Ngu Lao Street, Hanoi, Vietnam',
            'tel' => '',
            'fax' => ''
        ]);
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'VIETNAM',
            'name' => 'DANANG OFFICE',
            'address' => '66 Nguyen Du Street, Hai Chau District, Da Nang City, Vietnam',
            'tel' => '',
            'fax' => ''
        ]);
        TmsOrganizationAddresses::create([
            'organization_id' => '8',
            'country' => 'VIETNAM',
            'name' => 'HO CHI MINH CITY OFFICE',
            'address' => '154, D1 Street, Him Lam New Urban, Tan Hung Ward, 7th District, Ho Chi Minh City, Vietnam',
            'tel' => '',
            'fax' => ''
        ]);
        #endregion

        #region AVANA
        TmsOrganizationAddresses::create([
            'organization_id' => '42',
            'country' => 'VIETNAM',
            'name' => '',
            'address' => 'Pieng Ve, Mai Chau District, Hoa Binh Province ',
            'tel' => '',
            'fax' => ''
        ]);
        #endregion

        #region EXOTIC
        TmsOrganizationAddresses::create([
            'organization_id' => '34',
            'country' => 'VIETNAM',
            'name' => 'VIETNAM HEAD OFFICE',
            'address' => 'ATS Hotel, Suites 326 & 327, 33b Pham Ngu Lao Street, Hanoi, Vietnam',
            'tel' => '',
            'fax' => ''
        ]);
        TmsOrganizationAddresses::create([
            'organization_id' => '34',
            'country' => 'VIETNAM',
            'name' => 'THAILAND',
            'address' => 'No. 11/1 AIA Sathorn Tower, 10th Floor, Room No. S10001 South Sathorn Road, Yannawa, Sathorn, Bangkok 10120',
            'tel' => '',
            'fax' => ''
        ]);
        #endregion

        #region BEGODI
        TmsOrganizationAddresses::create([
            'organization_id' => '26',
            'country' => 'VIETNAM',
            'name' => '',
            'address' => ' 33b Pham Ngu Lao Street, Hanoi, Vietnam',
            'tel' => '',
            'fax' => ''
        ]);
        #endregion
    }
}
