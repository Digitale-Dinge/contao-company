<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Event;

use DigitaleDinge\CompanyBundle\Company\Socials;
use Symfony\Contracts\EventDispatcher\Event;

class AddSocialMediaOptionsEvent extends Event
{
    private array $socials = [];

    public function getSocialMedia(): array
    {
        return empty($this->socials) ? $this->getDefaults() : $this->socials;
    }

    public function setSocialMedia(array $socials): void
    {
        $this->socials = $socials;
    }

    private function getDefaults(): array
    {
        return [
            'General' => $this->enumToArray(Socials\General::cases()),
            'Video' => $this->enumToArray(Socials\Video::cases()),
            'Creative' => $this->enumToArray(Socials\Creative::cases()),
            'Development' => $this->enumToArray(Socials\Development::cases()),
        ];
    }

    private function enumToArray($enum): array
    {
        return array_column($enum, 'value', 'name');
    }
}
