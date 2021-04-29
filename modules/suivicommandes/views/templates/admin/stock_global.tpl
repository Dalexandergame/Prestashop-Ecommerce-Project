<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="active">
        <a href="#all_warehouse" data-toggle="tab">Résumé global</a>
    </li>
    {foreach from=$warehouses item=warehouse key=index name=warehouseLoop}
        <style>
            .tab-title-warehouse{$warehouse["id_warehouse"]} {
                border-bottom-color: {$list_warehouses[$index]["color"]} !important;
                border-bottom-width: thick !important;
            }

            .tab-content-warehouse{$warehouse["id_warehouse"]} .panel-heading {
                border-bottom-color: {$list_warehouses[$index]["color"]} !important;
                border-bottom-width: thick !important;
            }
        </style>
        <li>
            <a href="#warehouse{$warehouse["id_warehouse"]}" data-toggle="tab"
               class="tab-title-warehouse{$warehouse["id_warehouse"]}">
                {$warehouse["name"]} (id:{$warehouse["id_warehouse"]})
            </a>
        </li>
    {/foreach}
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="all_warehouse">
        {foreach from=$all_warehouses item=warehouse key=index}
            <div class="tab-content-warehouse{$warehouse["id_warehouse"]}">
                {$warehouse['products']}
            </div>
        {/foreach}
    </div>
    {foreach from=$warehouses item=warehouse}
        <div class="tab-pane" id="warehouse{$warehouse["id_warehouse"]}">
            {$warehouse['products']}
        </div>
    {/foreach}
</div>