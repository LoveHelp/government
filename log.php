<?php
class Log
{
    private static $_log = null;

    private function __construct(){}

    private function __clone(){
        trigger_error('clone is not allowed!', E_USER_ERROR);
    }

    public static function getInstance(){
        if(!(self::$_log instanceof self)){
            self::$_log = new self;
        }
        return self::$_log;
    }

    private function logfile(){
        $name = date('Ymd') . '.log';
        $path = $_SERVER['DOCUMENT_ROOT'] . '/government/logs';       
        if (!file_exists($path)) {
            mkdir($path);
        }
        $file = "$path/$name";
        return $file;
    }

    private function write($con){
        $file = $this->logfile();
        file_put_contents($file, $con, FILE_APPEND);
    }

    private function writeArray($arr, $chk=false){
        $this->write('array(');
        $i=0;
        foreach ($arr as $key => $val) {
            if($i > 0)
                $this->write(', ');
            $con = "'".$key."' => ";
            if($chk){
                $type = gettype($val);
                $con = $con . '(' . $type;
                if(is_string($val)){
                    $con = $con . '[' . strlen($val) . ']';
                }else if(is_array($val)){
                    $con = $con . '[' . sizeof($val) . ']';
                }
                $con = $con . ')' . $val;
            }
            $this->write($con);
            $i++;
        }
        $this->write(');' . PHP_EOL);
    }

    public function msg($msg){
        $sdate = date('Y-m-d H:i:s ');
        $param = $sdate . "[INFO]" . $msg;
        $this->write($param . PHP_EOL);
    }

    public function  err($errcode, $errinfo){
        $sdate = date('Y-m-d H:i:s ');
        $con = $sdate . "[ERROR]" . $errcode . ': ';
        $this->write($con);
        if(is_array($errinfo)){
            $this->writeArray($errinfo);
        }else {
            $this->write($errinfo);
        }
    }

    public function sql($msg, $param){
        $sdate = date('Y-m-d H:i:s ');
        $con = $sdate . "[INFO]" . $msg . PHP_EOL;
        $this->write($con);
        if(is_array($param)){
            $this->write($sdate);
            $this->writeArray($param, true);
        }else {
            $this->write($param);
        }
    }
}