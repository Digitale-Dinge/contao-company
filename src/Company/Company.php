<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Company;

use Contao\PageModel;
use DigitaleDinge\CompanyBundle\Model\CompanyModel;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;

class Company
{
    private array $cache = [];

    public function __construct(
        readonly private RequestStack $requestStack,
        readonly private Connection $connection,
    ) {
    }

    public function get(int|string|null $id = null): CompanyModel|null
    {
        if (null === $id) {
            return $this->getByPageModel();
        }

        return $this->cache[(int) $id] ??= CompanyModel::findById($id);
    }

    private function getByPageModel(): CompanyModel|null
    {
        $pageModel = $this->requestStack->getCurrentRequest()?->attributes->get('pageModel');

        if (!($pageModel instanceof PageModel)) {
            return null;
        }

        if (0 === ($companyId = $this->connection->fetchOne('SELECT dd_company FROM tl_page WHERE id=?', [$pageModel->rootId]))) {
            return null;
        }

        return $this->cache[(int) $companyId] ??= CompanyModel::findById($companyId);
    }
}
