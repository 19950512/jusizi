<?php

declare(strict_types=1);

namespace App\Application\Commands\Billing\Fronteiras;

/**
 * @see cada implementação utiliza ou não o array $filtros
 * @param string $dataInicio
 * @param string $dataFim
 * @param string $tipoData
 * @param string $filtros
 * @param string $pagina
 * @return void
 */
final class InputBoundaryBoletosByFilters
{
    public function __construct(
        public string $dataInicio,
        public string $dataFim,
        public string $tipoData,
        public array $filtros,
        public int $pagina,
        public int $qtdPorPagina
    ){}
}