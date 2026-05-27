<h1 align="center">Contao Company</h1>
<p align="center">
    <a href="https://github.com/Digitale-Dinge/contao-company"><img src="https://img.shields.io/github/v/release/Digitale-Dinge/contao-company" alt="github version"/></a>
    <a href="https://packagist.org/packages/digitaledinge/contao-company"><img src="https://img.shields.io/packagist/dt/digitaledinge/contao-company?color=f47c00" alt="amount of downloads"/></a>
    <a href="https://packagist.org/packages/digitaledinge/contao-company"><img src="https://img.shields.io/packagist/dependency-v/digitaledinge/contao-company/php?color=474A8A" alt="minimum php version"></a>
</p>

## Description

Manage companies and render company information across multiple domains.

## Setup

1. **Create a company**

   Navigate to Content → Companies and create your company, filling in the details you want to make available across
   your site.

2. **Assign to a root page**

   For the company Twig global and insert tags to resolve automatically based on the current page context, assign your
   company to the respective root page under Layout → Site Structure → Edit root page → Company.

3. **Use in templates or RTE**

   This bundle does not provide any frontend modules or content elements. It is intended to be used directly within your Twig templates via the company global variable or in text via insert tags.
   Refer to the Twig Global section for available properties and examples.

## Insert Tags

Your company information can be displayed using the following *insert tags*.

There are two available insert tag prefixes. Use `{{company::...}}` to display the company associated with the current page context, or `{{company_id::ID::...}}` to target a specific company by its ID.

**Examples**
```
{{company::name}}
{{company::phone}}
{{company_id::5::name}}
{{company_id::5::phone}}
```

---

### Company Details

These insert tags return raw field values directly from the company record.

| Insert tag             | Description                                         |
|------------------------|-----------------------------------------------------|
| `{{company::logo}}`    | Renders the company logo (`company/logo.html.twig`) |
| `{{company::name}}`    | Displays the company name                           |
| `{{company::street}}`  | Displays the street                                 |
| `{{company::postal}}`  | Displays the postal code                            |
| `{{company::city}}`    | Displays the city                                   |
| `{{company::state}}`   | Displays the state                                  |
| `{{company::country}}` | Displays the country                                |

> Any field on the company model can be accessed this way via `{{company::FIELD_NAME}}`.

---

### Phone Numbers

Phone numbers are stored as a list and can be accessed by their position (1-based index) as configured in the backend.

| Insert tag              | Description                                                   |
|-------------------------|---------------------------------------------------------------|
| `{{company::phone}}`    | Displays the first phone number                               |
| `{{company::phone::2}}` | Displays the second phone number                              |
| `{{company::tel}}`      | Renders the first phone number as a `<a href="tel:...">` link |
| `{{company::tel::2}}`   | Renders the second phone number as a `tel:` link              |

---

### Fax Numbers

| Insert tag            | Description                    |
|-----------------------|--------------------------------|
| `{{company::fax}}`    | Displays the first fax number  |
| `{{company::fax::2}}` | Displays the second fax number |

---

### E-mail Addresses

| Insert tag               | Description                                                        |
|--------------------------|--------------------------------------------------------------------|
| `{{company::mail}}`      | Displays the first e-mail address                                  |
| `{{company::mail::2}}`   | Displays the second e-mail address                                 |
| `{{company::mailto}}`    | Renders the first e-mail address as a `<a href="mailto:...">` link |
| `{{company::mailto::2}}` | Renders the second e-mail address as a `mailto:` link              |

---

### Websites

| Insert tag                | Description                     |
|---------------------------|---------------------------------|
| `{{company::website}}`    | Displays the first website URL  |
| `{{company::website::2}}` | Displays the second website URL |

---

### Address

| Insert tag                   | Description                                                             |
|------------------------------|-------------------------------------------------------------------------|
| `{{company::address}}`       | Renders the full address block (`company/component/_address.html.twig`) |
| `{{company::address::name}}` | Renders the full address block including the company name               |

---

### Logo

| Insert tag                    | Description                                           |
|-------------------------------|-------------------------------------------------------|
| `{{company::logo}}`           | Renders the company logo                              |
| `{{company::logo::my-class}}` | Renders the company logo with an additional CSS class |

---

### Social Media

| Insert tag                      | Description                                                           |
|---------------------------------|-----------------------------------------------------------------------|
| `{{company::socials}}`          | Renders the full social media list (`company/social_media.html.twig`) |
| `{{company::social::facebook}}` | Renders a link for the social media entry with the key `facebook`     |

---

### Additional Fields

| Insert tag                   | Description                            |
|------------------------------|----------------------------------------|
| `{{company::additional}}`    | Displays the first additional field value  |
| `{{company::additional::2}}` | Displays the second additional field value |

---

### Targeting a Specific Company by ID

All of the above insert tags are also available with `company_id`, passing the company ID as the first parameter:

```
{{company_id::5::name}}
{{company_id::5::tel}}
{{company_id::5::tel::2}}
{{company_id::5::mailto::2}}
{{company_id::5::address::name}}
{{company_id::5::logo::my-class}}
{{company_id::5::social::facebook}}
```

