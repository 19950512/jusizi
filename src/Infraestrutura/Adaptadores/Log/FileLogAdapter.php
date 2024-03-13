<?php

declare(strict_types=1);

namespace App\Infraestrutura\Adaptadores\Log;

use Exception;
use App\Application\Commands\Log\Log;
use App\Application\Commands\Log\Discord;
use App\Application\Commands\Log\Enumerados\Level;

class FileLogAdapter implements Log
{
    private string $_fileName = __DIR__.'/logs.txt';

    public function __construct(
        readonly private Discord $discord
    ){}
    
    public function log(Level $level, string $message): void
    {

        try {
            
            $repeater =  str_repeat('-', 50);

            $message = PHP_EOL.date('d/m/Y H:i:s') ." | {$level->name} | $message ".PHP_EOL.$repeater;

            if($level === Level::CRITICAL){

                $this->discord->send(
                    channel: $this->discord->getChannel($level),
                    message: $message
                );
            }

            if(!is_writable($this->_fileName)){
                throw new Exception('O arquivo não tem permissão de escrita. - '.$this->_fileName);
            }

            file_put_contents($this->_fileName, $message, FILE_APPEND);

        } catch (Exception $erro) {
        
            throw new Exception('Aconteceu algum erro no momento de criar o LOG. - '.$erro->getMessage());
        }
    }
}