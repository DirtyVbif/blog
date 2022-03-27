<?php

namespace Blog\Controller;

use Blog\Client\User;
use Blog\Interface\Form\FormFactory;
use Blog\Request\LoginRequest;
use Exception;

class UserController extends BaseController
{
    public function prepare(): void
    {
        parent::prepare();
        $request_is_valid = true;
        if ($sub_argument = app()->router()->arg(3)) {
            $request_is_valid = false;
        }
        if ($argument = app()->router()->arg(2)) {
            $method = pascalCase("get request {$argument}");
            if (method_exists($this, $method)) {
                $request_is_valid = $this->$method();
            } else {
                $request_is_valid = false;
            }
        } else if (!app()->user()->isAuthorized()) {
            $this->getRequestLogin();
        } else {
            $this->getRequestProfile();
        }
        if (!$request_is_valid) {
            app()->controller('error')->prepare();
        } else {
            // TODO: complete user profile view
            // add noindex meta tag
            // reason is that authorization only for admins
            app()->page()->metaRobots('noindex');
        }
        return;
    }

    protected function getRequestLogin(): bool
    {
        if (app()->user()->isAuthorized()) {
            app()->router()->redirect('/user');
        }
        app()->page()->setMetaTitle(stok('User login | :[site]'));
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
            FormFactory::get('login')
        );
        $this->getTitle()->set(t('Admin authorization'));
        return;
    }

    public function postRequest(): void
    {
        $type = $_POST['type'] ?? null;
        $result = false;
        if ($type) {
            $method = camelCase("post request {$type}");
            try {
                $result = $this->{$method}();
            } catch (Exception $e) {
                pre([
                    'error' => "Unknown request and method " . static::class . "::{$method}()",
                    'post-data' => $_POST,
                    'exception' => $e
                ], '--q');
            }
        }
        app()->router()->redirect('<current>');
        return;
    }

    public function postRequestLogin(): bool
    {
        $request = new LoginRequest($_POST);
        $result = ($request->isValid() && app()->user()->authorize($request));
        if ($result) {
            msgr()->notice(t('You have successfully authorized.'));
        } else {
            msgr()->error(t('Wrong login or password.'));
        }
        return $result;
    }

    public function postRequestCloseSession(): bool
    {
        $result = false;
        if (!user()->verifyAccessLevel(User::ACCESS_LEVEL_USER)) {
            msgr()->error(t('You are not authorized for this request.'));
            // TODO: complete 401 error page view
            // /** @var ErrorController $conerr */
            // $conerr = app()->controller('error');
            // $conerr->prepare(401);
            // exit;
        } else if ($sesid = $_POST['sesid'] ?? false) {
            $sql = sql_delete(from: User::SESSIONS_TABLE);
            $sql->where(['sesid' => $sesid]);
            $sql->andWhere(['uid' => user()->id()]);
            $result = (bool)$sql->delete();
        }
        if ($result) {
            $title = $_POST['title'];
            msgr()->notice(
                t(
                    "Session &laquo;@name&raquo; successfully closed.",
                    ['name' => $title]
                )
            );
            user()->verifySession();
        } else {
            msgr()->error(t('There is no such session to close.'));
        }
        return $result;
    }

    public function getTitle(): ControlledPageTitle
    {
        parent::getTitle();
        if (!$this->title->isset()) {
            $this->title->set(t('Admin authorization'));
        }
        return $this->title;
    }

    protected function getRequestProfile(): void
    {
        $this->getTitle()->set(
            t('Hello, @name', ['name' => app()->user()->name()])
        );
        app()->page()->setMetaTitle(stok('User profile | :[site]'));
        app()->page()->addContent(
            app()->builder()->getUserSessions()
        );
        app()->page()->useCss('/css/user.min');
    }
}
