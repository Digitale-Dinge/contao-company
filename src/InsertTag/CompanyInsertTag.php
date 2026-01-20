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
use DigitaleDinge\CompanyBundle\Event\AddSocialMediaOptionsEvent;
use DigitaleDinge\CompanyBundle\Model\CompanyModel;
use Psr\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

#[AsInsertTag('company')]
#[AsInsertTag('company_id')]
class CompanyInsertTag implements InsertTagResolverNestedResolvedInterface
{
    private int|null $companyId = null;

    public function __construct(
        private readonly Company $company,
        private readonly Environment $twig,
        private readonly EventDispatcherInterface $eventDispatcher,
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
            'social' => $this->getSocial($company->socials, $modifier),
            'tel' => $this->getFromSerialized($company->phone_numbers, $modifier, 'tel'),
            'website' => $this->getFromSerialized($company->websites, $modifier),
            default => $company->{$name} ?? '',
        };
    }

    private function getFromSerialized(string|null $serialized, string|null $position, string|null $extra = null): string
    {
        $string = $this->getValueByAliasOrId($position, StringUtil::deserialize($serialized, true));

        if (null === $extra) {
            return $string;
        }

        return match ($extra) {
            'tel', 'mailto' => $this->twig->render('@Contao/company/component/_link.html.twig', [
                'link' => $string,
                'href' => $string,
                'href_prefix' => $extra . ':',
            ]),
            default => $string,
        };
    }

    private function getValueByAliasOrId(int|string|null $identifier, array $values): string
    {
        if (!isset($values[0]) || !\is_array($values[0])) {
            return '';
        }

        if ($identifier === null) {
            return (string) reset($values[0]);
        } elseif (\is_numeric($identifier)) {
            $value = $values[$identifier - 1] ?? [];
            return (string) (count($value) === 1 ? reset($value) : $value['value'] ?? '');
        }

        $list = array_column($values, 'value', 'key');

        return $list[$identifier] ?? '';
    }

    private function getSocial(string|null $serialized, int|string|null $identifier): string
    {
        $options = $this->getFlattenedSocialMediaOptions();
        $socials = StringUtil::deserialize($serialized, true);

        foreach ($socials as $social) {
            $key = $social['social'] ?? null;

            if ($key !== $identifier || !isset($options[$key])) {
                continue;
            }

            return $this->twig->render('@Contao/company/component/_link.html.twig', [
                'link' => $options[$key],
                'href' => $social['url'] ?? null,
            ]);
        }

        return '';
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
        return $this->twig->render('@Contao/company/logo.html.twig', [
            'company_model' => $company,
            'logo_class' => $modifier
        ]);
    }

    private function renderSocialMedia(CompanyModel $company): string
    {
        return $this->twig->render('@Contao/company/social_media.html.twig', [
            'company_socials' => $company->socials,
        ]);
    }

    private function getFlattenedSocialMediaOptions(): array
    {
        $event = new AddSocialMediaOptionsEvent();
        $this->eventDispatcher->dispatch($event);
        $options = $event->getSocialMedia();

        return array_merge(...array_values($options));
    }
}
