function parse_str(str, array){
    // http://kevin.vanzonneveld.net
    // +   original by: Cagri Ekin
    // +   improved by: Michael White (http://crestidg.com)
    // *     example 1: parse_str('first=foo&second=bar');
    // *     returns 1: { first: 'foo', second: 'bar' }
    // *     example 2: parse_str('str_a=Jack+and+Jill+didn%27t+see+the+well.');
    // *     returns 2: { str_a: "Jack and Jill didn't see the well." }

    var glue1 = '=';
    var glue2 = '&';

    var array2 = str.split(glue2);
    var array3 = [];
    for(var x=0; x<array2.length; x++){
        var tmp = array2[x].split(glue1);
        array3[unescape(tmp[0])] = unescape(tmp[1]).replace(/[+]/g, ' ');
    }

    if(array){
        array = array3;
    } else{
        return array3;
    }
}

function getSize(){
var f = document.forms[0], nl = f.elements, ed = tinyMCEPopup.editor, dom = ed.dom, n = ed.selection.getNode();
//nl.src.value = dom.getAttrib(n, 'src');
var adressstring = dom.getAttrib(n, 'src');
var queryarray = [];

queryarray = parse_str(adressstring);
//alert(queryarray['size']);

if(isNaN(queryarray['size'])){
	document.getElementById('sizetr01').style.display = 'none';
	//dom.setStyle(sizetr01, 'display', 'none');
	}
else{
	//document.getElementById('olddimensions').style.display = 'none';
	//document.getElementById('size01').value = queryarray['size'];
	//dom.setStyle('olddimensions', 'display', 'none');
	//dom.setAttrib('size01', 'value', queryarray['size']);
	
	count = f.length;
	for(i=0;i<count;i++){
		if((f.elements[i].name == "size01")){
			//alert(f.elements[i].value);
			f.elements[i].value = '300';
			//alert(f.elements[i].value);
			}
		}
	}
}

function writeSizeBack(){
var size = document.getElementById('size01').value;
var adressstring = document.getElementById('src').value;

if(!isNaN(size)){
	var temparray = adressstring.split('&size=');
	}

var newadress = temparray[0]+'&size='+size;
document.getElementById('src').value = newadress;
}