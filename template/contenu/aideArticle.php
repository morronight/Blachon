<aside id="aideArticle">
	<div class="commandes">
		<span class="icone cancel" title="Fermer" onclick="return adminArticle_masquerAideMemoire();"></span>
	</div>
	<h2>Aide mémoire pour la rédaction d'un article</h2>
	<h3>La publication et l'archivage</h3>
	<p>
		Lorsqu'un nouvel article est enregistré, il est automatiquement ajouté en tant que bouillon.<br/>		
		Un brouillon est un article dont la rédaction est en cours et/ou le contenu n'est pas encore validé. Les articles en brouillons ne sont pas visibles directement sur le site <a href="/" title="Site Internet de la commune de Larnage">www.larnage.fr</a>.<br/>
		Un brouillon n'est visible dans la liste des articles en administration que lorsque la case <b>brouillons</b> est cochée.
		Il est possible de modifier autant de fois que nécessaire un brouillon.
	</p>
	<p>
		Lorsque l'article est entièrement rédigé, il est possible de le publier.<br/>
		Un article publié est accessible au public sur le <a href="/" title="Site Internet de la commune de Larnage">site Internet de la commune de Larnage</a>.<br/>
		Il est toujours possible de modifier un article publié, mais ce n'est pas recommandé. En effet, les modifications enregistrées seront immédiatement visibles par le public.
	</p>
	<p>
		Enfin, lorsqu'un article est dépassé, il est possible de l'archiver.<br/>
		Un article archivé n'est plus accessible au public.<br/>
		Un article archivé n'est visible dans la liste des articles en administration que lorsque la case <b>archives</b> est cochée.
	</p>
<!---
	<h3>Les commandes disponibles</h3>
	<img title="Aperçu" alt="Aperçu" src="/images/fotolia/Apercu.jpg" class="icone"/>
	<p>Cette commande permet d'avoir un premier aperçu de l'article, pour savoir comment il sera vu par le public.</p>
	<img title="Enregistrer" alt="Enregistrer" src="/images/fotolia/Valider.jpg" class="icone"/>
	<p>Cette commande permet d'enregistrer toutes les modifications effectuées sur l'article. Tant que l'article n'est pas enregistré, aucune modification ne sera prise en compte.</p>
	<img title="Annuler" alt="Annuler" src="/images/fotolia/Annuler.jpg" class="icone"/>
	<p>Cette commande permet d'annuler toutes les modifications effectuées depuis le dernier enregistrement. La page de modification de l'article est replacée par la page d'administration du site.</p>
	<img title="Descendre" alt="Descendre" src="/images/fotolia/Bas.jpg" class="icone"/>
	<p>Cette commande permet de faire descendre le paragraphe sous le paragraphe suivant. Cela permet de réorganiser l'article sans le réécrire.</p>
	<img title="Monter" alt="Monter" src="/images/fotolia/Haut.jpg" class="icone"/>
	<p>Cette commande permet de faire monter le paragraphe au dessus de paragraphe précédent. Cela permet de réorganiser l'article sans le réécrire.</p>
	<img title="Supprimer le dernier paragraphe" alt="Supprimer le dernier paragraphe" src="/images/fotolia/Supprimer.jpg" class="icone"/>
	<p>Cette commande permet de supprimer le dernier paragraphe de l'article.</p>
	<img title="Ajouter un paragraphe" alt="Ajouter un paragraphe" src="/images/fotolia/Droite.jpg" class="icone"/>
	<p>Cette commande permet d'ajouter un nouveau paragraphe à l'article.</p>
