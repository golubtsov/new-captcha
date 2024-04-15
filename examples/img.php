<?php

require_once "vendor/autoload.php";

use Nigo\CaptchaImg\Captcha\Captcha;

$captcha = new Captcha();
$captcha
    ->fill(200, 200, 200)
    ->save(__DIR__ . '/captcha.png');


use Nigo\CaptchaImg\Captcha\ReCaptcha;

$recaptcha = new ReCaptcha();
echo $recaptcha->create();

