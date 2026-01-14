<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Company\Socials;

enum Video: string
{
    case youtube = 'YouTube';
    case vimeo   = 'Vimeo';
    case tiktok  = 'TikTok';
    case twitch  = 'Twitch';
}
