;(function ($, Dnxte_Essential) {
    'use strict';

        var $tabsNav = $('.dnxte-nav').find('.dnxte-tabs-nav'),
        $tabsContent = $('.dnxte-admin-tabs').find('.dnxte-admin-tabs-content');

    $tabsNav.on('click', '.dnxte-admin-nav-item-link', function(e) {     
        var $currentTab = $(e.currentTarget),
            tabTargetHash = e.currentTarget.hash,
            tabIdSelector = '#tab-content-' + tabTargetHash.substring(1),
            $currentTabContent = $tabsContent.find(tabIdSelector);

        e.preventDefault();

        $currentTab
            .addClass('active-tab')
            .siblings()
            .removeClass('active-tab');

        $currentTabContent
            .addClass('active-tab')
            .siblings()
            .removeClass('active-tab');

        window.location.hash = tabTargetHash;
    });

    if (window.location.hash) {
        $tabsNav.find('a[href="'+window.location.hash+'"]').click();
    }

    var $adminForm = $('#dnxte-admin-modules-form'),
        $saveButton = $adminForm.find('.dnxte-btn-save');

    $adminForm.on('submit', function (e) {
        e.preventDefault();

        $.post({
            url: Dnxte_Essential.ajaxurl,
            data: {
                nonce: Dnxte_Essential.nonce,
                action: Dnxte_Essential.action,
                data: $adminForm.serialize(),
            },
            
            beforeSend: function () {
                $saveButton
                    .text(".....")
                    .css("animation", "animateTextIndent infinite 2.5s");
            },

            success: function(res) {
                if ( res.success ) {
                    var t = setTimeout(function () {
                        $saveButton
                            .attr('disabled', true)
                            .text('Changes Saved');
                        clearTimeout(t);
                    }, 500);
                }
            }
        });


    });
    $adminForm.on('change', ':checkbox, :radio', function() {
        $saveButton.attr('disabled', false).text('Save Changes');
    });

    $adminForm.on('change', ':text', function() {
        $saveButton.attr('disabled', false).text('Save Changes');
    });

    // enable/disable all widgets
	$('.dnxte-toggle-all').click(function(e){
		if($('.dnxte-toggle-all-wrap input').is(':checked')) {
            $('.dnxte-toggle-check:visible').prop('checked', false).change();
            $(".dnxte-toggle-all").text("Disable All");
		}
		else {
            $('.dnxte-toggle-check:visible').prop('checked', true).change();
            $(".dnxte-toggle-all").text("Enable All");
		}
	});
    
    // Search Filter
	const inputSearch = $('#dnxte-modules-filter-search-input'),
        searchResult = $('#dnxte-admin-modules-form .dnxte-admin-modules-item');
    
    inputSearch.on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(searchResult).filter(function () {
		    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
	});
    
}(jQuery, window.Dnxte_Essential));