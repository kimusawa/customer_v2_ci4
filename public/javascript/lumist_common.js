		function ymd_check00(id,pflg){
			//	引数
			//	id 		:コントロールID
			//	pflg	:1.入力必須　2：空白OK
			var checknum ;
			checknum = ymd_check(id,pflg);
			if(checknum != 0){
				if( checknum=="OK") return true;
				var y = checknum.substring(0, 4);
				var m = checknum.substring(4, 6);
				var d = checknum.substring(6);

				$(id).val(y+'/'+m+'/'+d);
			}else{
				return false ;
			}
		}
		function ymd_check(id,pflg){
			//	引数
			//	id 		:コントロールID
			//	pflg	:1.入力必須　2：空白OK
			var checknum = $(id).val();
			// 「/」を省く
			checknum=checknum.replace(/[^0-9]/g, "");

			if(pflg==2){
				if((checknum-0)==0 || checknum=="" || (checknum-0)==99999999) return "OK";
			}
			// 右から６ケタをセットする。
			var checknum6 = new String(checknum.slice(-6));
			// ２ケタづつ年月日を分解する。
			var syy = checknum6.substring(0, 2);
			var smm = checknum6.substring(2, 4);
			var sdd = checknum6.substring(4);

			/////////////////////////////////////////////////////////////////////////////////
			////		サファリのバグ（？）　「07」「08」の時parseIntが正しく動作しない。
			/////////////////////////////////////////////////////////////////////////////////
			//if(syy=="08") syy=8;
			//if(syy=="09") syy=9;
			//if(smm=="08") smm=8;
			//if(smm=="09") smm=9;
			//if(sdd=="08") sdd=8;
			//if(sdd=="09") sdd=9;
			/////////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////////////////
			// 年を４ケタにする
			if((syy-0)>70)	syy=(syy-0) + 1900;
			else			syy=(syy-0) + 2000;
			//alert("syy1= " + syy);
			//alert("smm1= " + smm);
			//alert("sdd1= " + sdd);
			//alert("parseInt(sdd1)= " + parseInt(sdd));
			//alert("parseInt(smm1)= " + parseInt(smm));
			if((smm-0)>12){
				//alert("if01");
				smm  = 0;
			}
			if((smm-0)==0){
				//alert("if02");
				sdd  = 0;
			}
			if((sdd-0)>31){
				//alert("if03");
				sdd  = 0;
			}
			if((sdd-0)==0){
				//alert("if04");
				syy  = 0;
				syy  = 0;
			}
			if ((smm-0) < 10) {smm = "0" + (smm-0);}
			if ((sdd-0) < 10) {sdd = "0" + (sdd-0);}
			//alert("smm2= " + smm);
			//alert("sdd2= " + sdd);
			var y = syy;
			var m = smm;
			var d = sdd;
			checknum = y + m + d;
			//alert("checknum=" + checknum);
			//alert(m);
			//alert(d);
			//入力日付チェック
			var di = new Date(y, m - 1, d);
			if (!(di.getFullYear() == y && di.getMonth() == m - 1 && di.getDate() == d) || !(checknum.length == 8)) {
				alert('正しい日付を入力してください。');
				//check_dsp('正しい日付を入力してください。');
				return 0;
			}
			// 整合化された日付文字列を返す。
			//alert(checknum);
			return checknum;
		}
		function ym_check(id,pflg){
			//	引数
			//	id 		:コントロールID
			//	pflg	:1.入力必須　2：空白OK
			var checknum = $(id).val();
			// 「/」を省く
			checknum=checknum.replace(/[^0-9]/g, "");

			if(pflg==2){
				if((checknum-0)==0 || checknum=="") return "OK";
			}
			// 右から６ケタをセットする。
			var checknum6 = new String(checknum.slice(-4));
			// ２ケタづつ年月日を分解する。
			var syy = checknum6.substring(0, 2);
			var smm = checknum6.substring(2, 4);
			// 年を４ケタにする
			if((syy-0)>70)	syy=(syy-0) + 1900;
			else			syy=(syy-0) + 2000;
			//alert("syy= " + syy);
			//alert("smm1= " + smm);
			//alert("sdd1= " + sdd);
			//alert("parseInt(sdd1)= " + parseInt(sdd));
			//alert("parseInt(smm1)= " + parseInt(smm));
			if((smm-0)>12){
				//alert("if01");
				smm  = 0;
			}
			if ((smm-0) < 10) {smm = "0" + (smm-0);}
			//alert("smm2= " + smm);
			//alert("sdd2= " + sdd);
			var y = syy;
			var m = smm;
			checknum = y + m;
			//alert("checknum=" + checknum);
			//alert(m);
			//alert(d);
			//入力日付チェック
			var di = new Date(y, m - 1);
			if (!(di.getFullYear() == y && di.getMonth() == m - 1 ) || !(checknum.length == 6)) {
				alert('正しい日付を入力してください。');
				//check_dsp('正しい日付を入力してください。');
				return 0;
			}
			// 整合化された日付文字列を返す。
			//alert(checknum);
			return checknum;
		}
		// 日付の空白NG 入力必須　///////////////////////////
		function date_check1(id){
			var checknum;
			checknum = ymd_check(id,1);
			if(checknum!=0){
				if(checknum=="OK") return true;
				var y = checknum.substring(0, 4);
				var m = checknum.substring(4, 6);
				var d = checknum.substring(6);
				$(id).val(y+'/'+m+'/'+d);
			}else{
				return false;
			}
		}
		// 日付の空白OK ///////////////////////////////////
		function date_check2(id){
			var checknum;
			checknum = ymd_check(id,2);
			if(checknum!=0){
				if(checknum=="OK") return true;
				var y = checknum.substring(0, 4);
				var m = checknum.substring(4, 6);
				var d = checknum.substring(6);
				$(id).val(y+'/'+m+'/'+d);
			}else{
				return false;
			}
		}
		// 日付の範囲チェック /////////////////////////////
		function fromto_date_check(fromid,toid){
			var fromdate = $(fromid).val();
			var todate = $(toid).val();
			if(fromdate==''){
				$(fromid).val('0000/00/00');
				fromdate = '0000/00/00';
			}
			if(todate==''){
				$(toid).val('9999/99/99');
				todate = '9999/99/99';
			}
			// 「/」を省く
			fromdate=fromdate.replace(/[^0-9]/g, "");
			todate=todate.replace(/[^0-9]/g, "");

			if ( (fromdate-0) > (todate-0) ){
				$(toid).focus();
				alert('終了日を開始日より過去に指定できません。');
				return false;
			}
		}
		// 年月の空白NG 入力必須　///////////////////////////
		function ym_check1(id){
			var checknum;
			checknum = ym_check(id,1);
			if(checknum!=0){
				if(checknum=="OK") return true;
				var y = checknum.substring(0, 4);
				var m = checknum.substring(4, 6);
				$(id).val(y+'/'+m);
			}else{
				return false;
			}
		}
		// コード指定チェック ※引数はそれぞれのid
		function fromto_data_check(id1,id2,keta){
			var checknum1 = $(id1).val();
			var checknum2 = $(id2).val();
			if((checknum1-0)==0){
				checknum1=zeropadding("0",keta,"0");
				$(id1).val(checknum1);
			}
			if((checknum2-0)==0){
				checknum2=zeropadding("9",keta,"9");
				$(id2).val(checknum2);
			}
			if((checknum1-0)>(checknum2-0)){
				alert('開始の値が終了より大きいです。確認して下さい。');
				$(id2).focus();
				return false;
			}
			return true;
		}
		function time_check(id,pflg){
			//	引数
			//	id 		:コントロールID
			//	pflg	:1.入力必須　2：空白OK
			var checknum = $(id).val();
			// 「:」を省く
			checknum=checknum.replace(/:/g, "");
			//alert(checknum);
			if(pflg==2){
				if((checknum-0)==0 || checknum=="" || (checknum-0)==999999) return "OK";
			}
			//alert('ここくる');
			// 右から６ケタをセットする。
			//var checknum6 = new String(checknum.slice(-6));
			// ２ケタづつ年月日を分解する。
			var h = checknum.substring(0, 2);
		//	var m = checknum.substring(2, 4);
			var m = checknum.substring(2, 2);
			var s = checknum.substring(4);

			// 年を４ケタにする
			//if((shh-0)>23)	return 0;
			//if((smm-0)>59)	return 0;
			//if((sss-0)>59)	return 0;
			//var h = shh;
			//var m = smm;
			//var s = sss;
			//alert("checknum=" + checknum);
			//alert('h' +h);
			//alert('m' +m);
			//alert('s' +s);
			//入力日付チェック
			var tt=new Date('1970',0,1,h,m,s);
			if(!(tt.getHours()==h && tt.getMinutes()==m && tt.getSeconds()==s)){
				alert('正しい時間を入力してください。');
				//check_dsp('正しい日付を入力してください。');
				return 0;
			}
			checknum=zeropadding(tt.getHours()-0 ,2,"0")+zeropadding(tt.getMinutes()-0 ,2,"0")+zeropadding(tt.getSeconds()-0 ,2,"0");
			checknum=zeropadding(h-0 ,2,"0")+zeropadding(m-0 ,2,"0")+zeropadding(s-0 ,2,"0");
			alert('checknum=' +checknum);
			// 整合化された日付文字列を返す。
			//alert(checknum);
			return checknum;
		}
		// 時間の空白OK ///////////////////////////////////
		function time_check2(id){
			var checknum=0;
			//alert('aaa');	
			checknum = time_check(id,2);
			if(checknum!=0){
				if(checknum=="OK") return true;
				var h = checknum.substring(0, 2);
				var m = checknum.substring(2, 4);
				var s = checknum.substring(4);
				$(id).val(h+':'+m+':'+s);
			}else{
				return false;
			}
		}
		///////////////////////////////////////////////////////////////
		//数字以外の入力された文字のみ削除
		///////////////////////////////////////////////////////////////
		function num_comp(inStr){
			var strMatch = inStr.match(/[0-9]/g);
		    var rtnMatch = "";
		    try{
		        for (i=0; i < strMatch.length; i++){
		             rtnMatch = rtnMatch + strMatch[i];
		        }
		    } catch (e) {}
		    return rtnMatch;
		}

		// 桁数を求める関数	///////////////////////////////
		function countLength(str) {
			var r = 0;
			for (var i = 0; i < str.length; i++) {
				var c = str.charCodeAt(i);
				// Shift_JIS: 0x0 ～ 0x80, 0xa0 , 0xa1 ～ 0xdf , 0xfd ～ 0xff
				// Unicode : 0x0 ～ 0x80, 0xf8f0, 0xff61 ～ 0xff9f, 0xf8f1 ～ 0xf8f3
				if ( (c >= 0x0 && c < 0x81) || (c == 0xf8f0) || (c >= 0xff61 && c < 0xffa0) || (c >= 0xf8f1 && c < 0xf8f4)) {
					r += 1;
				} else {
					r += 2;
				}
			}
			return r;
		}
		String.prototype.bytes = function () {
			var length = 0;
			for (var i = 0; i < this.length; i++) {
			  var c = this.charCodeAt(i);
			  if ((c >= 0x0 && c < 0x81) || (c === 0xf8f0) || (c >= 0xff61 && c < 0xffa0) || (c >= 0xf8f1 && c < 0xf8f4)) {
				length += 1;
			  } else {
				length += 2;
			  }
			}
			return length;
		  };

		// ▼指定桁になるまで、先頭に指定文字を加える
		function zeropadding( targetNum, setFigure, setChar ) {
		   var targetFigure = targetNum.length; // 対象の現在の桁数を得る
		   var addZeros = "";
		   // 先頭に加える文字列(0など)を作成
		   for( var i=0 ; i < (setFigure - targetFigure) ; i++ ) {
		      addZeros += setChar;
		   }
		   // 文字列を合成して返す
		   return (addZeros + targetNum);
		}
		///////////////////////////////////////////////////////////////
		//　端数丸め処理
		///////////////////////////////////////////////////////////////
		function marume_func(hasuu,keta,kin){
			var wkkin;
			var wktemp;
			//alert("hasuu=" + hasuu);
			//alert("keta=" + keta);
			//alert("kin=" + eval(kin));
			if((kin-0)==0) return 0;
			// 絶対値を返す。
			wktemp=Math.abs(kin);

			baisuu=10;
			if(hasuu==9)
			{
				wkkin=(Math.floor((wktemp-0))*10)/(baisuu-0);
				if((kin-0)<0){
					wkkin=(wkkin-0) * -1;
					//alert("wkkin=" + wkkin);
				}
				return wkkin;
			}
			ketaage=(9-(hasuu-0))/10;
			if((hasuu-0)==10){
				ketaage=0.9;
			}
			wkkin=Math.floor((wktemp-0)+(ketaage-0)) * 10 / (baisuu-0);
			if((kin-0) < 0) {
				wkkin=(wkkin-0) * -1;
				//alert("wkkin=" + wkkin);
			}
			return wkkin;
		}

		///////////////////////////////////////////////////////////////
		// * 3桁のカンマ区切りの値をセット
		// *
		// * @param target
		///////////////////////////////////////////////////////////////
		function set_comma3(target) {
		    var value = $(target).val();
		    value = get_comma3(value);
		    $(target).val(value);
		}
		///////////////////////////////////////////////////////////////
		// * 3桁のカンマ区切りの値を取得
		// *
		// * @param value
		// * @returns
		///////////////////////////////////////////////////////////////
		function get_comma3(value) {
		    // カンマとスペースを除去（入力ミスを考慮）
		    var value = get_comma3_deleted(value);

		    // カンマ区切り
		    while (value != (value = value.replace(/^(-?\d+)(\d{3})/, "$1,$2")));

		    // 数値以外の場合は 0
		    if (isNaN(parseInt(value))) {
		        value = "0";
		    }

		    return value;
		}

		///////////////////////////////////////////////////////////////
		// * 3桁のカンマ区切りを除外した値をセット
		// *
		// * @param target
		///////////////////////////////////////////////////////////////
		function set_comma3_deleted(target) {
		    var value = $(target).val();
		    value = get_comma3_deleted(value);
		    $(target).val(value);
		}

		///////////////////////////////////////////////////////////////
		// * 3桁のカンマ区切りを除外した値を取得
		// *
		// * @param value
		// * @returns
		///////////////////////////////////////////////////////////////
		function get_comma3_deleted(value) {
		    // 正規表現で扱うために文字列に変換
		    value = "" + value;
		    // スペースとカンマを削除
		    return value.replace(/^\s+|\s+$|,/g, "");
		}
		///////////////////////////////////////////////////////////////
		// コード（数値）チェック（必須・数字・桁数）
		//		id:フィールドID
		//		keta:桁数
		//		koumoku:日本語フィールド名
		//		hissu:必須項目かどうか　YESは必須、それ以外は任意
		///////////////////////////////////////////////////////////////
		function cd_check(id,keta,koumoku,hissu){
			// テキストの未入力
			var checknum = $(id).val();
			if(hissu=='YES'){
				if (!checknum.match(/\S/g)){
					alert(koumoku + "は必須です。");
					return false;
				}
			}
			//alert('1');
			if(checknum==0){
				alert(koumoku + "にゼロは受け付けられません。");
				return false;
			}
			//alert('2');
			if(checknum!=''){
				// 数字以外の入力
				if(!checknum.match(/^-?[0-9]+$/)){
					alert(koumoku + "には数字を入力して下さい。");
					return false;
				}
				//alert('3');
				var thisValueLength = countLength($(id).val());
				if (thisValueLength > keta){
					alert(koumoku + 'が桁数オーバーです。' + keta + '桁以内で指定して下さい。');
					return false;
				}
				//alert('4');
				//符号無し整数を判定（マイナスは不可）
				if(!checknum.match(/^[0-9]+$/)){
					//CheckNumが符号の付いている整数の場合の処理
					alert(koumoku + "にマイナスは入力できません。");
					return false;
				}
				//alert('5');
			}
			var code_format = zeropadding(checknum,keta,"0");
			$(id).val(code_format);
			//$(id).val(checknum);
			//alert('6');
		}
		///////////////////////////////////////////////////////////////
		// コード（数値）チェック（必須・数字・桁数） ゼロ・空白はOKとする
		//		id:フィールドID
		//		keta:桁数
		//		koumoku:日本語フィールド名
		//		hissu:必須項目かどうか　YESは必須、それ以外は任意
		///////////////////////////////////////////////////////////////
		function cd_check_zerook(id,keta,koumoku){
			// テキストの未入力
			var checknum = $(id).val();
			if((checknum-0)==0) return true;
			//alert('2');
			if(checknum!=''){
				// 数字以外の入力
				if(!checknum.match(/^-?[0-9]+$/)){
					alert(koumoku + "には数字を入力して下さい。");
					return false;
				}
				//alert('3');
				var thisValueLength = countLength($(id).val());
				if (thisValueLength > keta){
					alert(koumoku + 'が桁数オーバーです。' + keta + '桁以内で指定して下さい。');
					return false;
				}
				//alert('4');
				//符号無し整数を判定（マイナスは不可）
				if(!checknum.match(/^[0-9]+$/)){
					//CheckNumが符号の付いている整数の場合の処理
					alert(koumoku + "にマイナスは入力できません。");
					return false;
				}
				//alert('5');
			}
			var code_format = zeropadding(checknum,keta,"0");
			$(id).val(code_format);
			//$(id).val(checknum);
			//alert('6');
		}
		///////////////////////////////////////////////////////////////
		// 文字列チェック（必須・桁数）
		///////////////////////////////////////////////////////////////
		function str_check(id,keta,koumoku,hissu){
			//alert('aaa');
			// テキストの未入力
			var checknum = $(id).val();
			//alert(checknum);
			if(hissu=='YES'){
				if (!checknum.match(/\S/g)){
					alert(koumoku + "は必須です。");
					return false;
				}
			}
			//var thisValueLength = countLength($(id).val());
			var thisValueLength = ($(id).val()).bytes();
			//alert(($(id).val()).bytes());
			if (thisValueLength > keta){
				alert(koumoku + 'が桁数オーバーです。' + keta + '桁以内で指定して下さい。');
				return false;
			}
		}
		////////////////////////////////////////////////////////////////////
		// 数値チェック（数値・全体桁数・フィールド名・上限値・下限値・小数点桁数）
		////////////////////////////////////////////////////////////////////
		function numeric_check_new(checknum,ketaall,koumoku,max,min,ketashou){
			// テキストの未入力
			checknum=get_comma3_deleted(checknum);
			//alert('①');
			if (!checknum.match(/\S/g)){
				alert(koumoku + "は必須です。");
				return false;
			}
			//alert('②');
			// 数字以外の入力
			if(ketashou!=0){
				// 数字以外の入力
				if(!checknum.match(/^[-]?([1-9]\d*|0)(\.\d+)?$/)){
					alert(koumoku + "には数字を入力して下さい。");
					return false;
				}

			}else{
				if(!checknum.match(/^-?[0-9]+$/)){
					alert(koumoku + "には数字を入力して下さい。");
					return false;
				}
			}
			//alert('③');
			// 桁数
			var thisValueLength = countLength(checknum);
			if (thisValueLength > ketaall){
				alert(koumoku + 'が桁数オーバーです。' + ketaall + '桁以内で指定して下さい。');
				return false;
			}
			//alert('④');
			if(checknum>max){
				alert(koumoku + "は" + max + "を超えて指定できません。");
				return false;
			}
			//alert('⑤');
			if(checknum<min){
				alert(koumoku + "は" + min + "未満は指定できません。");
				return false;
			}
			//alert("チェックから出る時" + checknum);
			return true;
		}
		////////////////////////////////////////////////////////////////////
		// 数値チェック（必須・数字・桁数・上限値・下限値）
		////////////////////////////////////////////////////////////////////
		function numeric_check(id,keta,koumoku,max,min){
			// テキストの未入力
			set_comma3_deleted(id);
			var checknum = $(id).val();
			//checknum= checknum.replace('','0');
			//var checknum = $(id).val();
			if (!checknum.match(/\S/g)){
				alert(koumoku + "は必須です。");
				return false;
			}
			// 数字以外の入力
			if(!checknum.match(/^-?[0-9]+$/)){
				alert(koumoku + "には数字を入力して下さい。");
				return false;
			}
			// 桁数
			var thisValueLength = countLength($(id).val());
			if (thisValueLength > keta){
				alert(koumoku + 'が桁数オーバーです。' + keta + '桁以内で指定して下さい。');
				return false;
			}
			if((checknum-0)>max){
				alert(koumoku + "は" + max + "を超えて指定できません。");
				return false;
			}
			if((checknum-0)<min){
				alert(koumoku + "は" + min + "未満は指定できません。");
				return false;
			}
			return true;
		}
		////////////////////////////////////////////////////////////////////
		// 数値チェック※小数点あり（必須・数字・桁数・上限値・下限値）
		////////////////////////////////////////////////////////////////////
		function numeric_check2(id,keta,koumoku,max,min){
			// テキストの未入力
			set_comma3_deleted(id);
			var checknum = $(id).val();
			//checknum=parseFloat(checknum).toFixed(1);
			if (!checknum.match(/\S/g)){
				alert(koumoku + "は必須です。");
				return false;
			}
			// 数字以外の入力
			if(!checknum.match(/^[-]?([1-9]\d*|0)(\.\d+)?$/)){
				alert(koumoku + "には数字を入力して下さい。");
				return false;
			}
			// 上で数字チェックをした後で、小数点を切り出し、9より上（2桁以上）の時はチェックすうｒ
			// 小数点がない時は「undefined」になるため下のif文には影響なし
			var decPart = String(checknum).split(".")[1];
			if(decPart>9){
				alert(koumoku + "の小数点は1桁で入力して下さい。");
				return false;
			}
			// 桁数
			var thisValueLength = countLength($(id).val());
			if (thisValueLength > keta){
				alert(koumoku + 'が桁数オーバーです。' + keta + '桁以内で指定して下さい。');
				return false;
			}
			if((checknum-0)>max){
				alert(koumoku + "は" + max + "を超えて指定できません。");
				return false;
			}
			if((checknum-0)<min){
				alert(koumoku + "は" + min + "を超えて指定できません。");
				return false;
			}
		}
		////////////////////////////////////////////////////////////////////
		// 数値チェック（必須・数字・桁数・上限値・下限値）
		////////////////////////////////////////////////////////////////////
		function numeric_check_zerook(id,keta,koumoku,max,min){
			// テキストの未入力
			set_comma3_deleted(id);
			var checknum = $(id).val();
			if((checknum-0)==0) return true;
			//checknum= checknum.replace('','0');
			//var checknum = $(id).val();
			if (!checknum.match(/\S/g)){
				alert(koumoku + "は必須です。");
				return false;
			}
			// 数字以外の入力
			if(!checknum.match(/^-?[0-9]+$/)){
				alert(koumoku + "には数字を入力して下さい。");
				return false;
			}
			// 桁数
			var thisValueLength = countLength($(id).val());
			if (thisValueLength > keta){
				alert(koumoku + 'が桁数オーバーです。' + keta + '桁以内で指定して下さい。');
				return false;
			}
			if((checknum-0)>max){
				alert(koumoku + "は" + max + "を超えて指定できません。");
				return false;
			}
			if((checknum-0)<min){
				alert(koumoku + "は" + min + "未満は指定できません。");
				return false;
			}
			return true;
		}
		////////////////////////////////////////////////////////////////////
		// 数値チェック※小数点あり（必須・数字・桁数・上限値・下限値）
		////////////////////////////////////////////////////////////////////
		function numeric_check2_zerook(id,keta,koumoku,max,min){
			// テキストの未入力
			set_comma3_deleted(id);
			var checknum = $(id).val();
			if((checknum-0)==0) return true;
			//checknum=parseFloat(checknum).toFixed(1);
			if (!checknum.match(/\S/g)){
				alert(koumoku + "は必須です。");
				return false;
			}
			// 数字以外の入力
			if(!checknum.match(/^[-]?([1-9]\d*|0)(\.\d+)?$/)){
				alert(koumoku + "には数字を入力して下さい。");
				return false;
			}
			// 上で数字チェックをした後で、小数点を切り出し、9より上（2桁以上）の時はチェックすうｒ
			// 小数点がない時は「undefined」になるため下のif文には影響なし
			var decPart = String(checknum).split(".")[1];
			if(decPart>9){
				alert(koumoku + "の小数点は1桁で入力して下さい。");
				return false;
			}
			// 桁数
			var thisValueLength = countLength($(id).val());
			if (thisValueLength > keta){
				alert(koumoku + 'が桁数オーバーです。' + keta + '桁以内で指定して下さい。');
				return false;
			}
			if((checknum-0)>max){
				alert(koumoku + "は" + max + "を超えて指定できません。");
				return false;
			}
			if((checknum-0)<min){
				alert(koumoku + "は" + min + "を超えて指定できません。");
				return false;
			}
		}
		////////////////////////////////////////////////////////////////////////////
		//	json参照による指定項目の内容表示
		////////////////////////////////////////////////////////////////////////////
		function naiyou_dsp(target, sanshou_id, dsp_id, agency, base_url){
			// target = 対象とする項目の種類（コントローラのsearch_json参照
			// sanshou_id = 参照する入力欄のid
			// dsp_id = 表示先タグのid（spanタグ）
			//alert(agency);
			//alert(base_url);
			if((agency-0)==0 || agency===void 0){
				alert("エージェンシーコードが設定されていません。");
			}
			if(base_url=='' || base_url===void 0){
				alert("「base_url」が設定されていません。");
			}
			var sanshou_idx = $("#"+sanshou_id).val();	// 入力欄からコードをもってくる
			//alert('target=' + target);
			//alert('agency=' + agency);
			//alert('sanshou_idx=' + sanshou_idx);
			// search_json/対象項目/エージェンシーコード/該当コード
			// 下の記述ではエージェンシーコードをformから参照するので、これも引数で持ってくる仕様に変えた方が良いかも
			var jsonUrl = base_url+'lumist/search_json/' + target + '/' + agency + '/' + sanshou_idx;
			//alert(jsonUrl);
			// $.ajaxでリクエスト（通信結果をキャッシュさせない必要があるため
			// 表示枠用class
			if($('#'+dsp_id).hasClass('naiyou_label')==false){
				$('#'+dsp_id).addClass('naiyou_label');
			}
			var usercd_idx = '';
			if(target=='usr'){
				if(sanshou_id != 'newusercd'){
					usercd_idx = sanshou_id.slice(6);
				}
			}
			$.ajax( {
				type: 'GET',
				scriptCharset: 'utf-8',
				dataType:'json',
				url: jsonUrl,
				cache: false,	// キャッシュを使わない
				success: function(json) {
					//alert("dsp_id=" +dsp_id + ' ' + json[0].name);
					$('#'+dsp_id).html(json[0].name);
					/* ルミスト仕様は必要なし */
					/*
					if(target=='usr'){
						$('#keiyaku'+usercd_idx).val(json[0].keikbn);
						$('#keitanka'+usercd_idx).val(json[0].keitanka);
						$('#toktanka'+usercd_idx).val(json[0].toktanka);
						//alert('keiyaku='+$('#keiyaku'+usercd_idx).val());
						//alert('keitanka='+$('#keitanka'+usercd_idx).val());
						//alert('toktanka='+$('#toktanka'+usercd_idx).val());
					}
					*/
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : 'normal'
					});

					// 2020/05/30 enoki この処理は何をやってる？？？
					// OKの時order_dispを呼び出し
					//if(target=='shain' && sanshou_id=='haitancd'){
					//	$('#order_disp').click();
					//}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					// 「該当なし」と「その他エラー」（通信事情などによる失敗）を分ける
					if(textStatus=="parsererror"){
						$('#'+dsp_id).html("該当なし");
						$('#'+dsp_id).css({
							'border-color' : '#f99',
							'color' : '#f00',
							'font-weight' : 'bold'
						});
					}else{
						$('#'+dsp_id).html("参照エラー");
						$('#'+dsp_id).css({
							'border-color' : '#f99',
							'color' : '#f00',
							'font-weight' : 'bold'
						});
					}
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
				}
			});
		}
		////////////////////////////////////////////////////////////////////////////
		//	json参照による指定項目の内容表示・・・ｅｎｄ
		////////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////////
		//	json参照による指定項目の内容表示（売掛用
		////////////////////////////////////////////////////////////////////////////

		function naiyou_dsp_urikake(sanshou_id, den_id, ymd_id, agency, base_url,beforestoccd){
			//alert('配列の数=' + beforestoccd.length);
			if((agency-0)==0 || agency===void 0){
				alert("エージェンシーコードが設定されていません。");
			}
			if(base_url=='' || base_url===void 0){
				alert("「base_url」が設定されていません。");
			}
			var sanshou_idx = $("#"+sanshou_id).val();		// 入力欄からコードをもってくる
			var den_idx 	= $("#"+den_id).val();			// 伝票区分を取得する
			var ymd 		= $("#"+ymd_id).val();			// 伝票日付
			ymd=ymd.replace(/[^0-9]/g, "");
			//alert(ymd);
			var jsonUrl = base_url+'lumist/hin_search_json/' + agency + '/' + sanshou_idx + '/' + ymd;
			// $.ajaxでリクエスト（通信結果をキャッシュさせない必要があるため
			if(sanshou_id=='newstoccd'){	// 登録時の値を入れる先
				var stoccd_idx = '';
				var zeikbn_id = 'newstoczeikbn';
				var bumoncd_id = 'newstocbumoncd';
				var zeicd_id = 'newstoczeicd';
				var zeiritu_id = 'newstoczeiritu';
				var name_id = 'newstocname';
				var tanka_id = 'newtanka';
				var nisugata_id = 'newstocnisugata';
				var suuketa_id = 'newstocsuuketa';
				var tani_id = 'newstoctani';
			}else{	// 更新時の値を入れる先
				var stoccd_idx = sanshou_id.slice(6);
				zeikbn_id = 'stoczeikbn' + stoccd_idx;
				bumoncd_id = 'stocbumoncd' + stoccd_idx;
				zeicd_id = 'stoczeicd' + stoccd_idx;
				zeiritu_id = 'stoczeiritu' + stoccd_idx;
				name_id = 'stocname' + stoccd_idx;
				tanka_id = 'tanka' + stoccd_idx;
				nisugata_id = 'stocnisugata' + stoccd_idx;
				suuketa_id = 'stocsuuketa' + stoccd_idx;
				tani_id = 'stoctani' + stoccd_idx;
			}
			$.ajax( {
				type: 'GET',
				scriptCharset: 'utf-8',
				dataType:'json',
				url: jsonUrl,
				cache: false,	// キャッシュを使わない
				success: function(json) {
					//alert('変更前=' + beforestoccd[stoccd_idx-1]);
					//alert('変更後=' + sanshou_idx);
					$('#'+zeikbn_id).val(json[0].zeikbn);
					$('#'+bumoncd_id).val(json[0].bumoncd);
					$('#'+zeicd_id).val(json[0].zeicd);
					$('#'+zeiritu_id).val(json[0].zeiritu);
					$('#'+nisugata_id).val(json[0].nisugata);
					$('#'+suuketa_id).val(json[0].suuketa);
					//$('#'+tani_id).val(json[0].tani);
					//$('#'+name_id).val(json[0].name);
					$('#'+name_id).css('color', '#000');
					if(sanshou_id=='newstoccd'){	// 新規登録時のみ
						$('#'+name_id).val(json[0].name);
						$('#'+tani_id).val(json[0].tani);
					}
					if(sanshou_id=='stoccd'+ stoccd_idx){	
						if(beforestoccd[stoccd_idx-1]!=sanshou_idx){
							$('#'+name_id).val(json[0].name);
							$('#'+tani_id).val(json[0].tani);
						}
					}
					if(den_idx!=3){
						// 単価が既に入力済の時はとりあえず上書きしない。
						//alert($('#'+tanka_id).val());
						if(sanshou_id=='newstoccd'){	// 新規登録時
							$('#'+tanka_id).val(json[0].tanka);
							set_comma3('#'+tanka_id);
						}
						if(sanshou_id=='stoccd'+ stoccd_idx){	
							if(beforestoccd[stoccd_idx-1]!=sanshou_idx){
								$('#'+tanka_id).val(json[0].tanka);
								set_comma3('#'+tanka_id);
							}
						}
	
					}
					else{
						$('#'+tanka_id).val('');
						$('#'+tani_id).val('');
					}
					//alert('nisugata=' + $('#'+nisugata_id).val());
					//alert('tani=' + $('#'+tani_id).val());
					//alert('suuketa=' + $('#'+suuketa_id).val());

				},
				error: function(jqXHR, textStatus, errorThrown) {
					// 「該当なし」と「その他エラー」（通信事情などによる失敗）を分ける
					if(textStatus=="parsererror"){
						$('#'+name_id).val("該当なし");
					}else{
						$('#'+name_id).val("参照エラー");
					}
					$('#'+name_id).css('color', '#f00');
					//alert('？？？');
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
				}
			});
		}
		////////////////////////////////////////////////////////////////////////////
		//	json参照による指定項目の内容表示（売掛用・・・ｅｎｄ
		////////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////////
		//	指定項目の内容表示２（ｊｓｏｎを使用しない
		////////////////////////////////////////////////////////////////////////////
		function naiyou_dsp2(target, sanshou_id, dsp_id){
			// target = 対象とする項目の種類
			// sanshou_id = 参照する入力欄のid
			// dsp_id = 表示先タグのid（spanタグ）
			var sanshou_idx = ($("#"+sanshou_id).val()-0);	// 入力欄からコードをもってくる

			// 表示枠用class追加
			if($('#'+dsp_id).hasClass('naiyou_label')==false){
				$('#'+dsp_id).addClass('naiyou_label');
			}
			if(target=='keishou'){
				if(sanshou_idx=='1'){
					$('#'+dsp_id).html("様");
				//	$('#'+dsp_id).css('border-color','#9df');
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else if(sanshou_idx=='2'){
					$('#'+dsp_id).html("御中");
				//	$('#'+dsp_id).css('border-color','#9df');
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else{
					$('#'+dsp_id).html("該当なし");
				//	$('#'+dsp_id).css('border-color','#f99');
					$('#'+dsp_id).css({
						'border-color' : '#f99',
						'color' : '#f00',
						'font-weight' : 'bold'
					});
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
					return false;
				}
			}else if(target=='seikyu'){
				if(sanshou_idx=='1'){
					$('#'+dsp_id).html("発行する");
				//	$('#'+dsp_id).css('border-color','#9df');
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else if(sanshou_idx=='2'){
					$('#'+dsp_id).html("発行しない");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else{
					$('#'+dsp_id).html("該当なし");
					$('#'+dsp_id).css({
						'border-color' : '#f99',
						'color' : '#f00',
						'font-weight' : 'bold'
					});
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
					return false;
				}
			}else if(target=='henko'){
				if(sanshou_idx=='1'){
					$('#'+dsp_id).html("変更しない");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else if(sanshou_idx=='2'){
					$('#'+dsp_id).html("変更する");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else{
					$('#'+dsp_id).html("該当なし");
						$('#'+dsp_id).css({
							'border-color' : '#f99',
							'color' : '#f00',
							'font-weight' : 'bold'
						});
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
					return false;
				}
			}else if(target=='urikbn'){
				if(sanshou_idx=='1'){
					$('#'+dsp_id).html("売上済");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
					return true;
				}else if(sanshou_idx=='0'){
					$('#'+dsp_id).html("");
					return true;
				}else{
					$('#'+dsp_id).html("該当なし");
					$('#'+dsp_id).css({
						'border-color' : '#f99',
						'color' : '#f00',
						'font-weight' : 'bold'
					});
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
					return false;
				}
			}else if(target=='haiso'){
				if(sanshou_idx=='1'){
					$('#'+dsp_id).html("注文");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else if(sanshou_idx=='2'){
					$('#'+dsp_id).html("定期");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else{
					$('#'+dsp_id).html("該当なし");
					$('#'+dsp_id).css({
						'border-color' : '#f99',
						'color' : '#f00',
						'font-weight' : 'bold'
					});
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
					return false;
				}
			}else if(target=='keisan'){
				if(sanshou_idx=='1'){
					$('#'+dsp_id).html("日数指定");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else if(sanshou_idx=='2'){
					$('#'+dsp_id).html("曜日指定");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else{
					$('#'+dsp_id).html("該当なし");
					$('#'+dsp_id).css({
						'border-color' : '#f99',
						'color' : '#f00',
						'font-weight' : 'bold'
					});
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
					return false;
				}
			}else if(target=='tukikankaku'){
				if(sanshou_idx=='1'){
					$('#'+dsp_id).html("毎月");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else if(sanshou_idx=='2'){
					$('#'+dsp_id).html("２ヶ月");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else{
					$('#'+dsp_id).html("該当なし");
					$('#'+dsp_id).css({
						'border-color' : '#f99',
						'color' : '#f00',
						'font-weight' : 'bold'
					});
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
					return false;
				}
			}else if(target=='keimark'){
				if(sanshou_idx=='1'){
					$('#'+dsp_id).html("未計算");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
					return true;
				}else if(sanshou_idx=='0'){
					$('#'+dsp_id).html("計算済み");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
					return true;
				}else{
					$('#'+dsp_id).html("該当なし");
					$('#'+dsp_id).css({
						'border-color' : '#f99',
						'color' : '#f00',
						'font-weight' : 'bold'
					});
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
					return false;
				}
			}else if(target=='nouhinazukari'){
				if(sanshou_idx=='1'){
					$('#'+dsp_id).html("未発行");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
					return true;
				}else if(sanshou_idx=='0'){
					$('#'+dsp_id).html("発行済み");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
					return true;
				}else{
					$('#'+dsp_id).html("該当なし");
					$('#'+dsp_id).css({
						'border-color' : '#f99',
						'color' : '#f00',
						'font-weight' : 'bold'
					});
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
					return false;
				}
			}else if(target=='shiharaisite'){
				if(sanshou_idx=='1'){
					$('#'+dsp_id).html("翌月");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else if(sanshou_idx=='2'){
					$('#'+dsp_id).html("翌々月");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else{
					$('#'+dsp_id).html("該当なし");
					$('#'+dsp_id).css({
						'border-color' : '#f99',
						'color' : '#f00',
						'font-weight' : 'bold'
					});
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
					return false;
				}

			}else if(target=='inspeckbn'){
				if(sanshou_idx=='1'){
					$('#'+dsp_id).html("施工時");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else if(sanshou_idx=='2'){
					$('#'+dsp_id).html("半年");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else if(sanshou_idx=='3'){
					$('#'+dsp_id).html("スポット");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else if(sanshou_idx=='4'){
					$('#'+dsp_id).html("１年");
					$('#'+dsp_id).css({
						'border-color' : '#9df',
						'color' : '#000',
						'font-weight' : ''
					});
				}else{
					$('#'+dsp_id).html("該当なし");
					$('#'+dsp_id).css({
						'border-color' : '#f99',
						'color' : '#f00',
						'font-weight' : 'bold'
					});
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
					return false;
				}
			}
		}
		////////////////////////////////////////////////////////////////////////////
		//	指定項目の内容表示２・・・ｅｎｄ
		////////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////////
		//	json参照による指定項目の内容表示（仕入入力用
		////////////////////////////////////////////////////////////////////////////
		function naiyou_dsp_shiire(sanshou_id, agency, base_url){
			if((agency-0)==0 || agency===void 0){
				alert("エージェンシーコードが設定されていません。");
			}
			if(base_url=='' || base_url===void 0){
				alert("「base_url」が設定されていません。");
			}
			var sanshou_idx = $("#"+sanshou_id).val();	// 入力欄からコードをもってくる
			var jsonUrl = base_url+'lumist/search_json/hin/' + agency + '/' + sanshou_idx;
			// $.ajaxでリクエスト（通信結果をキャッシュさせない必要があるため
			if(sanshou_id=='newstoccd'){	// 登録時の値を入れる先
			//	var zeikbn_id = 'newstoczeikbn';
			//	var bumoncd_id = 'newstocbumoncd';
				var name_id = 'newstocname';
			//	var tanka_id = 'newtanka';
			}else{	// 更新時の値を入れる先
				var stoccd_idx = sanshou_id.slice(6);
			//	zeikbn_id = 'stoczeikbn' + stoccd_idx;
			//	bumoncd_id = 'stocbumoncd' + stoccd_idx;
				name_id = 'stocname' + stoccd_idx;
			//	tanka_id = 'tanka' + stoccd_idx;
			}
			$.ajax( {
				type: 'GET',
				scriptCharset: 'utf-8',
				dataType:'json',
				url: jsonUrl,
				cache: false,	// キャッシュを使わない
				success: function(json) {
				//	$('#'+zeikbn_id).val(json[0].zeikbn);
				//	$('#'+bumoncd_id).val(json[0].bumoncd);
					$('#'+name_id).val(json[0].name);
					$('#'+name_id).css('color', '#000');
					$('#'+tanka_id).val(json[0].tanka);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					// 「該当なし」と「その他エラー」（通信事情などによる失敗）を分ける
					if(textStatus=="parsererror"){
						$('#'+name_id).val("該当なし");
					}else{
						$('#'+name_id).val("参照エラー");
					}
					$('#'+name_id).css('color', '#f00');
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
				}
			});
		}

		////////////////////////////////////////////////////////////////////////////
		//	json参照による指定項目の内容表示（仕入入力用
		////////////////////////////////////////////////////////////////////////////
		function naiyou_dsp_shiiresaki(sanshou_id, agency, base_url){
			if((agency-0)==0 || agency===void 0){
				alert("エージェンシーコードが設定されていません。");
			}
			if(base_url=='' || base_url===void 0){
				alert("「base_url」が設定されていません。");
			}
			var sanshou_idx = $("#"+sanshou_id).val();	// 入力欄からコードをもってくる
			var jsonUrl = base_url+'lumist/search_json/shiire/' + agency + '/' + sanshou_idx;
			// $.ajaxでリクエスト（通信結果をキャッシュさせない必要があるため
			$.ajax( {
				type: 'GET',
				scriptCharset: 'utf-8',
				dataType:'json',
				url: jsonUrl,
				cache: false,	// キャッシュを使わない
				success: function(json) {
					$('#name').val(json[0].name);
					$('#cd').css('color', '#000');
					$('#shiire_button').click();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					// 「該当なし」と「その他エラー」（通信事情などによる失敗）を分ける
					if(textStatus=="parsererror"){
						$('#name').val("該当なし");
					}else{
						$('#name').val("参照エラー");
					}
					$('#name').css('color', '#f00');
					$('#'+sanshou_id).focus();
					$('#'+sanshou_id).select();
				}
			});
		}
		// 他のツールチップを消す
		function naiyou_dsp_allclear(){
			if($('#kokyaku_tt').css('display')!='none'){
				$('#kokyaku_tt').css('display','none');
			}
			if($('#kado_tt').css('display')!='none'){
				$('#kado_tt').css('display','none');
			}
			if($('#keiyaku_tt').css('display')!='none'){
				$('#keiyaku_tt').css('display','none');
			}
			if($('#shiharaisite_tt').css('display')!='none'){
				$('#shiharaisite_tt').css('display','none');
			}
			if($('#keishou_tt').css('display')!='none'){
				$('#keishou_tt').css('display','none');
			}
			if($('#kaiyaku_tt').css('display')!='none'){
				$('#kaiyaku_tt').css('display','none');
			}
			/*
			if($('#sisetu_tt').css('display')!='none'){
				$('#sisetu_tt').css('display','none');
			}
			if($('#dounyu_tt').css('display')!='none'){
				$('#dounyu_tt').css('display','none');
			}
			*/
			if($('#kaishu_tt').css('display')!='none'){
				$('#kaishu_tt').css('display','none');
			}
			if($('#seikyu_tt').css('display')!='none'){
				$('#seikyu_tt').css('display','none');
			}
			if($('#seikyuhenko_tt').css('display')!='none'){
				$('#seikyuhenko_tt').css('display','none');
			}
			if($('#den_tt').css('display')!='none'){
				$('#den_tt').css('display','none');
			}
			if($('#inspeckbn_tt').css('display')!='none'){
				$('#inspeckbn_tt').css('display','none');
			}
		}
		////////////////////////////////////////////////////////////////////////////
		// tooltip表示内容をajaxで取り出して書き換える
		////////////////////////////////////////////////////////////////////////////
		function tooltip_content_write(target, sanshou_id, agency, base_url){
			// target = 対象とする項目の種類（コントローラのsearch_json参照
			// sanshou_id = ツールチップを表示する入力欄のid
			if((agency-0)==0 || agency===void 0){
				alert("エージェンシーコードが設定されていません。");
			}
			if(base_url=='' || base_url===void 0){
				alert("「base_url」が設定されていません。");
			}
			var ret ='';
			// search_json/対象項目/エージェンシーコード/tt（※ttはツールチップ目的で全てを取り出すときの値
			var jsonUrl = base_url +'lumist/search_json/' + target + '/' + agency + '/tt';
			$.ajax( {
				type: 'GET',
				scriptCharset: 'utf-8',
				dataType:'json',
				url: jsonUrl,
				cache: false,	// キャッシュを使わない
				success: function(json) {
					for (var i in json) {
						ret += '['+json[i].cd +'：'+ json[i].name +']<br />';	// ※改行タグも使える
					}
					$('#'+sanshou_id).html(ret);
				},
			});
		}
		////////////////////////////////////////////////////////////////////////////
		//	json参照によるツールチップ表示・・・ｅｎｄ
		////////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////////
		// tooltip表示内容を書き換える（指定項目の内容表示２　と関連するので、修正時には注意
		////////////////////////////////////////////////////////////////////////////
		function tooltip_content_write2(target, sanshou_id){
			// target = 対象とする項目の種類（コントローラのsearch_json参照
			// sanshou_id = ツールチップを表示する入力欄のid
			var ret = '';
			if(target=='keishou'){
				ret += '[１：様]<br />';
				ret += '[２：御中]<br />';
			}else if(target=='seikyu'){
				ret += '[１：発行する]<br />';
				ret += '[２：発行しない]<br />';
			}else if(target=='henko'){
				ret += '[１：変更しない]<br />';
				ret += '[２：変更する]<br />';
			}else if(target=='urikbn'){
				ret += '[０：未計上]<br />';
				ret += '[１：売上済]<br />';
			}else if(target=='haiso'){
				ret += '[１：注文]<br />';
				ret += '[２：定期]<br />';
			}else if(target=='keisan'){
				ret += '[１：日数指定]<br />';
				ret += '[２：曜日指定]<br />';
			}else if(target=='tukikankaku'){
				ret += '[１：毎月]<br />';
				ret += '[２：２ヶ月]<br />';
			}else if(target=='keimark'){
				ret += '[０：計算済み]<br />';
				ret += '[１：未計算]<br />';
			}else if(target=='nouhinazukari'){
				ret += '[０：発行済み]<br />';
				ret += '[１：未発行]<br />';
			}else if(target=='shiharaisite'){	// 2020/05/30 enoki ルミスト仕様
				ret += '[１：翌月]<br />';
				ret += '[２：翌々月]<br />';
			}else if(target=='inspeckbn'){		// 2020/05/30 enoki ルミスト仕様
				ret += '[１：施工時]<br />';
				ret += '[２：半年]<br />';
				ret += '[３：スポット]<br />';
				ret += '[４：１年]<br />';
			}
			$('#'+sanshou_id).html(ret);
		}
		////////////////////////////////////////////////////////////////////////////
		//	json参照によるツールチップ表示・・・ｅｎｄ
		////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////
		////	商品の税コードから税率テーブルを検索する。(同期：非推奨でゲットする）
		//////////////////////////////////////////////////////////////////////////////////
		function zeicd_get(ymd,zeicd,zeigroup,base_url){
			var jsonUrl = base_url + 'lumist/taxjson/' + ymd + '/' + zeicd;
			// $.ajaxでリクエスト（通信結果をキャッシュさせない必要があるため
			$.ajax( {
				type: 'GET',
				scriptCharset: 'utf-8',
				dataType:'json',
				async:false,
				url: jsonUrl,
				cache: false,	// キャッシュを使わない
				success: function(json) {
					zeigroup[0]=(json[0].zei-0)/100;
					zeigroup[1]=json[0].zei;
				},
				error: function(jqXHR, textStatus, errorThrown) {
					// 「該当なし」と「その他エラー」（通信事情などによる失敗）を分ける
					zeigroup[0]=0;
					zeigroup[1]=0;
				}
			});
		}

		//////////////////////////////////////////////////////////////////////////////////
		////	エージェンシーをAJAXで取得する。　↓開発時点では未使用。
		////	未使用の理由：エージェンシーはキーであるため、ページの再表示（ポストバック）を行っても問題ないから
		//////////////////////////////////////////////////////////////////////////////////
/*
		function agency_dsp(sanshou_id, base_url){
			var agency = $("#"+sanshou_id).val();	// 入力欄からコードをもってくる
			var jsonUrl = base_url+'lumist/agency_check/' + agency ;
			alert(jsonUrl);
			return $.ajax({
				type: 'GET',
				scriptCharset: 'utf-8',
				dataType:'json',
				url: jsonUrl,
				cache: false	// キャッシュを使わない
			})
		}
		
		agency_dsp().done(function(result) {
			// OKの時は新しいエージェンシーを持ってポストバックします。
			alert('OK')
			$('#agency_dsp').off('submit');
			$('#agency_dsp').submit();
		}).fail(function(result) {
			alert('エラー')
			$('#agency').focus();
			$('#agency').select();
		});
*/
		//////////////////////////////////////////////////////////////////////////////////
		////	入力された顧客コードが存在するかJSONを用いてチェックする。(同期：非推奨でゲットする）
		//////////////////////////////////////////////////////////////////////////////////
		function usercd_get(cd,agency,base_url){
			if((agency-0)==0 || agency===void 0){
				alert("エージェンシーコードが設定されていません。");
			}
			if(base_url=='' || base_url===void 0){
				alert("「base_url」が設定されていません。");
			}
			var cd =$(cd).val();	// 入力欄からコードをもってくる

			var jsonUrl = base_url+'lumist/search_json/usr/' + agency + '/' + cd;

			// $.ajaxでリクエスト（通信結果をキャッシュさせない必要があるため
			$.ajax( {
				type: 'GET',
				scriptCharset: 'utf-8',
				dataType:'json',
				async:false,
				url: jsonUrl,
				cache: false,	// キャッシュを使わない
				success: function(json) {
					// 存在する時は何もせずサーバーへ処理を移す。
					//alert("存在します");
				//	alert(json[0].name);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$('#usercd').val();
					$('#usercd2').val("error");
					$('#usercd').focus();
					$('#usercd').select();
				}
			});
		}
		//////////////////////////////////////////////////////////////////////////////////
		////	郵便番号直接入力から、住所を検索する。(同期：非推奨でゲットする）
		//////////////////////////////////////////////////////////////////////////////////
		function address_get(post,addr,base_url){
			var jsonUrl = base_url+'lumist/postjson/' + post;
			// $.ajaxでリクエスト（通信結果をキャッシュさせない必要があるため
			$.ajax( {
				type: 'GET',
				scriptCharset: 'utf-8',
				dataType:'json',
				async:false,
				url: jsonUrl,
				cache: false,	// キャッシュを使わない
				success: function(json) {
					addr[0]=json[0].addr;
				},
				error: function(jqXHR, textStatus, errorThrown) {
					// 「該当なし」と「その他エラー」（通信事情などによる失敗）を分ける
					addr[0]='';
				}
			});
		}
	
		//////////////////////////////////////////////////////////////////////////////////
		////	すでに登録があるかをDBから確認する。(同期：非推奨でゲットする）
		//////////////////////////////////////////////////////////////////////////////////
		function exist_check(target, sanshou_id, agency, base_url,cur_location,next_location){
			if((agency-0)==0 || agency===void 0){
				alert("エージェンシーコードが設定されていません。");
			}
			if(base_url=='' || base_url===void 0){
				alert("「base_url」が設定されていません。");
			}
			var sanshou_idx = $("#"+sanshou_id).val();	// 入力欄からコードをもってくる
			//alert(target);
			//alert(sanshou_id);
			//alert(agency);
			//alert(cur_location);
			//alert(next_location);
			var jsonUrl = base_url + 'lumist/exist_check/' + target + '/' + agency + '/' + sanshou_idx;
			// $.ajaxでリクエスト（通信結果をキャッシュさせない必要があるため
			$.ajax( {
				type: 'GET',
				scriptCharset: 'utf-8',
				dataType:'json',
				async:false,
				url: jsonUrl,
				cache: false,	// キャッシュを使わない
				success: function(json) {
					if((json[0].flg-0)==1){
						alert('入力されたコードは既に登録済みです。');
						$('#'+cur_location).focus();
						$('#'+cur_location).select();
					}else{
						$('#'+next_location).focus();
						$('#'+next_location).select();
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					// 「該当なし」と「その他エラー」（通信事情などによる失敗）を分ける
					///var res = $.parseJSON(jqXHR.responseText);
			}
			});
		}

		function halfchange(str) {
			return str.replace(/[０-９]/g, function(s) {
				return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
			});
		}
	