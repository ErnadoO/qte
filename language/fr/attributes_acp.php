<?php
//
//	file: language/fr/mods/info_acp_attributes.php
//	author: abdev
//	begin: 11/22/2010
//	version: 0.1.4 - 12/27/2013
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

// ignore
if (!defined('IN_PHPBB'))
{
	exit;
}

// init lang ary, if it doesn't !
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// administration
$lang = array_merge($lang, array(
	'QTE_ADD' => 'Ajouter un nouvel attribut',
	'QTE_ADD_EXPLAIN' => 'Vous pouvez définir ici les champs du nouvel attribut.',
	'QTE_EDIT' => 'Modifier un attribut',
	'QTE_EDIT_EXPLAIN' => 'Vous pouvez modifier ici les champs de l’attribut sélectionné.',

	'QTE_FIELDS' => 'Champs de l’attribut',
	'QTE_TYPE' => 'Type d’attribut',
	'QTE_TYPE_TXT' => 'Texte',
	'QTE_TYPE_IMG' => 'Image',
	'QTE_NAME' => 'Intitulé de l’attribut',
	'QTE_NAME_EXPLAIN' => '- Utilisez la variable de langue si elle est définie dans le fichier de langue, ou saisissez directement le nom de l’attribut.<br />- Insérer <strong>%%mod%%</strong> affichera le nom de l’utilisateur ayant appliqué l’attribut.<br />- Insérer <strong>%%date%%</strong> affichera la date du jour où l’attribut a été appliqué.<br /><br />- Example : <strong>[Réglé par %%mod%%]</strong> affichera <strong>[Réglé par %s]</strong>',
	'QTE_DESC' => 'Description de l’attribut',
	'QTE_DESC_EXPLAIN' => 'Vous pouvez entrer un commentaire court, lequel sera utilisé pour différencier vos attributs si certains doivent avoir le même nom.',
	'QTE_IMG' => 'Image de l’attribut',
	'QTE_IMG_EXPLAIN' => 'Vous pouvez utilisez le nom de l’image s’il est défini dans le pack d’images, ou saisir le chemin relatif de l’image.',
	'QTE_DATE' => 'Format de date de l’attribut',
	'QTE_DATE_EXPLAIN' => 'La syntaxe utilisée est identique à la fonction PHP <a href="http://www.php.net/date">date()</a>.',
	'QTE_COLOUR' => 'Couleur de l’attribut',
	'QTE_COLOUR_EXPLAIN' => 'Sélectionnez une valeur dans la <strong>palette de couleurs</strong>, ou saisissez-la manuellement.<br />Laisser vide pour utiliser une classe CSS nommée comme l’attribut.',
	'QTE_USER_COLOUR' => 'Colorer le nom de l’utilisateur ayant appliqué l’attribut',
	'QTE_USER_COLOUR_EXPLAIN' => 'Si vous utilisez l’argument <strong>%mod%</strong> et que cette option est activée, la couleur du groupe de l’utilisateur sera appliquée.',
	'QTE_COPY_AUTHS' => 'Copier les permissions depuis',
	'QTE_COPY_AUTHS_EXPLAIN' => 'Si vous choisissez de copier les permissions, l’attribut aura les mêmes permissions que celles sélectionnées ici. Elles remplaceront toutes les permissions précédemment définies pour cet attribut, par les permissions de l’attribut sélectionné. Si l’option <strong>Personnalisée</strong> est sélectionnée, les permissions actuelles seront conservées.',

	'QTE_PERMISSIONS' => 'Permissions de l’attribut',
	'QTE_ALLOWED_FORUMS' => 'Forums autorisés',
	'QTE_ALLOWED_FORUMS_EXPLAIN' => 'Forums dont les groupes autorisés peuvent utiliser cet attribut.<br />Sélectionnez plusieurs forums en maintenant la touche <samp>CTRL</samp> ou la touche <samp>COMMAND</samp> et en cliquant.',
	'QTE_ALLOWED_GROUPS' => 'Groupes autorisés',
	'QTE_ALLOWED_GROUPS_EXPLAIN' => 'Groupes autorisés à utiliser cet attribut.<br />Sélectionnez plusieurs groupes en maintenant la touche <samp>CTRL</samp> ou la touche <samp>COMMAND</samp> et en cliquant.',
	'QTE_ALLOWED_AUTHOR' => 'Autoriser l’auteur du sujet à utiliser cet attribut dans les forums sélectionnés',
	'QTE_ALLOWED_AUTHOR_EXPLAIN' => 'Si cette option est activée, l’auteur du sujet pourra utiliser cet attribut, même s’il ne fait pas partie des groupes sélectionnés.',
	'QTE_COPY_PERMISSIONS' => 'Copier les permissions d’attributs depuis',
	'QTE_COPY_PERMISSIONS_EXPLAIN' => 'Une fois créé, le forum aura les mêmes permissions d’attributs que celui sélectionné. Si aucun forum n’est choisi, les attributs ne seront pas visibles tant que leurs permissions n’auront pas été définies.',

	'QTE_AUTH_ADD' => 'Ajouter une permission',
	'QTE_AUTH_REMOVE' => 'Supprimer cette permission',
	'QTE_AUTH_NO_PERMISSIONS' => 'Ne pas copier les permissions',

	'QTE_ATTRIBUTE' => 'Attribut',
	'QTE_ATTRIBUTES' => 'Attributs',
	'QTE_USAGE' => 'Utilisation',

	'QTE_CSS' => 'Probablement géré en CSS',
	'QTE_NONE' => 'N/A',
	'QTE_KEY_USERNAME' => '¦utilisateur¦',
	'QTE_KEY_DATE' => '¦date¦',

	'QTE_MUST_SELECT' => 'Vous devez sélectionner un attribut.',
	'QTE_NAME_ERROR' => 'Le champ “Intitulé de l’attribut” semble être vidé.',
	'QTE_DESC_ERROR' => 'Le champ “Description de l’attribut” semble être trop long.',
	'QTE_COLOUR_ERROR' => 'Le champ “Couleur de l’attribut” semble comporter une erreur.',
	'QTE_DATE_ARGUMENT_ERROR' => 'Vous avez défini un format de date. Hors, vous n’avez pas défini l’argument <strong>%date%</strong> dans votre attribut.',
	'QTE_DATE_FORMAT_ERROR' => 'Vous avez défini l’argument <strong>%date%</strong> dans votre attribut. Hors, vous n’avez pas défini de format de date.',
	'QTE_USER_COLOUR_ERROR' => 'Vous avez activé l’option pour colorer le nom de l’utilisateur. Hors, vous n’avez pas défini l’argument <strong>%mod%</strong> dans votre attribut.',
	'QTE_FORUM_ERROR' => 'Vous ne pouvez pas spécifier une catégorie ou un forum-lien.',

	'QTE_ADDED' => 'Un nouvel attribut a été ajouté.',
	'QTE_UPDATED' => 'L’attribut sélectionné a été mis à jour.',
	'QTE_REMOVED' => 'L’attribut sélectionné a été supprimé.',

));

// forums
$lang = array_merge($lang, array(
	'QTE_TOPICS_ATTR_SETTINGS' => 'Paramètres des attributs de sujet',

	'QTE_DEFAULT_ATTR' => 'Attribut par défaut du forum',
	'QTE_DEFAULT_ATTR_EXPLAIN' => 'L’attribut sélectionné sera appliqué à la création d’un sujet, quel que soient les permissions de l’utilisateur.',
	'QTE_HIDE_ATTR' => 'Masquer l’option de suppression',
	'QTE_HIDE_ATTR_EXPLAIN' => 'Les groupes sélectionnés ne pourront pas accéder à l’option de suppression.',
	'QTE_FORCE_USERS' => 'Forcer les utilisateurs à appliquer un attribut à leur sujet',
	'QTE_FORCE_USERS_EXPLAIN' => 'Si activée, les utilisateurs devront sélectionner un attribut pour leur sujet dans ce forum.',
));