<?php

namespace hesabro\hris\controllers;

use backend\controllers\TicketController as BaseTicketController;

class TicketController extends BaseTicketController
{
    public $layout = 'panel';

    public function getViewPath()
    {
        return '@backend/views/ticket';
    }
}