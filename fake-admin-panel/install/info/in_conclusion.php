<?php
namespace PH7;
defined('PH7') or exit('Restricted access');
/* Install Conclusion */

// Default contents value
$sHtml = '';

/*** Begin Contents ***/

$sHtml .= t('The installation is finished.');
$sHtml .= t('Thank you for using our module!');

/*** End Contents ***/

// Output!
return $sHtml;
