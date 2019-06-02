$(function() {
	$.datepicker.regional["ja"] = {
		clearText: "クリア", clearStatus: "日付をクリアします",
		closeText: "閉じる", closeStatus: "変更せずに閉じます",
		prevText: "&#x3c;前", prevStatus: "前月を表示します",
		prevBigText: "&#x3c;&#x3c;", prevBigStatus: "前年を表示します",
		nextText: "次&#x3e;", nextStatus: "翌月を表示します",
		nextBigText: "&#x3e;&#x3e;", nextBigStatus: "翌年を表示します",
		currentText: "今日", currentStatus: "今月を表示します",
		monthNames: ["1月","2月","3月","4月","5月","6月",
		"7月","8月","9月","10月","11月","12月"],
		monthNamesShort: ["1月","2月","3月","4月","5月","6月",
		"7月","8月","9月","10月","11月","12月"],
		monthStatus: "表示する月を変更します", yearStatus: "表示する年を変更します",
		weekHeader: "週", weekStatus: "暦週で第何週目かを表します",
		dayNames: ["日曜日","月曜日","火曜日","水曜日","木曜日","金曜日","土曜日"],
		dayNamesShort: ["日","月","火","水","木","金","土"],
		dayNamesMin: ["日","月","火","水","木","金","土"],
		dayStatus: "週の始まりをDDにします", dateStatus: "Md日(D)",
		dateFormat: "yy-mm-dd", firstDay: 0,
		initStatus: "日付を選択します", isRTL: false,
		showMonthAfterYear: true
	};
	$.datepicker.setDefaults($.datepicker.regional["ja"]);

	$.extend($.validator.messages, {
		required: "必須項目です",
		maxlength: jQuery.format("{0} 文字以下を入力してください"),
		minlength: jQuery.format("{0} 文字以上を入力してください"),
		rangelength: jQuery.format("{0} 文字以上 {1} 文字以下で入力してください"),
		email: "メールアドレスを入力してください",
		url: "URLを入力してください",
		date: "日付を入力してください",
		dateISO: "日付を入力してください(ISO)",
		number: "有効な数字を入力してください",
		digits: "0-9までを入力してください",
		equalTo: "同じ値を入力してください",
		range: jQuery.format(" {0} から {1} までの値を入力してください"),
		max: jQuery.format("{0} 以下の値を入力してください"),
		min: jQuery.format("{0} 以上の値を入力してください"),
		creditcard: "クレジットカード番号を入力してください"
	});
});

var Util = {
    copy: function(text) {
        $('#copy-boad').text(text).show().select();
        document.execCommand('copy');
        $('#copy-boad').hide();
    }
};
