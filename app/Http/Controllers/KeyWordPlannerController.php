<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;
use function foo\func;
use Illuminate\Http\Request;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Facades\DB;

class KeyWordPlannerController extends Controller
{
    public function checkKeywordPlanner($stream)
    {
        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $prefs = array('download.default_directory' => 'c:/temp');
        $options->setExperimentalOption("prefs", $prefs);
        //$ss = array('--headless','--start-maximized');
        $caps->setCapability(ChromeOptions::CAPABILITY, $options);
        $capabilities = DesiredCapabilities::firefox();
//        $capabilities->setCapability(
//            'moz:firefoxOptions',
//            ['args' => ['-headless']]
//        );
        if ($USE_FIREFOX)
        {
            $driver = RemoteWebDriver::create(
                $host,
                $capabilities
            );
        }
        else
        {
            $driver = RemoteWebDriver::create(
                $host,
                $caps
            );
        }

        try
        {
            $driver->get("https://ads.google.com/intl/vi_vn/home/");
            sleep(3);

            $cookies = file_get_contents(public_path('cookie_gg.txt'));
            $cookies = explode(PHP_EOL,$cookies);

            foreach ($cookies as $cookie) {

                $tmp = explode(':',$cookie);
                if(isset($tmp[1])){
                    $tmp_ck = array('name' => $tmp[0], 'value' => $tmp[1]);
                    $driver->manage()->addCookie($tmp_ck);
                }

            }


            #Access link planner Ads
            $user_link = DB::table('account')->where('id',1)->first()->user_link;
            //$driver->get('https://ads.google.com/aw/keywordplanner/home?ocid=404176715&euid=386562172&__u=9317744028&uscid=404176715&__c=1874106035&authuser=0');
            $driver->get($user_link);
            sleep(10);


            $driver->findElement(WebDriverBy::cssSelector('div.card-frame'))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys('test');
            sleep(0.5);
            #Click enter on keyboard to search
            $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys(WebDriverKeys::ENTER);
            sleep(2);
            #Click button 'search' to redirect page resutl
            $driver->findElement(
                WebDriverBy::cssSelector('div.get-results-button-container > material-button.get-results-button')
            )->click();
            sleep(3);

            #Remove localtion in Viet Nam
            $driver->findElement(WebDriverBy::className('location-button'))->click();
            sleep(2);
            $driver->findElement(WebDriverBy::cssSelector('td.remove'))->click();
            sleep(3);
            try {
                $driver->findElement(WebDriverBy::cssSelector('material-button.highlighted'))->click();
                sleep(3);
            } catch (\Exception $e1) {
                $driver->findElement(WebDriverBy::cssSelector('material-button.highlighted'))->click();
                sleep(3);
            }

            #Select language English
            $driver->findElement(WebDriverBy::cssSelector('language-selector'))->click();
            sleep(2);
            $driver->findElement(WebDriverBy::cssSelector('input[aria-label="Tìm kiếm ngôn ngữ"]'))
                ->sendKeys('Anh');
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('input[aria-label="Tìm kiếm ngôn ngữ"]'))
                ->sendKeys(WebDriverKeys::ENTER);

            sleep(2);
            //============================================================================================================

            $page = DB::table('index_kw')->where('id',$stream)->first();

            for($i = $page->page_from; $i < $page->page_to ; $i++) {

                $listKeyWord = $this->get_Api($i);

                $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
                sleep(1);

                for ($j = 0; $j < 10; $j++) {
                    // xóa từ khóa cũ đi
                    $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))->sendKeys(WebDriverKeys::BACKSPACE);
                    sleep(0.3);
                }

                $dem = 0;
                for ($j = 0; $j < count($listKeyWord); $j++)
                {
                    if (isset($listKeyWord[$j]['keyword']))
                    {
                        if ($this->xoa_ky_tu_dac_biet($listKeyWord[$j]['keyword']) != '')
                        {
                            $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->click();
                            sleep(0.5);
                            $input = $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'));
                            $input->sendKeys($this->xoa_ky_tu_dac_biet($listKeyWord[$j]['keyword']));
                            sleep(0.7);
                            $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys(WebDriverKeys::ENTER);
                            sleep(0.5);
                            $dem++;
                        }
                    }

                }

                if ($dem == 0)
                {
                    $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->click();
                    sleep(0.5);
                    $input = $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'));
                    $input->sendKeys('test');
                    sleep(0.7);
                    $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys(WebDriverKeys::ENTER);
                    sleep(0.5);
                }

                $driver->findElement(
                    WebDriverBy::cssSelector('div.get-results-button-container > material-button.get-results-button')
                )->click();
                sleep(3);

                try{

                    $keys = $driver->findElements(WebDriverBy::xpath('//ess-cell[@essfield="text"]'));

                    $search = $driver->findElements(WebDriverBy::xpath('//ess-cell[@essfield="search_volume"]'));

                    $compete = $driver->findElements(WebDriverBy::xpath('//ess-cell[@essfield="competition"]'));
                }
                catch (\Exception $e)
                {
                    var_dump('error');
                    DB::table('index_kw')->where('id',$stream)->update([
                        'page_from' => $i+1
                    ]);
                    continue;
                }

