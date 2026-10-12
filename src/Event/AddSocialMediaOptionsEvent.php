<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Event;

use DigitaleDinge\CompanyBundle\Company\Socials;
use Symfony\Contracts\EventDispatcher\Event;

class AddSocialMediaOptionsEvent extends Event
{
    /**
     * @var array<string, array<string, string>>
     */
    private array $socials = [];

    /**
     * @return array<string, array<string, string>>
     */
    public function getSocialMedia(): array
    {
        return [] === $this->socials ? $this->getDefaults() : $this->socials;
    }

    /**
     * @param array<string, array<string, string>> $socials
     */
    public function setSocialMedia(array $socials): void
    {
        $this->socials = $socials;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function getDefaults(): array
    {
        return [
            'General' => $this->enumToArray(Socials\General::cases()),
            'Video' => $this->enumToArray(Socials\Video::cases()),
            'Creative' => $this->enumToArray(Socials\Creative::cases()),
            'Development' => $this->enumToArray(Socials\Development::cases()),
        ];
    }

    /**
     * @param list<\BackedEnum> $cases
     *
     * @return array<string, string>
     */
    private function enumToArray(array $cases): array
    {
        $options = [];

        foreach ($cases as $case) {
            $options[$case->name] = (string) $case->value;
        }

        return $options;
    }
}
