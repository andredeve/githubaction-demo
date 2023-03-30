<?php

namespace App\Log\Migration;

use Core\Model\MigrationHelper;

class MigracaoLogS2 extends MigrationHelper
{

    function run()
    {
        $this->db->exec("ALTER TABLE _create ADD COLUMN number TEXT DEFAULT NULL");
        $this->db->exec("ALTER TABLE _update ADD COLUMN number TEXT DEFAULT NULL");
    }

    function validate(): bool
    {
        return true;
    }
}