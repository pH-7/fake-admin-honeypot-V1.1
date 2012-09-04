<?php
/**
 * @title Logger Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / Module / Fake Admin Panel / Inc / Class
 * @version        1.1.8
 */

namespace PH7;
use
PH7\Framework\Security\Ban\Ban,
PH7\Framework\Http\Http,
PH7\Framework\Ip\Ip,
PH7\Framework\Mail\Mail;

class Logger extends Core
{
    /**
     * Folder of the informations logs files.
     */
    const FILE_ATTACK = '_attackers/';

    /**
     * IP address.
     *
     * @access private
     * @var float $_fIp
     */
    private $_fIp;
    /**
     * The informations contents.
     *
     * @access private
     * @var string $_sContents
     */
    private $_sContents;

    /**
     * Constructor.
     *
     * @access public
     * @param array $aData The data.
     * @return void
     */
    public function init(array $aData)
    {
        $sReferer = (null !== ($mReferer = $this->browser->getHttpReferer() )) ? $mReferer : 'NO HTTP REFERER';
        $sAgent = (null !== ($mAgent = $this->browser->getUserAgent() )) ? $mAgent : 'NO USER AGENT';
        $sQuery = (null !== ($mQuery = (new Http)->getQueryString() )) ? $mQuery : 'NO QUERY STRING';

        $this->_fIp = Ip::get();
        extract($aData);

        $this->_sContents =
        t('Date: %0%', $this->dateTime->get()->dateTime()) . "\n" .
        t('IP: %0%', $this->_fIp) . "\n" .
        t('QUERY: %0%', $sQuery) . "\n" .
        t('Agent: %0%', $sAgent) . "\n" .
        t('Referer: %0%', $sReferer) . "\n" .
        t('LOGIN - Email: %0% - Username: %1% - Password: %2%', $mail, $username, $password) . "\n\n\n";

        $this->writeFile();

        if ($this->config->values['module.setting']['report_email.enable'])
            $this->sendMessage();

        if ($this->config->values['module.setting']['auto_banned_ip.enable'])
            $this->blockIp();

    }

    /**
     * Writes a log file with the hacher informations.
     *
     * @access protected
     * @return void
     */
    protected function writeFile()
    {
        $sFileName = $this->_fIp . '.log';
        $sPath = $this->registry->path_module_inc . static::FILE_ATTACK . $sFileName;
        $iFlag = (is_file($sPath)) ? FILE_APPEND : 0;
        file_put_contents($sPath, $this->_sContents, $iFlag);
    }

    /**
     * Blocking IP address.
     *
     * @access protected
     * @return void
     */
     protected function blockIp()
     {
        $sContent = $this->_fIp . "\n";
        $sPathFile = PH7_PATH_APP_CONFIG . Ban::DIR . Ban::IP_FILE;

        $iFlag = (is_file($sPathFile)) ? FILE_APPEND : 0;
        file_put_contents($sPathFile, $sContent, $iFlag);
     }

    /**
     * Sends a email to admin.
     *
     * @access protected
     * @return void
     */
    protected function sendMessage()
    {
        $aInfo = [
          'to' => $this->config->values['logging']['bug_report_email'],
          'subject' => t('Reporting of the Fake Admin Honeypot')
        ];

        (new Mail)->send($aInfo, $this->_sContents, false);
    }

}
