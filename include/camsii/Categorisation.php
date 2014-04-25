<?php
	if (!class_exists('Categorisation'))
	{
		require_once 'Table.php';

		class	Categorisation	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Categorisation';
			}

			public function __destruct()
			{
				parent::__destruct();
			}

			public function Insert($articleId, $categorieId, $autocommit = true)
			{
				$this->id_article = intval($articleId);
				$this->id_categorie = intval($categorieId);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (id_article, id_categorie) VALUES ('.$this->id_article.', '.$this->id_categorie.')';
				$id_categorie = $this->getBase()->insertOne($sql, $autocommit);
				if ($id_categorie === false)
					$this->id_categorie = null;
				else
					$this->id_categorie = $id_categorie;
				return $this->id_categorie;
			}

			public function Remove($articleId, $categorieId, $autocommit = true)
			{
				$this->id_article = intval($articleId);
				if ($categorieId !== null)
				{
					$this->id_categorie = intval($categorieId);
					$sql = 'DELETE FROM '.strtolower($this->m_class).' WHERE id_article = '.$this->id_article.' AND id_categorie = '.$this->id_categorie;
				}
				else
					$sql = 'DELETE FROM '.strtolower($this->m_class).' WHERE id_article = '.$this->id_article;
				$rslt = $this->getBase()->deleteOne($sql, $autocommit);
				if ($rslt !== false)
				{
					$this->id_article = null;
					$this->id_categorie = null;
				}
				return $rslt;
			}
			
			public static function	GetListe($articleId)
			{
				$class = 'Categorisation';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_article = '.intval($articleId).' ORDER BY id_categorie';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	GetListeNonVide()
			{
				$class = 'Categorisation';
				$base = BD::OuvrirBase();
				$sql = 'SELECT DISTINCT id_categorie FROM '.strtolower($class).' ORDER BY id_categorie';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	GetLibelleCategorie($categorieId)
			{
				$class = 'Categorie';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE id = '.intval($categorieId);
				$categorie = $base->selectOne($sql, $class);
				$base->FermerBase();
				if ($categorie)
					return $categorie->nom;
				else
					return null;
			}
		}
	}
?>