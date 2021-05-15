<?php

class Auth extends DBModel {
	protected $db = 'blog';
	protected $tableName = 'auths';
	protected $fillable = ['id', 'username', 'password', 'email', 'roles'];

	private function validateAdd($data) {
		$validate = new Validate($data, [
			'username' => Validate::getValidateStr()->notEmpty(),
			'password' => Validate::getValidateStr()->notEmpty(),
			'email' => Validate::getValidateStr()->notEmpty()->email()
		]);
		return $validate->v()->isValid();
	}

	public function add($data) {
		if ($this->validateAdd($data)) {
			$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
			return $this->insert($data);
		} else {
			return false;
		}
	}

	public function findAll() {
		return $this->initialize()->all();
	}

	public function findById($id) {
		return $this->initialize()->where('id', '=', intval($id))->first();
	}

	public function findByName($name) {
		return $this->initialize()->where('username', '=', $name)->first();
	}

	public function findByEmail($email) {
		return $this->initialize()->where('email', '=', $email)->first();
	}

	public function findByNameOrEmail($credential) {
		return $this->initialize()->orWhere('email', '=', $credential)->orWhere('username', '=', $credential)->first();
	}
}
?>