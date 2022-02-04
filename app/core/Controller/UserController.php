<?php

namespace Blog\Controller;

use Blog\Request\LoginRequest;

class UserController extends BaseController
{
    protected string $title;

    public function prepare(): void
    {
        parent::prepare();
        // add main page elements
        app()->page()->addClass('page_login');
        // add page content
        if (!app()->user()->isAuthorized()) {
            app()->page()->addContent([
                // set login form
                app()->builder()->getLoginForm()
            ]);
        }
        return;
    }

    public function postRequest(): void
    {
        $login_data = new LoginRequest($_POST);
        if ($login_data->isValid() && app()->user()->authorize($login_data)) {
            msgr()->notice(t('You have successfully authorized.'));
        } else {
            msgr()->error(t('Wrong login or password.'));
        }
        app()->router()->redirect('<current>');
        return;
    }

    public function getTitle(): string
    {
        if (!app()->user()->isAuthorized()) {
            $this->title = t('Admin authorization');
        } else {
            $this->title = t('Hello, @name', ['name' => app()->user()->name()]);
        }
        return $this->title;
    }
}
