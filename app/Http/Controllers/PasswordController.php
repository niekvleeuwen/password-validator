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

        // check if password is pwnd
        $breaches = $this->checkIfPasswordPwnd($password);
        if($breaches > 0) 
        {
            array_push($errors, "Password found in " . $breaches . " breach(es).");
        }

        $duration = $this->estimateBruteForceTime($password);

        // check if the password takes less than 10 years to crack
        if($duration < (60*60*24*365*10))
        {
            array_push($errors, "Estimated brute force time is " . $this->formatDuration($duration));
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

    /**
     * Estimate how long it takes to brute force a password
     *
     * Duration in seconds
     * @return int
     */
    private function estimateBruteForceTime($password)
    {
        $searchSpaceDepth = 0;

        // Numbers -> 10 options (0-9)
        if(preg_match("#[0-9]+#", $password)) $searchSpaceDepth+=10;
        
        // Characters -> 26 options (Alphabet)
        if(preg_match("#[a-z]+#", $password)) $searchSpaceDepth+=26;
        
        // Upper case -> 26 options (ALPHABET)
        if(preg_match("#[A-Z]+#", $password)) $searchSpaceDepth+=26;
        
        // Symbols -> 33 options
        if(preg_match("#\W+#", $password)) $searchSpaceDepth+=33;

        $searchSpaceLength = strlen($password);

        $searchSpaceSize = 0;
        for($i=1; $i <= $searchSpaceLength; $i++)
        {
            $searchSpaceSize += $searchSpaceDepth**$i;
        }

        // TODO: this number varies for each hashing algorithm
        // 100 billion tries per second
        $triesPerSecond = 100000000000;

        return $searchSpaceSize / $triesPerSecond;
    }

    /**
     * Format duration from seconds to a readable format ranging from nanoseconds to trillions of centuries
     *
     * Readable duration
     * @return String
     */
    private function formatDuration($seconds)
    {
        $round_precision = 2;

        // return in seconds
        if($seconds < 1) return rtrim(rtrim(number_format($seconds, 20),'0'),'.') . " seconds";
        if($seconds < 60) return round($seconds,$round_precision). " seconds";

        // return in minutes
        if($seconds < (60*60)) return round($seconds/60,$round_precision) . " minutes";

        // return in hours
        if($seconds < (60*60*60)) return round($seconds/60/60,$round_precision) . " hours";

        // return in days
        if($seconds < (60*60*60*365)) return round($seconds/60/60/60,$round_precision) . " days";

        // return in years
        if($seconds < (60*60*24*365*100)) return round($seconds/60/60/24/365,$round_precision) . " years";

        // return in centuries
        $centuries = $seconds/60/60/24/365/100;
        if($centuries < 1000) return round($centuries,$round_precision) . " centuries"; 
        if($centuries < 1000000) return round($centuries*1000,$round_precision) . " thousand centuries";
        if($centuries < 100000000000) return round($centuries*1000000,$round_precision) . " million centuries";
        if($centuries < 100000000000000) return round($centuries*100000000000,$round_precision) . " billion centuries";
        
        return round($centuries*100000000000000,$round_precision) . " trillion centuries"; 
    }
}
