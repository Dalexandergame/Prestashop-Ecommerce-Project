{**
 * pm_advancedpack
 *
 * @author    Presta-Module.com <support@presta-module.com> - https://www.presta-module.com
 * @copyright Presta-Module - https://www.presta-module.com
 * @license   Commercial
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *}

{l s='Before being able to configure the module, make sure to set write permissions to files and folders listed below:' mod='pm_advancedpack'}<br /><br />
<ul>
{foreach from=$permission_errors item=permission_error}
	<li>{$permission_error|escape:'htmlall':'UTF-8'}</li>
{/foreach}
</ul>
<p style="text-align: center">
	<a class="btn btn-default" href="{$base_config_url|escape:'html':'UTF-8'}">{l s='Click here to check again' mod='pm_advancedpack'}</a>
</p>
