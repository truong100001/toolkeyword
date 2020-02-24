<?php

namespace App\Http\Controllers;
ini_set('memory_limit',-1);
use Illuminate\Http\Request;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Illuminate\Support\Facades\DB;

include 'simple_html_dom.php';
include_once 'anticaptcha.php';
include_once 'nocaptchaproxyless.php';

class SearchResultController extends Controller
{

    private $api_key = 'abe3054d7aef8ffa75d418744bdbad6c';
    private $site_key = '6LfwuyUTAAAAAOAmoS0fdqijC2PbbdH4kjq62Y1b';

    public $listKeyword;

    public function __construct()
    {
        $this->listKeyword = DB::select(DB::raw("SELECT * FROM keyword_out WHERE check_gg = 0"));
    }

    public function recaptcha($url)
    {
        $api = new NoCaptchaProxyless();
        $api->setVerboseMode(true);

        //your anti-captcha.com account key
        $api->setKey($this->api_key);

        //recaptcha key from target website
        $api->setWebsiteURL($url);
        $api->setWebsiteKey($this->site_key);

        if (!$api->createTask()) {
            return false;
        }

        if (!$api->waitForResult()) {
            return false;
        } else {
            $recaptchaToken =   $api->getTaskSolution();
            return $recaptchaToken;
        }
    }



    function is_url($uri)
    {
        $uri = htmlspecialchars_decode($uri);
        return preg_match( '/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$uri);
    }


    public function competitor($str)
    {
        $arr_competitor = [
            'apkpremier.com',
            'apkdl.in',
            'apkpure.com',
            'apkmirror.com',
            'apkcombo.com',
            'dropapk.to',
            'appsonwindows.com',
            'apkonline.net',
            'apkmonk.com',
            'apksum.com',
            'androidappsapk.co',
            'downloadatoz.com',
            'apkplz.net',
            'apkfab.com',
            'apk-dl.com',
            'a2zapk.com',
            '9apps.com',
            'apkfollow.com',
            'apk4now.com',
            'apkily.com',
            'appparapc.com',
            'apk.plus',
            'cloudapks.com',
            'apkpure.ai',
            'apktovi.com',
            'android-apk.org',
            'apksfull.com',
            'apk.tools',
            'apktada.com',
            'https://apk.co',
            'downloadapk.net',
            'appsapk.com',
            'sameapk.com',
            'apkturbo.com',
            'apkamp.com',
            'appchopc.com',
            'appnaz.com',
            'choilieng.com',
            'apkgk.com'
        ];


        $num_competior = 0;
        foreach ($arr_competitor as $competitor) {
            if (strpos($str, $competitor)) {
                $num_competior += 1;
            }
        }
        return $num_competior;
    }



    public function chech_result($tab,$proxy){

        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        // firefox
        $profile = new FirefoxProfile();
        $caps = DesiredCapabilities::firefox();
        // $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36');

        $caps->setCapability(
            'moz:firefoxOptions',
            ['args' => ['-headless']]
        );

        $profile->setPreference('dom.webnotifications.enabled',false);
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);

        if($proxy != false){

            $serve = explode(':',$proxy);

            $ip = $serve[0];
            $port = $serve[1];

            $profile->setPreference('network.proxy.type', 1);
            # Set proxy to Tor client on localhost
            $profile->setPreference('network.proxy.socks', $ip);
            $profile->setPreference('network.proxy.socks_port', $port);

        }

        if ($USE_FIREFOX)
        {
            $driver = RemoteWebDriver::create(
                $host,
                $caps
            );
            // $d = new WebDriverDimension($width,$height);
            //  $driver->manage()->window()->setSize($d);
        } else {
            $driver = RemoteWebDriver::create(
                $host,
                $caps
            );
        }
        $driver->get("https://www.google.com?hl=en");
        $keyword = 'demo';
        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys(WebDriverKeys::ENTER);
        sleep(1.5);

        try {

            $captcha = $driver->findElements(WebDriverBy::xpath("//iframe[contains(@src, 'recaptcha')]"));
            if(count($captcha) > 0)
            {
                $urlAry = $driver->executeScript('return window.location',array());
                $currentURL = $urlAry['href'];
                $recaptchaToken = $this->recaptcha($currentURL);

                if($recaptchaToken != false)
                {
                    $driver->executeScript('document.getElementById("g-recaptcha-response").innerHTML = "' . $recaptchaToken . '"');
                    $driver->executeScript('document.getElementById("captcha-form").submit()');
                    sleep(2);
                }
            }

            $index = DB::table('index_sr')->where('id',$tab)->first();

            for($i = $index->index_from; $i < $index->index_to; $i++)
            {
                $keyword = "\"".$this->listKeyword[$i]->keyword."\"";

                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                sleep(0.5);
                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                sleep(0.5);
                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
                sleep(0.7);
                $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                sleep(1.5);



                $result = $driver->findElement(WebDriverBy::id('appbar'))->getText();

                $search_result = 0;
                if (strlen(strstr($result, 'results')) > 0)
                {
                    $result = explode('(',$result);
                    array_pop($result);

                    $result = $result[0];
                    $result = str_replace('results','',$result);
                    $result = str_replace('About','',$result);
                    $search_result = str_replace(',','',$result);
                }


                $arr_url = [];

                $html = $driver->findElement(WebDriverBy::id('res'))->getAttribute('innerHTML');
                $dom = str_get_html($html);

                if(!empty($dom))
                {
                    $list = $dom->find('.g');

                    foreach ($list as $item)
                    {
                        if($item->find('a',0))
                        {
                            $url = htmlspecialchars_decode($item->find('a',0)->href);

                            if(count($arr_url) > 10)
                                break;

                            if($this->is_url($url))
                            {
                                if(!in_array($url,$arr_url))
                                    array_push($arr_url,$url);
                            }
                        }
                    }


                    DB::table('keyword_out')->where('id',$this->listKeyword[$i]->id)->update([
                        'search_result' => trim($search_result),
                        'competitor' => $this->competitor(join(' ',$arr_url)),
                        'top_site' => join(' ',$arr_url),
                        'check_gg' => 1
                    ]);

                    var_dump($this->listKeyword[$i]->keyword.' - '.$search_result);
                }

                DB::table('index_sr')->where('id',$tab)->update([
                    'index_from' => $i+1
                ]);

            }
        }
        catch (\Exception $e)
        {
            $driver->quit();
            $this->chech_result($tab,$proxy);
        }

    }

    public function script1()
    {
        $this->chech_result(1,false);
    }

    public function script2()
    {
        $this->chech_result(2,'159.69.2.57:28982');
    }
    public function script3()
    {
        $this->chech_result(3,'94.130.178.68:28982');
    }

    public function script4()
    {
        $this->chech_result(4,'159.69.156.39:28982');
    }
    public function script5()
    {
        $this->chech_result(5,'78.46.188.49:28982');
    }

    public function script6()
    {
        $this->chech_result(6,'128.199.196.242:28982');
    }

    public function script7()
    {
        $this->chech_result(7,'144.217.166.157:28982');
    }

    public function script8()
    {
        $this->chech_result(8,false);
    }

    public function script9()
    {
        $this->chech_result(9,false);
    }

    public function script10()
    {
        $this->chech_result(10,false);
    }

}
