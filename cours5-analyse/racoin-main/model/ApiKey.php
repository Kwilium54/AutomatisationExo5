<?php

namespace model;

class ApiKey extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'apikey';
    protected $primaryKey = 'id_apikey';
    public $timestamps = false;

}
?>