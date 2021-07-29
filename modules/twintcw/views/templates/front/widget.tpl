{extends file='page.tpl'}

{block name='page_content'}
   {capture name=path}{lcw s='Payment' mod='twintcw'}{/capture}

	<h1 class="page-heading">{lcw s='Payment' mod='twintcw'}</h1>

	<div class="twintcw-widget">{$widget nofilter}</div>
{/block}



