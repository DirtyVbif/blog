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
        } else if ($method = app()->router()->arg(2)) {
            $method = 'postRequest' . pascalCase($method);
        } else if ($type = $_POST['type'] ?? null) {
            $method = 'postRequest' . pascalCase($type);
        }
        if (
            isset($method)
            && method_exists($this, $method)
            && $this->{$method}()
        ) {
            return;
        }
        pre([
            'error' => 'Unknown request for ' . self::class . '::postRequest()',
            'data' => $_POST
        ]);
        exit;
    }

    /**
     * Called when POST request on `/admin/skill/*`
     */
    protected function postRequestSkill(): bool
    {
        $id = app()->router()->arg(3);
        $type = $_POST['type'] ?? null;
        if (!is_numeric($id) && !$type) {
            return false;
        }
        $request = RequestFactory::get('skill');
        $result = false;
        $title = $request->raw('title');
        if ($request->isValid()) {
            $title = $request->title;
            if (!$id && $type === 'create') {
                $result = Skill::create($request);
            } else {
                $result = Skill::edit($id, $request);
            }
        }
        if ($result) {
            $request->complete();
            msgr()->notice(
                t(
                    'New entity &laquo;@name&raquo; of type &laquo;skill&raquo; successfully saved.',
                    ['name' => $title]
                )
            );
            app()->router()->redirect('/');
        }
        msgr()->warning(
            t(
                'There was an error while creating new entity &laquo;@name&raquo; of type &laquo;skill&raquo;.',
                ['name' => $title]
            )
        );
        app()->router()->redirect('<previous>');
        return $result;
    }
}
