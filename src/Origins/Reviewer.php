<?php

declare(strict_types=1);

namespace PhpCfdi\SatCatalogosPopulate\Origins;

class Reviewer implements ReviewerInterface
{
    /** @var ResourcesGatewayInterface */
    private $gateway;

    public function __construct(ResourcesGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    public function accept(OriginInterface $origin): bool
    {
        return ($origin instanceof Origin);
    }

    public function review(OriginInterface $origin): Review
    {
        // obtener la información de la url del origen
        $response = $this->gateway->headers($origin->url());

        // si no se pudo obtener el recurso
        if (! $response->isSuccess()) {
            return new Review($origin, ReviewStatus::notFound());
        }

        // si el recurso no coincide con la última versión
        if (! $origin->hasLastVersion() || ! $response->dateMatch($origin->lastVersion())) {
            return new Review($origin, ReviewStatus::notUpdated());
        }

        // entonces el recurso coincide
        return new Review($origin, ReviewStatus::uptodate());
    }
}
