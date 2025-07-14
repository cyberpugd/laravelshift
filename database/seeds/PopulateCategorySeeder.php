<?php

use Illuminate\Database\Seeder;
use App\Category;
use App\Subcategory;
class PopulateCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          $categories = [
               ['name' => 'Hardware',
                     'subcategories' => [
                         ['name' => 'Desktop / Laptop',
                         'tags' => 'mouse keyboard monitor headset pc display memory',
                         'location_matters' => 1],
                         ['name' => 'Printer',
                          'tags' => 'jam paper toner ink',
                          'location_matters' => 1],
                          ['name' => 'Server',
                           'tags' => 'vm backup restore ram storage memory',
                           'location_matters' => 1]
                     ]
               ],
               ['name' => 'Software', 
                     'subcategories' => [
                         ['name' =>'Microsoft Office',
                          'tags' => 'lync skype word excel visio outlook access project power point',
                          'location_matters' => 1],
                          ['name' =>'Adobe',
                          'tags' => 'acrobat illustrator photoshop reader editor',
                          'location_matters' => 1],
                          ['name' =>'Certichex',
                          'tags' => '',
                          'location_matters' => 0],
                          ['name' =>'Citrix',
                          'tags' => '',
                          'location_matters' => 0],
                          ['name' =>'Oracle',
                          'tags' => 'environment oracle database schema',
                          'location_matters' => 0],
                          ['name' =>'SQL',
                          'tags' => '',
                          'location_matters' => 0],
                          ['name' =>'SumTotal',
                          'tags' => '',
                          'location_matters' => 0],
                          ['name' =>'Unix',
                          'tags' => 'backup restore AIX Linux Red Hat Sun VM virtual machine',
                          'location_matters' => 0],
                          ['name' =>'P2 Hub',
                          'tags' => 'website',
                          'location_matters' => 0],
                          ['name' =>'Microsoft Windows',
                          'tags' => 'backup restore operating system O/S',
                          'location_matters' => 1],
                          ['name' =>'Virus',
                          'tags' => 'blue screen boot',
                          'location_matters' => 1],
                          ['name' =>'Microsoft Dynamics GP',
                          'tags' => '',
                          'location_matters' => 0],
                          ['name' =>'Cornerstone',
                          'tags' => '',
                          'location_matters' => 0],
                          ['name' =>'TFS',
                          'tags' => 'Team Foundation Server Test Manager',
                          'location_matters' => 0],
                          ['name' =>'Visual Studio',
                          'tags' => '',
                          'location_matters' => 0],
                          ['name' =>'DocStar',
                          'tags' => 'docvue',
                          'location_matters' => 0],
                          ['name' =>'AS/400',
                          'tags' => 'prism marvin',
                          'location_matters' => 0],
                          ['name' =>'Reports',
                          'tags' => 'reports.p2es.com',
                          'location_matters' => 0],
                     ]
               ],
               ['name' => 'P2 Applications',
                     'subcategories' => [
                         ['name' => 'BOLO / Excalibur',
                         'tags' => '',
                         'location_matters' => 0],
                         ['name' => 'P2 AFE',
                          'tags' => '',
                          'location_matters' => 0],
                          ['name' => 'P2 Land',
                           'tags' => '',
                           'location_matters' => 0],
                          ['name' => 'P2 Explorer',
                           'tags' => '',
                           'location_matters' => 0],
                           ['name' => 'P2 Merrick',
                           'tags' => '',
                           'location_matters' => 0],
                           ['name' => 'Qbyte Suite',
                           'tags' => '',
                           'location_matters' => 0],
                           ['name' => 'P2 Store',
                           'tags' => '',
                           'location_matters' => 0]
                     ]
               ],
               ['name' => 'Telephone',
                     'subcategories' => [
                         ['name' => 'Connection',
                         'tags' => 'ShoreTel Communicator voicemail cell phone lync skype',
                         'location_matters' => 1],
                         ['name' => 'ShoreTel Admin',
                         'tags' => 'workgroup schedule auto-attendant dial',
                         'location_matters' => 0],
                         ['name' => 'Hardware',
                         'tags' => 'desk set headset hear headphones',
                         'location_matters' => 1],
                     ]
               ],
               ['name' => 'Workplace',
                     'subcategories' => [
                         ['name' => 'Project Code Requests',
                         'tags' => 'project codes GP great plains time timesheets',
                         'location_matters' => 0],
                         ['name' => 'Timesheets / Expenses',
                         'tags' => 'holder',
                         'location_matters' => 1],
                     ]
               ],
               ['name' => 'Human Resources',
                     'subcategories' => [
                         ['name' => 'Account Admin',
                         'tags' => 'new hire contractor extension name change dept department organization termination transfer',
                         'location_matters' => 0],
                     ]
               ],
               ['name' => 'Network',
                     'subcategories' => [
                         ['name' => 'Network settings',
                         'tags' => 'DNS IP firewall VPN',
                         'location_matters' => 0],
                     ]
               ],
               ['name' => 'User ID / Password / Access',
                     'subcategories' => [
                         ['name' => 'Salesforce',
                         'tags' => '',
                         'location_matters' => 0],
                         ['name' => 'UNIX',
                         'tags' => '',
                         'location_matters' => 0],
                         ['name' => 'Network ',
                         'tags' => 'windows active directory office 365 samba folder',
                         'location_matters' => 0],
                         ['name' => 'DocStar',
                         'tags' => 'DocVue  ',
                         'location_matters' => 0],
                         ['name' => 'SharePoint',
                         'tags' => '',
                         'location_matters' => 0],
                         ['name' => 'Oracle',
                         'tags' => '',
                         'location_matters' => 0],
                         ['name' => 'Password Manager Pro',
                         'tags' => '',
                         'location_matters' => 0],
                         ['name' => 'P2 Hosted Portal',
                         'tags' => 'access management online portal online.qbyte.com',
                         'location_matters' => 0],
                         ['name' => 'Telephone',
                         'tags' => 'voicemail communicator ShoreTel',
                         'location_matters' => 0],
                         ['name' => 'Skype',
                         'tags' => '',
                         'location_matters' => 0],
                         ['name' => 'Dynamics GP',
                         'tags' => 'workplace',
                         'location_matters' => 0],
                         ['name' => 'P2 Outsourcing',
                         'tags' => '',
                         'location_matters' => 0],
                         ['name' => 'VM',
                         'tags' => 'virtual machine',
                         'location_matters' => 0],
                         ['name' => 'AS/400',
                         'tags' => '',
                         'location_matters' => 0],
                         ['name' => 'Reports',
                         'tags' => 'ssrs',
                         'location_matters' => 0],
                     ]
               ],
               ['name' => 'Dynamics GP',
                     'subcategories' => [
                         ['name' => 'GP Report',
                         'tags' => 'great plains',
                         'location_matters' => 0],
                         ['name' => 'reports.p2es.com',
                         'tags' => 'great plains',
                         'location_matters' => 0],
                         ['name' => 'Project Access',
                         'tags' => 'great plains',
                         'location_matters' => 0],
                         ['name' => 'Administration',
                         'tags' => 'great plains',
                         'location_matters' => 0],
                         ['name' => 'Error',
                         'tags' => 'great plains',
                         'location_matters' => 0],
                     ]
               ],
               ['name' => 'Salesforce',
                    'subcategories' => [
                         ['name' => 'Administration',
                          'tags' => 'user reporting password versions fields ID aha reopen case portal merge',
                          'location_matters' => 0],
                          ['name' => 'Big Machines',
                          'tags' => '',
                          'location_matters' => 0],
                          ['name' => 'Seismic',
                          'tags' => '',
                          'location_matters' => 0],
                          ['name' => 'Territory Plans',
                          'tags' => '',
                          'location_matters' => 0],
                          ['name' => 'Anaplan',
                          'tags' => '',
                          'location_matters' => 0],
                    ]
               ],
          ];
          foreach($categories as $category) {
               Category::create([
                    'name' => $category['name'],
                    'active' => 1,
               ]);
          }
          $created_categories = Category::all();
          foreach($created_categories as $category) {
               foreach($categories as $currentCategory) {
                    if($currentCategory['name'] == $category->name) {
                         foreach($currentCategory['subcategories'] as $subcategory) {
                              $category->subcategories()->create([
                                   'name' => $subcategory['name'],
                                   'tags' => $subcategory['tags'],
                                   'location_matters' => $subcategory['location_matters'],
                                   'created_by' => 1,
                                   'active' => 1
                              ]);
                         }
                    }
               }
          }
    }
}
