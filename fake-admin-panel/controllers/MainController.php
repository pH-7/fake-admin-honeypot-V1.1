<?php
namespace PH7;
class MainController extends Controller {

    public function login() {
        $this->view->page_title = t('Sign in for Admin Panel');
        $this->view->page_h1 = t('Sign in');
        $this->output();
    }

}
