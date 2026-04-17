<?php

use App\Lib\GoogleAuthenticator;
use App\Lib\SendSms;
use App\Models\EmailTemplate;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\SmsTemplate;
use App\Models\EmailLog;
use App\Models\Referral;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Secure UUID Encoding/Decoding Functions
 * Converts numeric IDs to secure short base64 hashes
 */

function encodeSecureId($id, $prefix = 'u')
{
    // Pad the ID to a fixed length (8 bytes) for consistency
    $padded = str_pad($id, 8, '0', STR_PAD_LEFT);
    
    // Add a random salt for security
    $salt = bin2hex(random_bytes(4));
    
    // Create the hash
    $toEncode = $padded . ':' . $salt;
    
    // Encode to base64 and make URL-safe
    $encoded = rtrim(strtr(base64_encode($toEncode), '+/', '-_'), '=');
    
    // Add prefix for identification
    return $prefix . '_' . $encoded;
}

function decodeSecureId($encoded, $prefix = 'u')
{
    // Remove prefix
    if (strpos($encoded, $prefix . '_') !== 0) {
        return null;
    }
    
    $encoded = substr($encoded, strlen($prefix) + 1);
    
    // Restore base64 padding
    $remainder = strlen($encoded) % 4;
    if ($remainder) {
        $encoded .= str_repeat('=', 4 - $remainder);
    }
    
    // Decode from base64
    $decoded = base64_decode(strtr($encoded, '-_', '+/'), true);
    
    if ($decoded === false) {
        return null;
    }
    
    // Extract the ID
    $parts = explode(':', $decoded);
    $id = (int) trim($parts[0]);
    
    return $id > 0 ? $id : null;
}


function sidebarVariation(){

    /// for sidebar
    $variation['sidebar'] = 'bg_img';

    //for selector
    $variation['selector'] = 'capsule--rounded';

    //for overlay
    $variation['overlay'] = 'overlay--indigo';

    //Opacity
    $variation['opacity'] = 'overlay--opacity-8'; // 1-10

    return $variation;

}

function systemDetails()
{
    $system['name'] = 'Premium Hyip';
    $system['version'] = '1.0';
    return $system;
}


function str_slug($title = null)
{
    return \Illuminate\Support\Str::slug($title);
}



function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}


function shortDescription($string, $length = 120)
{
    return Illuminate\Support\Str::limit($string, $length);
}


function shortCodeReplacer($shortCode, $replace_with, $template_string)
{
    return str_replace($shortCode, $replace_with, $template_string);
}


function verificationCode($length = 6)
{
    // Generate secured OTP: mixed alphanumeric uppercase with length 6-8
    // Ensures strong, random codes for email verification
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    // Default to 6 if not specified, max 8
    $length = max(6, min((int)$length, 8));
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}



//moveable
function uploadImage($file, $location, $size = null, $old = null, $thumb = null)
{
    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $image = Image::make($file);

    if ($size) {
        $size = explode('x', strtolower($size));
        $image->resize($size[0], $size[1]);
    }

    $remoteDisk = remoteAssetDisk($location);

    if ($remoteDisk) {
        $remotePath = trim($location, '/') . '/' . $filename;

        if ($old) {
            Storage::disk($remoteDisk)->delete(trim($location, '/') . '/' . $old);
            Storage::disk($remoteDisk)->delete(trim($location, '/') . '/thumb_' . $old);
        }

        Storage::disk($remoteDisk)->put(
            $remotePath,
            (string) $image->stream($file->getClientOriginalExtension(), 90),
            [
                'visibility' => 'public',
                'ContentType' => $file->getMimeType(),
            ]
        );

        if ($thumb) {
            $thumbSize = explode('x', $thumb);
            Storage::disk($remoteDisk)->put(
                trim($location, '/') . '/thumb_' . $filename,
                (string) Image::make($file)->resize($thumbSize[0], $thumbSize[1])->stream($file->getClientOriginalExtension(), 90),
                [
                    'visibility' => 'public',
                    'ContentType' => $file->getMimeType(),
                ]
            );
        }

        return $filename;
    }

    $location = publicAssetPath($location);
    $path = makeDirectory($location);
    if (!$path) throw new Exception('File could not been created.');

    if ($old) {
        removeFile($location . '/' . $old);
        removeFile($location . '/thumb_' . $old);
    }

    $image->save($location . '/' . $filename);

    if ($thumb) {
        $thumb = explode('x', $thumb);
        Image::make($file)->resize($thumb[0], $thumb[1])->save($location . '/thumb_' . $filename);
    }

    return $filename;
}

