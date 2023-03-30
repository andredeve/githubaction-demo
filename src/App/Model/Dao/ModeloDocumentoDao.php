<?php

namespace App\Model\Dao;

use App\Model\ModeloDocumento;
use Core\Model\AppDao;

class ModeloDocumentoDao extends AppDao {
    public function __construct()
    {
        parent::__construct(ModeloDocumento::class);
    }
}
