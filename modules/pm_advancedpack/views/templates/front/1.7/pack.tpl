{extends file='catalog/product.tpl'}

{block name='content'}

  <section id="main" itemscope itemtype="https://schema.org/Product">
    <meta itemprop="url" content="{$product.url}">

    {* Pack title *}
    {block name='ap5_pack_title'}
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
    {/block}

    <div class="row">

      {block name='ap5_product_list'}
        {* Product list of the pack *}
        {include file='module:pm_advancedpack/views/templates/front/1.7/pack-product-list.tpl'}
      {/block}

      {block name='ap5_buy_block'}
        {* Buy block *}
        {include file='module:pm_advancedpack/views/templates/front/1.7/pack-price-container.tpl'}
      {/block}
    </div>

    {block name='ap5_pack_description'}
      {* Pack description *}
      {if $product.description}
      <div class="row">
        <div id="ap5-pack-description-block" class="card">
          <div class="card-header">
            <h3 class="page-product-heading">{l s='Pack description' mod='pm_advancedpack'}</h3>
          </div>
          <div class="card-block">
            <div class="rte">{$product.description nofilter}</div>
          </div>
        </div>
      </div>
      {/if}
    {/block}

    {block name='ap5_tabs_header'}
      {* Product tabs *}
      {if $packShowProductsFeatures || $packShowProductsShortDescription || $packShowProductsLongDescription}
      <div class="row">
        <div id="ap5-pack-content-block" class="card">
          <div class="card-header">
            <h3 class="page-product-heading">{l s='Pack content' mod='pm_advancedpack'}</h3>
          </div>
          <div class="card-block">
            {include file='module:pm_advancedpack/views/templates/front/1.7/pack-product-list-tabs.tpl'}
          </div>
        </div>
      </div>
      {/if}
    {/block}

    {block name='ap5_product_attachments'}
      {if $product.attachments}
        <div class="row">
          <div id="ap5-pack-download-block" class="card">
            <div class="card-header">
              <h3 class="page-product-heading">{l s='Download' d='Shop.Theme.Actions'}</h3>
            </div>
            <div class="card-block">
              {foreach from=$product.attachments item=attachment}
                <div class="attachment">
                  <h4>
                    <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                      {$attachment.name}
                    </a>
                  </h4>
                  <p>{$attachment.description}</p>
                  <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                    {l s='Download' d='Shop.Theme.Actions'} ({$attachment.file_size_formatted})
                  </a>
                </div>
              {/foreach}
            </div>
          </div>
        </div>
      {/if}
    {/block}

    {block name='ap5_accessories'}
      {block name='product_accessories'}
        {if $accessories}
          <section class="product-accessories clearfix">
            <h3 class="h5 text-uppercase">{l s='You might also like' d='Shop.Theme.Catalog'}</h3>
            <div class="products">
              {foreach from=$accessories item="product_accessory"}
                {block name='product_miniature'}
                  {include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory}
                {/block}
              {/foreach}
            </div>
          </section>
        {/if}
      {/block}
    {/block}

    {block name='ap5_footer'}
      {block name='product_footer'}
        {hook h='displayFooterProduct' product=$product category=$category}
      {/block}
    {/block}

    {block name='ap5_images'}
      {block name='product_images_modal'}
        {include file='catalog/_partials/product-images-modal.tpl'}
      {/block}
    {/block}

    {block name='ap5_footer_container'}
      {block name='page_footer_container'}
        <footer class="page-footer">
          {block name='page_footer'}
            <!-- Footer content -->
          {/block}
        </footer>
      {/block}
    {/block}
  </section>

{/block}
