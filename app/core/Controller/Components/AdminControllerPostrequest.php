<?php

namespace Blog\Controller\Components;

use Blog\Controller\AdminController;
use Blog\Modules\Entity\EntityFactory;
use Blog\Request\RequestFactory;

trait AdminControllerPostRequest
{
    public function postRequest(): void
    {
        if (!user()->verifyAccessLevel(AdminController::ADMIN_ACCESS_LEVEL)) {
            // if access denied
            $this->status = 403;
            /** @var ErrorController $conerr */
            $conerr = app()->controller('error');
            $conerr->prepare($this->status);
            return;
        } else if ($method = app()->router()->arg(2)) {
            $method = camelCase("post request {$method}");
        } else if ($type = $_POST['type'] ?? null) {
            $method = camelCase("post request {$type}");
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
        return $this->postRequestEntity('skill');
    }

    /**
     * Called when POST request on `/admin/article/*`
     */
    protected function postRequestArticle(): bool
    {
        return $this->postRequestEntity('article');
    }

    protected function postRequestEntity(string $entity_type): bool
    {
        $id = app()->router()->arg(3);
        $type = $_POST['type'] ?? null;
        if (!is_numeric($id) && !$type) {
            return false;
        }
        $request = RequestFactory::get($entity_type);
        if ($id) {
            $request->set('entity_id', $id);
        }
        $title = $request->label();
        if ($request->isValid()) {
            $title = $request->title;
            switch (true) {
                case(!$id && $type === 'create'):
                    $result = EntityFactory::create($entity_type, $request);
                    break;
                case ($id && $type === 'edit'):
                    $result = EntityFactory::edit($id, $entity_type, $request);
                    break;
                default:
                    $result = false;
            }
        }
        $entity_id = $id ? ' @id' : '';
        if ($result) {
            $request->complete();
            msgr()->notice(
                t(
                    'Entity' . $entity_id . ' &laquo;@name&raquo; of type &laquo;@type&raquo; was successfully saved.',
                    [
                        'name' => $title,
                        'type' => $entity_type,
                        'id' => $id ?? ''
                    ]
                )
            );
            if ($id) {
                app()->router()->redirect("/admin/{$entity_type}/{$id}");
            }
            app()->router()->redirect('<previous>');
        }
        $while_action = $type === 'create' ? 'creating new' : 'editing';
        msgr()->warning(
            t(
                'There was an error while ' . $while_action . ' entity' . $entity_id . ' &laquo;@name&raquo; of type &laquo;@type&raquo;.',
                [
                    'name' => $title,
                    'type' => $entity_type,
                    'id' => $id ?? ''
                ]
            )
        );
        app()->router()->redirect('<previous>');
        return $result;
    }
}
