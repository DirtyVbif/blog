<?php

namespace Blog\Controller\Components;

use Blog\Controller\AdminController;
use Blog\Modules\Entity\Skill;
use Blog\Request\RequestFactory;

trait AdminControllerPostRequest
{
    public function postRequest(): void
    {
        if (!user()->verifyAccessLevel(AdminController::ADMIN_ACCESS_LEVEL)) {
            // if access denied
            $this->status = 403;
            /** @var ErrorController $err_c */
            $conerr = app()->controller('error');
            $conerr->prepare($this->status);
            return;
        } else if ($type = $_POST['type'] ?? null) {
            $method = pascalCase("post request {$type}");
            if (method_exists($this, $method)) {
                $this->$method();
                return;
            }
        }
        pre([
            'error' => 'Unknown request for ' . self::class . '::postRequest()',
            'data' => $_POST
        ]);
        exit;
    }

    protected function postRequestEntitySkillCreate(): void
    {
        $request = RequestFactory::get('skill');
        $result = false;
        if ($request->isValid()) {
            $result = Skill::create($request);
        }
        if ($result) {
            msgr()->notice(t('New entity &laquo;@name&raquo; of type &laquo;skill&raquo; successfully saved.', ['name' => $request->title]));
            app()->router()->redirect('/');
        } else {
            msgr()->warning(t('There was an error while creating new entity &laquo;@name&raquo; of type &laquo;skill&raquo;.', ['name' => $request->raw('title')]));
            app()->router()->redirect('<previous>');
        }
        exit;
    }
}
