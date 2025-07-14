<?php

use Illuminate\Database\Seeder;

class PopulatePlaceholdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $messages = [
               'What about this…',
               'I have the answer...',
               'Well listen to this...',
               "But here's the thing...",
               'I have something to say...',
               'There is just one thing...',
               'How about we try this...',
         ];
         foreach($messages as $message) {
              DB::table('placeholders')->insert([
                    'message' => $message,
               ]);
          }
    }
}
