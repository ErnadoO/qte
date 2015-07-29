<?php
/**
 *
 * @package Quick Title Edition Extension
 * @copyright (c) 2015 ABDev
 * @copyright (c) 2015 PastisD
 * @copyright (c) 2015 Geolim4 <http://geolim4.com>
 * @copyright (c) 2015 Zoddo <zoddo.ino@gmail.com>
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

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
	'QTE_ADD' => 'Nuovo attributo',
	'QTE_ADD_EXPLAIN' => 'Qui è possibile definire nuovi attributi.',
	'QTE_EDIT' => 'Modifica attributo',
	'QTE_EDIT_EXPLAIN' => 'Qui è possibile modificare l’attributo selezionato.',

	'QTE_FIELDS' => 'Campi attributo',
	'QTE_TYPE' => 'Tipo attributo',
	'QTE_TYPE_TXT' => 'Testo',
	'QTE_TYPE_IMG' => 'Immagine',
	'QTE_NAME' => 'Nome attributo',
	'QTE_NAME_EXPLAIN' => '- Usare una costante di lingua se il nome dev’essere estratto dal file di lingua oppure specificarlo esplicitamente.<br />- Inserendo <strong>%%mod%%</strong> sarà mostrato il nome dell’utente che ha applicato l’attributo.<br />- Inserendo <strong>%%date%%</strong> sarà mostrata la data in cui è stato applicato l’attributo.<br /><br />- Esempio: <strong>[Risolto da %%mod%%]</strong> mostrerà <strong>[Risolto da %s]</strong>',
	'QTE_DESC' => 'Descrizione attributo',
	'QTE_DESC_EXPLAIN' => 'È possibile inserire un breve commento che servirà a distinguere attributi che abbiano lo stesso nome.',
	'QTE_IMG' => 'Immagine attributo',
	'QTE_IMG_EXPLAIN' => 'È possibile specificare il nome immagine se presente nel set immagini oppure specificarne il percorso.',
	'QTE_DATE' => 'Formato data attributo',
	'QTE_DATE_EXPLAIN' => 'La sintassi usata è la stessa della funzione <a href="http://www.php.net/date">date()</a> di PHP.',
	'QTE_COLOUR' => 'Colore attributo',
	'QTE_COLOUR_EXPLAIN' => 'Selezionare un valore dal <strong>selettore</strong> o inserirlo direttamente.',
	'QTE_USER_COLOUR' => 'Colora nome utente',
	'QTE_USER_COLOUR_EXPLAIN' => 'Se usato l’argomento <strong>%mod%</strong> con l’opzione attiva, sarà mostrato il colore del gruppo dell’utente.',
	'QTE_COPY_AUTHS' => 'Copia permessi da',
	'QTE_COPY_AUTHS_EXPLAIN' => 'Se si sceglie di copiare i permessi, l’attributo avrà gli stessi permessi di quello selezionato qui. I permessi precedentemente impostati saranno sovrascritti da quelli dell’attributo specificato. Se l’opzione <strong>Personalizzato</strong> è attiva, i permessi correnti saranno mantenuti.',

	'QTE_PERMISSIONS' => 'Permessi attributo',
	'QTE_ALLOWED_FORUMS' => 'Forum permessi',
	'QTE_ALLOWED_FORUMS_EXPLAIN' => 'I forum in cui è possibile usare l’attributo.<br />Selezionare più forum tenendo premuto il tasto <samp>CTRL</samp> (o <samp>COMMAND</samp>) e cliccare sui nomi dei forum.',
	'QTE_ALLOWED_GROUPS' => 'Gruppi permessi',
	'QTE_ALLOWED_GROUPS_EXPLAIN' => 'I gruppi abilitati all’uso dell’attributo.<br />Selezionare più gruppi tenendo premuto il tasto <samp>CTRL</samp> (o <samp>COMMAND</samp>) e cliccare sui nomi dei gruppi.',
	'QTE_ALLOWED_AUTHOR' => 'Permetti all’autore del topic l’uso dell’attributo',
	'QTE_ALLOWED_AUTHOR_EXPLAIN' => 'Se l’opzione è abilitata, l’autore del topic potrà fare uso dell’attributo pur non facendo parte di uno dei gruppi abilitati.',
	'QTE_COPY_PERMISSIONS' => 'Copia permessi attributo da',
	'QTE_COPY_PERMISSIONS_EXPLAIN' => 'Una volta creato, il forum avrà gli stessi permessi attributo di quello selezionato. Se non viene selezionato un forum, gli attributi non saranno mostrati finché non saranno aggiornati i loro permessi.',

	'QTE_AUTH_ADD' => 'Aggiungi permesso',
	'QTE_AUTH_REMOVE' => 'Rimuovi permesso',
	'QTE_AUTH_NO_PERMISSIONS' => 'Non copiare permessi',

	'QTE_ATTRIBUTE' => 'Attributo',
	'QTE_ATTRIBUTES' => 'Attributi',
	'QTE_USAGE' => 'Uso',

	'QTE_CSS' => 'Definito con CSS',
	'QTE_NONE' => 'Non disponibile',

	'QTE_MUST_SELECT' => 'È necessario selezionare un attributo.',
	'QTE_NAME_ERROR' => 'Il campo “Nome attributo” è vuoto o non valido.',
	'QTE_DESC_ERROR' => 'Il campo “Descrizione attributo” è troppo lungo..',
	'QTE_COLOUR_ERROR' => 'Il campo “Colore attributo” è non valido.',
	'QTE_DATE_ARGUMENT_ERROR' => 'È stato definito un formato data ma non l’argomento <strong>%date%</strong> nell’attributo.',
	'QTE_DATE_FORMAT_ERROR' => 'È stato definito l’argomento <strong>%date%</strong> ma non un formato data nell’attributo.',
	'QTE_USER_COLOUR_ERROR' => 'È stata abilitata l’opzione di colorare il nome utente ma non l’argomento <strong>%mod%</strong> nell’attributo.',
	'QTE_FORUM_ERROR' => 'Non è possibile specificare un link a categoria o forum.',

	'QTE_ADDED' => 'Un nuovo attributo è stato aggiunto.',
	'QTE_UPDATED' => 'L’attributo selezionato è stato aggiornato.',
	'QTE_REMOVED' => 'L’attributo selezionato è stato rimosso.',

	'QTE_MIGRATIONS_OUTDATED' => 'Il database non è aggiornato.<br />Disabilitare e riabilitare l’estensione per procedere all’aggiornamento.<br /><br />Versione database: %1$s<br />Versione file: %2$s',
	'QTE_DEV_WARNING' => 'Si sta usando una versione dell’estensione in via di sviluppo (%s).<br />Non bisogna farne uso in ambito produttivo.<br />Queste versioni possono contenere funzioni incomplete, problemi di sicurezza o minare la stabilità della board.',
	'QTE_DEV_WARNING_DEV' => 'Non ci riteniamo responsabili per eventuali perdite di dati.',
	'QTE_BETA_WARNING' => 'Si sta usando una versione dell’estensione in via di sviluppo (%s).<br />È altamente sconsigliato farne uso in ambito produttivo.<br />Queste versioni possono contenere funzioni incomplete, problemi di sicurezza o minare la stabilità della board.',
));

// forums
$lang = array_merge($lang, array(
	'QTE_TOPICS_ATTR_SETTINGS' => 'Impostazioni attributi topic',

	'QTE_DEFAULT_ATTR' => 'Attributo predefinito del forum',
	'QTE_DEFAULT_ATTR_EXPLAIN' => 'L’attributo selezionato sarà applicato alla creazione di un topic, a prescindere dai permessi dell’utente.',
	'QTE_HIDE_ATTR' => 'Nascondi l’opzione per la rimozione',
	'QTE_HIDE_ATTR_EXPLAIN' => 'I gruppi selezionati non vedranno l’opzione per la rimozione dell’attributo.',
	'QTE_FORCE_USERS' => 'Forza utenti ad applicare un attributo ai propri topic',
	'QTE_FORCE_USERS_EXPLAIN' => 'Se abilitata, gli utenti dovranno specificare un attributo per i propri topic in questo forum.',
));
