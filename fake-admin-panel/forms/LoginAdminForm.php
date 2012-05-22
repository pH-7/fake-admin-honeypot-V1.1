<?php
namespace PH7;
use PH7\Framework\Session\Session;

class LoginAdminForm {

    public static function display() {
        if(isset($_POST['submit_admin_login'])) {
            if(\PFBC\Form::isValid($_POST['submit_admin_login'])) {
                new LoginAdminFormProcessing();
            }
            Framework\Url\HeaderUrl::redirect();
        }

        $oForm = new \PFBC\Form('form_admin_login', 500);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_admin_login', 'form_admin_login'));
        $oForm->addElement(new \PFBC\Element\Token('admin_login'));
        $oForm->addElement(new \PFBC\Element\Email(t('Your Email:'), 'mail', array('required' => 1, 'validation' => new \PFBC\Validation\Email )));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your Username:'), 'username', array('required' => 1 )));
        $oForm->addElement(new \PFBC\Element\Password(t('Your Password:'), 'password', array('required' => 1 )));

        if((new Session)->exists('captcha_admin_enabled'))
            $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha:'), 'captcha', array ('description' => t('Enter the code above:'))));

        $oForm->addElement(new \PFBC\Element\Button(t('Login'),'submit',array('icon'=>'key')));
        $oForm->render();
    }

}
