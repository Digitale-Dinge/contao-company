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
        $position = (int) ($parameters[1] ?? 1);
        $modifier = $parameters[2] ?? null;

        return match ($name) {
            'phone' => $this->getFromSerialized($company->phone_numbers, $position, $modifier),
            'tel' => $this->getFromSerialized($company->phone_numbers, $position, 'tel'),
            'mail' => $this->getFromSerialized($company->emails, $position, $modifier),
            'mailto' => $this->getFromSerialized($company->emails, $position, 'mailto'),
            'website' => $this->getFromSerialized($company->websites, $position, $modifier),
            'fax' => $this->getFromSerialized($company->fax_numbers, $position, $modifier),
            'additional' => $this->getFromSerialized($company->additional, $position, $modifier),
            'address' => $this->getAddress($company),
            default => $company->{$name} ?? '',
        };
    }

    private function getFromSerialized(string|null $string, int $position, string|null $modifier = null): string
    {
        $values = StringUtil::deserialize($string, true);

            $value = $values[$position - 1] ?? [];

        $string = (string) reset($value);

        if ($modifier) {
            $string = match ($modifier) {
                'tel', 'mailto' => $this->twig->render('@Contao/company/component/_link.html.twig', [
                    'link' => $string,
                    'href' => $string,
                    'href_prefix' => $modifier . ':',
                ]),
                default => $string,
            };
        }

        return $string;
    }

    private function getAddress(CompanyModel $company): string
    {
        return '';
    }
}
