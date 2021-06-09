<div class="col-md-12 step-ul">
    <div class="cont-table container">
        {assign var='i' value=1}
        {assign var='isOk' value="isOk"}
        <div class="steps-container">
            {foreach from=$steps->getListStep()  item=step}
                {foreach from=$step->getListStepDetail()   item=stepDetail}
                    {*<li class="{if $stepDetail->getActive() }active {assign var='isOk' value=""}{else} {$isOk} {/if}"> {$stepDetail->getTitre()}</li>*}
                    <div class="step {if $stepDetail->getActive() }active {assign var='isOk' value=""}{else} {$isOk} {/if}">
                        <div class="step-number">{$i}</div>
                        <div class="step-description">{$stepDetail->getTitre()}</div>
                    </div>
                    {assign var='i' value=$i+1}
                {/foreach}
            {/foreach}
        </div>
    </div>
    <script>
        $(function() {
            /*$('.step_1').addClass('active');
            $('.step_2').addClass('inactive');
            $('.step_3').addClass('inactive');*/
        })
    </script>
</div>