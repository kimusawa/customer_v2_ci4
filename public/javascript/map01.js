//<![CDATA[

// グローバル変数の宣言
var map;
var geocoder;
var lat;
var lng;
var name;
var addr;

//　地図の中心に十字アイコンを表示する。
var cross_icon = new GIcon();
cross_icon.image = "http://googlemaps.googlermania.com/uploads/cross_marker.gif";
cross_icon.iconSize = new GSize(39, 39);
cross_icon.iconAnchor = new GPoint(20, 20);
var option = {
    icon  : cross_icon   //アイコン = cross_icon
    ,clickable : false   //クリック = 不可
};
var cross_marker=null;



// 初期化
function initAddress(){
	  google.maps.event.addDomListener(window, 'load', function() {
	      var map = document.getElementById("map");
	     var options = {
	            zoom: 16,
	          center: new google.maps.LatLng(35.686773, 139.68815),
	          mapTypeId: google.maps.MapTypeId.ROADMAP
	       };
	     new google.maps.Map(map, options);
	 });
/*
    if ( GBrowserIsCompatible() ) {
    	var point = new GLatLng(36.056697,139.510574);
        map = new GMap2(document.getElementById("map"));
        // 初期化時は任意の経度、緯度の地点を表示させる
        map.setCenter(new GLatLng(35.644934,139.699166), 13);
        // ジオコーディングオブジェクトの生成
        geocoder = new GClientGeocoder();

        map.addControl(new GMapTypeControl(),
            new GControlPosition(G_ANCHOR_TOP_RIGHT,
	    new GSize(10,40)));
        // その他の位置の定数
        // G_ANCHOR_BOTTOM_LEFT
        // G_ANCHOR_BOTTOM_RIGHT
        // G_ANCHOR_TOP_LEFT
        // G_ANCHOR_TOP_RIGHT
        // GSizeの値はpx値

        //　方向・縮尺の詳細コントロール
        //map.addControl(new GLargeMapControl());
        //　3Dのズームコントロールを追加する (v=2.144 を忘れずに)
        map.addControl(new GLargeMapControl3D());
        //　オーバービューコントロール
        map.addControl(new GOverviewMapControl());
        //　キーボード操作可能
        new GKeyboardHandler(map);
        //　現在地を表示するナビコントローラを追加する
        map.addControl(new GNavLabelControl());

        // 地図に十字マークを登録しておく
        cross_marker = new GMarker(point, cross_icon);
        map.addOverlay(cross_marker); // イベントの登録 : 地図が移動中のとき、呼び出されるようにする
        GEvent.addListener(map, "move", function(){
	        drawCrossScope(map);
        });// 十字マークの描画
        drawCrossScope(map);

        //マーカーマネージャーを作成する
        mgr = new GMarkerManager(map);
        //ズームレベル15以上のとき
        var photoA_marker = createMarker(point, {
        	image : "../image/h.gif"
        		,iconSize : new GSize(40, 40)
	    	,iconAnchor: new GPoint(0, 125)
	    });
        mgr.addMarker( photoA_marker, 15 );
        //ズームレベル12～14のとき
        var photoB_marker = createMarker(point, {
        	image : "../image/h.gif"
        		,iconSize : new GSize(25, 25)
	    	,iconAnchor: new GPoint(0, 63)
	    });
        mgr.addMarker( photoB_marker, 12, 14 );
        //ズームレベル3～11のとき
        var photoC_marker = createMarker(point, {
        	image : "../image/h.gif"
        		,iconSize : new GSize(15, 15)
	    ,iconAnchor: new GPoint(0, 33)
	    });
        mgr.addMarker( photoC_marker, 3, 11 );
        //マーカーマネージャーを稼動させる
        mgr.refresh();

        //地図をクリックしたら、マーカーを追加
        GEvent.addListener(map, "click", function(overlay, latlng, overlaylatlng) {
        	if (overlay) {
        		//overlay が null でないときは、何かしらのオーバーレイがクリックされた
        		//マーカーかどうか判定する
        		//(openInfoWindow を持っていれば、マーカーとみなす)
        		if ("openInfoWindow" in overlay) {
        			//マーカーなら削除
        			map.removeOverlay(overlay);
        		}
        	}else{
        		//overlay が null のときは、マーカを追加する
        		var m = new GMarker(latlng);
        		map.addOverlay(m);
        		//緯度・経度の表示
        		m.openInfoWindowHtml("lat:" + latlng.lat() + "<br>lng:" + latlng.lng());
        	}
        });
    }
*/
}