function uploadFile($file, $location, $size = null, $old = null){
    $location = publicAssetPath($location);
    $path = makeDirectory($location);
    if (!$path) throw new Exception('File could not been created.');

    if ($old) {
        removeFile($location . '/' . $old);
    }

    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $file->move($location,$filename);
    return $filename;
}

function makeDirectory($path)
{
    $path = publicAssetPath($path);
    if (file_exists($path)) return true;
    return mkdir($path, 0755, true);
}


function removeFile($path)
{
    $path = publicAssetPath($path);
    return file_exists($path) && is_file($path) ? @unlink($path) : false;
}

function publicAssetPath($path)
{
    if ($path === null || $path === '') {
        return public_path();
    }

    if (preg_match('#^(?:[A-Za-z]:[\\\\/]|/)#', $path)) {
        return $path;
    }

    return public_path($path);
}

function defaultLocaleCode()
{
    return strtolower((string) config('app.locale', config('app.fallback_locale', 'en'))) ?: 'en';
}

function supportedLocaleCodes()
{
    static $codes = null;

    if ($codes !== null) {
        return $codes;
    }

    try {
        $codes = collect(File::files(resource_path('lang')))
            ->filter(fn ($file) => strtolower($file->getExtension()) === 'json')
            ->map(fn ($file) => strtolower($file->getFilenameWithoutExtension()))
            ->filter()
            ->unique()
            ->values()
            ->all();
    } catch (\Throwable $e) {
        $codes = [];
    }

    return $codes;
}

function normalizeLocale($locale = null)
{
    $locale = strtolower(trim((string) $locale));
    $supported = supportedLocaleCodes();

    if ($locale !== '' && in_array($locale, $supported, true)) {
        return $locale;
    }

    $default = defaultLocaleCode();

    if (in_array($default, $supported, true)) {
        return $default;
    }

    return $supported[0] ?? 'en';
}


function activeTemplate($asset = false)
{
    if (app()->runningInConsole() && ! app()->runningUnitTests()) {
        return $asset ? 'assets/templates/default/' : 'user.';
    }

    try {
        $general = GeneralSetting::first(['active_template']);
        $template = $general->active_template ?? 'default';
        $sess = session()->get('template');
        if (trim((string) $sess)) {
            $template = $sess;
        }

        return $asset ? 'assets/templates/' . $template . '/' : 'user.';
    } catch (\Throwable $e) {
        return $asset ? 'assets/templates/default/' : 'user.';
    }
}

function activeTemplateName()
{
    if (app()->runningInConsole() && ! app()->runningUnitTests()) {
        return 'default';
    }

    try {
        $general = GeneralSetting::first(['active_template']);
        $template = $general->active_template ?? 'default';
        $sess = session()->get('template');
        if (trim((string) $sess)) {
            $template = $sess;
        }

        return $template;
    } catch (\Throwable $e) {
        return 'default';
    }
}


function loadReCaptcha()
{
    $reCaptcha = Extension::where('act', 'google-recaptcha2')->where('status', 1)->first();
    return $reCaptcha ? $reCaptcha->generateScript() : '';
}


