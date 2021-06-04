{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 9589 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{foreach from=$messages item=message}
    <div class="message-body" data-messages='{$messages_json|escape:'html':'UTF-8'}'>
            {*<h4 class="message-item-heading">
                <span class="message-date">&nbsp;<i class="icon-calendar"></i>
                    {dateFormat date=$message['date_add']} -
                </span>
                {if ($message['elastname']|escape:'html':'UTF-8')}
                    {$message['efirstname']|escape:'html':'UTF-8'} {$message['elastname']|escape:'html':'UTF-8'}
                {else}
                    {$message['cfirstname']|escape:'html':'UTF-8'} {$message['clastname']|escape:'html':'UTF-8'}
                {/if}
                {if ($message['private'] == 1)}
                    <span class="badge badge-info">{l s='Private'}</span>
                {/if}
            </h4>*}
            <p class="message-item-text">
                    {$message['message']|escape:'html':'UTF-8'|nl2br}
            </p>
    </div>    
{/foreach}