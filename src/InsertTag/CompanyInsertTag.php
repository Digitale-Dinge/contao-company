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
use DigitaleDinge\CompanyBundle\Company\OpeningTimes;
use DigitaleDinge\CompanyBundle\Event\AddSocialMediaOptionsEvent;
use DigitaleDinge\CompanyBundle\Model\CompanyModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

#[AsInsertTag('company')]
#[AsInsertTag('company_id', method: 'resolveCompanyId')]
class CompanyInsertTag implements InsertTagResolverNestedResolvedInterface
{
    public function __construct(
        private readonly Company $company,
        private readonly Environment $twig,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        return $this->resolve(null, $this->getParameters($insertTag));
    }

    public function resolveCompanyId(ResolvedInsertTag $insertTag): InsertTagResult
    {
        $parameters = $this->getParameters($insertTag);

        return $this->resolve((int) array_shift($parameters), $parameters);
    }

    /**
     * @return non-empty-list<string>
     */
    private function getParameters(ResolvedInsertTag $insertTag): array
    {
        $parameters = $insertTag->getParameters()->all();

        if ([] === $parameters) {
            throw new InvalidInsertTagException('Missing parameters for insert tag.');
        }

        return $parameters;
    }

    /**
     * @param list<string> $parameters
     */
    private function resolve(int|null $companyId, array $parameters): InsertTagResult
    {
        $company = $this->company->get($companyId);

        if (!$company instanceof CompanyModel) {
            return new InsertTagResult('');
        }

        return new InsertTagResult(
            $this->replaceInsertTag($company, $parameters),
            OutputType::html,
            cacheTags: ['contao.db.tl_company.'.$company->id],
        );
    }

    /**
     * @param list<string> $parameters
     */
    private function replaceInsertTag(CompanyModel $company, array $parameters): string
    {
        $name = $parameters[0] ?? null;
        $modifier = $parameters[1] ?? null;

        return match ($name) {
            'additional' => $this->getFromSerialized($company->additional, $modifier),
            'address' => $this->renderAddress($company, $modifier),
            'fax' => $this->getFromSerialized($company->fax_numbers, $modifier),
            'is_open' => $this->renderOpeningStatus($company, 'next' === $modifier),
            'logo' => $this->renderLogo($company, $modifier),
            'mail' => $this->getFromSerialized($company->emails, $modifier),
            'mailto' => $this->getFromSerialized($company->emails, $modifier, 'mailto'),
            'phone' => $this->getFromSerialized($company->phone_numbers, $modifier),
            'socials' => $this->renderSocialMedia($company),
            'social' => $this->getSocial($company->socials, $modifier),
            'tel' => $this->getFromSerialized($company->phone_numbers, $modifier, 'tel'),
            'website' => $this->getFromSerialized($company->websites, $modifier),
            default => null === $name ? '' : $this->toString($company->{$name}),
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
                'href_prefix' => $extra.':',
            ]),
            default => $string,
        };
    }

    /**
     * @param array<mixed> $values
     */
    private function getValueByAliasOrId(int|string|null $identifier, array $values): string
    {
        if (!isset($values[0]) || !\is_array($values[0])) {
            return '';
        }

        $identifier ??= 1;

        if (is_numeric($identifier)) {
            $value = $values[(int) $identifier - 1] ?? [];

            if (!\is_array($value)) {
                return '';
            }

            return $this->toString(1 === \count($value) ? reset($value) : $value['value'] ?? '');
        }

        $list = array_column($values, 'value', 'key');

        return $this->toString($list[$identifier] ?? '');
    }

    private function toString(mixed $value): string
    {
        return \is_scalar($value) ? (string) $value : '';
    }

    private function getSocial(string|null $serialized, int|string|null $identifier): string
    {
        $options = $this->getFlattenedSocialMediaOptions();
        $socials = StringUtil::deserialize($serialized, true);

        foreach ($socials as $social) {
            if (!\is_array($social)) {
                continue;
            }

            $key = $social['social'] ?? null;

            if (!\is_string($key) || $key !== $identifier || !isset($options[$key])) {
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
            'include_name' => 'name' === $modifier,
        ]);
    }

    private function renderLogo(CompanyModel $company, string|null $modifier): string
    {
        return $this->twig->render('@Contao/company/logo.html.twig', [
            'company_model' => $company,
            'logo_class' => $modifier,
        ]);
    }

    private function renderOpeningStatus(CompanyModel $company, bool $showNextOpening): string
    {
        return $this->twig->render('@Contao/company/component/_opening_status.html.twig', [
            'opening_times' => new OpeningTimes($company),
            'show_next_opening' => $showNextOpening,
        ]);
    }

    private function renderSocialMedia(CompanyModel $company): string
    {
        return $this->twig->render('@Contao/company/social_media.html.twig', [
            'company_socials' => $company->socials,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function getFlattenedSocialMediaOptions(): array
    {
        $event = new AddSocialMediaOptionsEvent();
        $this->eventDispatcher->dispatch($event);
        $options = $event->getSocialMedia();

        return array_merge(...array_values($options));
    }
}
