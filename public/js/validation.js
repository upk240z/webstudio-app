var Validation = {
    form: null,
    isZenkaku : function(str)
    {
        var txt = 'ｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾅﾆﾇﾈﾉﾊﾋﾌﾍﾎﾏﾐﾑﾒﾓﾔﾕﾖﾗﾘﾙﾚﾛﾜｦﾝｧｨｩｪｫｬｭｮｯｰ､｡｢｣ﾞﾟ';

        for (var i=0;i<str.length;i++) {
            if (escape(str.charAt(i)).length <= 4) return false;
            if (txt.indexOf(str.charAt(i), 0) >= 0) return false;
        }

        return true;
    },
    isKana : function(str)
    {
        return str.match(/^[ァ-ロワヲンー 　\r\n\t]*$/) !== null;
    },
    isHira : function(str)
    {
        return str.match(/^[ぁ-ろわをんー 　\r\n\t]*$/) !== null;
    },
    zen2han : function(str)
    {
        return str.replace(/[Ａ-Ｚａ-ｚ０-９．]/g, function(s)
        {
            return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
        });
    },
    han2zen : function(str)
    {
        return str.replace(/[A-Za-z0-9\-]/g, function(s)
        {
            return String.fromCharCode(s.charCodeAt(0) + 0xFEE0);
        });
    },
    daysOfMonth : function(month, year)
    {
        month = parseInt(month);
        year = parseInt(year);

        if (month === 2) {
            if (
                year % 4 === 0 && (
                    year % 100 > 0 ||
                    (year % 100 === 0 && year % 400 === 0)
                )
            ) {
                return 29;
            } else {
                return 28;
            }
        } else if (
            month === 4 ||
            month === 6 ||
            month === 9 ||
            month === 11
        ) {
            return 30;
        }

        return 31;
    },
    escape: function(val) {
        return val.replace(/[ !"#$%&'()*+,.\/:;<=>?@\[\\\]^`{|}~]/g, '\\$&');
    },
    setForm: function (pform)
    {
        Validation.form = $(pform);
        Validation.watchError();
    },
    findTip: function (target)
    {
        var formName = target.attr('name');
        var tip = Validation.form.find('p[for=' + Validation.escape(formName) + ']');

        if (tip.length == 0) {
            tip = $('<p></p>').attr('for', formName).addClass('validation');
            tip.hide();
            target.before(tip);
        }

        return tip;
    },
    checkTip: function(target)
    {
        var targetName = target.attr('name');
        var count = 0;
        $('[name=' +  Validation.escape(targetName) + ']').each(function() {
            var _errors = $(this).data('errors');
            for (var type in _errors) {
                if (_errors[type] == true) {
                    count++;
                }
            }
        });
        if (count == 0) {
            Validation.hideTip(target);
        }
    },
    showTip: function (target, msg)
    {
        var tip = Validation.findTip(target);

        tip.parent().addClass('validation-parent');
        tip.text(msg);
        tip.show();

        var targetName = target.attr('name');
        var targets = $('[name=' + Validation.escape(targetName)  +  ']');
        var targetFirst = $('[name=' + Validation.escape(targetName)  +  ']:first');

        targets.on('change', function()
        {
            if ($(this).val().length > 0) {
                Validation.clearError('require', targetFirst);
            }
        });
    },
    hideTip: function (target)
    {
        Validation.findTip(target).hide();
    },
    error: function (type, target, msg)
    {
        Validation.hideTip(target);
        Validation.showTip(target, msg);
        var _errors = target.data('errors');
        if (_errors === undefined)
            _errors = {};
        _errors[type] = true;
        target.data('errors', _errors);
    },
    clearError: function (type, target)
    {
        var _errors = target.data('errors');
        if (_errors === undefined) {
            _errors = {};
        }
        _errors[type] = false;

        target.data('errors', _errors);

        Validation.checkTip(target);
    },
    existError: function ()
    {
        return Validation.form.find('p.validation:visible').length > 0;
    },
    scrollError: function ()
    {
        var target = Validation.form.find('p.validation:visible:first').closest('.validation-parent');
        $('html, body').animate({ scrollTop : target.offset().top - 50 });
    },
    validate: function ()
    {
        var map = {};

        Validation.form.find('[validation*=require]').each(function()
        {
            var tagName = $(this).get(0).tagName;
            var name = $(this).attr('name');
            var type = $(this).attr('type');
            if (type) { type = type.toLowerCase(); }
            if (
                tagName == 'SELECT' ||
                tagName == 'TEXTAREA' ||
                (tagName == 'INPUT' && (
                        type == 'text' ||
                        type == 'password' ||
                        type == 'search' ||
                        type == 'tel' ||
                        type == 'url' ||
                        type == 'email' ||
                        type == 'date' ||
                        type == 'number' ||
                        type === undefined)
                )
            ) {
                if ($(this).val().length == 0) {
                    Validation.error(
                        'require',
                        $(this),
                        tagName == 'SELECT' ? '選択して下さい' : '必須項目です'
                    );
                } else {
                    Validation.clearError('require', $(this));
                }
            } else if (
                (tagName == 'INPUT' && (type == 'radio' || type == 'checkbox'))
            ) {
                if (map[name] === undefined)
                    map[name] = 0;
                if ($(this).prop('checked'))
                    map[name]++;
            }
            Validation.checkTip($(this));
        });

        for (var k in map) {
            if (map[k] === 0) {
                Validation.error(
                    'require',
                    Validation.form.find('[name=' + k + ']:first'),
                    '選択して下さい'
                );
            } else {
                Validation.clearError('require', Validation.form.find('[name=' + k + ']:first'));
            }
        }
    },
    watchError: function ()
    {
        // 全角制限
        Validation.form.find('[validation*=zen]').on('change', function (e)
        {
            var target = $(this);
            var txt = Validation.han2zen(target.val());
            if (txt != target.val()) { target.val(txt); }
            if (Validation.isZenkaku(txt) === false) {
                Validation.error('zen', target, '入力できない文字があります');
            } else {
                Validation.clearError('zen', target);
            }
        });

        // 全角カナ制限
        Validation.form.find('[validation*=kana]').on('change', function (e)
        {
            var target = $(this);
            var txt = Validation.han2zen(target.val());
            if (txt != target.val()) { target.val(txt); }
            if (Validation.isKana(txt) === false) {
                Validation.error('kana', target, '入力できない文字があります');
            } else {
                Validation.clearError('kana', target);
            }
        });

        // 全角ひらがな制限
        Validation.form.find('[validation*=hira]').on('change', function (e)
        {
            var target = $(this);
            var txt = Validation.han2zen(target.val());
            if (txt != target.val()) { target.val(txt); }
            if (Validation.isHira(txt) === false) {
                Validation.error('hira', target, '入力できない文字があります');
            } else {
                Validation.clearError('hira', target);
            }
        });

        // 半角数字
        Validation.form.find('[validation*=number]').on('change', function (e)
        {
            var target = $(this);
            var txt = Validation.zen2han(target.val());
            if (txt != target.val()) { target.val(txt); }
            if (txt.length > 0 && txt.match(/^[0-9]+$/) === null) {
                Validation.error('number', target, '入力できない文字があります');
            } else {
                Validation.clearError('number', target);
            }
        });

        // 半角英数字
        Validation.form.find('[validation*=alphanum]').on('change', function (e)
        {
            var target = $(this);
            var txt = Validation.zen2han(target.val());
            if (txt != target.val()) { target.val(txt); }
            if (txt.length > 0 && txt.match(/^[0-9a-zA-Z]+$/) === null) {
                Validation.error('alphanum', target, '入力できない文字があります');
            } else {
                Validation.clearError('alphanum', target);
            }
        });

        // 最低値
        Validation.form.find('[validation*=min]').on('blur', function (e)
        {
            var target = $(this);
            var matched = target.attr('validation').match(/min([0-9]+)/);
            if (matched !== null && parseInt(target.val()) < parseInt(matched[1])) {
                Validation.error('min', target, '入力できない数値です');
            } else {
                Validation.clearError('min', target);
            }
        });

        // 文字数
        Validation.form.find('[validation*=minlen]').on('blur', function (e)
        {
            var target = $(this);
            var matched = target.attr('validation').match(/minlen([0-9]+)/);
            if (target.val().length > 0 && target.val().length < parseInt(matched[1])) {
                Validation.error('minlen', target, '文字数が足りません');
            } else {
                Validation.clearError('minlen', target);
            }
        });

        // メール制限
        Validation.form.find('[validation*=email]').on('blur', function (e)
        {
            var target = $(this);
            var txt = Validation.zen2han(target.val());
            target.val(txt);

            if (txt.length === 0)
                return;

            if (txt.match(/^[a-z0-9\-\._]+@([0-9a-z\-]*\.){1,}[a-z]{1,5}$/i) === null) {
                Validation.error('email', target, '入力内容に誤りがあります');
            } else {
                Validation.clearError('email', target);
            }
        });
    },
    checkDate : function(y, m, d)
    {
        y = parseInt(y);
        m = parseInt(m);
        d = parseInt(d);

        if (m < 1 || m > 12) {
            return false;
        }

        if (Validation.daysOfMonth(m, y) >= d) {
            return true;
        } else {
            return false;
        }
    },
    init: function(form, func)
    {
        Validation.setForm(form);
        Validation.watchError();
        Validation.form.on('submit', function() {
            Validation.validate();
            if (func !== undefined) {
                func();
            }
            if (Validation.existError()) {
                Validation.scrollError();
                return false;
            }
        });
    }
};
