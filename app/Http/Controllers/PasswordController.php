<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Password;

class PasswordController extends Controller
{
    public function makePassword()
    {
        function generatePassword($length = 8){
            $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
            $numChars = strlen($chars);
            $string = '';
            for ($i = 0; $i < $length; $i++) {
                $string .= substr($chars, rand(1, $numChars) - 1, 1);
            }
            return $string;
        }
        Password::truncate();
        
        $randomString = generatePassword(12);
        
        $pass = new Password();
        $pass->password = password_hash($randomString, PASSWORD_BCRYPT, ['cost' => 12]);
        $pass->save();

        return $randomString;
    }
}