## Twig Global

The `company` variable is available globally in all Twig templates. It provides access to the company associated with
the current page context, or to a specific company by ID.

### Get the company model

In some cases you want to access one value like `company.name`. If you want to use more values of the company, you can
get the full company model by using `company.get` and access the values directly.

```twig
{# Current page context #}
{% set company_model = company.get %}

{# Specific company by ID #}
{% set company_model = company.get(5) %}
```

### Simple fields

Simple fields are accessed directly as properties on the model:

```twig
{% set company_model = company.get %}

{{ company_model.name }}
{{ company_model.street }}
{{ company_model.postal }}
{{ company_model.city }}
{{ company_model.state }}
{{ company_model.country }}
{{ company_model.logo }}
```

### Serialized lists

The following properties return serialized strings on the model and must be deserialized first.
When accessing lists directly via the `company` global, deserialization is handled automatically.

```twig
{% set company_model = company.get %}

{% for row in company_model.emails|deserialize|default([]) %}
    {{ row.email }}
{% endfor %}

{% for row in company_model.phone_numbers|deserialize|default([]) %}
    {{ row.phone }}
{% endfor %}

{% for row in company_model.fax_numbers|deserialize|default([]) %}
    {{ row.fax }}
{% endfor %}

{% for row in company_model.websites|deserialize|default([]) %}
    {{ row.website }}
{% endfor %}

{% for row in company_model.socials|deserialize|default([]) %}
    {{ row.social }} {# platform label #}
    {{ row.url }}
{% endfor %}

{% for row in company_model.additional|deserialize|default([]) %}
    {{ row.key }}
    {{ row.value }}
{% endfor %}
```

Alternatively, the `company` global exposes these directly as pre-deserialized arrays — without needing a model instance:

```twig
{% set emails = company.emails %}
{% set phone_numbers = company.phone_numbers %}
{% set fax_numbers = company.fax_numbers %}
{% set websites = company.websites %}
{% set socials = company.socials %}
{% set additional = company.additional %}
```

For a specific company by ID, get the model first and then deserialize:

```twig
{% set company_model = company.get(5) %}
{% set emails = company_model.emails|deserialize %}
{% set socials = company_model.socials|deserialize %}
```

### Accessing entries of a list

Lists are arrays, so the first item is at index `0`, the second at `1`, and so on. Note that direct index access works on the pre-deserialized `company.*` properties. When using `company_model.*`, deserialize first.

```twig
{# Via company global (already deserialized) #}
{% set first_email = company.emails[0].email ?? null %}
{% set third_email = company.emails[2].email ?? null %}

{# Via company model (deserialize first) #}
{% set first_email = company_model.emails|deserialize[0].email ?? null %}
{% set third_email = company_model.emails|deserialize[2].email ?? null %}
```

Alternatively, use the `|first` Twig filter as a shorthand for the first entry:

```twig
{% set first_email = company.emails|first?.email ?? null %}
{% set first_phone = company.phone_numbers|first?.phone ?? null %}
```

### Social media

Each social media entry exposes `social` (platform label) and `url`.

```twig
{% for row in company.socials %}
    {{ include('@Contao/company/component/_link.html.twig', {
        link: row.social,
        href: row.url,
        title: row.social,
        target_blank: true,
    }) }}
{% endfor %}
```

## Events

### `AddSocialMediaOptionsEvent`

This event is dispatched whenever the social media options are built — both in the backend select field and when resolving insert tags. It allows you to replace the default social media options with your own.

The following platforms are available by default:

| Group       | Platforms                                              |
|-------------|--------------------------------------------------------|
| General     | Facebook, Instagram, LinkedIn, Xing, Threads, Mastodon |
| Video       | YouTube, Vimeo, TikTok, Twitch                         |
| Creative    | Pinterest, Behance, Reddit                             |
| Development | GitHub, GitLab                                         |

To add or replace platforms, create an event listener and call `setSocialMedia()` with your own grouped array:

```php
<?php

declare(strict_types=1);

namespace App\EventListener;

use DigitaleDinge\CompanyBundle\Event\AddSocialMediaOptionsEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class CustomSocialMediaListener
{
    public function __invoke(AddSocialMediaOptionsEvent $event): void
    {
        $event->setSocialMedia([
            'General' => [
                'facebook'  => 'Facebook',
                'instagram' => 'Instagram',
            ],
            'Custom' => [
                'myplatform' => 'My Platform',
            ],
        ]);
    }
}
```

> Note that calling `setSocialMedia()` replaces all defaults. If you want to keep the existing platforms, retrieve them
> first via `getSocialMedia()` and merge your additions in.

```php
public function __invoke(AddSocialMediaOptionsEvent $event): void
{
    $existing = $event->getSocialMedia();

    $event->setSocialMedia(array_merge($existing, [
        'Custom' => [
            'myplatform' => 'My Platform',
        ],
    ]));
}
```
