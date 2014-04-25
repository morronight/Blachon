<?php
	if (!class_exists('Menu'))
	{
		require_once 'Table.php';
		require_once 'Article.php';
		require_once 'Categorie.php';
		require_once 'Formatage.php';

		class	Menu	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Menu';
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public function	Charge($id_categorie, $ordre)
			{
				$sql = $this->prepare_Charge($id_categorie, $ordre);
				return $this->getBase()->selectOne($sql, $this->m_class);
			}
			
			protected function	prepare_Charge($id_categorie, $ordre)
			{
				$sql = null;
				if ($this->getBase() !== false)
					$sql = 'SELECT * FROM '.strtolower(get_class($this)).' WHERE id_categorie = '.intval($id_categorie).' AND ordre = '.intval($ordre);
				return $sql;
			}

			public function Insert($categorieId, $articleId, $autocommit = true)
			{
				$sql = 'SELECT id_categorie, '.intval($articleId).' AS id_article, IFNULL(MAX(ordre) + 1, 1) AS ordre FROM '.strtolower(get_class($this)).' WHERE id_categorie = '.intval($categorieId).' GROUP BY id_categorie';
				$new = new Menu();
				$new = $new->getBase()->selectOne($sql, $this->m_class);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (id_categorie, id_article, ordre) VALUES ('.intval($categorieId).', '.intval($articleId).', '.intval($new->ordre).')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
				{
					$this->id_categorie = null;
					$this->id_article = null;
					$this->ordre = null;
				}
				else
				{
					$this->id_categorie = intval($categorieId);
					$this->id_article = intval($articleId);
					$this->ordre = intval($ordre);
				}
				return $this->ordre;
			}

			public function Update($articleId, $autocommit = true)
			{
				$rslt = null;
				if ($this->id_article != intval($articleId)) {
					$sql = 'UPDATE '.strtolower($this->m_class).' SET id_article = '.intval($articleId).' WHERE id_categorie = '.intval($this->id_categorie).' AND ordre = '.intval($this->ordre);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->id_article = intval($articleId);
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public function Remove($autocommit = true)
			{
				$sql = 'DELETE FROM '.strtolower($this->m_class).' WHERE id_categorie = '.intval($this->id_categorie).' AND ordre = '.intval($this->ordre);
				$rslt = $this->getBase()->deleteOne($sql, $autocommit);
				if ($rslt !== false)
				{
					$this->id_categorie = null;
					$this->ordre = null;
				}
				return $rslt;
			}
			
			public static function GetListe($idCategorie)
			{
				$class = 'Menu';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM v_menu '.strtolower($class).' WHERE id_categorie = '.intval($idCategorie).' ORDER BY ordre ASC';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function buildMenu($idCategorie)
			{
				$rslt = array();
				$publics = self::GetListe($idCategorie);
				foreach($publics as $public)
				{
					$rslt[$public->titre] = '/articles/'.Formatage::Lien($public->titre);
				}
				return $rslt;
			}
			
			public static function buildAriane($idArticle)
			{
				$categories = array();
				$class = 'Menu';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_article = '.intval($idArticle);
				$menu = $base->selectOne($sql, $class);
				if ($menu !== false)				
					$categories = self::buildArianeForCategorie($menu->id_categorie);
				$base->FermerBase();
				return $categories;
			}			

			public static function buildArianeForCategorie($idCategorie)
			{
				$categories = array();
				$base = BD::OuvrirBase();
				$categorieId = $idCategorie;
				$categorie = new Categorie();
				$categorie = $categorie->Charge($categorieId);
				while (($categorie !== null) && (intval($categorie->ariane) == 1))
				{
					array_unshift($categories, $categorie);
					$categorieId = $categorie->id_parent;
					$categorie = $categorie->Charge($categorieId);
				}
				$base->FermerBase();
				return $categories;
			}			
		}
	}
?>