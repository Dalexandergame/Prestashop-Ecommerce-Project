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

{capture name=path}
{if $blocknewsplusis_urlrewrite == 1}
<a href="{$base_dir_ssl|escape:'UTF-8'}{$blocknewsplusiso_lng|escape:'UTF-8'}news">
{else}
<a href="{$base_dir_ssl|escape:'UTF-8'}modules/blocknewsplus/items.php">
{/if}
{l s='News' mod='blocknewsplus'}</a>
	<span class="navigation-pipe">></span>
{$meta_title|escape:'UTF-8'}
{/capture}


{if $blocknewsplusis16 == 0}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{$meta_title|escape:'UTF-8'}</h2>
{else}
<h1 class="page-heading text-center mt-50">{$meta_title|escape:'UTF-8'}</h1>
{/if}

<div class="blog-post-item" id="item-detail">

<div id="list_reviews" class="productsBox1 list-item">
{foreach from=$posts item=post name=myLoop}
             
                                
        <h1 class="hide">{$post.title|escape:'htmlall':'UTF-8'}</h1>
        <span class="news_date  text-center">{$post.time_add|date_format:"%A %d %B %Y"}</span>
       
        {if strlen($post.img)>0}
            <img class="default-img img-wsy" src="{$base_dir_ssl|escape:'UTF-8'}upload/blocknewsplus/{$post.img|escape:'UTF-8'}" 
                 title="{$post.title|escape:'htmlall':'UTF-8'}" 
                />
       {* {else}
                   <img class="default-img" src="{$img_dir}/default-image.png" 
                        title="{$post.title|escape:'htmlall':'UTF-8'}" 
                       />*}
        {/if}
        
        <div class="commentbody_center">
            {$post.content|escape:'UTF-8'}
           
        </div>
            <a href="javascript:window.history.back(-1);" title="{l s='Retour' mod='blocknewsplus'}" class="btn btn-default link-home">{l s='Retour' mod='blocknewsplus'}</a>
{/foreach}
{if $blocknewsplusis16==1}<div class="clear"></div>{/if}
</div>



{if count($related_posts)>0}

<div class="rel-posts-block">
<div class="related-posts-title">{l s='Related News' mod='blocknewsplus'}</div>

<div class="realated-posts-items">
	{foreach from=$related_posts item=relpost name=myLoop}
	<a title="{$relpost.title|escape:'html'}"
	{if $blocknewsplusis_urlrewrite == 1}
	    href="{$base_dir_ssl|escape:'UTF-8'}{$blocknewsplusiso_lng|escape:'UTF-8'}news/{$relpost.url|escape:'UTF-8'}" 
	{else}
	 	href="{$base_dir_ssl|escape:'UTF-8'}modules/blocknewsplus/item.php?item_id={$relpost.id|escape:'UTF-8'}" 
	{/if}
	>
		{$relpost.title|escape:'html'}
	</a>
	<br/>
	{/foreach}
</div>		


</div>
{/if}


{if $blocknewsplusis16==1}<div class="clear"></div>{/if}


</div>