<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\InsertTag;

use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\Exception\InvalidInsertTagException;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\InsertTag\Resolver\InsertTagResolverNestedResolvedInterface;
use Contao\StringUtil;
use DigitaleDinge\CompanyBundle\Company\Company;
use DigitaleDinge\CompanyBundle\Model\CompanyModel;
use Twig\Environment;

#[AsInsertTag('company')]
#[AsInsertTag('company_id')]
class CompanyInsertTag implements InsertTagResolverNestedResolvedInterface
{
    private int|null $companyId = null;

    public function __construct(
        private readonly Company $company,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        if (null === $insertTag->getParameters()->get(0)) {
            throw new InvalidInsertTagException('Missing parameters for insert tag.');
        }

        $this->companyId = null;

        $parameters = $insertTag->getParameters()->all();

        if ('company_id' === $insertTag->getName()) {
            $this->companyId = (int) array_shift($parameters);
        }

        $result = $this->replaceInsertTag($parameters);

        return new InsertTagResult($result, OutputType::html);
    }

    private function replaceInsertTag(array $parameters): string
    {
        $company = $this->company->get($this->companyId);

        if (null === $company) {
            return '';
        }

        $name = $parameters[0] ?? null;
        $modifier = $parameters[1] ?? null;

        return match ($name) {
            'additional' => $this->getFromSerialized($company->additional, $modifier),
            'address' => $this->renderAddress($company, $modifier),
            'fax' => $this->getFromSerialized($company->fax_numbers, $modifier),
            'logo' => $this->renderLogo($company, $modifier),
            'mail' => $this->getFromSerialized($company->emails, $modifier),
            'mailto' => $this->getFromSerialized($company->emails, $modifier, 'mailto'),
            'phone' => $this->getFromSerialized($company->phone_numbers, $modifier),
            'socials' => $this->renderSocialMedia($company),
            'tel' => $this->getFromSerialized($company->phone_numbers, $modifier, 'tel'),
            'website' => $this->getFromSerialized($company->websites, $modifier),
            default => $company->{$name} ?? '',
        };
    }

    private function getFromSerialized(string|null $string, string|null $position, string|null $extra = null): string
    {
        $position ??= 1;
        $values = StringUtil::deserialize($string, true);

        $value = $values[$position - 1] ?? [];

        $string = (string) reset($value);

        if ($extra) {
            $string = match ($extra) {
                'tel', 'mailto' => $this->twig->render('@Contao/company/component/_link.html.twig', [
                    'link' => $string,
                    'href' => $string,
                    'href_prefix' => $extra . ':',
                ]),
                default => $string,
            };
        }

        return $string;
    }

    private function renderAddress(CompanyModel $company, string|null $modifier): string
    {
        return $this->twig->render('@Contao/company/component/_address.html.twig', [
            'company_model' => $company,
            'include_name' => $modifier === 'name',
        ]);
    }

    private function renderLogo(CompanyModel $company, string|null $modifier): string
    {
        return $this->twig->render('@Contao/company/component/_logo.html.twig', [
            'company_model' => $company,
            'logo_class' => $modifier
        ]);
    }

    private function renderSocialMedia(CompanyModel $company): string
    {
        return $this->twig->render('@Contao/company/component/_social_media.html.twig', [
            'company_socials' => $company->socials,
        ]);
    }
}
