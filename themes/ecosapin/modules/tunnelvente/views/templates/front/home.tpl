<div class="btns container">
    <a href="{$urls.base_url}module/tunnelvente/type" class="choix_spain_tunn"> {l s="Choisir mon sapin" mod='tunnelvente'}</a>
    {*<div class="voir_video"> Voir le video</div>*}
</div>
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
   
    {include file="module:tunnelvente/views/templates/front/stepblock/blocksteps.tpl" steps=$steps}
    
</div>
<div id="video_eco">
    <div class="container">
        <h1>{l s="Découvrez écosapin en video" mod='tunnelvente'}</h1>
        {*<p>
            {l s="Ecosapin bous présente sont concep en video ... de première qualité qui sent bon la forêt, livré et récupéré à domicile, aux dates de votre choix. Après les fêtes, nous replantons les sapins en pot jusqu'à l'année suivante." mod='tunnelvente'}
        </p>*}
        <a href="{l s="https://www.youtube.com/watch?v=Co6Q6u8JDxQ" mod='tunnelvente'}" class="play" title="Ecosapin bous présente sont concept en video...">Play</a>
    </div>
</div>

<div id="my_littel_ecosapin">
    <div class="container">
        <h1>{l s="little ecosapin" mod='tunnelvente'}</h1>
        <p>
            {l s="Votre petit sapin en pot, idéal pour décorer votre table ou votre bureau" mod='tunnelvente'}            
        </p>
        <a href="{$urls.base_url}stock-pack/93-my-little-ecosapin.html" class="little_ecosapin" title="{l s="Choisir mon little ecosapin " mod='tunnelvente'}">{l s="Choisir mon little ecosapin " mod='tunnelvente'} </a>
    </div>
</div>

{*<div class="btns container">*}
{*<a href="http://www.ecosapin.fr?redirect=1" class="choix_spain_tunn"> {l s="Ouvrir site de France" mod='tunnelvente'}</a>*}
{*</div>*}
      
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
<script type="text/javascript">
    $(document).ready(function () {
        $.getJSON('https://ipapi.co/json/', function (data) {
            let desired_code = 'ch';
            let country_code = data.country.toLowerCase();

            $(".site-" + desired_code).click(function (e) {
                e.preventDefault();
                $("#popup-pays").removeClass("popup-show");
            });

            if (get("redirect") != 1 && !document.referrer.includes("ecosapin." + desired_code)) {
                if (country_code != desired_code) {
                    $("#popup-pays").addClass("popup-show");
                }
            }
        });

        $("body").on("click", ".popup-show", function () {
            $("#popup-pays").removeClass("popup-show");
        });
    });

    function get(name) {
        if (name = (new RegExp('[?&]' + encodeURIComponent(name) + '=([^&]*)')).exec(location.search))
            return decodeURIComponent(name[1]);
    }
</script>