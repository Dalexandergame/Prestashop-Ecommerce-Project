<div class="col-md-12 step-ul">
    <div class="cont-table">
    {assign var='i' value=1}
    {assign var='isOk' value="isOk"}
    {foreach from=$steps->getListStep()  item=step}
        <div class="step_{$i} steps_step {if $step->getActive()}active{/if}">
        	<div class="bloc-cont">
                <div class="title">{$step->getTitre()} </div>
                <ul class="{if !$step->getActive()}   {/if}">
                    {foreach from=$step->getListStepDetail()   item=stepDetail}
                        {*<li class="{if $stepDetail->getActive() }active {assign var='isOk' value=""}{else} {$isOk} {/if}"> {$stepDetail->getTitre()}</li>*}
                       <li class="{if $stepDetail->getActive() }active {assign var='isOk' value=""}{else} {$isOk} {/if}"></li>
                    {/foreach}             
                </ul>
            </div>
        </div>
        {assign var='i' value=$i+1}
    {/foreach}
    </div>
    <script>
        $(function() {
            /*$('.step_1').addClass('active');
            $('.step_2').addClass('inactive');
            $('.step_3').addClass('inactive');*/
        })
    </script>
</div>