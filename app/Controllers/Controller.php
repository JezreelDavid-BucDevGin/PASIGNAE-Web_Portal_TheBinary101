<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Validator;

abstract class Controller
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    protected function validate(array $rules): bool
    {
        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            $_SESSION['_old'] = $this->request->all();
            flash('error', $validator->firstError() ?? 'Validation failed.');
            return false;
        }

        return true;
    }

    protected function requireCsrf(): bool
    {
        if (!$this->request->validateCsrf()) {
            flash('error', 'Invalid security token. Please try again.');
            return false;
        }
        return true;
    }
}
