<?php
namespace  Irfa\Lockout\Func;

use Illuminate\Support\Facades\Request,File,Lang;
use Log;
use Symfony\Component\Console\Helper\Table;

class Core
{
    protected function eventFailedLogin(){
        $ip = Request::ip();
        $matchip= config('irfa.lockout.match_ip') == true ? $ip :null;
        $dir = config('irfa.lockout.lockout_file_path');
        $path = $dir.md5(Request::input(config('irfa.lockout.input_name')));

        if(!File::exists($dir)){
             File::makeDirectory($dir, 0750, true);
        }

        if(!File::exists($path))
        {
            $login_fail = 1;
        } else{

            $get = json_decode(File::get($path));
            $ip_list = $get->ip;
            if(!$this->checkIp($ip_list,$ip)){
                array_push($ip_list,$ip);
            }
            if($get->attemps == "lock"){
              $login_fail = "lock";
            } else{
              $login_fail = $get->attemps+1;
            }
        }
        
            $content = ['attemps' => $login_fail,'ip' => isset($ip_list)?$ip_list:[$ip],'last_attemps' => time()];
            File::put($path,json_encode($content));
          
    }
    protected function logging(){
      if(config('irfa.lockout.logging')){
                 Log::notice("Login attemps fail | "."username : ".Request::input(config('irfa.lockout.input_name'))." | ipAddress : ".Request::ip()." | userAgent : ".$_SERVER['HTTP_USER_AGENT'].PHP_EOL);
            }
    }
    protected function lockLogin(){
        $ip = Request::ip();
        $matchip= empty(config('irfa.lockout.match_ip'))?false:config('irfa.lockout.match_ip');
        $dir = config('irfa.lockout.lockout_file_path');
        $attemps = config('irfa.lockout.login_attemps');
        $path = $dir.md5(Request::input('email'));
        if(File::exists($path))
        {
              $get = json_decode(File::get($path));
          // dd($get->attemps.">".$attemps);
              if($get->attemps == "lock"){
                return true;
              }
              if($get->attemps > $attemps){
                  if($matchip){
                    if($this->checkIp($ip_list,$ip)){
                        return true;
                    } else{
                        return false;
                    }
                  } else{
                    return true;
                  }
              } else{
                return false;
              }
        } else{
            return false;
            }
    }
    private function checkIp($ip_list,$ip){
        if(collect($ip_list)->contains($ip)){
            return true;
        } else{
            return false;
        }

    }

    public function unlock_account($username){
        $ip = Request::ip();
        $matchip= empty(config('irfa.lockout.match_ip'))?false:config('irfa.lockout.match_ip');
        $dir = config('irfa.lockout.lockout_file_path');
        $attemps = config('irfa.lockout.attemps');
        $path = $dir.md5($username);

        if(File::exists($path)){
          $readf = File::get($path);
             File::delete($path);
            if(php_sapi_name() == "cli"){
              echo Lang::get('lockoutMessage.user_unlock_success')."\n";
              return $readf;
              
            } else{
             return redirect()->back()->with(config('irfa.lockout.message_name'), Lang::get('lockoutMessage.user_unlock_success'));
            }
        } else{
           if(php_sapi_name() == "cli"){
              echo Lang::get('lockoutMessage.user_lock_404')."\n";
                exit();
           } else{
             return redirect()->back()->with(config('irfa.lockout.message_name'), Lang::get('lockoutMessage.user_lock_404'));
           }
        }
      }
      public function check_account($username){
        $dir = config('irfa.lockout.lockout_file_path');
        $path = $dir.md5($username);

        if(File::exists($path)){
          $readf = File::get($path);
            if(php_sapi_name() == "cli"){
              
              return $readf;
              
            } else{
             return redirect()->back()->with(config('irfa.lockout.message_name'), Lang::get('lockoutMessage.user_unlock_success'));
            }
        } else{
           if(php_sapi_name() == "cli"){
              echo Lang::get('lockoutMessage.user_lock_404')."\n";
                exit();
           } else{
             return redirect()->back()->with(config('irfa.lockout.message_name'), Lang::get('lockoutMessage.user_lock_404'));
           }
        }
      }

      public function lock_account($username){
        $ip = php_sapi_name() == "cli"?"lock-via-cli":"lock-via-web";
        $matchip= empty(config('irfa.lockout.match_ip'))?false:config('irfa.lockout.match_ip');
        $dir = config('irfa.lockout.lockout_file_path');
        $attemps = config('irfa.lockout.login_attemps');
        $path = $dir.md5($username);
        try{
          if(!File::exists($dir)){
               File::makeDirectory($dir, 0750, true);
          }
              $login_fail = "lock";
          
              $content = ['attemps' => $login_fail,'ip' => isset($ip_list)?$ip_list:[$ip],'last_attemps' => time()];
              File::put($path,json_encode($content));
              if(php_sapi_name() == "cli"){
                return Lang::get('lockoutMessage.user_lock_success')."\n";
                  
              } else{
                return redirect()->back()->with(config('irfa.lockout.message_name'), Lang::get('lockoutMessage.user_lock_success'));
                }
            } catch(Exception $e){
               if(php_sapi_name() == "cli"){
                return "error";
                  
              } else{
                return redirect()->back()->with(config('irfa.lockout.message_name'), Lang::get('lockoutMessage.user_lock_fail'));
                }
            }
      }
}