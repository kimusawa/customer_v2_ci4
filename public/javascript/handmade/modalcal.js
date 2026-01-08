	// モーダルウィンドウのマスク
	ko.bindingHandlers.koModalMask = {
		update: function(element, valueAccessor, allBindingsAccessor)
		{
			// 現状の値と、サブプロパティ一覧の取得
			var value = valueAccessor(), allBindings = allBindingsAccessor();
			var valueUnwrapped = ko.utils.unwrapObservable(value);
			
			// サブプロパティ：maskBgColorの値をセット
			var maskBgColor = allBindings.maskBgColor || "#777";
			
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
				$(element).fadeIn(600);
				$(element).fadeTo("fast", 0.8);
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
				$(element).fadeIn(1000);
			}
		}
	};

	$(document).ready(function () {
		ko.applyBindings(new ViewModel());
	});

	function ViewModel() {
		var self = this;

		self.maskModal = ko.observable(false);
		self.windowModal = ko.observable(false);
		
		// 開く
		self.openModal = function()
		{
			self.maskModal(true);
			self.windowModal(true);
		}
		
		// 閉じる
		self.closeModal = function()
		{
			self.maskModal(false);
			self.windowModal(false);
		}
	}

