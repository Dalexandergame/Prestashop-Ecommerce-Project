{*
 * This file is part of the prestahsop-adroll module.
 *
 * (c) AdRoll
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Damián Nohales <damian.nohales@adroll.com>
 * @copyright AdRoll
 * @license   https://opensource.org/licenses/MIT The MIT License
 *}
<div class="panel">
    <h2>{l s='Please select the shop you want to connect to AdRoll' mod='adroll'}</h2>
    <ul>
        {foreach $shop_tree as $shop_group}
            <li>
                {$shop_group['name']|escape:'htmlall':'UTF-8'}
                <ul>
                    {foreach $shop_group['shops'] as $shop}
                        <li>
                            <a href="{"`$link_base`&setShopContext=s-`$shop['id_shop']`"|escape:'htmlall':'UTF-8'}">
                                {$shop['name']|escape:'htmlall':'UTF-8'}
                            </a>
                        </li>
                    {/foreach}
                </ul>
            </li>
        {/foreach}
    </ul>
</div>
