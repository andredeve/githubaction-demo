<?php

require_once "bootstrap.php";

use Core\Util\EntityManagerConn;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

return ConsoleRunner::createHelperSet(EntityManagerConn::getEntityManager());