function loadAnalytics()
{
    $analytics = Extension::where('act', 'google-analytics')->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function loadTawkto()
{
    $tawkto = Extension::where('act', 'tawk-chat')->where('status', 1)->first();
    return $tawkto ? $tawkto->generateScript() : '';
}


function loadFbComment()
{
    $comment = Extension::where('act', 'fb-comment')->where('status',1)->first();
    return  $comment ? $comment->generateScript() : '';
}

function loadCustomCaptcha($height = 46, $width = '300px', $bgcolor = '#003', $textcolor = '#abc')
{
    $general = GeneralSetting::first(['base_color']);
    $textcolor = '#'.($general->base_color ?? 'ffffff');
    $captcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
    if (!$captcha) {
        return 0;
    }
    $code = rand(100000, 999999);
    $char = str_split($code);
    $ret = '<link href="https://fonts.googleapis.com/css?family=Henny+Penny&display=swap" rel="stylesheet">';
    $ret .= '<div style="height: ' . $height . 'px; line-height: ' . $height . 'px; width:' . $width . '; text-align: center; background-color: ' . $bgcolor . '; color: ' . $textcolor . '; font-size: ' . ($height - 20) . 'px; font-weight: bold; letter-spacing: 20px; font-family: \'Henny Penny\', cursive;  -webkit-user-select: none; -moz-user-select: none;-ms-user-select: none;user-select: none;  display: flex; justify-content: center;">';
    foreach ($char as $value) {
        $ret .= '<span style="    float:left;     -webkit-transform: rotate(' . rand(-60, 60) . 'deg);">' . $value . '</span>';
    }
    $ret .= '</div>';
    $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
    $ret .= '<input type="hidden" name="captcha_secret" value="' . $captchaSecret . '">';
    return $ret;
}


function captchaVerify($code, $secret)
{
    $captcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
    $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
    if ($captchaSecret == $secret) {
        return true;
    }
    return false;
}

function getTrx($length = 6)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function getAmount($amount, $length = 2)
{
    $amount = round($amount, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false){
    $separator = '';
    if($separate){
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if($exceptZeros){
    $exp = explode('.', $printAmount);
        if($exp[1]*1 == 0){
            $printAmount = $exp[0];
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{

    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}

//moveable
function curlContent($url)
{
    return Http::withoutVerifying()->get($url)->body();
}

//moveable
function curlPostContent($url, $arr = null)
{
    $request = Http::withoutVerifying()->asForm();

    return $arr ? $request->post($url, $arr)->body() : $request->post($url)->body();
}


function inputTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function str_limit($title = null, $length = 10)
{
    return \Illuminate\Support\Str::limit($title, $length);
}

//moveable
function getIpInfo()
{
    $ip = $_SERVER["REMOTE_ADDR"];

    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }


    $xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);


    $country = @$xml->geoplugin_countryName;
    $city = @$xml->geoplugin_city;
    $area = @$xml->geoplugin_areaCode;
    $code = @$xml->geoplugin_countryCode;
    $long = @$xml->geoplugin_longitude;
    $lat = @$xml->geoplugin_latitude;

    $data['country'] = $country;
    $data['city'] = $city;
    $data['area'] = $area;
    $data['code'] = $code;
    $data['long'] = $long;
    $data['lat'] = $lat;
    $data['ip'] = request()->ip();
    $data['time'] = date('d-m-Y h:i:s A');


    return $data;
}

//moveable
function osBrowser(){
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $osPlatform = "Unknown OS Platform";
    $osArray = array(
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );
    foreach ($osArray as $regex => $value) {
        if (preg_match($regex, $userAgent)) {
            $osPlatform = $value;
        }
    }
    $browser = "Unknown Browser";
    $browserArray = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    );
    foreach ($browserArray as $regex => $value) {
        if (preg_match($regex, $userAgent)) {
            $browser = $value;
        }
    }

    $data['os_platform'] = $osPlatform;
    $data['browser'] = $browser;

    return $data;
}

function siteName()
{
    $general = GeneralSetting::first();
    $sitname = str_word_count($general->sitename);
    $sitnameArr = explode(' ', $general->sitename);
    if ($sitname > 1) {
        $title = "<span>$sitnameArr[0] </span> " . str_replace($sitnameArr[0], '', $general->sitename);
    } else {
        $title = "<span>$general->sitename</span>";
    }

    return $title;
}


//moveable
function getTemplates()
{
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . config('app.url');
    $url = '#' . systemDetails()['name'];
    $result = curlPostContent($url, $param);
    if ($result) {
        return $result;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{

    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image,$size = null)
{
    $clean = '';

    if (filter_var($image, FILTER_VALIDATE_URL)) {
        return $image . $clean;
    }

    $path = publicAssetPath($image);
    if (file_exists($path) && is_file($path)) {
        return asset($image) . $clean;
    }

    $remoteUrl = remoteAssetUrl($image);
    if ($remoteUrl) {
        return $remoteUrl . $clean;
    }

    if ($size) {
        return route('placeholder.image',$size);
    }
    return asset('assets/images/default.png');
}

function remoteAssetDisk($path = null)
{
    $disk = (string) config('filesystems.user_profile_disk', 'local');

    if ($disk === '' || $disk === 'local' || $path === null) {
        return null;
    }

    $normalizedPath = trim((string) $path, '/');
    $profilePath = trim((string) data_get(imagePath(), 'profile.user.path'), '/');

    if ($normalizedPath === '' || $normalizedPath !== $profilePath) {
        return null;
    }

    if (!config("filesystems.disks.{$disk}")) {
        return null;
    }

    return $disk;
}

function remoteAssetUrl($path)
{
    $disk = remoteAssetDisk(dirname((string) $path));

    if (!$disk) {
        return null;
    }

    $normalizedPath = trim((string) $path, '/');
    if ($normalizedPath === '' || str_ends_with($normalizedPath, '/')) {
        return null;
    }

    $configuredUrl = trim((string) config("filesystems.disks.{$disk}.url"), '/');
    if ($configuredUrl !== '') {
        return $configuredUrl . '/' . $normalizedPath;
    }

    try {
        return Storage::disk($disk)->url($normalizedPath);
    } catch (\Throwable $e) {
        return null;
    }
}

function notify($user, $type, $shortCodes = null)
{

    sendEmail($user, $type, $shortCodes);
    sendSms($user, $type, $shortCodes);
}



function sendSms($user, $type, $shortCodes = [])
{
    $general = GeneralSetting::first();
    $smsTemplate = SmsTemplate::where('act', $type)->where('sms_status', 1)->first();
    $gateway = $general->sms_config->name;
    $sendSms = new SendSms;
    if ($general->sn == 1 && $smsTemplate) {
        $template = $smsTemplate->sms_body;
        foreach ($shortCodes as $code => $value) {
            $template = shortCodeReplacer('{{' . $code . '}}', $value, $template);
        }
        $message = shortCodeReplacer("{{message}}", $template, $general->sms_api);
        $message = shortCodeReplacer("{{name}}", $user->username, $message);
        $sendSms->$gateway($user->mobile,$general->sitename,$message,$general->sms_config);
    }
}

function sendEmail($user, $type = null, $shortCodes = [])
{
    $general = GeneralSetting::first();

    $emailTemplate = EmailTemplate::where('act', $type)->where('email_status', 1)->first();
    if ($general->en != 1 || !$emailTemplate) {
        return;
    }


    $message = shortCodeReplacer("{{fullname}}", $user->fullname, $general->email_template);
    $message = shortCodeReplacer("{{username}}", $user->username, $message);
    $message = shortCodeReplacer("{{message}}", $emailTemplate->email_body, $message);

    if (empty($message)) {
        $message = $emailTemplate->email_body;
    }

    foreach ($shortCodes as $code => $value) {
        $message = shortCodeReplacer('{{' . $code . '}}', $value, $message);
    }

    $config = $general->mail_config;

    $emailLog = new EmailLog();
    $emailLog->user_id = $user->id;
    $emailLog->mail_sender = $config->name;
    $emailLog->email_from = $general->sitename.' '.$general->email_from;
    $emailLog->email_to = $user->email;
    $emailLog->subject = $emailTemplate->subj;
    $emailLog->message = $message;
    $emailLog->save();


    if ($config->name == 'php') {
        sendPhpMail($user->email, $user->username,$emailTemplate->subj, $message, $general);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    }
}


function sendPhpMail($email, $name, $subject, $message,$general,$contact = false)
{
    if ($contact) {
        $headers = "From: $name <$email> \r\n";
        $headers .= "Reply-To: $name <$email> \r\n";
        $email = $general->email_from;
    }else{
        $headers = "From: $general->sitename <$general->email_from> \r\n";
        $headers .= "Reply-To: $general->sitename <$general->email_from> \r\n";
    }
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    @mail($email, $subject, $message, $headers);
}


function sendPHPMailer($config, $receiver_email, $receiver_name, $subject, $message,$general,$contact = false)
{
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $config->host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $config->username;
        $mail->Password   = $config->password;
        if ($config->enc == 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }else{
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->Port       = $config->port;
        $mail->CharSet = 'UTF-8';
        //Recipients
        if ($contact) {
            $mail->setFrom($receiver_email, $receiver_name);
            $mail->addAddress($general->email_from, $general->sitename);
            $mail->addReplyTo($receiver_email, $receiver_name);
        }else{
            $mail->setFrom($general->email_from, $general->sitename);
            $mail->addAddress($receiver_email, $receiver_name);
            $mail->addReplyTo($general->email_from, $general->sitename);
        }
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
    } catch (Exception $e) {
        throw new Exception($e);
    }
}

function sendSmtpMail($config, $receiver_email, $receiver_name, $subject, $message,$general,$contact = false)
{


    //$mail = new PHPMailer(true);

    try {
        //Server settings
    $to_name = $receiver_name;
    $to_email = $receiver_email;
    $data = array("name"=>$receiver_name, "body" => $message);

    Mail::send("mail", $data, function($message) use ($to_name, $to_email,$subject) {
        $message->to($to_email, $to_name)->subject($subject);
        $message->from(config('mail.from.address', config('mail.mailers.smtp.username')), config('mail.from.name', config('app.name')));
        });
    } catch (Exception $e) {
        throw new Exception($e);
    }
}


function sendSendGridMail($config, $receiver_email, $receiver_name, $subject, $message,$general,$contact = false)
{
    $sendgridMail = new \SendGrid\Mail\Mail();
    if ($contact) {
        $sendgridMail->setFrom($receiver_email, $receiver_name);
        $sendgridMail->setSubject($subject);
        $sendgridMail->addTo($general->email_from, $general->sitename);
    }else{
        $sendgridMail->setFrom($general->email_from, $general->sitename);
        $sendgridMail->setSubject($subject);
        $sendgridMail->addTo($receiver_email, $receiver_name);
    }

    $sendgridMail->addContent("text/html", $message);
    $sendgrid = new \SendGrid($config->appkey);
    try {
        $response = $sendgrid->send($sendgridMail);
    } catch (Exception $e) {
        throw new Exception($e);
    }
}


function sendMailjetMail($config, $receiver_email, $receiver_name, $subject, $message,$general,$contact = false)
{
    $mj = new \Mailjet\Client($config->public_key, $config->secret_key, true, ['version' => 'v3.1']);
    if ($contact) {
        $fromMail = $receiver_email;
        $fromName = $receiver_name;
        $toMail = $general->email_from;
        $toName = $general->sitename;
    }else{
        $toMail = $receiver_email;
        $toName = $receiver_name;
        $fromMail = $general->email_from;
        $fromName = $general->sitename;
    }
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => $fromMail,
                    'Name' => $fromName,
                ],
                'To' => [
                    [
                        'Email' => $toMail,
                        'Name' => $toName,
                    ]
                ],
                'Subject' => $subject,
                'TextPart' => "",
                'HTMLPart' => $message,
            ]
        ]
    ];
    $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
}


function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data, $design = 'admin.partials.paginate'){
    return $data->appends(request()->all())->links($design);
}


