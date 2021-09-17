<div id="steps" style="display:none">
   {* <div class="row">
        <div class="col-md-6">
            <div id="header_logo2">
                <a href="{$urls.base_url}" title="{$shop_name|escape:'html':'UTF-8'}">
                        <img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <div id="menuh_2">
                {hook h='displayMymenu'}
                <div class="cleafix clear"></div>
            </div>
        </div>
        <div class="cleafix clear"></div>
    </div>*}
   
    {include file="module:tunnelventeabies/views/templates/front/stepblock/blocksteps.tpl" steps=$steps}
    
</div>


      
{literal}

<script type="text/javascript">
    $(function($){
        var texteOutStock2 = 'Sold out! Tous les  Ecosapins ont trouvé preneur cette année. Revenez nous voir en 2016';
        $('<a href="#" class="close-tunnel"><i class="icon-remove"></i></a>').appendTo('.thirdCol');
        $('.close-tunnel').on("click", function(e){
             $('#steps').slideUp("slow",function(){
                $('#video_eco,#my_littel_ecosapin').slideDown();
            });
            $('html, body').animate({
                        scrollTop: 0
                    }, 1500, 'easeInOutQuart');
           e.preventDefault();
        });
        //click Choisir mon sapin
        $('.btns .choix_spain').on("click", function(){
           // $('#test-popup strong').text(texteOutStock2);
            //$('.popup-link').magnificPopup('open');
            //return false;
             $('#steps').slideDown("slow",function(){
                $('#video_eco,#my_littel_ecosapin').slideUp();
            });
            if( $('#header').hasClass('header-fixed') ) {
                $('html, body').animate({
                        scrollTop: $("#steps").offset().top - 69 
                    }, 1500, 'easeInOutQuart');
            }  else {
                $('html, body').animate({
                        scrollTop: $("#steps").offset().top - 237 
                    }, 1500, 'easeInOutQuart');
            } 
            
           
        } /*, function() {
           $('#steps').slideToggle("slow",function(){
                $('#video_eco,#my_littel_ecosapin').slideToggle();
            });   
            $('html, body').animate({
                        scrollTop: 0
                    }, 700);
        }*/);
         

          $('.play').magnificPopup({
          disableOn: 700,
          type: 'iframe',
          mainClass: 'mfp-fade',
          removalDelay: 160,
          preloader: false,

          fixedContentPos: true
        });

    });
</script>
<script>
    $(document).ready(function() {
    $(".input_npa").numeric()
});
</script>
{/literal}