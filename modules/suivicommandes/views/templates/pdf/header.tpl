{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{*
<table style="width: 100%">
<tr>
	<td style="width: 20%">
		{if $logo_path}
			<img src="{$logo_path}" style="width:60px" />
		{/if}
        </td>

        <td style="width: 80%;font-size: 12pt;">
            <h3>{$title_text}</h3>
            <h4>{$date}</h4>
	</td>
</tr>
</table>*}
<h3 style="margin-bottom: -50px;">{$title_text} - {$date}</h3>