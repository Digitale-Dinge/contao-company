<h1 align="center">Contao Company</h1>

<p align="center">
    <a href="https://packagist.org/packages/digitaledinge/contao-company"><img src="https://img.shields.io/packagist/v/digitaledinge/contao-company" alt="packagist version"/></a>
    <a href="https://packagist.org/packages/digitaledinge/contao-company"><img src="https://img.shields.io/packagist/dt/digitaledinge/contao-company?color=f47c00" alt="amount of downloads"/></a>
    <a href="https://packagist.org/packages/digitaledinge/contao-company"><img src="https://img.shields.io/packagist/dependency-v/digitaledinge/contao-company/php?color=474A8A" alt="minimum php version"></a>
</p>

---

## Description

Manage your company details in the Contao back end and assign a company to each root page. Show them with insert tags,
the `company` Twig global or the content elements for opening times and schema.org structured data.

Requires PHP `^8.4` and Contao `^5.7 || ^6.0`.

---

+ [Getting started](#getting-started)
+ [Insert tags](#insert-tags)
+ [Content elements](#content-elements)
    + [Opening times](#opening-times)
    + [Closing periods](#closing-periods)
    + [Structured data](#structured-data)
+ [Twig global](#twig-global)
+ [Events](#events)
+ [Changelog and upgrading](#changelog-and-upgrading)

---

## Getting started

1. **Install the bundle**

   ```bash
   composer require digitaledinge/contao-company
   ```

   Then update the database with `vendor/bin/contao-console contao:migrate`.

2. **Create a company**

   Go to Content → Companies and create your company with the details you want to show on your site.

3. **Assign it to a root page**

   The Twig global and the insert tags resolve the company of the current page. Assign the company under Layout →
   Site structure → Edit root page → Company.

4. **Use it**

   Show company data with the [insert tags](#insert-tags), in Twig with the [`company` global](#twig-global) or with
   the [content elements](#content-elements) for opening times and structured data.

5. **Optional: time zone**

   Opening times are read in the company's time zone. Leave the field empty to use the time zone from the Contao system
   settings. Set it when the company is in a different time zone than the app.

---

## Insert tags

`{{company::...}}` shows the company of the current page. `{{company_id::ID::...}}` targets a specific company by its
ID.

```
{{company::name}}
{{company::phone}}
{{company_id::5::name}}
{{company_id::5::phone}}
```

### Company details

These insert tags return the field values of the company record.

| Insert tag             | Description                                         |
|------------------------|-----------------------------------------------------|
| `{{company::logo}}`    | Renders the company logo (`company/logo.html.twig`) |
| `{{company::name}}`    | Displays the company name                           |
| `{{company::street}}`  | Displays the street                                 |
| `{{company::postal}}`  | Displays the postal code                            |
| `{{company::city}}`    | Displays the city                                   |
| `{{company::state}}`   | Displays the state                                  |
| `{{company::country}}` | Displays the country                                |

> [!TIP]
> Any field of the company record works this way with `{{company::FIELD_NAME}}`.

### Phone numbers

Phone numbers are a list. Access them by their position in the back end, starting at 1.

| Insert tag              | Description                                                   |
|-------------------------|---------------------------------------------------------------|
| `{{company::phone}}`    | Displays the first phone number                               |
| `{{company::phone::2}}` | Displays the second phone number                              |
| `{{company::tel}}`      | Renders the first phone number as a `<a href="tel:...">` link |
| `{{company::tel::2}}`   | Renders the second phone number as a `tel:` link              |

### Fax numbers

| Insert tag            | Description                    |
|-----------------------|--------------------------------|
| `{{company::fax}}`    | Displays the first fax number  |
| `{{company::fax::2}}` | Displays the second fax number |

### E-mail addresses

| Insert tag               | Description                                                        |
|--------------------------|--------------------------------------------------------------------|
| `{{company::mail}}`      | Displays the first e-mail address                                  |
| `{{company::mail::2}}`   | Displays the second e-mail address                                 |
| `{{company::mailto}}`    | Renders the first e-mail address as a `<a href="mailto:...">` link |
| `{{company::mailto::2}}` | Renders the second e-mail address as a `mailto:` link              |

### Websites

| Insert tag                | Description                     |
|---------------------------|---------------------------------|
| `{{company::website}}`    | Displays the first website URL  |
| `{{company::website::2}}` | Displays the second website URL |

### Address

| Insert tag                   | Description                                                             |
|------------------------------|-------------------------------------------------------------------------|
| `{{company::address}}`       | Renders the full address block (`company/component/_address.html.twig`) |
| `{{company::address::name}}` | Renders the full address block including the company name               |

### Logo

| Insert tag                    | Description                                           |
|-------------------------------|-------------------------------------------------------|
| `{{company::logo}}`           | Renders the company logo                              |
| `{{company::logo::my-class}}` | Renders the company logo with an additional CSS class |

### Social media

| Insert tag                      | Description                                                           |
|---------------------------------|-----------------------------------------------------------------------|
| `{{company::socials}}`          | Renders the full social media list (`company/social_media.html.twig`) |
| `{{company::social::facebook}}` | Renders a link for the social media entry with the key `facebook`     |

### Additional fields

| Insert tag                   | Description                                |
|------------------------------|--------------------------------------------|
| `{{company::additional}}`    | Displays the first additional field value  |
| `{{company::additional::2}}` | Displays the second additional field value |

### Opening status

| Insert tag                   | Description                                                                              |
|------------------------------|------------------------------------------------------------------------------------------|
| `{{company::is_open}}`       | Renders the current opening status (`company/component/_opening_status.html.twig`)       |
| `{{company::is_open::next}}` | Shows "Opens today at 14:00" instead of "Currently closed" when it opens later today     |

The status is computed in the browser in the company's time zone and respects the closing periods.

### A specific company

Every insert tag above also works with `company_id`, with the company ID as the first parameter:

```
{{company_id::5::name}}
{{company_id::5::tel}}
{{company_id::5::tel::2}}
{{company_id::5::mailto::2}}
{{company_id::5::address::name}}
{{company_id::5::logo::my-class}}
{{company_id::5::social::facebook}}
{{company_id::5::is_open}}
```

---

## Content elements

Leave the company selection empty to use the company of the current root page, or pick one explicitly.

### Opening times

Renders the weekly opening times as a table, the closing periods as a list and the live opening status.

| Option               | Description                                                                             |
|----------------------|-----------------------------------------------------------------------------------------|
| Company              | A specific company, or empty for the company of the current root page                   |
| Abbreviate weekdays  | `Mon` instead of `Monday`, localized via ICU                                            |
| Show next opening    | "Opens today at 14:00" instead of "Currently closed" when the company opens later today |

Choose a variant in the element's template dropdown:

| Template                                          | Output                                                                |
|---------------------------------------------------|-----------------------------------------------------------------------|
| `content_element/company_opening_times`           | All seven days, closed days read "Closed"                             |
| `content_element/company_opening_times/open_only` | Days without opening times are left out                               |
| `content_element/company_opening_times/grouped`   | Consecutive days with identical times collapse: `Mon–Fri 08:00–17:00` |

The templates work on a [spatie/opening-hours] object:

```twig
{% for day, hours in opening_times.openingHours.forWeek %}
    {{ day|format_datetime(pattern: 'EEEE') }}:
    {% for range in hours %}{{ range.start }}–{{ range.end }}{% else %}closed{% endfor %}
{% endfor %}
```

Overlapping ranges from the back end are merged, so `08:00–13:00` and `12:00–17:00` become `08:00–17:00`.

### Closing periods

Each closing period has a start and end date, optional start and end times and an optional reason. Without a time, the
whole day applies, so "Opens at 14:00 due to inventory" is a period on one day with the end time `14:00`. The reason
supports insert tags such as `{{iflng::de}}Inventur{{iflng}}{{iflng::en}}Inventory{{iflng}}`.

In the templates, `opening_times` provides:

| Variable                               | Content                                                                                                                       |
|----------------------------------------|-------------------------------------------------------------------------------------------------------------------------------|
| `opening_times.closingTimes`           | The periods with `from`, `from_time`, `to`, `to_time` and the raw `reason`. The times are `null` when the whole day applies |
| `opening_times.specialOpeningHours`    | The opening hours of every date a closing period changes. Dates whose hours stay the same are left out                       |

The `closing_period` block renders each period and the reason with `|insert_tag`. Add attributes to the list, the
entry and the reason with `company_closing_times_attributes`, `company_closing_period_attributes` and
`company_closing_reason_attributes`.

### Structured data

Adds a schema.org `LocalBusiness` or `Organization` node to the page's JSON-LD and renders nothing visible.

| Option  | Description                                                                   |
|---------|-------------------------------------------------------------------------------|
| Company | A specific company, or empty for the company of the current root page         |
| Type    | `LocalBusiness` (default) or `Organization`                                   |
| URL     | Used as `url` and `@id`. Empty uses the absolute URL of the current root page |

The node contains `name`, `url`, `@id`, `logo`, `address` as `PostalAddress`, the first `telephone`, the first
`faxNumber`, the first `email`, `vatID` and all social media links as `sameAs`.

A `LocalBusiness` also gets the opening hours as `openingHoursSpecification` and the closing periods as
`specialOpeningHoursSpecification`, as long as the company has any. `Organization` has no such properties. Closing
periods are written per affected date with the opening hours that remain on that day. Fully closed days become
`00:00`–`00:00`, and the reason becomes the `description`.

Several elements for the same company on one page merge into one node.

---

## Twig global

The `company` variable is available in every Twig template. It returns the company of the current page or a specific
company by ID.

### The company model

`company.name` is enough for a single value. To use several values, get the model with `company.get`:

```twig
{# Current page #}
{% set company_model = company.get %}

{# Specific company by ID #}
{% set company_model = company.get(5) %}
```

### Simple fields

```twig
{{ company_model.name }}
{{ company_model.street }}
{{ company_model.postal }}
{{ company_model.city }}
{{ company_model.state }}
{{ company_model.country }}
{{ company_model.logo }}
```

### Lists

The lists are serialized strings on the model, so deserialize them first:

```twig
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

The `company` global returns them already deserialized for the company of the current page:

```twig
{% set emails = company.emails %}
{% set phone_numbers = company.phone_numbers %}
{% set fax_numbers = company.fax_numbers %}
{% set websites = company.websites %}
{% set socials = company.socials %}
{% set additional = company.additional %}
```

### List entries

Lists start at index `0`. Index access works directly on the `company.*` lists. Deserialize `company_model.*` first.

```twig
{# company global, already deserialized #}
{% set first_email = company.emails[0].email ?? null %}
{% set third_email = company.emails[2].email ?? null %}

{# company model, deserialize first #}
{% set first_email = company_model.emails|deserialize[0].email ?? null %}
{% set third_email = company_model.emails|deserialize[2].email ?? null %}
```

`|first` is a shorthand for the first entry:

```twig
{% set first_email = company.emails|first?.email ?? null %}
{% set first_phone = company.phone_numbers|first?.phone ?? null %}
```

### Social media

Each social media entry has `social` (the platform label) and `url`:

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

---

## Events

### `AddSocialMediaOptionsEvent`

Dispatched whenever the social media options are built, in the back end select field and when insert tags are
resolved. Use it to replace the default platforms with your own.

| Group       | Platforms                                              |
|-------------|--------------------------------------------------------|
| General     | Facebook, Instagram, LinkedIn, Xing, Threads, Mastodon |
| Video       | YouTube, Vimeo, TikTok, Twitch                         |
| Creative    | Pinterest, Behance, Reddit                             |
| Development | GitHub, GitLab                                         |

Call `setSocialMedia()` with your own grouped array:

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
                'facebook' => 'Facebook',
                'instagram' => 'Instagram',
            ],
            'Custom' => [
                'myplatform' => 'My Platform',
            ],
        ]);
    }
}
```

> [!NOTE]
> `setSocialMedia()` replaces all defaults. To keep them, merge your additions into `getSocialMedia()`:

```php
public function __invoke(AddSocialMediaOptionsEvent $event): void
{
    $event->setSocialMedia([
        ...$event->getSocialMedia(),
        'Custom' => [
            'myplatform' => 'My Platform',
        ],
    ]);
}
```

---

## Changelog and upgrading

| Document                   | Content                                         |
|----------------------------|-------------------------------------------------|
| [Changelog](CHANGELOG.md)  | What changed in each version                    |
| [Upgrade](UPGRADE.md)      | What a project has to do when it updates        |

[spatie/opening-hours]: https://github.com/spatie/opening-hours