-->
	<h3>Les mises en forme spéciales</h3>
	<p>Il existe quelques "trucs" pour rédiger un article :</p>
	<h4>Les liens vers d'autres sites Internet</h4>
	<p>
		Il est possible de faire un lien vers une page d'un autre site Internet.<br/>
		Pour cela, il suffit de mettre l'adresse complète de la page dans l'article. Par exemple: <code>http://www.cansii.com</code><br/>
		Le texte sera alors automatiquement transformé en lien vers la page souhaité, pour notre exemple : <a href="http://www.cansii.com" title="www.cansii.com">www.cansii.com</a>.<br/>
		Il est de plus possible de choisir le texte du lien en l'indiquant juste après (sans espace) et entre guillemets. Par exemple : <code>http://www.cansii.com"CANSII, développement informatique"</code>.<br/>
		Le résultat sera alors <a href="http://www.cansii.com" title="CANSII, développement informatique">CANSII, développement informatique</a>.
	</p>
	<h4>Les liens vers d'autres pages du site Internet de la commune de Larnage</h4>
	<p>
		Il est possible de faire un lien vers une autre page du site Internet.<br/>
		Pour cela, il suffit de mettre l'adresse complète de la page dans l'article. Par exemple: <code>http://www.larnage.fr/articles/Nous-contacter</code><br/>
		Le texte sera alors automatiquement transformé en lien vers la page souhaité, pour notre exemple : <a href="http://www.larnage.fr/articles/Nous-contacter" title="Nous contacter">Nous contacter</a>.<br/>
		Le texte du lien est le titre de la page correspondante. Pour notre exemple <code>"Nous contacter"</code>.<br/>
		Il est cependant possible de choisir le texte du lien en l'indiquant juste après (sans espace) et entre guillemets. Par exemple : <code>http://www.larnage.fr/articles/articles/Nous-contacter"Contacts"</code>.<br/>
		Le résultat sera alors <a href="http://www.larnage.fr/articles/Nous-contacter" title="Contacts">Contacts</a>.
	</p>
	<h4>Les liens vers les documents en ligne</h4>
	<p>
		Il est possible de faire un lien vers les documents en ligne du site Internet.<br/>
		Pour cela, il suffit de mettre l'adresse complète du document dans l'article. Par exemple: <code>http://www.larnage.fr/documents/reglement-d-utilisation-de-la-salle-polyvalente.pdf</code><br/>
		Le texte sera alors automatiquement transformé en lien vers le document souhaité, pour notre exemple : <a href="http://www.larnage.fr/documents/reglement-d-utilisation-de-la-salle-polyvalente.pdf" title="Fichier PDF 739.44 Ko">Règlement d'utilisation de la salle polyvalente.pdf</a>.<br/>
		Le texte du lien est le titre du document correspondant. Pour notre exemple <code>"Règlement d'utilisation de la salle polyvalente.pdf"</code>.<br/>
		Il est cependant possible de choisir le texte du lien en l'indiquant juste après (sans espace) et entre guillemets. Par exemple : <code>http://www.larnage.fr/documents/reglement-d-utilisation-de-la-salle-polyvalente.pdf"Télécharger le règlement d'utilisation de la salle polyvalente"</code>.<br/>
		Le résultat sera alors <a href="http://www.larnage.fr/documents/reglement-d-utilisation-de-la-salle-polyvalente.pdf" title="Fichier PDF 739.44 Ko">Télécharger le règlement d'utilisation de la salle polyvalente</a>.
	</p>
	<h4>Les adresses mails</h4>
	<p>
		Les adresses mails sont automatiquement détectées.<br/>
		Le texte sera alors automatiquement transformé en adresse mail cliquable, pour notre exemple : <a href="mailto:mairie@larnage.fr">mairie@larnage.fr</a>.<br/>
		Il est possible de choisir le texte du lien en l'indiquant juste après (sans espace) et entre guillemets. Par exemple : <code>mairie@larnage.fr"Nous contacter par mail"</code>.<br/>
		Le résultat sera alors <a href="mailto:mairie@larnage.fr">Nous contacter par mail</a>.
	</p>
	<h4>Les listes numérotées</h4>
	<p>
		Il est possible de mettre en forme le texte pour obtenir des listes numérotées.<br/>
		Pour cela, chaque ligne de la liste doit respecter la forme suivante. Commencer la ligne par un nombre entre parenthèses, suivi d'un espace, et enfin le texte souhaité pour la ligne.<br/>
		Par exemple : "<code>(1) Première ligne</code>".<br/>
		Le texte sera alors automatiquement transformé en liste numérotée. Pour note exemple :
	</p>
	<ol><li>Première ligne</li></ol>
	<h4>Les listes à puces</h4>
	<p>
		Il est possible de mettre en forme le texte pour obtenir des listes à puces.<br/>
		Pour cela, chaque ligne de la liste doit respecter la forme suivante. Commencer la ligne par un * entre parenthèses, suivi d'un espace, et enfin le texte souhaité pour la ligne.<br/>
		Par exemple : "<code>(*) Texte exemple</code>".<br/>
		Le texte sera alors automatiquement transformé en liste à puces. Pour note exemple :
	</p>
	<ul><li>Texte exemple</li></ul>
	<div class="commandes">
		<span class="icone cancel" title="Fermer" onclick="return adminArticle_masquerAideMemoire();"></span>
	</div>
</aside>