function createMarker(markerPos, photoInfo){
    //オリジナルマーカーの画像を作成
    var myIcon = new GIcon(photoInfo);
    //画像として表示させるため、クリック・オートパンの禁止
    var markerOpt = {
        icon: myIcon
	,clickable: false
	,autoPan: false
	};
    var marker = new GMarker(markerPos, markerOpt);
    return marker;
}


//　地図の中心に十字マークを描画する
function drawCrossScope(map){
    // 現在表示している地図の中心地点を取得する
    var mapCenter = map.getCenter();
    //マーカーを地図の中心地点に移動させる
    cross_marker.setPoint(mapCenter);
}

// ファイルの読み込み前処理(CSVファイル)
function readDat(){
    GDownloadUrl("data.txt",moveAddress);
}
// ファイルの読み込み前処理(XMLファイル)
function readDatXML(){
	alert("000");
    GDownloadUrl("data.xml",moveAddressXML);
}

function moveAddress(data,statusCode) {
    var points = data.split('\n');
    for(var i = 0; i < points.length; i++){
        var point = points[i].split(',');
	lat = point[0];
	lng = point[1];
	name = point[2];
	addr = point[3];
	////////////////////////////////
	//alert(lat);
	//alert(lng);
	//alert(name);
	//alert(addr);
	////////////////////////////////
        //addressSearch(addr);
	latlngSearch();
        //var marker = new GMarker(new GLatLng(lat, lng));
        //map.addOverlay(marker);
        //marker.openInfoWindowHtml(name);

	var point = new GLatLng(lat, lng);
        map.setCenter(point, 18);
        var icon = new GIcon();
        icon.image = "../image/h.gif";
        icon.iconSize = new GSize(40, 40);
        icon.iconAnchor = new GPoint(0, 0);
        var markeropts = new Object();
        markeropts.icon = icon;
        var marker = new GMarker(point, markeropts);
        map.addOverlay(marker);
    }
}
function moveAddressXML(xmldata, statusCode) {
    var xml = GXml.parse(xmldata);
alert("startA");
    var markers = xml.documentElement.getElementsByTagName("marker");
alert("startB");

    for (var i = 0; i < markers.length; i++) {
        var lats = markers[i].getElementsByTagName("lat");
        var lngs = markers[i].getElementsByTagName("lng");
        var names = markers[i].getElementsByTagName("name");

        var lat = parseFloat(GXml.value(lats[0]));
        var lng = parseFloat(GXml.value(lngs[0]));
        var name = GXml.value(names[0]);

	var point = new GLatLng(lat, lng);
        map.setCenter(point, 18);
        var icon = new GIcon();
        icon.image = "../image/h.gif";
        icon.iconSize = new GSize(40, 40);
        icon.iconAnchor = new GPoint(0, 0);
        var markeropts = new Object();
        markeropts.icon = icon;
        var marker = new GMarker(point, markeropts);
        map.addOverlay(marker);
    }
}

////////////////////////////////////////////////////////////////////////////
/////// 住所（日本語文字列）による検索 /////////////////////////////////////
function addressSearch(addr,zoom){
    if (geocoder) {
        geocoder.getLatLng(
        addr,

	function(latlng) {
	    if (!latlng) {
	        //alert(addr + " not found");
	    } else {
		map.setCenter(latlng, parseInt(zoom));
                //var marker = new GMarker(new GLatLng(lat, lng));
                var marker = new GMarker(latlng);
                map.addOverlay(marker);
                marker.openInfoWindowHtml(addr);
	    }
	}
	);
    }
}
////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
/////// 緯度・経度による検索 ///////////////////////////////////////////////
function latlngSearch(){
    map.setCenter(new GLatLng(lat, lng), 13);
}
////////////////////////////////////////////////////////////////////////////


//]]>
