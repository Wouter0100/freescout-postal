<?php

namespace Modules\Postal\Entities;

use Symfony\Component\Console\Output\ConsoleOutput;

class FetchEmails extends \App\Console\Commands\FetchEmails
{

    public function __construct()
    {
        parent::__construct();

        $this->output = new ConsoleOutput();
    }

}
