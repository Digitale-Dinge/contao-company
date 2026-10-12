# Upgrade

This file explains what a project has to do when it updates contao-company. What changed and why is in the
[changelog](CHANGELOG.md). Before 1.0, every minor version was considered a major one (SemVer).

> [!CAUTION]
> Always create a database backup before you run a migration. No backup, no sorry (=.

## Backwards compatibility

Minor and patch versions keep what projects build on:

- Stored values and DCA field names
- PHP classes and methods not marked `@internal`, and service IDs
- Template names, the insert tags and the getters of the `company` Twig global

Not covered are template blocks and markup, the CSS classes and the back end widgets. Inherit through the template
variables instead of overriding blocks. When one of these changes, this file says how to adapt.

## From 1.0 to 1.1

### Requirements

contao-company requires PHP 8.4 now.

### Abbreviate weekdays

The "Abbreviate weekdays" option of the opening times element (`company_short_days`) is a virtual field now. Contao
stores it in the `jsonData` column of `tl_content`, and the old column is no longer read. Elements that had the option
enabled show full weekday names again. You have two ways to deal with that.

**Find the elements and re-save them by hand.** This lists every opening times element with the option enabled:

```sql
SELECT id, pid, ptable FROM tl_content WHERE type = 'company_opening_times' AND company_short_days = 1;
```

**Or migrate the value on Contao 6.** Contao 6 ships `AbstractColumnToVirtualMigration`, which copies the column into
`jsonData` and drops it afterwards. Add this to your app and run `contao:migrate`:

```php
<?php

declare(strict_types=1);

namespace App\Migration;

use Contao\CoreBundle\Migration\Version600\AbstractColumnToVirtualMigration;

class CompanyShortDaysMigration extends AbstractColumnToVirtualMigration
{
    protected function getMapping(): array
    {
        return ['tl_content' => ['company_short_days' => 'jsonData']];
    }
}
```

Run the query before the migration. Without the migration, the old column stays in the database until
`contao:migrate --with-deletes` removes it, so the query keeps working until then.

### Code and templates

- The back end date widget is registered as `company_date` instead of `date`. If your own DCA uses
  `'inputType' => 'date'` from this bundle, change it to `'inputType' => 'company_date'`.
- Its template moved from `backend/widget/date.html.twig` to `backend/widget/company_date.html.twig`. Move your
  override along with it.
- The closing periods list renders each entry through the new `closing_period` block. If you overrode
  `company_closing_times`, check whether overriding `closing_period` or passing
  `company_closing_period_attributes` is enough now.
- In `company/logo.html.twig`, the `logoHref` variable inside the `logo_inner` block is now `logo_href`.

## From 0.2 to 1.0

Nothing to do. 1.0 is the same bundle as 0.2.

## From 0.1 to 0.2

Run `contao:migrate` to add the `timezone` column to `tl_company` and the fields of the new content elements to
`tl_content`.
