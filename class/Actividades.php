<?php 

	class Actividad{
        private $id_actividad;
        private $nombre;

		public function getIdActividad(){
			return $this->id_actividad;
		}

		public function setIdActividad($id_actividad){
			$this->id_actividad = $id_actividad;
        }
        
        public function getNombre(){
			return $this->nombre;
		}

		public function setNombre($nombre){
			$this->nombre = $nombre;
        }

		public function __construct($id_actividad, $nombre){
            $this->setIdProyecto($id_actividad);
            $this->setNombre($nombre);
			
		}

	}


 ?>