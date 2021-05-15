<?php
use Respect\Validation\Validator;

/*
* document : https://respect-validation.readthedocs.io/en/latest
 */
class Validate {
    protected $ins = [];
    protected $errors = [];
    protected $rules = [];
    protected $data = [];
    protected $valid = true;

    public function __construct($data, $rules) {
        $this->rules = $rules;
        $this->data = $data;
        return $this;
    }

    public static function getValidateStr() {
        return Validator::stringVal();
    }

    public static function getValidatorArray() {
        return Validator::arrayVal();
    }

    public static function getValidatorObject() {
        return Validator::ObjectType();
    }

    public static function getValidatorInt() {
        return Validator::intVal();
    }

    public static function getValidatorFloat() {
        return Validator::floatVal();
    }

    public static function getValidatorBool() {
        return Validator::boolVal();
    }

    public static function getValidatorNull() {
        return Validator::nullType();
    }

    public static function getValidatorFile() {
        return Validator::file();
    }

    public function isValid() {
        return $this->valid;
    }

    public function isInvalid() {
        return !$this->valid;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function v() {
        $this->errors = [];
        $r = $this->rules;
        $d = $this->data;
        foreach($r as $key => $value) {
            if(!$value->validate($d[$key])) {
                $this->valid = false;
                array_push($this->errors, $key); 
            }
        }
        return $this;
    }
}
?>