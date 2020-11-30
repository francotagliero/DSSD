<?php 

	class Protocolo{
        private $id_protocolo;
        private $nombre;
        private $id_responsable;
		private $fecha_fin;
        private $fecha_inicio;
        private $orden;
        private $es_local;
        private $puntaje;
        private $id_proyecto;
        private $estado;

        public function getIdProtocolo(){
			return $this->id_protocolo;
		}

		public function setIdProtocolo($id_protocolo){
			$this->id_protocolo = $id_protocolo;
        }

        public function getNombre(){
			return $this->nombre;
		}

		public function setNombre($nombre){
			$this->nombre = $nombre;
        }
        
		public function getIdResponsable(){
			return $this->id_responsable;
		}

		public function setIdResponsable($id_responsable){
			$this->id_responsable = $id_responsable;
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

        public function getOrden(){
            return $this->orden;
        }

        public function setOrden($orden){
            $this->orden = $orden;
        }

        public function getEsLocal(){
            return $this->es_local;
        }

        public function setEsLocal($es_local){
            $this->es_local = $es_local;
        }

        public function getPuntaje(){
            return $this->puntaje;
        }

        public function setPuntaje($puntaje){
            $this->puntaje = $puntaje;
        }
		public function getIdProyecto(){
			return $this->id_proyecto;
		}

		public function setIdProyecto($id_proyecto){
			$this->id_proyecto = $id_proyecto;
        }

        public function getEstado(){
            return $this->estado;
        }

        public function setEstado($estado){
            $this->estado = $estado;
        }

		public function __construct($id_protocolo, $nombre, $id_responsable, $fecha_fin, $fecha_inicio, $orden, $es_local, $puntaje, $id_proyecto, $estado){
            $this->setIdProtocolo($id_protocolo);
            $this->setNombre($nombre);
            $this->setIdResponsable($id_responsable);
            $this->setFechaFin($fecha_fin);
            $this->setFechaInicio($fecha_inicio);	
            $this->setIdProyecto($id_proyecto);
            $this->setOrden($orden);
            $this->setEsLocal($es_local);
            $this->setPuntaje($puntaje);
            $this->setEstado($estado);
        }

	}


 ?>