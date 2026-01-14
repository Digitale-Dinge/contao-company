<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Twig\Extension;

use DigitaleDinge\CompanyBundle\Twig\Global\CompanyVariable;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class CompanyExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly CompanyVariable $companyVariable,
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'company' => $this->companyVariable,
        ];
    }

    /*public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'company',
                [CompanyDataRuntime::class, 'getCompanyDetails']
            ),
        ];
    }*/
}
