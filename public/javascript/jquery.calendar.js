/* =============================================================================
	jQuery Calendar ver2.0.0
	Copyright(c) 2015, ShanaBrian
	Dual licensed under the MIT and GPL licenses.
============================================================================= */
(function($) {
	$.fn.calendar = function(options) {
		if ($(this).length > 0) {
			if ($(this).length > 1) {
				$.each(this, function() {
					$(this).calendar(options);
				});
				return this;
			}

			var $element     = this,
				$calendar    = null,
				settings     = {},
				calendarData = {};

			// 初期化
			var init = function() {
				/*
					year            : 表示する年
					month           : 表示する月
					weekValue       : 曜日
					changeButton    : 切り替えボタンを表示するかどうか [ true | false ]
					areaId          : カレンダーのID名
					areaClass       : カレンダーのクラス名
					activeDateClass : 現在表示している年月のヘッダークラス名
					prevNavClass    : 前の月へ移動する切り替えボタンのクラス名
					nextNavClass    : 次の月へ移動する切り替えボタンのクラス名
					sunClass        : 日曜日のクラス名
					satClass        : 土曜日のクラス名
					todayClass      : 現在の日付のクラス名
					emptyClass      : 1日・末日の前後に表示する余白セルのクラス名
					headerFormat    : 現在表示している年月のヘッダーの文字列書式
					prevValue       : 前の月へ移動する切り替えボタンの文字列
					nextValue       : 次の月へ移動する切り替えボタンの文字列
					emptyValue      : 1日・末日の前後に表示する余白セルの文字列
					outputMode      : カレンダーの出力先 // [ append | prepend | html | after | before ]
					changeCallback  : 切り替え時のコールバック関数
				*/
				settings = $.extend({
					year            : 0,
					month           : 0,
					weekValue       : ['日', '月', '火', '水', '木', '金', '土'],
					changeButton    : true,
					areaId          : '',
					areaClass       : 'calendar',
					activeDateClass : 'active-date',
					prevNavClass    : 'prev',
					nextNavClass    : 'next',
					sunClass        : 'sun',
					satClass        : 'sat',
					todayClass      : 'today',
					emptyClass      : '',
					headerFormat    : 'y年m月',
					prevValue       : '&lt;&lt;',
					nextValue       : '&gt;&gt;',
					emptyValue      : '&nbsp;',
					outputMode      : 'append',
					changeCallback  : function() {}
				}, options);

				var toDate = geToDate();

				calendarData = {
					year   : (settings.year) ? settings.year : toDate.year,
					month  : (settings.month) ? settings.month : toDate.month,
					toDate : toDate
				};

				setup();
			};

			// セットアップ
			var setup = function() {
				createBaseHTML();
				setHeader();
				createBody();

				$calendar.find('thead td a').on('click', function() {
					var mode = $(this).attr('href').replace(/^#/, '');

					if (mode === 'prev') {
						$element.prevMonth(settings.changeCallback);
					} else if (mode === 'next') {
						$element.nextMonth(settings.changeCallback);
					}

					return false;
				});

				if (settings.outputMode === 'append') {
					$element.append($calendar);
				} else if (settings.outputMode === 'prepend') {
					$element.prepend($calendar);
				} else if (settings.outputMode === 'html') {
					$element.html($calendar);
				} else if (settings.outputMode === 'after') {
					$element.after($calendar);
				} else if (settings.outputMode === 'before') {
					$element.before($calendar);
				}
			};

			// ゼロ埋め
			var zeroFormat = function(v, n) {
				var vl = String(v).length;
				if(n > vl) {
					return (new Array((n - vl) + 1).join(0)) + v;
				} else {
					return v;
				}
			};

			// 曜日のクラス名設定
			var weekClass = function(weekNumber, targetDate) {
				var toDate   = geToDate(),
					classArr = [],
					classStr = '';

				if (weekNumber === 0) {
					classArr.push(settings.sunClass);
				} else if (weekNumber === 6) {
					classArr.push(settings.satClass);
				}
				if (targetDate && targetDate === '' + toDate.year + zeroFormat(toDate.month, 2) + zeroFormat(toDate.days, 2)) {
					classArr.push(settings.todayClass);
				}
				if (classArr.length > 0) {
					classStr = ' class="' + classArr.join(' ') + '"';
				}
				return classStr;
			};

			// 基本要素の生成
			var createBaseHTML = function() {
				var htmlStr = '';

				htmlStr += '\n<table>\n';
				htmlStr += '<thead>\n';
				htmlStr += '<tr>\n';
				if (settings.changeButton === true) {
					htmlStr += '<td colspan="2"><a href="#prev">' + settings.prevValue + '</a></td>\n';
					htmlStr += '<th colspan="3"></th>\n';
					htmlStr += '<td colspan="2"><a href="#next">' + settings.nextValue + '</a></td>\n';
				} else {
					htmlStr += '<th colspan="7"></th>\n';
				}
				htmlStr += '</tr>\n';
				htmlStr += '</thead>\n';
				htmlStr += '<tbody>\n';
				htmlStr += '</tbody>\n';
				htmlStr += '</table>\n';

				$calendar = $(htmlStr);

				$calendar.addClass(settings.areaClass);
				if (settings.areaId) {
					$calendar.attr('id', settings.areaId);
				}

				$calendar.find('thead th').addClass(settings.activeDateClass);
				$calendar.find('thead td').eq(0).addClass(settings.prevNavClass);
				$calendar.find('thead td').eq(1).addClass(settings.nextNavClass);
			};

			// 日付要素の生成
			var createBody = function() {
				var year      = calendarData.year,
					month     = calendarData.month,
					lastdays  = new Date(year, month, 0),
					forDate   = new Date(year, month - 1, 1),
					emptyCell = '<td'+ (settings.emptyClass ? ' class="' + settings.emptyClass + '"' : '') + '>' + settings.emptyValue + '</td>\n',
					rowCount  = 1,
					htmlStr   = '',
					i,
					j,
					len;

				htmlStr= '<tr>\n';

				// 曜日
				$.each(settings.weekValue, function(i, value) {
					htmlStr += '<th' + weekClass(i) + '>' + value + '</th>\n';
				});

				htmlStr += '</tr>\n';
				htmlStr += '<tr>\n';

				// 1日前余白作成
				if (forDate.getDay() > 0) {
					for (j = 0; j < forDate.getDay(); j++) {
						htmlStr += emptyCell;
					}
				}

				// 日付
				for (i = 1, len = lastdays.getDate(); i <= len; i++) {
					forDate = new Date(year, month - 1, i);
					htmlStr += '<td' + weekClass(forDate.getDay(), '' + year + zeroFormat(month, 2) + zeroFormat(i, 2)) + '>' + i + '</td>\n';

					if (forDate.getDay() === 6 && i != lastdays.getDate()) {
						htmlStr += '</tr>\n';
						htmlStr += '<tr>\n';
						rowCount++;
					}
				}

				// 末日後余白作成
				if (forDate.getDay() < 6) {
					for (j = 0; j < (6 - forDate.getDay()); j++) {
						htmlStr += emptyCell;
					}
				}

				htmlStr += '</tr>\n';

				// 1行余白作成
				if (rowCount < 6) {
					for (i = 0, len = (6 - rowCount); i < len; i++) {
						htmlStr += '<tr>\n';
						for (j = 0; j < 7; j++) {
							htmlStr += emptyCell;
						}
						htmlStr += '</tr>\n';
					}
				}

				$calendar.find('tbody').html(htmlStr);
			};

			// 現在の日付を取得
			var geToDate = function() {
				var toDate  = new Date(),
					toYear  = toDate.getFullYear(),
					toMonth = toDate.getMonth() + 1,
					toDays  = toDate.getDate(),
					result  = {};

				result = {
					year  : toYear,
					month : toMonth,
					days  : toDays
				};

				return result;
			};

			// 前の年月を取得
			var getPrevDate = function() {
				var result = {
						year  : calendarData.year,
						month : calendarData.month
					};

				if (result.month === 1) {
					result.year--;
					result.month = 12;
				} else {
					result.month--;
				}

				return result;
			};

			// 次の年月を取得
			var getNextDate = function() {
				var result = {
						year  : calendarData.year,
						month : calendarData.month
					};

				if (result.month === 12) {
					result.year++;
					result.month = 1;
				} else {
					result.month++;
				}

				return result;
			};

			// ヘッダー設定
			var setHeader = function() {
				var headerText = settings.headerFormat.replace('y', calendarData.year).replace('m', calendarData.month);
				$calendar.find('thead th').html(headerText);
			};

			// カレンダーの切り替え（メソッド）
			$element.changeCalendar = function(year, month, callback) {
				var date = getPrevDate();

				if (year && month && String(year).match(/^[0-9]{4}$/) && String(month).match(/^[0-1]?[0-9]$/)) {
					calendarData.year  = Number(year);
					calendarData.month = Number(month);

					setHeader();
					createBody();

					if (callback && typeof callback === 'function') {
						callback(year, month);
					}
				}

				return $element;
			};

			// 前の月へ移動（メソッド）
			$element.prevMonth = function(callback) {
				var date = getPrevDate();

				if (typeof callback !== 'function') {
					callback = function() {};
				}

				$element.changeCalendar(date.year, date.month, callback);

				return $element;
			};

			// 次の月へ移動（メソッド）
			$element.nextMonth = function(callback) {
				var date = getNextDate();

				if (typeof callback !== 'function') {
					callback = function() {};
				}

				$element.changeCalendar(date.year, date.month, callback);

				return $element;
			};

			init();
		}

		return this;
	};
})(jQuery);
