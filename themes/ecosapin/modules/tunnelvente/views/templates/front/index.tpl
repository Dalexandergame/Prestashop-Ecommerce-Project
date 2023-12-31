{extends file='page.tpl'}
{block name='page_content'}
    <div id="steps">
    <a href="{$urls.base_url}" class="fermerTunnel"></a>
    {include file="module:tunnelvente/views/templates/front/stepblock/blocksteps.tpl" steps=$steps }
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

            $('.thirdCol').addClass('step1Col');

            ShowHideStep = function (num) {
                $('#steps .step-ul li').removeClass("isOk").removeClass("active");

                var classHidden = "hidden";
                var steps = {
                    lastStep1: 4,
                    lastStep2: 6,
                    lastStep3: 8,
                };

                $.each($('#steps .step-ul li'), function (i) {
                    if (parseInt(i) < (parseInt(num) - 1)) {
                        $(this).addClass("isOk");
                    } else if (parseInt(i) == (parseInt(num) - 1)) {
                        $(this).addClass("active");

                        RemoveStepClassOtherThan(num);

                        $('.thirdCol').addClass('step' + num + 'Col');
                    }
                });

                /* title & step blcok left */
                $.each($('#steps .step_1 ,#steps .step_2 ,#steps .step_3 '), function () {
                    $(this).removeClass("active").find('ul').addClass(classHidden);
                });

                if (steps.lastStep1 >= parseInt(num)) {
                    $('#steps .step_1').addClass("active").removeClass('completed').find('ul').removeClass(classHidden);
                    $('#steps .step_2').addClass("inactive").removeClass('completed');
                    $('#steps .step_3').addClass("inactive");

                } else if (steps.lastStep2 >= parseInt(num)) {
                    $('#steps .step_1').removeClass("active").addClass('completed');
                    $('#steps .step_2').removeClass('inactive').addClass("active").find('ul').removeClass(classHidden);
                } else if (steps.lastStep3 >= parseInt(num)) {
                    $('#steps .step_1,#steps .step_2').removeClass("active").addClass('completed');
                    $('#steps .step_3').removeClass('inactive').addClass("active").find('ul').removeClass(classHidden);
                }
            };

            $('#steps .step_2 ,#steps .step_3 ').addClass("inactive");

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