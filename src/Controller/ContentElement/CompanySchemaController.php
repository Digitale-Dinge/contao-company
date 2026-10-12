<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Filesystem\VirtualFilesystemInterface;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\Routing\PageFinder;
use Contao\CoreBundle\Routing\ResponseContext\JsonLd\JsonLdManager;
use Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use DigitaleDinge\CompanyBundle\Company\Company;
use DigitaleDinge\CompanyBundle\Company\OpeningTimes;
use DigitaleDinge\CompanyBundle\Model\CompanyModel;
use Spatie\SchemaOrg\LocalBusiness;
use Spatie\SchemaOrg\Organization;
use Spatie\SchemaOrg\Schema;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

#[AsContentElement('company_schema', category: 'company')]
class CompanySchemaController extends AbstractContentElementController
{
    public function __construct(
        private readonly Company $company,
        private readonly PageFinder $pageFinder,
        private readonly VirtualFilesystemInterface $filesStorage,
        private readonly InsertTagParser $insertTagParser,
        private readonly ResponseContextAccessor $responseContextAccessor,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $companyId = $model->company;

        if (!($companyModel = $this->company->get(is_numeric($companyId) ? (int) $companyId : null)) instanceof CompanyModel) {
            return new Response();
        }

        $responseContext = $this->responseContextAccessor->getResponseContext();
        $type = $model->company_schema_type;
        $url = $model->company_schema_url;

        if ($responseContext?->has(JsonLdManager::class)) {
            $responseContext
                ->get(JsonLdManager::class)
                ->getGraphForSchema(JsonLdManager::SCHEMA_ORG)
                ->set(
                    $this->getSchema(
                        $companyModel,
                        \is_string($type) ? $type : null,
                        \is_string($url) && '' !== $url ? $url : $this->getRootPageUrl($request),
                    ),
                    'company_'.$companyModel->id,
                )
            ;
        }

        $this->tagResponse($companyModel);

        return new Response();
    }

    private function getSchema(CompanyModel $company, string|null $type, string|null $url): LocalBusiness|Organization
    {
        $address = Schema::postalAddress()
            ->streetAddress($company->street)
            ->postalCode($company->postal)
            ->addressLocality($company->city)
            ->addressRegion($company->state)
            ->addressCountry(strtoupper((string) $company->country))
        ;

        $openingTimes = new OpeningTimes($company, $this->insertTagParser);
        $schema = 'Organization' === $type ? Schema::organization() : Schema::localBusiness();

        return $schema
            ->name($company->name)
            ->setProperty('url', $url)
            ->setProperty('@id', $url)
            ->setProperty('logo', $this->getLogoUrl($company->logo))
            ->telephone($this->getColumn($company->phone_numbers, 'phone')[0] ?? null)
            ->faxNumber($this->getColumn($company->fax_numbers, 'fax')[0] ?? null)
            ->email($this->getColumn($company->emails, 'email')[0] ?? null)
            ->vatID($company->vat)
            ->setProperty('sameAs', $this->getColumn($company->socials, 'url') ?: null)
            ->if([] !== $address->getProperties(), static fn (LocalBusiness|Organization $schema) => $schema->address($address))
            ->if($schema instanceof LocalBusiness, static fn (LocalBusiness $schema) => $schema
                ->setProperty('openingHoursSpecification', $openingTimes->getSchemaSpecification() ?: null)
                ->setProperty('specialOpeningHoursSpecification', $openingTimes->getSchemaSpecialSpecification() ?: null),
            )
        ;
    }

    private function getColumn(string|null $serialized, string $key): array
    {
        return array_values(array_filter(array_column(StringUtil::deserialize($serialized, true), $key)));
    }

    private function getLogoUrl(string|null $uuid): string|null
    {
        if (!$uuid) {
            return null;
        }

        try {
            return (string) $this->filesStorage->generatePublicUri(Uuid::fromBinary($uuid)) ?: null;
        }
        catch (\Throwable) {
            return null;
        }
    }

    private function getRootPageUrl(Request $request): string|null
    {
        if (!($rootPage = $this->pageFinder->findRootPageForRequest($request)) instanceof \Contao\PageModel) {
            return null;
        }

        return $this->generateContentUrl($rootPage, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