function menuActive($routeName, $type = null)
{
    if ($type == 3) {
        $class = 'side-menu--open';
    } elseif ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
    }
    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routeName)) {
        return $class;
    }
}


function imagePath()
{
    $data['gateway'] = [
        'path' => 'assets/images/gateway',
        'size' => '800x800',
    ];
    $data['verify'] = [
        'withdraw'=>[
            'path'=>'assets/images/verify/withdraw'
        ],
        'deposit'=>[
            'path'=>'assets/images/verify/deposit'
        ]
    ];
    $data['image'] = [
        'default' => 'assets/images/default.png',
    ];
    $data['withdraw'] = [
        'method' => [
            'path' => 'assets/images/withdraw/method',
            'size' => '800x800',
        ]
    ];
    $data['ticket'] = [
        'path' => 'assets/support',
    ];
    $data['language'] = [
        'path' => 'assets/images/lang',
        'size' => '64x64'
    ];
    $data['logoIcon'] = [
        'path' => 'assets/images/logoIcon',
    ];
    $data['favicon'] = [
        'size' => '128x128',
    ];
    $data['extensions'] = [
        'path' => 'assets/images/extensions',
        'size' => '36x36',
    ];
    $data['seo'] = [
        'path' => 'assets/images/seo',
        'size' => '600x315'
    ];
    $data['profile'] = [
        'user'=> [
            'path'=>'assets/images/user/profile',
            'size'=>'350x300'
        ],
        'admin'=> [
            'path'=>'assets/admin/images/profile',
            'size'=>'400x400'
        ]
    ];
    return $data;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

//moveable
function sendGeneralEmail($email, $subject, $message, $receiver_name = '')
{

    $general = GeneralSetting::first();


    if ($general->en != 1 || !$general->email_from) {
        return;
    }

    $message = shortCodeReplacer("{{message}}", $message, $general->email_template);
    $message = shortCodeReplacer("{{fullname}}", $receiver_name, $message);
    $message = shortCodeReplacer("{{username}}", $email, $message);

    $config = $general->mail_config;

    if ($config->name == 'php') {
        sendPhpMail($email, $receiver_name, $subject, $message, $general);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $email, $receiver_name, $subject, $message, $general);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $email, $receiver_name,$subject, $message,$general);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $email, $receiver_name,$subject, $message, $general);
    }
}
function sendContactEmail($email, $subject, $message, $receiver_name = '')
{

    $general = GeneralSetting::first();


    if ($general->en != 1 || !$general->email_from) {
        return;
    }
    $admin = App\Models\Admin::first();
    $message = shortCodeReplacer("{{message}}", $message, $general->email_template);
    $message = shortCodeReplacer("{{fullname}}", '', $message);
    $message = shortCodeReplacer("{{username}}", $admin->name, $message);

    $config = $general->mail_config;

    if ($config->name == 'php') {
        sendPhpMail($email, $receiver_name, $subject, $message, $general,true);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $email, $receiver_name, $subject, $message, $general,true);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $email, $receiver_name,$subject, $message,$general,true);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $email, $receiver_name,$subject, $message, $general,true);
    }
}

