<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use App\Models\Frontend;
use App\Models\Page;
use App\Models\MarketPrice; // Ensure the MarketPrice model is imported
use App\Http\Requests\ContactFormRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;



class SiteController extends Controller
{
    public function __construct(){
        $this->activeTemplate = activeTemplate();
    }

    public function index(){

        $count = Page::where('tempname',$this->activeTemplate)->where('slug','home')->count();
        if($count == 0){
            $page = new Page();
            $page->tempname = $this->activeTemplate;
            $page->name = 'HOME';
            $page->slug = 'home';
            $page->save();
        }

        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $pageTitle = 'Home';
        $sections = Page::where('tempname',$this->activeTemplate)->where('slug','home')->first();
		$prices = MarketPrice::all();
        $emptyMessage = 'Data Not Found';
        return view($this->activeTemplate . 'home', compact('pageTitle', 'sections', 'emptyMessage', 'prices'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname',$this->activeTemplate)->where('slug',$slug)->firstOrFail();
        $pageTitle = $slug;
		$prices = MarketPrice::all();
        //$sections = $page->secs;
        //return $slug;
        return view($this->activeTemplate . $slug, compact('pageTitle', 'prices'));
    }


    public function contact()
    {
        $pageTitle = "Contact Us";
        return view($this->activeTemplate . 'contact',compact('pageTitle'));
    }


    public function contactSubmit(ContactFormRequest $request)
    {
        // ContactFormRequest automatically validates:
        // - name, email, subject, message
        // - Cloudflare Turnstile token
        // - Rate limiting (3 attempts per 5 minutes)

        sendContactEmail($request->email, $request->subject, $request->message, $request->name);

        $notify[] = ['success', 'ticket created successfully!'];
        return redirect()->route('home')->withNotify($notify);
    }

    public function changeLanguage($lang = null)
    {
        session()->put('lang', normalizeLocale($lang));
        return redirect()->back();
    }

    public function cookieAccept(){
        session()->put('cookie_accepted',true);
        $notify[] = ['success','Cookie accepted successfully'];
        return back()->withNotify($notify);
    }

    public function placeholderImage($size = null){
        $imgWidth = explode('x',$size)[0];
        $imgHeight = explode('x',$size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = public_path('assets/font/RobotoMono-Regular.ttf');
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if($imgHeight < 100 && $fontSize > 30){
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function privacyPage($slug, $id){
        $content = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $content->data_values->title;
        return view($this->activeTemplate.'privacy_pages',compact('content','pageTitle'));
    }



}
