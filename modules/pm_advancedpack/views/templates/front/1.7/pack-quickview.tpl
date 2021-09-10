
<div id="quickview-modal-{$product.id}-{$product.id_product_attribute}" class="modal fade quickview" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
   <div class="modal-content">
     <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
       </button>
     </div>
     <div class="modal-body">
        {block name='content'}

          <section id="main" itemscope itemtype="https://schema.org/Product">
            <meta itemprop="url" content="{$product.url}">

            {* Pack title *}
            <div class="row">
              <div class="col-xs-12 col-12">
              {block name='page_header_container'}
                {block name='page_header'}
                  <h1 class="h1" itemprop="name">{block name='page_title'}{$product.name}{/block}</h1>
                  {if $product.description_short}
                    <div class="text-xs-justify text-justify" itemprop="description">{$product.description_short nofilter}</div>
                  {/if}
                {/block}
              {/block}
              </div>
            </div>

            <div class="row">
              {* Buy block *}
              {include file='module:pm_advancedpack/views/templates/front/1.7/pack-price-container.tpl' from_quickview=true}
            </div>
            <div class="row">
              {* Product list of the pack *}
              {include file='module:pm_advancedpack/views/templates/front/1.7/pack-product-list.tpl' from_quickview=true}
            </div>
          </section>
        {/block}
     </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="{$urls.base_url}modules/pm_advancedpack/views/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="{$urls.base_url}modules/pm_advancedpack/views/js/pack-17.js"></script>
<link rel="stylesheet" href="{$urls.base_url}modules/pm_advancedpack/views/css/owl.carousel.min.css" type="text/css" media="all">
<link rel="stylesheet" href="{$urls.base_url}modules/pm_advancedpack/views/css/animate.min.css" type="text/css" media="all">
<link rel="stylesheet" href="{$urls.base_url}modules/pm_advancedpack/views/css/pack-17.css" type="text/css" media="all">
<link rel="stylesheet" href="{$urls.base_url}modules/pm_advancedpack/{$ap5_dynamic_css_file}" type="text/css" media="all">
{include file="_partials/javascript.tpl" javascript=null vars=$ap5_js_custom_vars}
<script type="text/javascript">
  ap5Plugin.fromQuickView = true;
  $(document).ready(function() {
    setTimeout(function() {
      $(window).trigger('load');
    }, 200);
    setTimeout(function() {
      $(window).trigger('resize');
    }, 250);
  });
</script>