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
{if isset($HOOK_HOME_TAB_CONTENT) && $HOOK_HOME_TAB_CONTENT|trim}
    {if isset($HOOK_HOME_TAB) && $HOOK_HOME_TAB|trim}
        <ul id="home-page-tabs" class="nav nav-tabs clearfix">
			{$HOOK_HOME_TAB}
		</ul>
	{/if}
	<div class="tab-content">{$HOOK_HOME_TAB_CONTENT}</div>
{/if}

<div id="popup-pays">
    <div class="content-ouv-new">
        <a class="site-fr" href="//ecosapin.fr">fr</a>
        <a class="site-ch" href="//ecosapin.ch">ch</a>
        <a class="site-de" href="//ecosapin.de">de</a>
        <a class="site-ap" href="//ecosapin.ch/content/22-join-us">ap</a>

        <div class="newStyle">
            <p class="txtIcons">{l s="France"}</p>
            <p class="txtIcons">{l s="Suisse"}</p>
            <p class="txtIcons">{l s="Allemagne"}</p>
            <p class="txtIcons">{l s="Autre pays"}</p>
        </div>
    </div>
</div>

{if isset($HOOK_HOME) && $HOOK_HOME|trim}
	<div class="clearfix">{$HOOK_HOME}</div>
        
<script type="text/javascript">
    
$(document).ready(function() {
    
    if(localStorage.choixsapin && localStorage.choixsapin=="yes"){
        $('.btns .choix_spain').trigger("click");
        localStorage.removeItem("choixsapin");
    }
    
});
</script>
{/if}