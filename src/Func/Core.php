<?php
namespace  Irfa\Lockout\Func;

use Log;
use Illuminate\Support\Facades\Request, File, Lang, Session;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\Table;
use Irfa\Lockout\Initializing\Variable;

class Core extends Variable
{
    /**
     * Except account.
     *
     * @return boolean
     */
    protected function exceptAccount(){
        $this->initVar();
        if($this->except_enabled){
          if(in_array($this->input, $this->except_accounts)){
            return true;
          } else{
            return false;
          }
        } else{
         return false;
        }
    }

    /**
     * write login attempts if login attempt is triggered.
     *
     * @param string $username
     * @return void
     */
    protected function eventFailedLogin($username=null){
        $this->initVar();
        if($username !== null){
            $this->setPath($username);
        }
        if(!File::exists($this->dir)){
                File::makeDirectory($this->dir, 0755, true);
        }

        if(!File::exists($this->path))
        {
            $login_fail = 1;
        } else{

            $get = json_decode(File::get($this->path));
            $ip_list = $get->ip;
            if(!$this->checkIp($ip_list,$this->ip)){
                array_push($ip_list,$this->ip);
            }
            if($get->attempts == "lock"){
                $login_fail = "lock";
            } else{
                $login_fail = $get->attempts+1;
            }
        }
        
            $content = ['username' => $this->input,'attempts' => $login_fail,'ip' => isset($ip_list)?$ip_list:[$this->ip],'last_attempts' => date("Y-m-d H:i:s",time())];
            File::put($this->path,json_encode($content));
            if(File::exists($this->path)){
                chmod($this->path,0755);
            }
          
    }

    /**
     * Clean Lockout file if success login
     *
     * @param  string  $rootNamespace
     * @return void
     */
    protected function eventCleanLockoutAccount() {
       $this->initVar();
        $this->unlock_account($this->input);
          
    }

    /**
     * Logging Failed Login attempts
     * stored file in storage/logs/laravel.log
     *
     * @param  string  $middleware
     * @return void
     */
    protected function logging($middleware = "WEB") {
        if (config('irfa.lockout.logging')) {
                    Log::notice($middleware." | Login attempts fail | "."username : ".Request::input(config('irfa.lockout.input_name'))." | ipAddress : ".Request::ip()." | userAgent : ".$_SERVER['HTTP_USER_AGENT'].PHP_EOL);
            }
    }

        /**
         * Check if user is locked
         *
         * @param  string  $username
         * @return boolean
         */
    protected function is_locked($username){
        $this->initVar();
        $this->setPath($username);
        if(File::exists($this->path))
        {
            $get = json_decode(File::get($this->path));
            if($get->attempts > $this->attempts || $get->attempts == "lock"){
                return true;
            } else{
                return false;
            }
        } else{
            return false;
        }
    }

    /**
     * Show message if failed x attempts
     *
     * @return mixed
     */
    protected function showMessage() {
        if (Session::has(config('irfa.lockout.message_name'))) {
            return Session::get(config('irfa.lockout.message_name'));
        }

        return null;
    }

    /**
     * Locked account  if max attempts reached
     *
     * @return boolean
     */
    protected function lockLogin($username = null){
         $this->initVar();
        if(php_sapi_name() == "cli" AND $username != null){
            $this->setPath($username);
        }

        if(File::exists($this->path))
        {
                $get = json_decode(File::get($this->path));
                if($get->attempts == "lock"){
                return true;
                }
                if($get->attempts > $this->attempts){
                   
                    return true;
                } else {
                return false;
                }
        } else {
            return false;
            }
    }

        /**
         * Check ip locked
         *
         * @return boolean
         */
    private function checkIp($ip_list,$ip){
       $this->initVar();
        if(collect($ip_list)->contains($ip)){
            return true;
        } else{
            return false;
        }

    }

        /**
         * Clear all locked account
         *
         * @return boolean
         */
    public function clear_all(){
       $this->initVar();
        $file = new Filesystem();
        if($file->cleanDirectory($this->path)){
        return true;
        } else{
        return false;
        }
    }

        /**
         * Unlocking account manually.
         *
         * @param string $username
         * @return mixed
         */
    public function unlock_account($username){
        $this->initVar();
        $this->setPath($username);
            if(File::exists($this->path)){
            $readf = File::get($this->path);
                File::delete($this->path);
            if(php_sapi_name() == "cli"){
                echo Lang::get('lockoutMessage.user_unlock_success')."\n";
                return $readf;
              
            } else{
                return true;
            }
        } else{
            if(php_sapi_name() == "cli"){
                echo Lang::get('lockoutMessage.user_lock_404')."\n";
                return false;
            } else{
                return false;
            }
        }
        }

    /**
     * For Testing
     *
     * @return mixed
     */
    public function test_unlock_account($username){
        $this->initVar();
        $this->setPath($username);
            if(File::exists($this->path)){
            $readf = File::get($this->path);
                File::delete($this->path);
            if(php_sapi_name() == "cli"){
                return true;
              
            } else{
                return true;
            }
        } else{
            if(php_sapi_name() == "cli"){
                return false;
            } else{
                return false;
            }
        }
        }

    /**
     * Check account with details
     *
     * @param string $username
     * @return mixed
     */
    public function check_account($username){
        $this->initVar();
        $this->setPath($username);
        if(File::exists($this->path)){
                $readf = File::get($this->path);
                if(php_sapi_name() == "cli"){
                
                    return $readf;
                
                } else{
                    return $readf;
                }
            } else{
                if(php_sapi_name() == "cli"){
                    echo Lang::get('lockoutMessage.user_lock_404')."\n";
                    exit();
                } else{
                    return false;
                }
            }
        }

        /**
         * Locking account manually
         *
         * @param string $username
         * @return mixed
         */
    public function lock_account($username){
        $this->initVar();
        $sapi = php_sapi_name() == "cli"?"lock-via-cli":"lock-via-web";
        $this->setPath($username);
        try{
            if(!File::exists($this->dir)){
                File::makeDirectory($this->dir, 0755, true);
            }
                $login_fail = "lock";
        
                $content = ['username' => $this->input,'attempts' => $login_fail,'ip' => [$sapi],'last_attempts' => date("Y-m-d H:i:s",time())];
                File::put($this->path,json_encode($content));
                if(File::exists($this->path)){
                chmod($this->path,0755);
                }
                if(php_sapi_name() == "cli"){
                return Lang::get('lockoutMessage.user_lock_success')."\n";
                
                } else{
                return true;
                }
            } catch(\Exception $e){
                if(php_sapi_name() == "cli"){
                return "error";
                
                } else{
                return false;
                }
            }
    }
}