<?php
namespace PH7;
use
PH7\Framework\Http\Http,
PH7\Framework\Ip\Ip,
PH7\Framework\Mail\Mail;

class Logger extends Core {

    private $fIp, $sContents;

    public function init(array $aData) {
        $sReferer = (null !== ($mReferer = $this->browser->getHttpReferer() )) ? $mReferer : 'NO HTTP REFERER';
        $sAgent = (null !== ($mAgent = $this->browser->getUserAgent() )) ? $mAgent : 'NO USER AGENT';
        $sQuery = (null !== ($mQuery = (new Http)->getQueryString() )) ? $mQuery : 'NO QUERY STRING';

        $this->fIp = Ip::get();
        extract($aData);

        $this->sContents =
        t('Date: %0%', $this->dateTime->get()->dateTime()) . "\n" .
        t('IP: %0%', $this->fIp) . "\n" .
        t('QUERY: %0%', $sQuery) . "\n" .
        t('Agent: %0%', $sAgent) . "\n" .
        t('Referer: %0%', $sReferer) . "\n" .
        t('LOGIN - Email: %0% - Username: %1% - Password: %2%', $mail, $username, $password) . "\n\n\n";

        $this->writeFile();

        if($this->config->values['module.setting']['report_email'])
            $this->sendMessage();
    }

    protected function writeFile() {
        $sFileName = $this->fIp . '.log';
        $sFilePath = $this->registry->path_module_inc . '_attackers/' . $sFileName;
        $iFlag = (is_file($sFilePath)) ? FILE_APPEND : 0;
        file_put_contents($sFilePath, $this->sContents, $iFlag);
    }

    protected function sendMessage() {
        $aInfo = [
          'to' => $this->config->values['logging']['bug_report_email'],
          'subject' => t('Reporting of the Fake Admin Honeypot')
        ];

        (new Mail)->send($aInfo, $this->sContents, false);
    }

}
