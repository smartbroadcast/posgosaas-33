<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommonEmailTemplate extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $settings;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template, $settings)
    {
        $this->template = $template;
        $this->settings = $settings;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd( $this->template,$this->settings);
        if(\Auth::user()->isSuperAdmin()){
            $from = env('MAIL_FROM_ADDRESS');

            return $this->from(env('MAIL_FROM_ADDRESS'), $from)->markdown('emails.common_email_template')->subject($this->template->subject)->with(
                [
                    'content' => $this->template->content,
                    'mail_header' => env('APP_NAME'),
                ]
            );
        }else{
            $from = !empty($this->settings['company_email_from_name']) ? $this->settings['company_email_from_name'] : $this->template->from;

            return $this->from($this->settings['company_email'], $from)->markdown('emails.common_email_template')->subject($this->template->subject)->with(
                [
                    'content' => $this->template->content,
                    'mail_header' => $this->settings['company_name'],
                ]
            );
        }
       
    }
}