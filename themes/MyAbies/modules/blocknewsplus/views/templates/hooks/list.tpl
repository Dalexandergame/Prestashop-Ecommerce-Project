{*
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blocknewsplus
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */
*}

{foreach from=$posts item=post name=myLoop}
    
        {*<div class="col-md-6 visible-sm visible-xs">
            {if strlen($post.img)>0}
                <img src="{$base_dir_ssl|escape:'UTF-8'}upload/blocknewsplus/{$post.img|escape:'UTF-8'}" 
                             title="{$post.title|escape:'htmlall':'UTF-8'}" 
                            />
            {/if}
        </div>
        {if $smarty.foreach.myLoop.iteration is even}
        <div class="col-md-6 hidden-xs hidden-sm">
            {if strlen($post.img)>0}
                <img src="{$base_dir_ssl|escape:'UTF-8'}upload/blocknewsplus/{$post.img|escape:'UTF-8'}" 
                             title="{$post.title|escape:'htmlall':'UTF-8'}" 
                            />
            {else}
                <img src="{$img_dir}/default-image.png" 
                     title="{$post.title|escape:'htmlall':'UTF-8'}" 
                    />
            {/if}
        </div>
        {/if}*}
        <div class="col-md-6">
            <h3>
                {if $blocknewsplusis_urlrewrite == 1}
                        <a href="{$base_dir_ssl|escape:'UTF-8'}{$blocknewsplusiso_lng|escape:'UTF-8'}news/{$post.seo_url|escape:'UTF-8'}" title="{$post.title|escape:'htmlall':'UTF-8'}">
                {else}
                        <a href="{$base_dir_ssl|escape:'UTF-8'}modules/blocknewsplus/item.php?item_id={$post.id|escape:'UTF-8'}" title="{$post.title|escape:'htmlall':'UTF-8'}">
                {/if}
                {$post.title|escape:'UTF-8'|substr:0:36}
                {if strlen($post.title|escape:'UTF-8')>36}...{/if}
                </a>
            </h3>
                <span class="news_date">{$post.time_add|date_format:"%A %d %B %Y"}</span>
                
            <div class="commentbody_center">
                {$post.content|substr:0:140}
                {if strlen($post.content)>140}...{/if}
            </div>
            {if $blocknewsplusis_urlrewrite == 1}
            <a class="link-home" href="{$base_dir_ssl|escape:'UTF-8'}{$blocknewsplusiso_lng}news/{$post.seo_url|escape:'UTF-8'}" 
               title="{$post.title|escape:'htmlall':'UTF-8'}">
            {else}
            <a class="link-home" href="{$base_dir_ssl|escape:'UTF-8'}modules/blocknewsplus/item.php?item_id={$post.id|escape:'UTF-8'}" 
               title="{$post.title|escape:'htmlall':'UTF-8'}">
            {/if}
                {l s='more' mod='blocknewsplus'}
            </a>
        </div>
        {*{if $smarty.foreach.myLoop.iteration is odd}
                <div class="col-md-6 hidden-xs hidden-sm">
                    {if strlen($post.img)>0}
                        <img src="{$base_dir_ssl|escape:'UTF-8'}upload/blocknewsplus/{$post.img|escape:'UTF-8'}" 
                                     title="{$post.title|escape:'htmlall':'UTF-8'}" 
                                    />
                    {else}
                        <img src="{$img_dir}/default-image.png" 
                             title="{$post.title|escape:'htmlall':'UTF-8'}" 
                            />
                    {/if}
                </div>
                {/if}*}
    
{/foreach}
