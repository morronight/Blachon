<?php
	if (!class_exists('Categorie'))
	{
		require_once('Table.php');
		require_once('Menu.php');
		require_once('Article.php');

		class	Categorie	extends Table
		{	
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Categorie';
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public function Insert($nom, $parentId = null, $articleId = null, $ariane = 0, $ordre = null, $autocommit = true)
			{
				$this->nom = substr($nom, 0, 64);
				if ($parentId !== null)
					$this->id_parent = intval($parentId);
				else
					$this->id_parent = null;
				if ($articleId !== null)
					$this->id_article = intval($articleId);
				else
					$this->id_article = null;
				$this->ariane = intval($ariane);
				if ($ordre !== null)
					$this->ordre = intval($ordre);
				else
					$this->ordre = null;
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (nom, id_parent, id_article, ariane, ordre) VALUES (';
				$sql .= '\''.$this->getBase()->escapeString($this->nom).'\'';
				if ($this->id_parent === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.$this->id_parent;
				if ($this->id_article === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.$this->id_article;
				$sql .= ', '.$this->ariane;
				if ($this->ordre === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.$this->ordre;
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = intval($id);
				return $this->id;
			}
			
			public function Update($nom, $parentId = null, $articleId = null, $ariane = 0, $ordre = null, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if (($this->nom != substr($nom, 0, 64))
					|| (($this->id_parent === null) && ($parentId !== null)) || (($this->id_parent !== null) && ($parentId === null)) || (($parentId !== null) && (intval($this->id_parent) != intval($parentId)))
					|| (($this->id_article === null) && ($articleId !== null)) || (($this->id_article !== null) && ($articleId === null)) || (($articleId !== null) && (intval($this->id_article) != intval($articleId)))
					|| (intval($this->ariane) != intval($ariane))
					|| (($this->ordre === null) && ($ordre !== null)) || (($this->ordre !== null) && ($ordre === null)) || (($ordre !== null) && (intval($this->ordre) != intval($ordre)))
					)
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET nom = \''.$this->getBase()->escapeString(substr($nom, 0, 64)).'\', ariane = '.intval($ariane);
					if ($parentId === null)
						$sql .= ', id_parent = NULL';
					else
						$sql .= ', id_parent = '.intval($parentId);
					if ($articleId === null)
						$sql .= ', id_article = NULL';
					else
						$sql .= ', id_article = '.intval($articleId);
					if ($ordre === null)
						$sql .= ', ordre = NULL';
					else
						$sql .= ', ordre = '.intval($ordre);
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->nom = substr($nom, 0, 64);
						if ($parentId !== null)
							$this->id_parent = intval($parentId);
						else
							$this->id_parent = null;
						if ($articleId !== null)
							$this->id_article = intval($articleId);
						else
							$this->id_article = null;
						$this->ariane = intval($ariane);
						if ($ordre !== null)
							$this->ordre = intval($ordre);
						else
							$this->ordre = null;
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public function	Delete($id = null, $autocommit = true)
			{
				if ($id === null)
					$id = $this->id;
				if ($id === null)
					return false;
				$sql = 'DELETE FROM '.strtolower($this->m_class).' WHERE id = '.intval($id);
				$rslt = $this->getBase()->deleteOne($sql, $autocommit);
				if ($rslt !== false)
					$this->id = null;
				return $rslt;
			}
			
			public static function	GetListe($array_id = null)
			{
				$class = 'Categorie';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				if (($array_id !== null) && is_array($array_id))
				{
					$sql .= ' WHERE id IN (';
					$first = true;
					foreach($array_id as $id)
					{
						if ($first)
							$first = false;
						else
							$sql .= ', ';
						$sql .= intval($id);
					}
					$sql .= ')';
				}
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	GetListeFromParent($ids, $recursif = false)
			{
				$class = 'Categorie';
				$base = BD::OuvrirBase();
				if ($ids !== null)
				{
					if (is_array($ids))
					{
						$idsSafe = array();
						foreach($ids as $id)
							$idsSafe[] = intval($id);
						$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_parent IN ('.implode(',', $idsSafe).')';
					}
					else
						$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_parent = '.intval($ids);
				}
				else
					$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_parent IS NULL';
				$sql .= ' ORDER BY ordre, nom';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				if ($recursif === false)
					return $liste;
				$listeRecursive = $liste;
				$categories = array();
				if (($liste !== false) && (count($liste) > 0))
				{
					foreach($liste as $categorie)
						$categories[] = intval($categorie->id);
				}
				if (count($categories) > 0)
				{
					$liste = Categorie::GetListeFromParent($categories, true);
					$listeRecursive = array_merge($listeRecursive, $liste);
				}
				return $listeRecursive;
			}
			
			public static function GetListeArticles($idCategorie)
			{
				$categories = array(intval($idCategorie));
				$class = 'Categorie';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_parent = '.intval($idCategorie);
				$liste = $base->selectMany($sql, $class);
				while (($liste !== false) && (count($liste) > 0))
				{
					$categoriesNiveau = array();
					foreach($liste as $categorie)
					{
						$categories[] = intval($categorie->id);
						$categoriesNiveau[] = intval($categorie->id);
					}
					if (count($categoriesNiveau) > 0)
					{
						$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_parent IN ('.implode(',', $categoriesNiveau).')';
						$liste = $base->selectMany($sql, $class);
					}
					else
					{
						$liste = false;
					}
				}
				$sql = 'SELECT * FROM v_article_categorise WHERE id_categorie IN ('.implode(',', $categories).')';
				$items = $base->selectMany($sql);
				$articlesId = array();
				if ($items !== false)
				{
					foreach($items as $item)
						$articlesId[] = intval($item->id);
				}
				if (count($articlesId) > 0)
					$articles = Article::GetListeByIds($articlesId, false, false);
				else
					$articles = null;
				$base->FermerBase();
				return $articles;
			}

			public static function GetListeArticlesByMenu($idCategorie, $recursif = true)
			{
				$categories = array(intval($idCategorie));
				$base = BD::OuvrirBase();
				if ($recursif === true)
				{
					$class = 'Categorie';				
					$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_parent = '.intval($idCategorie);
					$liste = $base->selectMany($sql, $class);
					while (($liste !== false) && (count($liste) > 0))
					{
						$categoriesNiveau = array();
						foreach($liste as $categorie)
						{
							$categories[] = intval($categorie->id);
							$categoriesNiveau[] = intval($categorie->id);
						}
						if (count($categoriesNiveau) > 0)
						{
							$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_parent IN ('.implode(',', $categoriesNiveau).')';
							$liste = $base->selectMany($sql, $class);
						}
						else
						{
							$liste = false;
						}
					}
				}				
				$sql = 'SELECT * FROM menu WHERE id_categorie IN ('.implode(',', $categories).')';
				$menus = $base->selectMany($sql, 'Menu');
				$articlesId = array();
				if ($menus !== false)
				{
					foreach($menus as $menu)
						$articlesId[] = intval($menu->id_article);
				}
				if (count($articlesId) > 0)
					$articles = Article::GetListeByIds($articlesId, false, false);
				else
					$articles = null;
				$base->FermerBase();
				return $articles;
			}

			public static function GetSortedHierachicalListe()
			{
				$categories = Categorie::GetListe();
				$categoriesById = array();
				foreach($categories as $categorie)
					$categoriesById[$categorie->id] = $categorie;
				$sortedCategories = array();
				foreach($categories as $categorie)
				{
					$nom = $categorie->nom;
					$cat = $categorie;
					$catId = $cat->id_parent;
					while ($cat->id_parent !== null)
					{
						$cat = $categoriesById[$cat->id_parent];
						$nom = $cat->nom.' &gt; '.$nom;
						$catId = $cat->id_parent.','.$catId;
					}
					$sortedCategories[$nom] = $categorie;
					$sortedCategories[$nom]->ids = $catId;
					$sortedCategories[$categorie->id] = &$sortedCategories[$nom];
				}
				ksort($sortedCategories);
				foreach($sortedCategories as $nom => &$categorie)
				{
					if (strval(intval($nom)) != $nom)
					{
						$categorie->sids = '';
						$ids = preg_split('/,/', $categorie->ids);
						foreach($ids as $idCat)
						{
							if (isset($sortedCategories[$idCat]))
							{
								$cat = $sortedCategories[$idCat];
								if ($cat->sids != '')
									$cat->sids .= ','.$categorie->id;
								else
									$cat->sids = $categorie->id;
							}
						}
					}
				}
				return $sortedCategories;
			}
			
			public static function	Get($id)
			{
				$categorie = new Categorie();
				return $categorie->Charge($id);
			}
		}
	}
?>