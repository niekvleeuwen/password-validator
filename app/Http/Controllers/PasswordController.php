<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PasswordController extends Controller
{
    /**
     * Verify whether a password is safe to use
     *
     * Array of errors (empty array means password is good)
     * @return Array
     */
    public function validatePassword(Request $request)
    {
        $errors = [];
        $password = $request->password;

        // validate againts common password password rules
        // TODO: these rules should be based on some guideline
        if(strlen($password) < 8)
        {
            array_push($errors, "Password must be at least 8 characters");
        }

        if(!preg_match("#[0-9]+#", $password)) 
        {
            array_push($errors, "Password must include at least one number");
        }
        
        if(!preg_match("#[a-z]+#", $password)) 
        {
            array_push($errors, "Password must include at least one letter");
        }
        
        if(!preg_match("#[A-Z]+#", $password)) 
        {
            array_push($errors, "Password must include at least one uppercase");
        }
        
        if(!preg_match("#\W+#", $password)) 
        {
            array_push($errors, "Password must include at least one symbol");
        }

        // check if password is pwnd
        $breaches = $this->checkIfPasswordPwnd($password);
        if($breaches > 0) 
        {
            array_push($errors, "Password found in " . $breaches . " breach(es).");
        }

        $result["result"] = (count($errors) == 0) ? True : False;
        $result["errors"] = $errors;
        return json_encode($result);
    }

    /**
     * Check in databases if the password is breached
     *
     * Count of how many times it appears in the data sets
     * @return integer
     */
    private function checkIfPasswordPwnd($password)
    {
        // get first 5 chars of the password
        $hash = sha1($password);
        $firstFiveChars = mb_substr($hash, 0, 5);  

        $response = Http::get('https://api.pwnedpasswords.com/range/' . $firstFiveChars);
        
        if($response->ok())
        {
            $matches=explode("\r\n",$response->body());

            foreach($matches as $match)
            {
                // format: sha1:counts -> 003D68EB55068C33ACE09247EE4C639306B:3
                $match = explode(":", $match);

                // the hash returned in the response is missing the first five chars so we need to prepend that
                $matchHash = $firstFiveChars . $match[0];

                if(strtolower($matchHash) == $hash)
                {
                    // return number of breaches
                    return $match[1];
                }
            }
        }
        return 0;
    }
}
