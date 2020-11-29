<?php 

	class Usuario{
        private $id;
        private $username;
        private $password;
		private $email;
        private $is_active;
        private $roles;

		public function getId(){
			return $this->id;
		}

		public function setId($id){
			$this->id = $id;
        }
        
        public function getUsername(){
			return $this->username;
		}

		public function setUsername($username){
			$this->username = $username;
        }
        
        public function getPassword(){
			return $this->password;
		}

		public function setPassword($password){
			$this->password = $password;
		}

		public function getEmail(){
			return $this->email;
		}

		public function setEmail($email){
			$this->email = $email;
		}

		public function getIsActive(){
			return $this->is_active;
		}

		public function setIsActive($is_active){
			$this->is_active = $is_active;
		}

		public function getRoles(){
			return $this->roles;
		}

		public function setRoles($roles){
			$this->roles = $roles;
		}

		public function __construct($id, $username, $password, $email, $is_active, $roles){
            $this->setId($id);
            $this->setUsername($username);
			$this->setPassword($password);
			$this->setEmail($email);
			$this->setIsActive($is_active);
			$this->setRoles($roles);
			
		}

	}


 ?>