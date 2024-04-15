<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Mail;
use Config;
use Auth;
use DateTime;
use DateTimeZone;

class CronController extends Controller
{

    public function index(){
        //$this->check_booking_expired();
         $this->sqlDump();
    }

    public function check_booking_expired(){
        $date           = new DateTime;
        $date->modify('-24 hours');
        $formatted_date = $date->format('Y-m-d H:i:s');
        $results        = Bookings::where('created_at','<',$formatted_date)->where('status', 'Pending')->get();
        foreach ($results as $result) {
            Bookings::where('id', $result->id)->update(['status' => 'Expired', 'expired_at' => date('Y-m-d H:i:s')]);
        }
    }

    public function reset_data(){
        Artisan::call('db:seed', ['--class' => 'ResetDataSeeder']);
    }

    public function copyImage(){
       
       /* Logos copy start */
       /* $files_logos = scandir(public_path('backup/logos'));
        $source_logos = public_path('backup/logos/');
        $destination_logos = public_path('images/logos/');
            foreach ($files_logos as $file) {
              if (in_array($file, array(".",".."))) continue;
              copy($source_logos.$file, $destination_logos.$file);
            }*/
        /* Logos copy end */

        /* Profile copy start */

       // $profileCoounter = 1;
      /*  for($profile=1; $profile<=10; $profile++){
        $files_profile = scandir(public_path("backup/profile/$profile"));
        $source_profile = public_path("backup/profile/$profile/");
        $destination_profile = public_path("images/profile/$profile/");
            foreach ($files_profile as $file) {
              if (in_array($file, array(".",".."))) continue;
              copy($source_profile.$file, $destination_profile.$file);
            }
        }*/
        /* Profile copy end */

        /* Property copy start */
      /*  for($property=1; $property<=20; $property++){
        $files_property = scandir(public_path("backup/property/$property"));
        $source_property = public_path("backup/property/$property/");
        $destination_property = public_path("images/property/$property/");
            foreach ($files_property as $file) {
              if (in_array($file, array(".",".."))) continue;
              copy($source_property.$file, $destination_property.$file);
            }
        }*/
        /* Property copy end */

       /* starting_city copy start */
       /*
        $files_starting_city = scandir(public_path('backup/starting_city'));
        $source_starting_city = public_path('backup/starting_city/');
        $destination_starting_city = public_path('images/starting_city/');
            foreach ($files_starting_city as $file) {
              if (in_array($file, array(".",".."))) continue;
              copy($source_starting_city.$file, $destination_starting_city.$file);
            }
        */
        /* starting_city copy end */

       /* uploads copy start */
        /*$files_uploads = scandir(public_path('backup/uploads'));
        $source_uploads = public_path('backup/uploads/');
        $destination_uploads = public_path('images/uploads/');
            foreach ($files_uploads as $file) {
              if (in_array($file, array(".",".."))) continue;
              copy($source_uploads.$file, $destination_uploads.$file);
            }*/
        /* uploads copy end */


        ////////////////FOR FRONTEND BACKUP///////////

        /* banners copy start */
        $files_banners = scandir(public_path('frontend_backup/images/banners'));
        $source_images = public_path('frontend_backup/images/banners/');
        $destination_images = public_path('front/images/banners/');

            foreach ($files_banners as $file) {
              if (in_array($file, array(".",".."))) continue;
              copy($source_images.$file, $destination_images.$file);
            }
        /* banners copy end */

        /* logos copy start */
        $files_logos = scandir(public_path('frontend_backup/images/logos'));
        $source_logos = public_path('frontend_backup/images/logos/');
        $destination_logos = public_path('front/images/logos/');

            foreach ($files_logos as $file) {
              if (in_array($file, array(".",".."))) continue;
              copy($source_logos.$file, $destination_logos.$file);
            }
        /* logos copy end */

        /* starting_cities copy start */
        $files_starting_cities = scandir(public_path('frontend_backup/images/starting_cities'));
        $source_starting_cities = public_path('frontend_backup/images/starting_cities/');
        $destination_starting_cities = public_path('front/images/starting_cities/');

            foreach ($files_starting_cities as $file) {
              if (in_array($file, array(".",".."))) continue;
              copy($source_starting_cities.$file, $destination_starting_cities.$file);
            }
        /* starting_cities copy end */

        /* users copy start */
        $files_users = scandir(public_path('frontend_backup/images/users'));
        $source_users = public_path('frontend_backup/images/users/');
        $destination_users = public_path('front/images/users/');

            foreach ($files_users as $file) {
              if (in_array($file, array(".",".."))) continue;
              copy($source_users.$file, $destination_users.$file);
            }
        /* starting_cities copy end */



    }

public function sqlDump(){
      $minutes = 60;
        if($minutes==60){
            try{
                Log::info("Clearing the junk database");
                Artisan::call('migrate:refresh');
                DB::unprepared(file_get_contents('db/vrent.sql'));
            }catch (\Exception $e){
                Log::error($e->getMessage());
            }
        }
    }

}
