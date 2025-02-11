<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return void
     * @param NA
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return void
     * @param NA
     */
    public function handleGoogleCallback()
    {
            $timeStamp = Carbon::now();
            $userDetailes = Socialite::driver('google')->user(); //getting google user data
            $user = User::where('email', $userDetailes->email)->first();  //Checking in the user table where retrieved google id is available in the database
            
       
            //Checking if the user exisits in the database and email is verified
            if($user){
                if($user->email_verified_at){
                    Auth::login($user); //If the user exisits authenticate him/her
                    return redirect()->route('dashboard');  //redirecting authenticated user to the dashboard/ Home page
                }

                //If the user exisits but email is not verified, then we need to update the email_verified_at column in the database
                if(!$user->email_verified_at){
                    $createUser = User::updateOrCreate(
                        ['email' => $userDetailes->email], // Find the user by email
                        [
                            'name' => $userDetailes->name,
                            'password' => Hash::make('pass21345'),
                            'google_id' => $userDetailes->id,
                            'login_method' => "google",
                            'email_verified_at' => $timeStamp,
                        ]
                    );
                    
                    //If the email_verified_at column is updated, then login the user
                    if($createUser){   //if data is created in the database, loginin the use
                        Auth::login($createUser ); 
                        return redirect()->route('dashboard');  //redirecting authenticated user to the dashboard/ Home page
                    }
                }
            }

             else{  //If the user id doesn't exisist in the google id, then we need to register that user and insert his data in the database
    
               $createUser = User::updateOrCreate([         //Adding the user into the database
                    'name' => $userDetailes -> name,
                    'email' => $userDetailes -> email,
                    'password' => Hash::make('pass21345'),
                    'google_id' => $userDetailes -> id,
                    'login_method' => "google",
                    'email_verified_at' => $timeStamp,

                ]);
    
    
                if($createUser){   //if data is created in the database, loginin the use
                    Auth::login($createUser ); 
                    return redirect()->route('dashboard');  //redirecting authenticated user to the dashboard/ Home page
                }
    
             }
  

    }

//function to redirect to Github
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    //function to handle Github callback
    public function handleGithubCallback()
    {
        $timeStamp = Carbon::now();
        $userDetailes = Socialite::driver('github')->user(); //getting github user data
        $user = User::where('email', $userDetailes->email)->first();  //Checking in the user table where retrieved github id is available in the database
        $userID = User::where('github_id', $userDetailes->id)->first();
   
        //Checking if the user exisits in the database and email is verified
        if($user || $userID){
            if($user->email_verified_at){
                Auth::login($userID); //If the user exisits authenticate him/her
                return redirect()->route('dashboard');  //redirecting authenticated user to the dashboard/ Home page
            }

            //If the user exisits but email is not verified, then we need to update the email_verified_at column in the database
            if(!$userID->email_verified_at){
                $createUser = User::updateOrCreate(
                    ['github_id' => $userDetailes->id], // Find the user by email
                    [
                        'name' => $userDetailes->name,
                        'password' => Hash::make('pass21345'),
                        'github_id' => $userDetailes->id,
                        'login_method' => "github",
                        'email_verified_at' => $timeStamp,
                    ]
                );
                
                //If the email_verified_at column is updated, then login the user
                if($createUser){   //if data is created in the database, loginin the use
                    Auth::login($createUser ); 
                    return redirect()->route('dashboard');  //redirecting authenticated user to the dashboard/ Home page
                }
            }
        }

         else{  //If the user id doesn't exist in the github id, then we need to register that user and insert his data in the database

           $createUser = User::updateOrCreate([         //Adding the user into the database
                'name' => $userDetailes -> name,
                'email' => $userDetailes -> email,
                'password' => Hash::make('pass21345'),
                'github_id' => $userDetailes -> id,
                'login_method' => "github",
                'email_verified_at' => $timeStamp,
            ]);

                    //If the email_verified_at column is updated, then login the user
                    if($createUser){   //if data is created in the database, loginin the use
                        Auth::login($createUser ); 
                        return redirect()->route('dashboard');  //redirecting authenticated user to the dashboard/ Home page
                    }
        }
    }
}


