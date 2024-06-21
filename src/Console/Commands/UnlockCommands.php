<?php

namespace Irfa\Lockout\Console\Commands;

use Illuminate\Console\Command;
use Irfa\Lockout\Func\Core;
use Symfony\Component\Console\Helper\Table;

class UnlockCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lockout:unlock {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unlock Account';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Core $core)
    { 
        $ret = $core->unlock_account($this->argument('username'));
            $table = new Table($this->output);
                $read_enc = json_decode( $ret);
                if(!empty($read_enc)){
                    $time = $read_enc->last_attempts;
                    $attempts = $read_enc->attempts;
                    $ip = $read_enc->ip;
                    $table->setRows([
                            ['<fg=cyan>Login attempts',  $attempts],
                            ['<fg=cyan>Last login attempts',$time],
                            ['<fg=cyan>Last IP Address',empty(end($ip))? "unknown":end($ip)],
                            ['<fg=cyan>Unlocked at',date('Y-m-d H:i:s', time())],
                        ]);
                            $table->render();
                }
                // $this->line('<fg=yellow>Valid input is  lock, unlock, and attempts.');
        
    }
    
}