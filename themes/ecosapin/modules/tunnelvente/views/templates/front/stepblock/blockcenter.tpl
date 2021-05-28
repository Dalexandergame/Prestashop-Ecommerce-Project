<div class="col-md-6 step-desc">
    <div id="my_errors" class="errors">
        {if isset($errors) && $errors}
            <div class="alert alert-danger">		
                    <ol>
                    {foreach from=$errors key=k item=error}
                            <li>{$error}</li>
                    {/foreach}
                    </ol>
            </div>
        {/if}
    </div>
    <div id="resp_content">
        {if $steps->getStepByPosition(1)->getStepDetailByPosition(1)->getActive()}
       
            {include file='module:tunnelvente/views/templates/front/npa.tpl'}
            
        {elseif $steps->getStepByPosition(1)->getStepDetailByPosition(2)->getActive()}

            {include file='module:tunnelvente/views/templates/front/type.tpl' types=$result}
                
        {elseif $steps->getStepByPosition(1)->getStepDetailByPosition(3)->getActive()}

            {include file='module:tunnelvente/views/templates/front/taille.tpl' tailles=$result}

        {elseif $steps->getStepByPosition(1)->getStepDetailByPosition(4)->getActive()}

            {include file='module:tunnelvente/views/templates/front/sapin.tpl' result=$result}

        {elseif $steps->getStepByPosition(1)->getStepDetailByPosition(5)->getActive()}

            {include file='module:tunnelvente/views/templates/front/recyclage.tpl' product=$result}

        {/if}
    </div>
</div>