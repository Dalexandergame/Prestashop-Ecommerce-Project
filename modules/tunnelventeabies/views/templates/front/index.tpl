{extends file='page.tpl'}
{block name='page_content'}
    <div id="steps">
        <a href="{$urls.base_url}" class="fermerTunnel"></a>
        {include file="module:tunnelventeabies/views/templates/front/stepblock/blocksteps.tpl" steps=$steps }
    </div>
{literal}
    <script type="text/javascript">
        $(function ($) {
            /**
             * show message
             * @param string msg
             * @returns {undefined}
             */
            showError = function (msg) {
                if ($('#my_errors').find('.alert').length == 0) {
                    $('#my_errors').append($('<div />').attr('class', 'alert alert-danger'));
                }
                if ($('#my_errors').find('ol').length > 0) {
                    $('#my_errors').find('ol').empty().append($("<li/>").html(msg));
                } else {
                    $('#my_errors .alert').append($('<ol />').append($("<li/>").html(msg)));
                }
            };

            /**
             * display/hidden block setp
             * @param int num
             * @returns {undefined}
             */

            RemoveStepClassOtherThan = function (num) {

                var i = 1;
                for (i; i < 11; i++) {
                    if (i !== num) {
                        $('.thirdCol').removeClass('step' + (parseInt(i)) + 'Col');
                    }
                }

            };

            ShowHideStep = function (num) {
                $('.step-ul .step').removeClass("active");

                var currentStep = parseInt(num);

                $.each($('.step-ul .step'), function () {
                    var activeStep = parseInt($(this).attr('data-step'));

                    if (activeStep == currentStep) {
                        $(this).addClass('active');
                    }
                });
            };

            var windowWidth = $(window).width();

            if (windowWidth < 960) {
                $('.step-ul').insertAfter('.thirdCol');
            }
        });

        $(window).resize(function () {
            var windowWidth = $(window).width();

            if (windowWidth < 960) {
                $('.step-ul').insertAfter('.thirdCol');
            } else {
                $('.step-ul').insertBefore('.step-desc');
            }
        });


    </script>
{/literal}
{/block}