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
use DB;
include_once 'simple_html_dom.php';

class TrafficBotController extends Controller
{
    public function checkIndexByTitle(){
        //print_r(count($listApp));die;
        $time = date('Y-m-d H:i:s',time());
        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        // firefox
        $profile = new FirefoxProfile();
        $caps = DesiredCapabilities::firefox();
        $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36');
        $profile->setPreference('dom.webnotifications.enabled',false);
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);
        $proxy = true;
        $width = 500;
        $height = 1000;
        /*
        if($proxy != false){
            
            $profile->setPreference('network.proxy.type', 1);
            # Set proxy to Tor client on localhost
            $profile->setPreference('network.proxy.socks', '104.248.64.188');
            $profile->setPreference('network.proxy.socks_port', 28982);
            
        }*/
        if ($USE_FIREFOX)
        {
            $driver = RemoteWebDriver::create(
                $host, 
                $caps
            );
            $d = new WebDriverDimension($width,$height);
           $driver->manage()->window()->setSize($d);
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
        sleep(5);


        try {
            $listApp = DB::table('crawler_similar')->get();

            foreach ($listApp as $app) {
                $keyword = "\"".utf8_decode($app->app_title)."\"";
                try {
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                    sleep(0.5);
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                    sleep(0.5);
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
                    sleep(0.5);
                    $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                    sleep(1);

                    var_dump($keyword);

                    $more_result = $driver->findElements(WebDriverBy::xpath('//a[@aria-label="More results"]'));

                    if(count($more_result) > 0)
                    {
                        var_dump('true');
                        $driver->findElement(WebDriverBy::xpath('//a[@aria-label="More results"]'))->click();
                        sleep(2);
                    }

                    $html = $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
                    $dom = str_get_html($html);
                    $list = $dom->find('.mnr-c');

                    foreach ($list as $key => $item) {
                        //print_r($item->find('a',0)->href.PHP_EOL);

                        if($item->find('a',0))
                        {
                            $url = $item->find('a',0)->href;
                            var_dump($url);
                            var_dump('--------------------------------');
                        }
                    }

//                    $more_result = $driver->findElements(WebDriverBy::xpath('//a[@aria-label="More results"]'));
//
//                    if(count($more_result) > 0)
//                    {
//                        var_dump('true');
//                        $driver->findElement(WebDriverBy::xpath('//a[@aria-label="More results"]'))->click();
//                        sleep(2);
//                    }
//
//                    $domains = $driver->findElements(WebDriverBy::cssSelector('div.KJDcUb div.aLF0Z span.qzEoUe'));
//
//                    foreach ($domains as $domain)
//                    {
//                        var_dump($domain->getText());
//                    }

                    var_dump("-------------------------------");
                   // die();

                } catch (\Exception $e2) {
                    echo 'e2'.$e2->getMessage();
                }
            }
            die();
                //die;
        } catch (\Exception $e) {
            echo 'e'.$e->getMessage();
            $urlAry = $driver->executeScript('return window.location',array());
            $currentURL = $urlAry['href'];
            $captcha = new AllInTitleController();
            $recaptchaToken = $captcha->recaptcha($currentURL);
            if($recaptchaToken != false){
                $driver->executeScript('document.getElementById("g-recaptcha-response").innerHTML = "'.$recaptchaToken.'"');
                $driver->executeScript('document.getElementById("captcha-form").submit()');
                sleep(10);
                try {
                    foreach ($listApp as $app) {

                        $keyword = utf8_decode($app->app_title);

                        try {
                            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                            sleep(1);
                            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
                            sleep(1);
                            $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                            sleep(5);

                            var_dump(utf8_decode($app->app_title));

                            $more_result = $driver->findElements(WebDriverBy::xpath('//a[@aria-label="More results"]'));

                            if(count($more_result) > 0)
                            {
                                var_dump('true');
                                $driver->findElement(WebDriverBy::xpath('//a[@aria-label="More results"]'))->click();
                                sleep(2);
                            }

                            $domains = $driver->findElements(WebDriverBy::cssSelector('div.KJDcUb div.aLF0Z span.qzEoUe'));

                            foreach ($domains as $domain)
                            {
                                var_dump($domain->getText());

                            }
                        } catch (\Exception $e) {
                            
                        }
                    }
                } catch (\Exception $e1) {
                    $urlAry = $driver->executeScript('return window.location',array());
                    $currentURL = $urlAry['href'];
                    if($currentURL == 'https://www.google.com/sorry/index'){

                    } else {
                        foreach ($listApp as $app) {

                            $keyword = utf8_decode($app->app_title);

                            try {
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                                sleep(1);
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
                                sleep(1);
                                $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                                sleep(5);

                                var_dump(urldecode($app->app_title));

                                $more_result = $driver->findElements(WebDriverBy::xpath('//a[@aria-label="More results"]'));

                                if(count($more_result) > 0)
                                {
                                    var_dump('true');
                                    $driver->findElement(WebDriverBy::xpath('//a[@aria-label="More results"]'))->click();
                                    sleep(2);
                                }

                                $domains = $driver->findElements(WebDriverBy::cssSelector('div.KJDcUb div.aLF0Z span.qzEoUe'));

                                foreach ($domains as $domain)
                                {
                                    var_dump($domain->getText());

                                }

                            } catch (\Exception $e3) {
                                
                            }
                        }
                    }
                }
            }
        }
    }


    public function getRequest($url){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_SSL_VERIFYHOST => 0,
          CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        echo $err;
        curl_close($curl);
        return $response;

    }
}
