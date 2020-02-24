<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;
use function foo\func;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LoginGoogleController extends Controller
{

    private $mail;
    private $pass;

    public function __construct()
    {
        $this->mail = DB::table('account')->where('id',1)->first()->email;
        $this->pass = DB::table('account')->where('id',1)->first()->password;
    }


    public function loginGG(){

        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $prefs = array('download.default_directory' => 'c:/temp');
        $options->setExperimentalOption("prefs", $prefs);
//        $caps->setCapability(
//            'moz:firefoxOptions',
//            ['args' => ['-headless']]
//        );

        $caps->setCapability(ChromeOptions::CAPABILITY, $options);

        if ($USE_FIREFOX)
        {
            $driver = RemoteWebDriver::create(
                $host,
                DesiredCapabilities::firefox()
            );
        }
        else
        {
            $driver = RemoteWebDriver::create(
                $host,
                $caps
            );
        }
        #Access link Google Ads
        $driver->get("https://ads.google.com/intl/vi_vn/home/");
        sleep(2);

        #Click button sign-in
        $driver->findElement(WebDriverBy::linkText(
            'Đăng nhập'))
            ->click();
        sleep(1);

        #Fill email
        $driver->findElement(WebDriverBy::id('identifierId'))->sendKeys($this->mail);
        $driver->findElement(WebDriverBy::className('CwaK9'))->click();
        sleep(1);
        #Fill password
        $driver->findElement(WebDriverBy::cssSelector('input[type="password"]'))->sendKeys($this->pass);
        $driver->findElement(WebDriverBy::className('CwaK9'))->click();
        sleep(3);

        #Select account Ads
        /*$elements = $driver->findElements(WebDriverBy::cssSelector('a.umx-l'));
        foreach ($elements as $element) {
            if ($element->getAttribute('href') == "https://ads.google.com/um/identity?authuser=2&dst=/um/homepage?__e%3D6119414799") {
                $element->click();
                break;
            }
        }*/
        try {
            $driver->findElement(WebDriverBy::partialLinkText(
                'bui quoc toan'))->click();sleep(5);
        } catch (\Exception $e) {
            sleep(5);
        }
        $list = $driver->manage()->getCookies();
        foreach ($list as $cookie) {
            //$domain = $cookie->getDomain();
            print_r($cookie);
            $tmp = $cookie['name'].':'.$cookie['value'];
            file_put_contents(public_path('cookie_gg.txt'), $tmp.PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        $driver->quit();
    }
}
