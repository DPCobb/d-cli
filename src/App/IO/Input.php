<?php

namespace App\IO;

use App\IO\Output;

class Input
{
    public static function get(string $message)
    {
        Output::message($message);
        return readline();
    }

    public static function affirm(string $message): bool
    {
        Output::message($message);
        $response = readline();

        preg_match("/^(y|Y|yes|YES|Yes|True|true)\b/m", $response, $match);
        
        return !empty($match);
    }

    public static function warnAffirm(string $message, array $required = []): bool
    {
        Output::warning($message);
        $response = readline();

        if (!empty($required)) {
            if (!in_array($response, $required)) {
                $second_try = Input::get("Please enter one of the following answers: " . implode('/', $required));
                if (!in_array($second_try, $required)) {
                    Output::error('Your answer could not be processed. Exiting now.');
                    die;
                }
            }
        }

        preg_match("/^(y|Y|yes|YES|Yes|True|true)\b/m", $response, $match);
        
        return !empty($match);
    }
}
