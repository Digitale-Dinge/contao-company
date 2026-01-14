<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Model;

use Contao\Model;
use Contao\Model\Collection;

/**
 * @property integer $id
 * @property integer $tstamp
 * @property string  $name
 * @property string  $logo
 * @property string  $street
 * @property string  $postal
 * @property string  $city
 * @property string  $state
 * @property string  $country
 * @property string  $opening_times
 * @property string  $closing_times
 * @property string  $phone_numbers
 * @property string  $emails
 * @property string  $websites
 * @property string  $fax_numbers
 * @property string  $socials
 * @property string  $additional
 *
 * @method static CompanyModel|null findById($id, array $opt=[])
 * @method static CompanyModel|null findByPk($id, array $opt=[])
 * @method static CompanyModel|null findOneBy($col, $val, array $opt=[])
 * @method static CompanyModel|null findOneByTstamp($val, array $opt=[])
 * @method static CompanyModel|null findOneByName($val, array $opt=[])
 * @method static CompanyModel|null findOneByLogo($val, array $opt=[])
 * @method static CompanyModel|null findOneByStreet($val, array $opt=[])
 * @method static CompanyModel|null findOneByPostal($val, array $opt=[])
 * @method static CompanyModel|null findOneByCity($val, array $opt=[])
 * @method static CompanyModel|null findOneByState($val, array $opt=[])
 * @method static CompanyModel|null findOneByCountry($val, array $opt=[])
 * @method static CompanyModel|null findOneByOpening_times($val, array $opt=[])
 * @method static CompanyModel|null findOneByClosing_times($val, array $opt=[])
 * @method static CompanyModel|null findOneByPhone_numbers($val, array $opt=[])
 * @method static CompanyModel|null findOneByEmails($val, array $opt=[])
 * @method static CompanyModel|null findOneByWebsites($val, array $opt=[])
 * @method static CompanyModel|null findOneByFax_numbers($val, array $opt=[])
 * @method static CompanyModel|null findOneBySocials($val, array $opt=[])
 * @method static CompanyModel|null findOneByAdditional($val, array $opt=[])
 *
 * @method static Collection|CompanyModel[]|CompanyModel|null findByTstamp($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByName($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByLogo($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByStreet($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByPostal($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByCity($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByState($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByCountry($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByOpening_times($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByClosing_times($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByPhone_numbers($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByEmails($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByWebsites($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByFax_numbers($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findBySocials($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findByAdditional($val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findMultipleByIds($var, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findBy($col, $val, array $opt=[])
 * @method static Collection|CompanyModel[]|CompanyModel|null findAll(array $opt=[])
 *
 * @method static integer countById($id, array $opt=[])
 * @method static integer countByTstamp($val, array $opt=[])
 * @method static integer countByName($val, array $opt=[])
 * @method static integer countByLogo($val, array $opt=[])
 * @method static integer countByStreet($val, array $opt=[])
 * @method static integer countByPostal($val, array $opt=[])
 * @method static integer countByCity($val, array $opt=[])
 * @method static integer countByState($val, array $opt=[])
 * @method static integer countByCountry($val, array $opt=[])
 * @method static integer countByOpening_times($val, array $opt=[])
 * @method static integer countByClosing_times($val, array $opt=[])
 * @method static integer countByPhone_numbers($val, array $opt=[])
 * @method static integer countByEmails($val, array $opt=[])
 * @method static integer countByWebsites($val, array $opt=[])
 * @method static integer countByFax_numbers($val, array $opt=[])
 * @method static integer countBySocials($val, array $opt=[])
 * @method static integer countByAdditional($val, array $opt=[])
 */
class CompanyModel extends Model
{
    protected static $strTable = 'tl_company';
}
