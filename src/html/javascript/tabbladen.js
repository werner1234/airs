// code tbv tabbladen

var N="";

function tabClose(z)
{
		document.getElementById('tabbutton'+z).className='tabbuttonInActive';
		document.getElementById('tab'+z).style.visibility="hidden"
    document.getElementById('tab'+z).style.zIndex="0";
}
function tabOpen(z)
{
	if (N!=z&&N!="")
	{
		tabClose(N);
	} 
	document.getElementById('tab'+z).style.visibility="visible";
	document.getElementById('tabbutton'+z).className='tabbuttonActive';
  document.getElementById('tab'+z).style.zIndex="999";
	N=z;
}