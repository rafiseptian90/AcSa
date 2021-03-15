<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $apps = [
            ['app_type_id' => $this->getType('socmed'), 'name' => 'Facebook', 'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."],
           ['app_type_id' => $this->getType('socmed'), 'name' => 'Instagram', 'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."],
           ['app_type_id' => $this->getType('socmed'), 'name' => 'Twitter', 'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."],
           ['app_type_id' => $this->getType('game'), 'name' => 'PUBG Mobile', 'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."],
           ['app_type_id' => $this->getType('game'), 'name' => 'Clash of Clans', 'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."],
           ['app_type_id' => $this->getType('game'), 'name' => 'Clash Royale', 'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."],
       ];

       // create app
        foreach($apps as $app){
            Application::create($app);
        }
    }

    public function getType($type){
        // get each type
        $socmed = \App\Models\ApplicationType::whereSlug('social-media')->first();
        $game = \App\Models\ApplicationType::whereSlug('game')->first();
        $other = \App\Models\ApplicationType::whereSlug('other')->first();

        // select type
        if($type === 'socmed'){
            return $socmed->id;
        }
        else if($type === 'game'){
            return $game->id;
        }
        return $other->id;
    }
}
