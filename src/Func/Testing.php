<?php 
namespace Irfa\Lockout\Func;

use Irfa\Lockout\Func\Core;
use Illuminate\Console\Command;
use View;
use File;

class Testing extends Core {
    private $ret = [];

    public function testConfig(){
        $this->ret=[];
         $this->ret['err'] = 0;
        

        if(!empty(config('irfa.lockout'))){
            $this->confLoginAttemps();
            $this->confLogging();
            $this->confInput();
            $this->confFilePath();
            $this->confRedirectUrl();
            $this->confProtectActionPath();
            $this->confProtectMiddleware();
            $this->confMessage();
            $this->confExceptEnabled();
            $this->confExceptAccount();
  
        } else{
            $this->ret['err'] +=1;
            $this->ret['file'] = "<fg=yellow> Could't find config file. Try to run <fg=cyan>php artisan vendor:publish --tag=lockout-account"; 
        }

        return $this->ret;
    }
	public function testWriteEventFailedLogin($username){

    	$this->eventFailedLogin($username);

		$input = $username;
		$dir = config('irfa.lockout.lockout_file_path');
		$path = $dir.md5($input);

    	if(File::exists($path))
        {
        	return true;
        } 
    		return false;
    }
    public function testWritable($username){
		$input = $username;
		$dir = config('irfa.lockout.lockout_file_path');
		$path = $dir.md5($input);

    	if(is_writable($path))
        {
        	return true;
        } 
    		return false;
    }

    public function testManualLocking($username){
        $input = $username;
        $dir = config('irfa.lockout.lockout_file_path');
        $path = $dir.md5($input);

        if($this->lock_account($username))
        {
            return true;
        } 
            return false;
    }
    public function testUnlocking($username){
        $input = $username;
        $dir = config('irfa.lockout.lockout_file_path');
        $path = $dir.md5($input);
        $unlock =  $this->test_unlock_account($username);
        if($unlock)
        {
            return true;
        } 
            return false;
    }
    public function testLockLogin($username){
        $input = $username;
        $dir = config('irfa.lockout.lockout_file_path');
        $path = $dir.md5($input);
        $unlock =  $this->lockLogin($username);
        if($unlock)
        {
            return true;
        } 
            return false;
    }
//////Config
    private function confLoginAttemps(){
            if(is_numeric(config('irfa.lockout.login_attempts'))){
                $this->ret['login_attempts'] = '<fg=green>OK';
            } else{

                $this->ret['err'] +=1;
                $this->ret['login_attempts'] ='<fg=red>Must be a number';
            }
    }

    private function confLogging(){
       if(is_bool(config('irfa.lockout.logging'))){
               $this->ret['logging'] = '<fg=green>OK';
            } else{

                $this->ret['err'] +=1;
                $this->ret['logging'] = '<fg=red>Must be a Boolean'; 
            }
    }
    private function confInput(){
       
            if(is_string(config('irfa.lockout.input_name'))){
                $this->ret['input_name'] = '<fg=green>OK';
            } else{

                $this->ret['err'] +=1;
                $this->ret['input_name'] = '<fg=red>Must be a String'; 
            }
    }
     private function confFilePath(){
       
            
            if(is_writable(config('irfa.lockout.lockout_file_path'))){
                $this->ret['lockout_file_path'] = '<fg=green>OK';
            } else{
                $this->ret['lockout_file_path'] = '<fg=yellow>Write Permission Denied in '.config('irfa.lockout.lockout_file_path'); 
            }
        }
    private function confRedirectUrl(){
        if(!empty(config('irfa.lockout.redirect_url'))){
                $this->ret['redirect_url'] = '<fg=green>OK';
            } else{

                $this->ret['err'] +=1;
                $this->ret['redirect_url'] = '<fg=red>Must be provided'; 
            }
    }
    private function confProtectActionPath(){
        if(is_array(config('irfa.lockout.protected_action_path'))){
                $this->ret['protected_action_path'] = '<fg=green>OK';
                 if(!empty(config('irfa.lockout.protected_action_path'))){
                    $this->ret['protected_action_path'] = '<fg=green>OK';
                } else{
                     $this->ret['err'] +=1;
                     $this->ret['protected_action_path'] = '<fg=red>Must be provided'; 
                }
            } else{

                $this->ret['err'] +=1;
                $this->ret['protected_action_path'] = '<fg=red>Must be array'; 
            }

    }
    private function confProtectMiddleware(){
        if(is_array(config('irfa.lockout.protected_middleware_group'))){
                if(!empty(config('irfa.lockout.protected_middleware_group'))){
                    $this->ret['protected_middleware_group'] = '<fg=green>OK';
                } else{
                     $this->ret['err'] +=1;
                     $this->ret['protected_middleware_group'] = '<fg=red>Must be provided'; 
                }
            } else{

                $this->ret['err'] +=1;
                $this->ret['protected_middleware_group'] = '<fg=red>Must be array'; 
            }

    }
    private function confMessage(){
         if(is_string(config('irfa.lockout.message_name'))){
                $this->ret['message_name'] = '<fg=green>OK';
            } else{

                $this->ret['err'] +=1;
                $this->ret['message_name'] = '<fg=red>Must be a String'; 
            }

    }
     private function confExceptEnabled(){
         if(is_bool(config('irfa.lockout.enable_except_account'))){
                $this->ret['enable_except_account'] = '<fg=green>OK';
            } else{

                $this->ret['err'] +=1;
                $this->ret['enable_except_account'] = '<fg=red>Must be a String'; 
            }

    } 
    private function confExceptAccount(){
         if(is_array(config('irfa.lockout.except_account'))){
                if(!empty(config('irfa.lockout.except_account'))){
                    $this->ret['except_account'] = '<fg=green>OK';
                } else{
                    if(config('irfa.lockout.enable_except_account')){

                        $this->ret['except_account'] = '<fg=yellow>NOT SET';
                    } else{

                        $this->ret['except_account'] = '<fg=green>OK';
                    }
                }
            } else{

                $this->ret['err'] +=1;
                $this->ret['except_account'] = '<fg=red>Must be a array'; 
            }

    }
}
