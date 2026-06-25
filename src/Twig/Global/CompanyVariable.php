<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Twig\Global;

use Contao\StringUtil;
use DigitaleDinge\CompanyBundle\Company\Company;
use DigitaleDinge\CompanyBundle\Model\CompanyModel;

readonly class CompanyVariable
{
    public function __construct(private Company $company)
    {}

    public function get(int|null $id = null): CompanyModel|null
    {
        return $this->company->get($id);
    }

    public function getName(int|null $id = null): string|null
    {
        return $this->company->get($id)?->name;
    }

    public function getLogo(int|null $id = null): string|null
    {
        return $this->company->get($id)?->logo;
    }

    public function getStreet(int|null $id = null): string|null
    {
        return $this->company->get($id)?->street;
    }

    public function getPostal(int|null $id = null): string|null
    {
        return $this->company->get($id)?->postal;
    }

    public function getCity(int|null $id = null): string|null
    {
        return $this->company->get($id)?->city;
    }

    public function getState(int|null $id = null): string|null
    {
        return $this->company->get($id)?->state;
    }

    public function getCountry(int|null $id = null): string|null
    {
        return $this->company->get($id)?->country;
    }

    public function getOpening_times(int|null $id = null): array
    {
        return StringUtil::deserialize($this->company->get($id)?->opening_times, true);
    }

    public function getClosing_times(int|null $id = null): array
    {
        return StringUtil::deserialize($this->company->get($id)?->closing_times, true);
    }

    /** ToDo: Deprecated, to be replaced with the map variant! use getPhone_numbers_map() instead */
    public function getPhone_numbers(int|null $id = null): array
    {
        trigger_deprecation('digitaledinge/company-bundle', '0.x', 'Using "%s()" will change its return value, use "%s_map()" for now.', __METHOD__, __METHOD__);

        return StringUtil::deserialize($this->company->get($id)?->phone_numbers, true);
    }

    public function getPhone_numbers_map(int|null $id = null): array
    {
        return array_column(StringUtil::deserialize($this->company->get($id)?->phone_numbers, true), 'phone');
    }

    /** ToDo: Deprecated, to be replaced with the map variant! use getEmails_map() instead */
    public function getEmails(int|null $id = null): array
    {
        trigger_deprecation('digitaledinge/company-bundle', '0.x', 'Using "%s()" will change its return value, use "%s_map()" for now.', __METHOD__, __METHOD__);

        return StringUtil::deserialize($this->company->get($id)?->emails, true);
    }

    public function getEmails_map(int|null $id = null): array
    {
        return array_column(StringUtil::deserialize($this->company->get($id)?->emails, true), 'email');
    }

    /** ToDo: Deprecated, to be replaced with the map variant! use getWebsites_map() instead */
    public function getWebsites(int|null $id = null): array
    {
        trigger_deprecation('digitaledinge/company-bundle', '0.x', 'Using "%s()" will change its return value, use "%s_map()" for now.', __METHOD__, __METHOD__);

        return StringUtil::deserialize($this->company->get($id)?->websites, true);
    }

    public function getWebsites_map(int|null $id = null): array
    {
        return array_column(StringUtil::deserialize($this->company->get($id)?->websites, true), 'website');
    }

    /** ToDo: Deprecated, to be replaced with the map variant! use getFax_numbers_map() instead */
    public function getFax_numbers(int|null $id = null): array
    {
        trigger_deprecation('digitaledinge/company-bundle', '0.x', 'Using "%s()" will change its return value, use "%s_map()" for now.', __METHOD__, __METHOD__);

        return StringUtil::deserialize($this->company->get($id)?->fax_numbers, true);
    }

    public function getFax_numbers_map(int|null $id = null): array
    {
        return array_column(StringUtil::deserialize($this->company->get($id)?->fax_numbers, true), 'fax');
    }

    /** ToDo: Deprecated, to be replaced with the map variant! use getSocials_map() instead */
    public function getSocials(int|null $id = null): array
    {
        trigger_deprecation('digitaledinge/company-bundle', '0.x', 'Using "%s()" will change its return value, use "%s_map()" for now.', __METHOD__, __METHOD__);

        return StringUtil::deserialize($this->company->get($id)?->socials, true);
    }

    public function getSocials_map(int|null $id = null): array
    {
        return array_column(StringUtil::deserialize($this->company->get($id)?->socials, true), 'url', 'social');
    }

    /** ToDo: Deprecated, to be replaced with the map variant! use getAdditional_map() instead */
    public function getAdditional(int|null $id = null): array
    {
        trigger_deprecation('digitaledinge/company-bundle', '0.x', 'Using "%s()" will change its return value, use "%s_map()" for now.', __METHOD__, __METHOD__);

        return StringUtil::deserialize($this->company->get($id)?->additional, true);
    }

    public function getAdditional_map(int|null $id = null): array
    {
        return array_column(StringUtil::deserialize($this->company->get($id)?->additional, true), 'value', 'key');
    }
}
