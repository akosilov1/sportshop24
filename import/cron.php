<? 
if(!$_SERVER["DOCUMENT_ROOT"]) $_SERVER["DOCUMENT_ROOT"] = "/home/cc54877/web/sportshop24.ru/public_html";//"/home/cc54877/web/sportshop24.ru/public_html";
$f_cron = fopen($_SERVER["DOCUMENT_ROOT"]."/import/cron_log.txt", "a");
//fputcsv($f_cron, array(time(),date("d.m.Y H:i:s")),";");

date_default_timezone_set('Europe/Moscow');
$cron = new Cron("update.log");
fclose($f_cron);
class Cron{
    var $log, $path;
    function __construct($file)
    {
        global $f_cron;
        $this->path = $_SERVER["DOCUMENT_ROOT"]."/import/";
        $this->log = $this->GetLog($file);
        fwrite($f_cron,date("d.m.y H:i:s")."\n");
        $this->Init();
    }
    function GetLog($log){
        $f = fopen($this->path.$log,"r");
        $log = fread($f,9999);
        $rez = json_decode($log,true);
        fclose($f);
        return $rez;
    }
    function Init(){
        global $f_cron;
        echo date("d.m.y H:i:s")."<pre>\n*** INIT ***\n";
        
        print_r($this->log);
        echo "\n";
        switch ($this->log["STATUS"]){
            case "STOP":
                if($this->log["URL"]){
                    echo "STATUS STOP >>>\n*** START UPDATE ***\n";
                    fwrite($f_cron,print_r($this->log, true)."\n");
                    $rez = $this->GetScript("https://sportshop24.ru/import/soap.php".$this->log["URL"]."&cron=y");
                    print_r($rez);
                    echo "\n*** END UPDATE ***\n";
                    fwrite($f_cron,"> ".$rez."\n");
                }
                break;
            case "END":
                echo "STATUS END >>>";
                $old_time = $this->log["TIME"];
                $new_time = time();
                $old_dmy = explode("-", date("d-m-y",$old_time));
                $new_dmy = explode("-", date("d-m-y",$new_time));
                $ddd = false;
                foreach ($old_dmy as $k => $old){
                    if($new_dmy[$k] > $old){
                        $ddd = true;
                        break;
                    }
                }
                if($ddd && date("H",time()) > 1)
                    $this->Start();
                echo "\n/END\n";
                break;

        }

    }
    function Start(){
        global $f_cron;
        echo "START>>>\n";
        $url="https://sportshop24.ru/import/soap.php?date-m=0&date-d=1&action=update&cron=y";
        $this->GetScript($url);
        fwrite($f_cron,"*** START ***\n".date("d.m.y H")."\n".print_r($this->log, true)."\n");
    }
    function GetScript($url){
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        $r = curl_exec($ch);
        if($_GET['test']=='Y')
            echo htmlentities($r);
        $rez = curl_getinfo($ch,CURLINFO_HTTP_CODE );
        return $rez;
    }
}
