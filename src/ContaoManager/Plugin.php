<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use DigitaleDinge\CompanyBundle\DigitaleDingeCompanyBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            (new BundleConfig(DigitaleDingeCompanyBundle::class))
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
