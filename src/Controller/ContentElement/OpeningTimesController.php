<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use DigitaleDinge\CompanyBundle\Company\Company;
use DigitaleDinge\CompanyBundle\Company\OpeningTimes;
use DigitaleDinge\CompanyBundle\Model\CompanyModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('company_opening_times', category: 'company')]
class OpeningTimesController extends AbstractContentElementController
{
    public function __construct(private readonly Company $company)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $companyId = $model->company;

        if (!($companyModel = $this->company->get(is_numeric($companyId) ? (int) $companyId : null)) instanceof CompanyModel) {
            return new Response();
        }

        $template->set('company_model', $companyModel);
        $template->set('opening_times', new OpeningTimes($companyModel));
        $template->set('short_days', (bool) $model->company_short_days);
        $template->set('show_next_opening', (bool) $model->company_next_opening);

        $this->tagResponse($companyModel);

        return $template->getResponse();
    }
}
