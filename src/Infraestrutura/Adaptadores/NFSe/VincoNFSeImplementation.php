<?php

declare(strict_types=1);

namespace App\Infraestrutura\Adaptadores\NFSe;

use App\Application\Commands\NFSe\Fronteiras\InputBoundaryEmitirNFSeAPI;
use App\Application\Commands\NFSe\NFSeAPI;

class VincoNFSeImplementation implements NFSeAPI
{
    public function emitirNFSe(InputBoundaryEmitirNFSeAPI $params): void
    {

        

    }
}
