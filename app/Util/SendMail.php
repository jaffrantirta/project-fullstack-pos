<?php
namespace App\Util;
use App\Mail\Mail;
class SendMail {
    public function index(){
		Mail::to("franartika@gmail.com")->send(new Mail());
		return "Email telah dikirim";
 
	}
}