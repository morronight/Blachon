<?php
	if (!class_exists('Table'))
	{
		require_once('BD.php');
		
		abstract class	Table
		{
			protected	$m_class;
			private		$m_base;
			protected	$m_pkey;

			public function	__construct($pkey)
			{
				$this->m_class = null;
				$this->m_base = null;
				$this->m_pkey = $pkey;
			}

			public function __destruct()
			{
				if ($this->m_base !== null)
					$this->m_base->FermerBase();
			}


			protected function &getBase()
			{
				if ($this->m_base === null)
					$this->m_base =& BD::OuvrirBase();
				return $this->m_base;
			}

			public function	Charge($id)
			{
				$sql = $this->prepare_Charge($id);
				$instance = $this->getBase()->selectOne($sql, $this->m_class);
				if ($instance !== false)
					$instance->m_base =& BD::OuvrirBase();
				return $instance;
			}
			
			protected function	prepare_Charge($id)
			{
				$sql = null;
				if (($this->getBase() !== false) && !is_null($this->m_pkey))
					$sql = 'SELECT * FROM '.strtolower(get_class($this)).' WHERE '.$this->m_pkey.' = '.$this->getBase()->escapeString($id);
				return $sql;
			}
			
			public function commit()
			{
				if ($this->getBase() !== null)
					return $this->getBase()->commit();
				return false;
			}
			
			public function rollback()
			{
				if ($this->getBase() !== null)
					return $this->getBase()->rollback();
				return false;
			}
			
			public function error()
			{
				if ($this->getBase() !== null)
					return $this->getBase()->error();
				return false;
			}
		}
	}
?>