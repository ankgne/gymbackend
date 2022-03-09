<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EmailServices
{
    public static function appName()
    {
        return env("APP_NAME", "Gym app");
    }

    public static function sendRegistationMail($data)
    {
        $to_name = array('Ankur Khurana', 'Kulbhushan Khurana');
        $to_email = array('ankgne@gmail.com', 'kulbhushan2407@gmail.com');
        $subject = 'Welcome to ' . self::appName();

        Mail::send(['html' => 'emails.registration'], $data, function ($message) use ($to_name, $to_email, $subject) {
            $message->to($to_email, $to_name)
                ->subject($subject);
            $message->from('ankursmtp@gmail.com', self::appName());
        });
    }
}