                $keyword = '';
                for($j = 0; $j < count($listKeyWord); $j++)
                {
                    $search_volumn = '';
                    $canhtranh = '';

                    if(isset($listKeyWord[$j]['keyword']) && $listKeyWord[$j]['keyword'] != '')
                    {
                        $kw = strtolower($listKeyWord[$j]['keyword']);

                        for($k = 0; $k < count($keys); $k++)
                        {
                            $key_planner = strtolower($keys[$k]->getText());

                            if($this->check_latin($key_planner))
                            {
                                similar_text($kw,$key_planner,$percent);

                                if($percent > 80)
                                {
                                    $keyword = $keys[$k]->getText();
                                    $search_volumn = $this->convertToNumber($search[$k]->getText());
                                    $canhtranh = $compete[$k]->getText();
                                    break;
                                }
                            }
                            else
                            {
                                if($this->check_similar($kw,$key_planner))
                                {
                                    $keyword = $keys[$k]->getText();
                                    $search_volumn = $this->convertToNumber($search[$k]->getText());
                                    $canhtranh = $compete[$k]->getText();
                                    break;
                                }
                            }

                        }

                        if($search_volumn == '')
                            continue;

                        $ct = 0;
                        if($canhtranh == 'Vừa')
                            $ct = 1;
                        if($canhtranh == 'Cao')
                            $ct = 2;
                        if($canhtranh == '—')
                            $ct = 3;


                        DB::table('keyword_out')->insert([
                            'keyword' => $keyword,
                            'domain' => $listKeyWord[$j]['domain'],
                            'type' => $listKeyWord[$j]['type'],
                            'syntax' => $listKeyWord[$j]['syntax'],
                            'search_volumn' => $search_volumn,
                            'compete' => $ct,
                            'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
                            'updated_at' => Carbon::now('Asia/Ho_Chi_Minh'),
                        ]);
                    }

                }

                for($j = 0; $j < count($listKeyWord); $j++)
                {
                    DB::table('keyword_in')->where('id',$listKeyWord[$j]['id'])->update([
                        'check_kw' => 1
                    ]);
                }

                DB::table('index_kw')->where('id',$stream)->update([
                    'page_from' => $i+1
                ]);

            }
        }
        catch (\Exception $e) {
            $driver->quit();
            $this->checkKeywordPlanner($stream);
        }
    }

    public function xoa_ky_tu_dac_biet($str)
    {
        $ketqua = preg_replace('/([^\pL\.\ ]+)/u', '', $str);
        return $ketqua;
    }

    public function convertToNumber($str)
    {
        $string = explode('-',$str)[0];
        $arr = explode(' ',$string);
        $num = '';
        if($arr[1] == 'N')
            $num = '000';
        else if($arr[1] == 'Tr')
            $num = '000000';
        else if($arr[1] == '')
            $num = '';
        return $arr[0].$num;
    }

    public function check_latin($str)
    {
        $arr_latinh = [
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'q',
            'r',
            't',
            'w',
            'z',
            'v',
            'x',
            'u'
        ];
        $arr_str = [];

        for($i = 0; $i <  strlen($str);$i++)
        {
            array_push($arr_str,substr($str,$i,1));
        }

        foreach ($arr_latinh as $latinh)
        {
            foreach ($arr_str as $value)
            {
                if($latinh == $value)
                {
                    return true;
                }
            }
        }
        return false;
    }
    public function check_similar($str1,$str2)
    {
        if(strlen($str1) > strlen($str2))
        {
            $temp = $str2;
            $str2 = $str1;
            $str1 = $temp;
        }

        $str1 = strtolower($str1);
        $str2 = strtolower($str2);

        $str1 = preg_replace('/\s+/', '', $str1);
        $str2 = preg_replace('/\s+/', '', $str2);

        $arr_str1 = [];
        $arr_str2 = [];

        for($i = 0; $i <  strlen($str1);$i++)
        {
            array_push($arr_str1,substr($str1,$i,1));
        }

        for($i = 0; $i <  strlen($str2);$i++)
        {
            array_push($arr_str2,substr($str2,$i,1));
        }

        $dem = 0;
        for($i = 0; $i <  strlen($str1);$i++)
        {
            if(isset($arr_str2[$i]))
            {
                if($arr_str1[$i] == $arr_str2[$i])
                    $dem++;
            }
        }

        if($dem >= count($arr_str1) - 2)
        {
            return true;
        }
        return false;
    }

    public function get_Api($page)
    {
        $url = 'http://localhost/tools/public/getKeyWord?page='.$page;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $data  = json_decode($response);
        $value = json_decode(json_encode($data), true);

        return $value;
    }

    public function script1()
    {
        $this->checkKeywordPlanner(1);
    }

    public function script2()
    {
        $this->checkKeywordPlanner(2);
    }

    public function script3()
    {
        $this->checkKeywordPlanner(3);
    }

    public function script4()
    {
        $this->checkKeywordPlanner(4);
    }

    public function script5()
    {
        $this->checkKeywordPlanner(5);
    }

    public function script6()
    {
        $this->checkKeywordPlanner(6);
    }

    public function script7()
    {
        $this->checkKeywordPlanner(7);
    }

    public function script8()
    {
        $this->checkKeywordPlanner(8);
    }

    public function script9()
    {
        $this->checkKeywordPlanner(9);
    }

    public function script10()
    {
        $this->checkKeywordPlanner(10);
    }


}
