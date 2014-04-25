<?php
	require_once 'include/camsii/Video.php';
	function afficheVideosAdmin($videos = null, $idCategories = null, $brouillons = false, $archives = false, $filtre = null, $visible = true)
	{
		if ($videos === null)
			$videos = Video::Search($idCategories, $brouillons, $archives, $filtre, $visible);
		if ($videos !== null)
		{
			echo '<div id="liste" class="liste">';
			if (count($videos) > 0)
			{
				foreach ($videos as $video)
				{
					if (is_a($video, 'Video'))
					{
						echo '<div class="listeVideos">';
						$description = "Editer la vidéo...";
						if ($video->description !== null)
							$description = htmlentities($video->description, ENT_NOQUOTES, 'UTF-8');
						echo $video->Format();
						echo '<p><a href="/administration/video?video='.intval($video->id).'">'.$description.'</a></p>';
						echo '</div>';
					}
				}
				echo '<div class="sectionBottom"></div>';
			}
			else
			{
				if ($idCategories === null)
					echo '<span class="message">Aucune vidéo trouvée. Sélectionnez une catégorie.</span>';
				else
					echo '<span class="message">Aucune vidéo trouvée.</span>';
			}
			echo '</div>';
		}
	}
	
	function afficheVideos($videos = null, $idCategories = null, $filtre = null)
	{
		if ($videos === null)
		{
			if ($idCategories === null)
				$videos = Video::GetListe(null, true);
			else
				$videos = Video::Search($idCategories, false, false, $filtre);
		}
		if ($videos !== null)
		{
			echo '<div id="liste" class="liste">';
			if (count($videos) > 0)
			{
				foreach ($videos as $video)
				{
					if (is_a($video, 'Video'))
					{
						if ($video->description !== null)
							echo '<h2>'.htmlentities($video->description, ENT_NOQUOTES, 'UTF-8').'</h2>';
						echo $video->Format();
					}
				}
				echo '<div class="sectionBottom"></div>';
			}
			else
				echo 'Aucune vidéo trouvée';
			echo '</div>';
		}
	}
?>