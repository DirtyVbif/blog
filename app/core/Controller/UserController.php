<?php

namespace Blog\Controller;

use Blog\Request\LoginRequest;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->title = t('Admin authorization');
    }

    public function prepare(): void
    {
        parent::prepare();
        $request_is_valid = true;
        if ($sub_argument = app()->router()->arg(3)) {
            $request_is_valid = false;
        }
        if ($argument = app()->router()->arg(2)) {
            if ($argument === 'login' && !app()->user()->isAuthorized()) {
                $this->loadLoginForm();
            } else {
                $request_is_valid = false;
            }
            $method = pascalCase("get request {$argument}");
            if (method_exists($this, $method)) {
                $request_is_valid = $this->$method();
            } else {
                $request_is_valid = false;
            }
        } else if (!app()->user()->isAuthorized()) {
            $this->loadLoginForm();
        } else {
            $this->getTitle()->set(
                t('Hello, @name', ['name' => app()->user()->name()])
            );
        }
        if (!$request_is_valid) {
            app()->controller('error')->prepare();
        } else {
            // TODO: complete user profile view
            // add noindex meta tag
            // reason is that authorization only for admins
            app()->page()->setMeta('robots', [
                'name' => 'robots',
                'content' => 'noindex'
            ]);
        }
        return;
    }

    protected function getRequestLogin(): bool
    {
        if (app()->user()->isAuthorized()) {
            app()->router()->redirect('/user');
        }
        $this->loadLoginForm();
        return true;
    }

    protected function getRequestLogout(): bool
    {
        if (app()->user()->isAuthorized()) {
            app()->user()->logout();
            app()->router()->redirect('<previous>');
            return true;
        }
        app()->router()->redirect('/user');
        return false;
    }

    protected function loadLoginForm(): void
    {
        app()->page()->addClass('page_login');
        app()->page()->addContent(
            app()->builder()->getLoginForm()
        );
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

    public function getTitle(): ControlledPageTitle
    {
        parent::getTitle();
        if (!$this->title->isset()) {
            $this->title->set(t('Admin authorization'));
        }
        return $this->title;
    }
}
