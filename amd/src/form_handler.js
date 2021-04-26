    define(['jquery'], function($) {
        return {
            init: function(language = 'en') {
                let slideCount = 0;
                let maxEditors = 25;
                let minEditors = 1;

                let strings = {
                    pt_br: {
                      content: 'Conteúdo',
                      slide: 'Slide {n}'
                    },
                    en: {
                        content: 'Content',
                        slide: 'Slide {n}'
                    }
                };

                hideItem('fitem_id_introeditor');
                initView();

                function hideItem(id) {
                    $("#" + id).hide();
                    $("#" + id).find('.editor_atto_content').empty();
                    $("#" + id).find('textarea').val('');
                }

                function showItem(id) {
                    $("#" + id).show();
                }

                function showCollapse() {
                    changeLabel(false);
                    showItem('fitem_id_title');
                    showEditors(1);
                    hideEditors(2);
                }

                function showNone() {
                    changeLabel(false);
                    hideItem('fitem_id_title');
                    showEditors(1);
                    hideEditors(2);
                }

                function showSlide() {

                    changeLabel(true);

                    slideCount = 2;

                    hideItem('fitem_id_title');
                    showEditors(2);
                    hideEditors(3);

                }

                function showCollapseSlide() {
                    changeLabel(true);

                    slideCount = 2;

                    showItem('fitem_id_title');
                    showEditors(2);
                    hideEditors(3);
                }

                function showEditors(max = 25) {
                    for (let i = 1; i <= max; i++) {
                        showItem('fitem_id_content' + i);
                    }
                }

                function hideEditors(min = 1) {
                    for (let i = min; i <= maxEditors; i++) {
                        hideItem('fitem_id_content' + i);
                    }
                }

                function updateView() {
                    const value = $("#id_templatetype").val();

                    switch (value) {
                        case "none":
                            showNone();
                            break;
                        case "collapse":
                            showCollapse();
                            break;
                        case "slide":
                            showSlide();
                            break;
                        case 'collapse-slide':
                            showCollapseSlide();
                            break;
                    }
                }

                function changeLabel(isSlide) {
                    const string = isSlide ? 'slide' : 'content';

                     for (let i = minEditors; i <= maxEditors; i++ ) {
                         const element = $('#fitem_id_content' + i + ' label').first();
                         let label = strings[language][string].replace('{n}', i);
                         element.html(label);
                     }

                }

                function initView() {
                    updateView();
                    for (let i = 1; i <= 25; i++) {
                        const textAreaValue = $('#id_content' + i).val();

                        if (textAreaValue) {
                            showItem('fitem_id_content' + i);
                        }

                    }

                }

                $("#id_templatetype").on('change', function () {
                   updateView();
                });

                $("#id_addslide").on('click', function() {
                   slideCount++;
                   showEditors(slideCount);
                });
            }
        };
    });