function getContent($data_keys, $singleQuery = false, $limit = null,$orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $data_keys)->orderBy('id','desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if($orderById){
            $content = $article->where('data_keys', $data_keys)->orderBy('id')->get();
        }else{
            $content = $article->where('data_keys', $data_keys)->orderBy('id','desc')->get();
        }
    }
    return $content;
}


function gatewayRedirectUrl($type = false){
    if ($type) {
        return 'user.deposit.history';
    }else{
        return 'user.deposit';
    }
}

function verifyG2fa($user,$code,$secret = null)
{
    $ga = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $ga->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function GetCoinPrice()
{
        $url = url('/').'/coinrate';
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json"
        ),
        ));
       $response = curl_exec($curl);
        curl_close($curl);
        $res=json_decode($response, true);
        return $res;
}


function urlPath($routeName,$routeParam=null){
    if($routeParam == null){
        $url = route($routeName);
    } else {
        $url = route($routeName,$routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath,'',$url);
    return $path;
}


function levelCommision($id, $amount, $commissionType){
    $usr = $id;
    $i = 1;
    $gnl = GeneralSetting::first();

    $level = Referral::count();

    while($usr!="" || $usr!="0" || $i<$level ) {
        $me = User::find($usr);
        $refer= User::find($me->ref_by);


            if($refer == "") {
                break;
            }
            $comission = Referral::where('level',$i)->first();
            $com = ($amount * $comission->percent)/100;


            $referWallet = User::where('user_id',$refer->id)->first();
            $new_bal = number_format($referWallet->ref_bonus +$com);
            $referWallet->ref_bonus = $new_bal;
            $referWallet->save();

            $trx = getTrx();

            Transaction::create([
                'user_id' => $refer->id,
                'amount' => formatter_money($com),
                'post_balance' => $new_bal,
                'charge' => 0,
                'type' => '+',
                'ref' => 1,
                'level' => $i,
                'bywho' => $usr,
                'details' => $commissionType,
                'trx' => $trx,
            ]);

            $usr = $refer->id;
            $i++;
    }

    return 0;

}
