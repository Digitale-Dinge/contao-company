<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Company;

use Contao\CoreBundle\Routing\PageFinder;
use Contao\PageModel;
use DigitaleDinge\CompanyBundle\Model\CompanyModel;

class Company
{
    private array $cache = [];

    public function __construct(
        readonly private PageFinder $pageFinder,
    ) {
    }

    public function get(int|string|null $id = null): CompanyModel|null
    {
        if (!$id) {
            return $this->getForCurrentPage();
        }

        return $this->getForId((int) $id);
    }

    public function getForId(int $id): CompanyModel|null
    {
        if (0 === $id) {
            return null;
        }

        return $this->cache[$id] ??= CompanyModel::findById($id);
    }

    public function getForCurrentPage(): CompanyModel|null
    {
        if (!$page = $this->pageFinder->getCurrentPage()) {
            return null;
        }

        $rootPage = PageModel::findById($page->loadDetails()->rootId);

        return $this->getForId((int) ($rootPage?->dd_company ?? 0));
    }
}
