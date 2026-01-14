<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Company\Socials;

enum General: string
{
    case facebook  = 'Facebook';
    case instagram = 'Instagram';
    case linkedin  = 'LinkedIn';
    case xing      = 'Xing';
    case threads   = 'Threads';
    case mastodon  = 'Mastodon';
}
