<?php

namespace Blog\Controller\Components;

use Blog\Controller\AdminController;
use Blog\Request\RequestFactory;

trait AdminControllerPostrequest
{
    public function postRequest(): void
    {
        if (!user()->verifyAccessLevel(AdminController::ADMIN_ACCESS_LEVEL)) {
            // if access denied
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
        $request->validate();
        pre($request);
        exit;
        return;
    }
}
