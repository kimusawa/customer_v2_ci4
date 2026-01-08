	function convertDateFormat(date, type)
	{
		type = type || "yyyy/mm/dd";
		
		// いずれ各書式に対応させる予定
		convertDate = "";
		if (type == "yyyy/mm/dd")
		{
			tempY = date.getFullYear();
			tempM = convertNumberDigits(2, date.getMonth()+1);
			tempD = convertNumberDigits(2, date.getDate());
			convertDate = tempY + "/" + tempM + "/" + tempD;
		}
		else if (type == "yyyy/mm")
		{
			tempY = date.getFullYear();
			tempM = convertNumberDigits(2, date.getMonth()+1);
			tempD = convertNumberDigits(2, date.getDate());
			convertDate = tempY + "/" + tempM;
		}
		else
		{
			tempY = date.getFullYear();
			tempM = convertNumberDigits(2, date.getMonth()+1);
			tempD = convertNumberDigits(2, date.getDate());
			convertDate = tempY + "/" + tempM + "/" + tempD;
		}
		return convertDate;
	}
	
	// 桁数をそろえるために0を埋める
	function convertNumberDigits(digit, num)
	{
		var src = new String(num);
		var cnt = digit - src.length;
		if (0 < cnt) {
			while (cnt-- > 0) {
				src = "0" + src;
			}
		}
		return src;
	}

	/**
	 * 年月日に指定された数値を足して返す
	 * ymdDate：日付データ
	 * 追加する項目：年月日のどれか（ymd）
	 * 追加する数値
	 */
	function addDate(ymdDate, target, add)
	{
		addNum = parseFloat(add);
		
		yea = ymdDate.getFullYear();
		mon = ymdDate.getMonth();
		day = ymdDate.getDate();
		
		if(target == "y")
		{
			yea += addNum;
		}
		else if(target == "m")
		{
			mon += addNum;
		}
		else if(target == "d")
		{
			day += addNum;
		}
		return new Date(yea, mon, day);
	}

	/**
	 * カレンダー用に日曜から1日、月末から土曜までを何かで埋め、配列にして返す
	 * year		表示するカレンダーの年
	 * month	表示するカレンダーの月
	 * Fill		日曜から1日まで、月末から土曜まで埋める文字
	 */
	function convertDayOfCalendar(year, month, Fill)
	{
		// 月初と月末
		var startMonth = new Date(year + "/" + month + "/1");
		var endMonth   = addDate(new Date(year, month, 1), "d", -1);
		
		// カレンダー用に日にちを配列化：
		DayArray = new Array();
		
		// 日曜～1日の空白を埋める。
		for (i=0; i<startMonth.getDay(); i++)
		{
			DayArray.push(Fill);
		}
		
		// 月初め（1日）～月末までを埋める
		for (i=0; i<endMonth.getDate(); i++)
		{
			DayArray.push(i+1);
		}
		
		// 月末～土曜の空白を埋める
		if (endMonth.getDay() != 6)
		{
			startWeek = parseFloat(endMonth.getDay()) + 1;
			for (i=startWeek; i<7; i++)
			{
				DayArray.push(Fill);
			}
		}
		return DayArray;
	}

	/**
	 * カレンダー用に週単位で配列にして返す
	 */
	function convertWeekOfCalendar(dayAry)
	{
		ary = new Array();
		for (i=0, j=0, k=0; i<dayAry.length; i++)
		{
			j = parseInt(i/7);
			k = i%7;
			if (k == 0)
			{
				ary[j] = new Array();
			}
			// kが曜日、dayが日にち
			ary[j].push({"num": k, "day": dayAry[i]});
		}
		return ary;
	}

	// カスタムバインディング ===============================================
	// モーダルウィンドウのマスク
	ko.bindingHandlers.koModalMask = {
		update: function(element, valueAccessor, allBindingsAccessor)
		{
			// 現状の値と、サブプロパティ一覧の取得
			var value = valueAccessor(), allBindings = allBindingsAccessor();
			var valueUnwrapped = ko.utils.unwrapObservable(value);
			
			// サブプロパティ：
			var maskBgColor = allBindings.maskBgColor || "#000"; // maskBgColor：マスクの背景色
			var maskOpacity = allBindings.maskOpacity || "0.3";  // maskOpacity：maskOpacityマスクの不透明度
			
			// DOMをいじる
			if (valueUnwrapped == false)
			{
				$(element).hide();
			}
			else
			{
				$(element).css({'width':$(window).width(), 'height':$(document).height()});
				$(element).css({'backgroundColor': maskBgColor});
				$(element).css({'position': 'absolute', 'left': '0', 'top': '0'});
				$(element).css({'z-index': '9000'});
				$(element).fadeTo(0, maskOpacity);
			}
		}
	};
	
	// モーダルウィンドウ
	ko.bindingHandlers.koModalWindow = {
		update: function(element, valueAccessor, allBindingsAccessor)
		{
			// 現状の値と、サブプロパティ一覧の取得
			var value = valueAccessor(), allBindings = allBindingsAccessor();
			var valueUnwrapped = ko.utils.unwrapObservable(value);
			
			// サブプロパティ：modalBgColorの値をセット
			var modalBgColor = allBindings.modalBgColor || "#fff";
			
			// DOMをいじる
			if (valueUnwrapped == false)
			{
				$(element).hide();
			}
			else
			{
				$(element).css({'backgroundColor': modalBgColor});
				$(element).css({'z-index': '9999'});
				$(element).fadeIn(500);
			}
		}
	};

	// knockout.js ===============================================
	function ViewModel() {
		var self = this;

		self.inputData = ko.observable("");

		// モーダルウィンドウで使うヤツ
		self.maskModal = ko.observable(false);		// マスク用
		self.windowModal = ko.observable(false);	// ウィンドウ

		// カレンダーで使うヤツ
		var now = new Date();
		self.year = ko.observable(now.getFullYear());
		self.mont = ko.observable(now.getMonth() + 1);

		// カレンダーの年月の表示
		self.valYearMonth = ko.computed( function()
		{
			return convertDateFormat(new Date(self.year() + "/" + self.mont() + "/1"), "yyyy/mm");
		}, this);

		// カレンダー用に週ごとに配列化
		self.weekData = ko.computed( function()
		{
			// カレンダー用に1日の前、月末の後に適当な文字を入れる。
			dayDate = convertDayOfCalendar(self.year(), self.mont(), "");
			
			// 週単位で配列化
			weekDate = convertWeekOfCalendar(dayDate);
			
			return weekDate;
		}, this);

		// clickバインディング：前月・翌月・今月（[move]を月に足し引きする）
		self.moveCal = function(move)
		{
			move = move || 0;
			if (move == 0)
			{
				// 「0」だったら今月にする
				dateCal = new Date();
			}
			else
			{
				// 「0」じゃないんだったら[move]を月に足し引きする
				dateCal = addDate(new Date(self.year(), self.mont()-1, 1), "m", move)
			}
			
			self.year(dateCal.getFullYear());
			self.mont(dateCal.getMonth()+1);
		}

		// clickバインディング: モーダルウィンドウを開く（今月を表示）
		self.openModal = function(data, event)
		{
			var now = new Date();
			self.year(now.getFullYear());
			self.mont(now.getMonth()+1);
			
			self.maskModal(true);
			self.windowModal(true);
		}

		// clickバインディング: モーダルウィンドウを閉じる
		self.closeModal = function(data, event)
		{
			self.maskModal(false);
			self.windowModal(false);
		}

		// クリックされた日にちを入力：日にちの無いトコがクリックされたら何もしない。
		self.addData = function(koData)
		{
			if (koData.day.toString().match(/^[0-9]+$/) != null)
			{
				ymd = convertDateFormat(new Date(self.year() + "/" + self.mont() + "/" + parseFloat(koData.day)), "yyyy/mm/dd");
				self.inputData(ymd);
				self.maskModal(false);
				self.windowModal(false);
			}
		}

		// 「今日」だけCSS追加
		self.cToday = function(koData)
		{
			// 表示されたカレンダーの日にち：数字以外ならfalse
			ymd = "";
			if (koData.toString().match(/^[0-9]+$/) != null)
			{
				ymd = convertDateFormat(new Date(self.year() + "/" + self.mont() + "/" + parseFloat(koData)), "yyyy/mm/dd");
			}
			else
			{
				return false;
			}
			
			// 今日の年月日
			today = convertDateFormat(new Date(), "yyyy/mm/dd");
			
			// 表示されたカレンダーの日にちと今日の年月日が同じかチェック
			if (ymd == today)
			{
				return true;
			}
			return false;
		}
	}
