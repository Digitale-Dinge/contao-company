<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use DigitaleDinge\CompanyBundle\Event\AddSocialMediaOptionsEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class SocialMediaOptionsListener
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {}

    #[AsCallback('tl_company', 'fields.socials.attributes')]
    public function setSocialMediaOptions(array $attributes, DataContainer $dc): array
    {
        $event = new AddSocialMediaOptionsEvent();
        $this->eventDispatcher->dispatch($event);

        $attributes['fields']['social']['options'] = $event->getSocialMedia();

        return $attributes;
    }
}
