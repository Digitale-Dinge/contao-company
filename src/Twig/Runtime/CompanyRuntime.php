<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

final class CompanyRuntime implements RuntimeExtensionInterface
{
    public function getCompanyDetails(string|null $type = null): string|null
    {
        return match ($type) {
            default => null,
        };
    }
}
