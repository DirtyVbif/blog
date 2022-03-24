<?php

namespace Blog\Modules\Entity;

use Blog\Mediators\AjaxResponse;

class EntityModule implements \Blog\Components\AjaxModule
{
    public function ajaxRequest(): AjaxResponse
    {
        // TODO: add csrf token verification
        $response = new AjaxResponse();
        // Takes raw data from the request
        $json = file_get_contents('php://input');
        // Converts it into a PHP assoc array
        $data = json_decode($json, true);
        $method = isset($data['method']) ? 'ajaxRequest' . pascalCase($data['method']) : '';
        if (method_exists($this, $method)) {
            $response->set($this->{$method}($data));
        } else {
            $response->setResponse('Unknown request');
            $response->setCode(400);
        }
        return $response;
    }

    protected function ajaxRequestUpdate(array $data): AjaxResponse
    {
        $response = new AjaxResponse();
        $id = $data['id'] ?? null;
        $type = $data['type'] ?? null;
        $argument = $data['argument'] ?? null;
        if (!$type || !$id || !$argument) {
            $response->setResponse('Entity not found');
            $response->setCode(400);
            $response->setData([
                'id' => $id,
                'type' => $type,
                'argument' => $argument
            ]);
        } else {
            $entity_class = '\\Blog\\Modules\\Entity\\' . pascalCase($type);
            $method = camelCase("update {$argument}");
            if (method_exists($entity_class, $method)) {
                $entity_class::{$method}($id, $data, $response);
            } else {
                $response->setCode(400);
                $response->setResponse("Call to undefined method {$entity_class}::{$method}().");
            }
        }
        return $response;
    }
}
