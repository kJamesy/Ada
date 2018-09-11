<script>
    jQuery(document).ready(function($) {
        'use strict';

        $('[data-toggle="offcanvas"]').on('click', function () {
            $('.offcanvas-collapse').toggleClass('open');
        });

        let $anchoredLinks = $('.anchor-menu a');
        let headerHeight = $('.navbar').outerHeight();

        $anchoredLinks.each(function() {
            if ( $(this).attr('href').toLowerCase() === location.hash.toLowerCase() ) {
                $(this).parent().addClass('active');
                return false;
            }
        });

        $anchoredLinks.click(function() {
            if ( location.pathname.replace(/^\//,'').toLowerCase() === this.pathname.replace(/^\//,'').toLowerCase() && location.hostname.toLowerCase() === this.hostname.toLowerCase() ) {
                let target = $(this.hash);
                let $this = $(this);
                let $parent = $this.parent();
                let hash = $this.attr('href').toLowerCase();
                let $htmlBody = $('html,body');

                if (target.length) {
                    let t = target.offset().top - headerHeight;

                    $('.offcanvas-collapse').removeClass('open');

                    $htmlBody.stop().animate({
                        scrollTop: t
                    }, 800, function() {
                        location.hash = hash;
                        $parent.siblings().removeClass('active');
                        $parent.addClass('active');
                        $htmlBody.scrollTop(t);
                    });

                    return false;
                }
            }
        });

        $('.main-content img').each(function() {
            $(this).addClass('img-fluid');
        });

        //Animated UL open
        let $ulToggle = $('.animated-toggle');

        //Open the Menu for the Active Parent
        $ulToggle.each(function() {
            let $this = $(this);
            let $parent = $this.parent();

            if ( $parent.hasClass('active') ) {
                $parent.addClass('active-parent');
                let $closedUl = $this.parent().find('ul');

                if ( $this.hasClass('open') ) {
                    $this.removeClass('open');
                    $closedUl.slideUp();
                }
                else {
                    $this.addClass('open');
                    $closedUl.slideDown();
                }
            }
        });

        $ulToggle.click(function(event) {
            event.preventDefault();
            let $this = $(this);
            let $closedUl = $this.parent().find('ul');

            if ( $this.hasClass('open') ) {
                $this.removeClass('open');
                $closedUl.slideUp();
            }
            else {
                $this.addClass('open');
                $closedUl.slideDown();
            }
        });

        // Add Class to Parent of Active Item
        let $childrenLi =  $('.second-level-ul li');
        $childrenLi.each(function() {
            let $this = $(this);
            if ( $this.hasClass('active') ) {
                let $parentUl = $this.parent();
                $parentUl.parent().addClass('active-parent');
                $parentUl.parent().find('.animated-toggle').addClass('open');
                $parentUl.slideDown();
            }
        })
    });
</script>