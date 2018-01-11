<?php
/**
 *
 * @package Quick Title Edition Extension
 * @copyright (c) 2015 ABDev
 * @copyright (c) 2015 PastisD
 * @copyright (c) 2015 Geolim4 <http://geolim4.com>
 * @copyright (c) 2015 Zoddo <zoddo.ino@gmail.com>
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 * Brazilian Portuguese translation by eunaumtenhoid (c) 2017 [ver 2.0.0-a2] (https://github.com/phpBBTraducoes)
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
	'QTE_ADD' => 'Adicionar um novo atributo',
	'QTE_ADD_EXPLAIN' => 'Aqui você pode definir os novos campos de atributos.',
	'QTE_EDIT' => 'Editar atributo',
	'QTE_EDIT_EXPLAIN' => 'Aqui você pode modificar os campos do atributo selecionado.',

	'QTE_FIELDS' => 'Campos de atributo',
	'QTE_TYPE' => 'Tipo de atributo',
	'QTE_TYPE_TXT' => 'Texto',
	'QTE_TYPE_IMG' => 'Imagem',
	'QTE_NAME' => 'Nome do atributo',
	'QTE_NAME_EXPLAIN' => '- Use a constante de idioma se o nome for exibido a partir do arquivo de idioma ou insira diretamente o nome do atributo.<br /> - Insira <strong>%%mod%%</strong> exibirá o nome de usuário que aplicou o atributo.<br /> - Insira <strong>%%date%%</strong> exibirá a data do dia em que o atributo foi aplicado.<br /><br /> - Exemplo: <strong>[Resolvido por %%mod%%]</strong> exibirá <strong>[Resolvido por %s]</strong>',
	'QTE_DESC' => 'Descrição do atributo',
	'QTE_DESC_EXPLAIN' => 'Você pode inserir um breve comentário, que será usado para diferenciar seus atributos se alguns precisarem ter o mesmo nome.',
	'QTE_IMG' => 'Imagem do atributo',
	'QTE_IMG_EXPLAIN' => 'Você pode usar o nome da imagem se estiver guardado no imageset, ou aproveitar o caminho relativo da imagem.',
	'QTE_DATE' => 'Formato da data do atributo',
	'QTE_DATE_EXPLAIN' => 'A sintaxe utilizada é idêntica à função PHP <a href="http://www.php.net/date">date()</a>.',
	'QTE_COLOUR' => 'Cor do atributo',
	'QTE_COLOUR_EXPLAIN' => 'Selecione um valor do <strong>seletor de cores</strong>, ou insira-o diretamente.',
	'QTE_USER_COLOUR' => 'Cor do nome de usuário, que aplicou o atributo',
	'QTE_USER_COLOUR_EXPLAIN' => 'Se você usar o argumento <strong>%mod%</strong> e essa opção estiver habilitada, a cor do grupo de usuários será aplicada.',
	'QTE_COPY_AUTHS' => 'Copiar permissões de',
	'QTE_COPY_AUTHS_EXPLAIN' => 'Se você optar por copiar permissões, o atributo terá as mesmas permissões do que você selecionou aqui. Isso substituirá todas as permissões que você definiu anteriormente para esse atributo com as permissões do atributo que você selecionou aqui. Se a opção <strong>Personalizada</strong> for selecionada, as permissões atuais serão mantidas.',

	'QTE_ALLOWED_GROUPS' => 'Grupos permitidos',
	'QTE_ALLOWED_GROUPS_EXPLAIN' => 'Grupos, que são autorizados a usar esse atributo.<br /> Selecione vários grupos segurando <samp>CTRL</samp> ou <samp>COMANDO</samp> e clicando sobre.',
	'QTE_ALLOWED_AUTHOR' => 'Permitir que o autor do tópico use esse atributo nos fóruns selecionados',
	'QTE_ALLOWED_AUTHOR_EXPLAIN' => 'Se essa opção estiver ativada, o autor do tópico poderá usar esse atributo, mesmo que ele não seja um membro dos grupos selecionados.',
	'QTE_COPY_PERMISSIONS' => 'Copiar permissões de atributos',
	'QTE_COPY_PERMISSIONS_EXPLAIN' => 'Quando criado, o fórum terá as mesmas permissões de atributos do que você selecionou. Se nenhum fórum for selecionado, os atributos não serão exibidos enquanto suas permissões não serão definidas.',

	'QTE_AUTH_ADD' => 'Adicionar uma permissão',
	'QTE_AUTH_REMOVE' => 'Remover essa permissão',
	'QTE_AUTH_NO_PERMISSIONS' => 'Não copie permissões',

	'QTE_ATTRIBUTE' => 'Atributo',
	'QTE_ATTRIBUTES' => 'Atributos',
	'QTE_USAGE' => 'Uso',

	'QTE_CSS' => 'Provavelmente gerenciado pelo CSS',
	'QTE_NONE' => 'N/D',

	'QTE_MUST_SELECT' => 'Você deve selecionar um atributo.',
	'QTE_NAME_ERROR' => 'O campo "Nome do atributo" parece estar vazio.',
	'QTE_DESC_ERROR' => 'O campo "Descrição do atributo" parece ser muito longo.',
	'QTE_COLOUR_ERROR' => 'O campo "Cor do atributo" parece conter um erro.',
	'QTE_DATE_ARGUMENT_ERROR' => 'Você definiu um formato de data. Mas você não definiu o argumento <strong>%date%</strong> dentro de seu atributo.',
	'QTE_DATE_FORMAT_ERROR' => 'Você definiu o argumento <strong>%date%</strong> dentro de seu atributo. Mas você não definiu o formato da data.',
	'QTE_USER_COLOUR_ERROR' => 'Você ativou o que garante a cor do nome de usuário. Mas você não definiu o argumento <strong>%mod%</strong> dentro de seu atributo.',
	'QTE_FORUM_ERROR' => 'Você não pode especificar uma categoria ou um link do fórum.',

	'QTE_ADDED' => 'Um novo atributo foi adicionado.',
	'QTE_UPDATED' => 'O atributo selecionado foi atualizado.',
	'QTE_REMOVED' => 'O atributo selecionado foi deletado.',

	'QTE_MIGRATIONS_OUTDATED' => 'Seu banco de dados não está atualizado.<br /> Desative e reative a extensão para atualizá-lo.<br /><br />Versão do banco de dados: %1$s<br />Versão dos arquivos: %2$s',
	'QTE_DEV_WARNING' => 'Você usa atualmente uma versão de desenvolvimento da extensão (%s).<br /> Você não deve usar essas versões em um ambiente de produção.<br /> Essas versões podem conter muitos recursos e problemas de segurança inacabados ou tornam instável seu fórum.',
	'QTE_DEV_WARNING_DEV' => 'Não seremos responsáveis ​​por eventuais perdas de dados ou corrupções.',
	'QTE_BETA_WARNING' => 'Você usa atualmente uma versão instável da extensão (%s).<br /> As versões instáveis ​​podem conter muitos erros, problemas de segurança ou tornar instável seu fórum.<br /> É altamente recomendável que você não use essas versões em um ambiente de produção.',
));

// forums
$lang = array_merge($lang, array(
	'QTE_TOPICS_ATTR_SETTINGS' => 'Configurações dos atributos do tópico',

	'QTE_DEFAULT_ATTR' => 'Atributo padrão do fórum',
	'QTE_DEFAULT_ATTR_EXPLAIN' => 'O atributo selecionado será aplicado quando um novo tópico for criado, independentemente das permissões do usuário.',
	'QTE_FORCE_USERS' => 'Forçar usuários a aplicar um atributo ao tópico',
	'QTE_FORCE_USERS_EXPLAIN' => 'Se ativado, os usuários terão que selecionar um atributo para o tópico nesse fórum.',
));
