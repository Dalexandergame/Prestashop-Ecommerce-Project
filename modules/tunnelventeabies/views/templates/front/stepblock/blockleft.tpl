<div class="col-md-12 step-ul">
    <div class="cont-table container">
        {assign var='i' value=1}
        {assign var='isOk' value="isOk"}
        <div class="steps-container">
            {foreach from=$steps->getListStep()  item=step}
                {foreach from=$step->getListStepDetail()   item=stepDetail}
                    <div data-step="{$i}" class="step{if $stepDetail->getActive() } active{/if}">
                        <div class="step-number">{$i}</div>
                        <div class="step-description">{$stepDetail->getTitre()}</div>
                    </div>
                    <div class="line-step"></div>
                    {assign var='i' value=$i+1}
                {/foreach}
            {/foreach}
        </div>
    </div>
    <div class="priceCalcContainer" data-currency="{$currency.sign}"></div>
    <script>
        $(function() {
            /*$('.step_1').addClass('active');
            $('.step_2').addClass('inactive');
            $('.step_3').addClass('inactive');*/
        })
    </script>
</div>