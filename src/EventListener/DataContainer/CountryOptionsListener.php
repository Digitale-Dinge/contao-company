<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Intl\Countries;

readonly class CountryOptionsListener
{
    public function __construct(private Countries $countries)
    {
    }

    /**
     * @return array<string, string>
     */
    #[AsCallback('tl_company', 'fields.country.options')]
    public function getCountryOptions(): array
    {
        return $this->countries->getCountries();
    }
}
