# Changelog

> [!IMPORTANT]
> Since 1.0.0, contao-company follows [semantic versioning][semver]. Breaking changes only happen in major versions.
> Before 1.0.0, every minor version could contain breaking changes.

## [1.1.0] (2026-10-12)

**Tag:** [`1.1.0`][tag-1.1.0]

**Branch:** `main`

### Added

- Closing periods can have a start time, an end time and a reason. A period without times still covers the whole day,
  so "Opens at 14:00 due to inventory" is a period on one day that ends at `14:00`. The reason supports insert tags
  such as `iflng` for multilingual sites
- The opening times element has a "Show next opening" option, and `{{company::is_open::next}}` does the same. Both show
  "Opens today at 14:00" instead of "Currently closed" when the company opens later that day
- `OpeningTimes::getSpecialOpeningHours()` returns the opening hours of every date a closing period changes, which is
  the weekly hours minus the closing periods
- The closing periods list renders each entry through the new `closing_period` block. The
  `company_closing_times_attributes`, `company_closing_period_attributes` and `company_closing_reason_attributes`
  variables add attributes to the list, the entry and the reason

### Changed

- contao-company requires PHP `^8.4` instead of `^8.3`
- Closing periods in the structured data are written per affected date with the opening hours that remain on that
  day. Fully closed days stay `00:00` to `00:00`, and the reason becomes the `description`
- Companies without opening hours or closing periods no longer get empty `openingHoursSpecification` and
  `specialOpeningHoursSpecification` entries in the structured data
- "Abbreviate weekdays" in the opening times element is a virtual field now, so its value lives in `jsonData`. Elements
  that had it enabled show full weekday names again until you re-save them or migrate the value. See
  [UPGRADE.md][upgrade-1.1]
- The back end date widget is registered as `company_date` instead of `date`, and its template moved from
  `backend/widget/date.html.twig` to `backend/widget/company_date.html.twig`
- `{{company::additional}}` returns the value of the first additional field instead of its key
- `{{company::id}}` returns the ID instead of failing with a type error
- Companies and root pages are loaded through the Contao framework, which is initialized first if needed

## [1.0.0] (2026-10-08)

**Tag:** [`1.0.0`][tag-1.0.0]

**Branch:** `main`

**Commit:** [`3bd45e2`][3bd45e2]

The first stable version. The bundle is the same as in 0.2.0. Semantic versioning applies from here on.

## [0.2.0] (2026-09-18)

**Tag:** [`0.2.0`][tag-0.2.0]

**Branch:** `main`

**Commit:** [`079e7b6`][079e7b6]

### Added

- New opening times content element. It renders the weekly schedule, the closing periods and the live opening status.
  The `open_only` variant leaves out days without opening times, and the `grouped` variant collapses consecutive days
  with identical times
- New structured data content element. It adds a schema.org `LocalBusiness` or `Organization` node with the company
  details and, for a `LocalBusiness`, the opening hours and closing periods to the page's JSON-LD
- `{{company::is_open}}` renders the current opening status. It is computed in the browser, so it stays correct on
  cached pages
- Companies have a time zone field. Opening times are read in that time zone, and an empty field falls back to the
  Contao system settings
- The `OpeningTimes` helper wraps [`spatie/opening-hours`][opening-hours] for templates and controllers

### Changed

- The company of the current page is resolved through Contao's page finder instead of the request attributes
- The back end date widget renders through a Twig template
- The DCA uses Doctrine DBAL types for its columns

## [0.1.0.2] (2026-09-17)

**Tag:** [`0.1.0.2`][tag-0.1.0.2]

**Branch:** `main`

**Commit:** [`ae9f356`][ae9f356]

### Changed

- Updated the Composer keywords

## [0.1.0.1] (2026-09-17)

**Tag:** [`0.1.0.1`][tag-0.1.0.1]

**Branch:** `main`

**Commit:** [`b29e768`][b29e768]

### Changed

- Updated the Composer keywords

## [0.1.0] (2026-08-13)

**Tag:** [`0.1.0`][tag-0.1.0]

**Branch:** `main`

**Commit:** [`cd275bc`][cd275bc]

### Added

- Companies are managed in the back end under Content and assigned to root pages, so every domain can show its own
  company
- The `company` Twig global returns the company of the current page or of a given ID, with the serialized lists
  already deserialized
- The `company` and `company_id` insert tags output company fields, phone numbers, e-mail addresses, websites, fax
  numbers and additional fields. `tel` and `mailto` render links, and `address`, `logo`, `socials` and `social` render
  templates
- Social media entries are sortable, open in a new tab with `noreferrer` and can be replaced or extended through the
  `AddSocialMediaOptionsEvent`
- The logo template has `logo_inner` and `logo_after` blocks, accepts figure classes and respects the `urlPrefix` of
  the root page
- Contao `^5.7` and `^6.0` are supported

[1.1.0]: https://github.com/Digitale-Dinge/contao-company/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/Digitale-Dinge/contao-company/compare/0.2.0...1.0.0
[0.2.0]: https://github.com/Digitale-Dinge/contao-company/compare/0.1.0.2...0.2.0
[0.1.0.2]: https://github.com/Digitale-Dinge/contao-company/compare/0.1.0.1...0.1.0.2
[0.1.0.1]: https://github.com/Digitale-Dinge/contao-company/compare/0.1.0...0.1.0.1
[0.1.0]: https://github.com/Digitale-Dinge/contao-company/releases/tag/0.1.0

[tag-1.1.0]: https://github.com/Digitale-Dinge/contao-company/releases/tag/1.1.0
[tag-1.0.0]: https://github.com/Digitale-Dinge/contao-company/releases/tag/1.0.0
[tag-0.2.0]: https://github.com/Digitale-Dinge/contao-company/releases/tag/0.2.0
[tag-0.1.0.2]: https://github.com/Digitale-Dinge/contao-company/releases/tag/0.1.0.2
[tag-0.1.0.1]: https://github.com/Digitale-Dinge/contao-company/releases/tag/0.1.0.1
[tag-0.1.0]: https://github.com/Digitale-Dinge/contao-company/releases/tag/0.1.0

[3bd45e2]: https://github.com/Digitale-Dinge/contao-company/commit/3bd45e241ac4b2a513137c79ce19afa361f5bee5
[079e7b6]: https://github.com/Digitale-Dinge/contao-company/commit/079e7b6e805ee842325fbe496866e958af07598b
[ae9f356]: https://github.com/Digitale-Dinge/contao-company/commit/ae9f356713abdc845aaa3f9eb68efd38795170f7
[b29e768]: https://github.com/Digitale-Dinge/contao-company/commit/b29e768d6243df011393df51b5fd9448a37ddd20
[cd275bc]: https://github.com/Digitale-Dinge/contao-company/commit/cd275bc8396e5a681ca0020d368ef16c517de3ef

[semver]: https://semver.org
[opening-hours]: https://github.com/spatie/opening-hours
[upgrade-1.1]: UPGRADE.md#from-10-to-11
