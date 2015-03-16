<?php
/**
 * LINUX操作
 * @author  mosen
 */
class Process_System 
{
    /**
     * [getCpu description]
     * @return [type] [description]
     */
    public static function getCpu() {
        if (file_exists('/proc/cpuinfo')) {
            $info = shell_exec("cat /proc/cpuinfo");
            return substr_count($info, "processor");
        }
        return 1;
    }
    
    /**
     * [getProcessId description]
     * @param  [type] $script [description]
     * @return [type]         [description]
     */
    public static function getProcessId($script) {
        exec("ps -ef | grep '{$script}' | grep -v grep | awk '{print $2}'", $output);
        return $output;
    }
    
    /**
     * [getProcessInfo description]
     * @param  [type] $script [description]
     * @return [type]         [description]
     */
    public static function getProcessInfo($script) {
        exec("ps -ef | grep '{$script}' | grep -v grep | awk '{print $9}'", $output);
        return $output;
    }
    
    /**
     * [getProcessNum description]
     * @param  [type] $script [description]
     * @return [type]         [description]
     */
    public static function getProcessNum($script) {
        return shell_exec ( "ps -ef | grep '{$script}' | grep -v grep | awk '{count++}END{print count}'");
    }
    
    /**
     * [stop description]
     * @param  [type] $script [description]
     * @return [type]         [description]
     */
    public static function stop($script) {
        $ids = self::getProcessId($script);
        $status = true;
        if ($ids && is_array($ids)) {
            // echo "kill: {$script}...";
            foreach($ids as $id){
                if (self::stopByid($id)) {
                    // echo "ok";
                } else {
                    $status = false;
                    // echo "fail";
                    break;
                }
            }
            // echo "\n";
        }
        return $status;
    }
    
    /**
     * [stopByid description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function stopByid($id) {        
        exec("kill -9 $id", $output, $status);
        return $status === 0 ? true : false;
    }

    /**
     * [start description]
     * @param  [type]  $bin    [description]
     * @param  [type]  $script [description]
     * @param  integer $num    [description]
     * @param  string  $log    [description]
     * @param  string  $sudo   [description]
     * @return [type]          [description]
     */
    public static function start($bin, $script, $num = 1, $log = '/dev/null', $sudo = 'sudo -u nobody ') {
        $current = self::getProcessNum($script);
        $num -= $current;
        if ($num > 0) {
            $command = "{$sudo} {$bin} {$script} >> $log &";
            for($i = 0; $i<$num; $i++) {
                exec($command, $output, $status);
                // echo "{$command}...{$status}\n";
                if ($status !== 0) {
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * [startByRoot description]
     * @param  [type]  $bin    [description]
     * @param  [type]  $script [description]
     * @param  integer $num    [description]
     * @param  string  $log    [description]
     * @return [type]          [description]
     */
    public static function startByRoot($bin, $script, $num = 1, $log = '/dev/null') {
        return self::start($bin, $script, $num, $log, '');
    }
    
    /**
     * [exists description]
     * @param  [type] $script [description]
     * @return [type]         [description]
     */
    public static function exists($script) {
        return self::getProcessNum($script) > 1;
    }
    
    /**
     * [getServerIp description]
     * @return [type] [description]
     */
    public static function getServerIp() {
        exec( "/sbin/ifconfig | awk '/inet addr/{print $2}' | awk -F: '{print $2}'", $output);
        return $output;
    }
}

