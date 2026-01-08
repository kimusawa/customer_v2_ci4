var TimeOut    = 2000;
var closeTimer = null;

function SubOpen(sub_id,order,indent){

	var sub_element = document.getElementById(sub_id);
	var elements    = document.getElementsByTagName("ul");
	var size;

	if (window.addEventListener) {  /* Firefox */
		blowse = 'Firefox';
	} 
	else if (window.attachEvent) {  /* IE */
		blowse = 'IE';
	}    
	else if (document.getElementById || document.all) { /* ‚»‚Ì‘¼DOM€‹’ */
		blowse = 'ALL';
	}

	if(order != "" && order != 0){
		size = 27 * order + 'px';
	}else{
		size = '0px';
	}

	if (sub_element){
		sub_element.style.visibility='visible';
		sub_element.style.top=size;
		if(indent >= 1){
			if(blowse == 'IE'){
				sub_element.style.left='159px';
			}else{
				sub_element.style.left='161px';
			}
		}
	}
}

function SubOpen2(sub_id){
	var sub_element = document.getElementById(sub_id);

	sub_element.style.visibility='visible';
}


function SubClose(sub_id){
	var sub_element = document.getElementById(sub_id);
	if (sub_element){
		sub_element.style.visibility='hidden';
	}
}

