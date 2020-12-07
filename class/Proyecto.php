<?php 

	class Proyecto{
        private $id_proyecto;
        private $nombre;
        private $fecha_inicio;
		private $fecha_fin;
        private $id_responsable;
		private $case_id;
        private $borrado;
		private $estado;

		public function getIdProyecto(){
			return $this->id_proyecto;
		}

		public function setIdProyecto($id_proyecto){
			$this->id_proyecto = $id_proyecto;
        }
        
        public function getNombre(){
			return $this->nombre;
		}

		public function setNombre($nombre){
			$this->nombre = $nombre;
        }
        
        public function getFechaInicio(){
			return $this->fecha_inicio;
		}

		public function setFechaInicio($fecha_inicio){
			$this->fecha_inicio = $fecha_inicio;
        }
        
        public function getFechaFin(){
			return $this->fecha_fin;
		}

		public function setFechaFin($fecha_fin){
			$this->fecha_fin = $fecha_fin;
		}

		public function getIdResponsable(){
			return $this->id_responsable;
		}

		public function setIdResponsable($id_responsable){
			$this->id_responsable = $id_responsable;
		}

		public function getCaseId(){
			return $this->case_id;
		}

		public function setCaseId($case_id){
			$this->case_id = $case_id;
		}

		public function getBorrado(){
            return $this->estado;
        }

        public function setBorrado($borrado){
            $this->borrado = $borrado;
        }

        public function getEstado(){
            return $this->estado;
        }

        public function setEstado($estado){
            $this->estado = $estado;
        }

		public function __construct($id_proyecto, $nombre, $fecha_inicio, $fecha_fin, $id_responsable, $case_id, $borrado, $estado){
            $this->setIdProyecto($id_proyecto);
            $this->setNombre($nombre);
			$this->setFechaInicio($fecha_inicio);
			$this->setFechaFin($fecha_fin);
			$this->setIdResponsable($id_responsable);
			$this->setCaseId($case_id);
			$this->setBorrado($borrado);
			$this->setEstado($estado);
			
		}

	}


 ?>