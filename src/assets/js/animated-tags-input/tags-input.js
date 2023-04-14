
const selectTags = (id, tags) => {
    let select = document.getElementById(id).parentElement.parentElement;
    select
        .nextElementSibling
        .querySelectorAll('li')
        .forEach(li => {
            tags.forEach(tag => {
                if (tag == li.textContent.toLowerCase()) {
                    select.insertAdjacentHTML('beforeend', `<a class="notShown shown" style=""><em>${li.textContent}</em><i></i></a>`);
                    select.querySelector('span').classList.add('hide');
                    li.remove();
                    $(select).find('option:contains(' + li.textContent + ')').prop('selected', true);
                }
            });
        });
}

$(document).ready(function () {
    $('select[multiple]').each(function () {
        var options = $(this).find('option');

        var div = $('<div />').addClass('selectMultiple');
        var active = $('<div />');
        var list = $('<ul />');
        var placeholder = $(this).data('placeholder');
        var span = $('<span />').text(placeholder).appendTo(active);

        options.each(function () {
            var text = $(this).text();
            if ($(this).is(':selected')) {
                active.append($('<a />').html('<em>' + text + '</em><i></i>'));
                span.addClass('hide');
            } else {
                list.append($('<li />').html(text));
            }
        });

        active.append($('<div />').addClass('arrow'));
        div.append(active).append(list);

        $(this).wrap(div);
    })


    $(document).on('click', '.selectMultiple ul li', function (e) {
        var select = $(this).parent().parent();
        var li = $(this);
        if (!select.hasClass('clicked')) {
            select.addClass('clicked');
            li.prev().addClass('beforeRemove');
            li.next().addClass('afterRemove');
            li.addClass('remove');
            var a = $('<a />').addClass('notShown').html('<em>' + li.text() + '</em><i></i>').hide().appendTo(select.children('div'));
            a.slideDown(300, function () {
                setTimeout(function () {
                    a.addClass('shown');
                    select.children('div').children('span').addClass('hide');
                    select.find('option:contains(' + li.text() + ')').prop('selected', true);
                }, 300);
            });
            setTimeout(function () {
                if (li.prev().is(':last-child')) {
                    li.prev().removeClass('beforeRemove');
                }
                if (li.next().is(':first-child')) {
                    li.next().removeClass('afterRemove');
                }
                setTimeout(function () {
                    li.prev().removeClass('beforeRemove');
                    li.next().removeClass('afterRemove');
                }, 100);

                li.slideUp(300, function () {
                    li.remove();
                    select.removeClass('clicked');
                });
            }, 500);
        }
    });

    $(document).on('click', '.selectMultiple > div a', function (e) {
        var select = $(this).parent().parent();
        var self = $(this);
        self.removeClass().addClass('remove');
        select.addClass('open');
        setTimeout(function () {
            self.addClass('disappear');
            setTimeout(function () {
                self.animate({
                    width: 0,
                    height: 0,
                    padding: 0,
                    margin: 0
                }, 200, function () {
                    var li = $('<li />').text(self.children('em').text()).addClass('notShown').appendTo(select.find('ul'));
                    li.slideDown(300, function () {
                        li.addClass('show');
                        setTimeout(function () {
                            select.find('option:contains(' + self.children('em').text() + ')').prop('selected', false);
                            if (!select.find('option:selected').length) {
                                select.children('div').children('span').removeClass('hide');
                            }
                            li.removeClass();
                        }, 300);
                    });
                    self.remove();
                })
            }, 200);
        }, 300);
    });

    $(document).on('click', '.selectMultiple > div .arrow, .selectMultiple > div span', function (e) {
        $(this).parent().parent().toggleClass('open');
    });



});
