

function cl(text){
  console.log(text);
}

$(document).ready(function(){
  
  var t = $('input[type="checkbox"]:checked').length;
  var beheerder = 1;
  var attributie = '{attr}';
  var categorie  = '{cat}';
  
  $(".selAttr").change(function(){
    console.log("in selAttr change");
    console.log("val = " + $(this).val() );
    console.log("attributie = " + attributie);
    if (attributie == 1 && $(this).val() != "")
    {
      $(this).removeClass("selectRood")
    }
    if (attributie == 1 && $(this).val() == "")
    {
      $(this).addClass("selectRood")
    }
  });
  
  $(".selBeleg").change(function(){
    console.log("in selBeleg change");
    console.log("val = " + $(this).val() );
    console.log("categorie = " + categorie);
    if (categorie == 1 && $(this).val() != "")
    {
      $(this).removeClass("selectRood")
    }
    if (categorie == 1 && $(this).val() == "")
    {
      $(this).addClass("selectRood")
    }
  });
  
  $("#btnSubmit").click(function(a){
    a.preventDefault();
    var errorTxt = "";
    if (t > 0)
    {
      for (n=100; n <= indexCount; n++ )
      {
        var checkbox = n+"_check";
        
        if ($("#"+checkbox).is(':checked'))
        {
          
          var field = $('input[name='+n+'_rekNr]').attr("name");
          var test = $('input[name='+field+']').val();
          console.log("field: " + field + "  test: " +test);
          
          if (test.length < 1)
          {
            errorTxt += "<br/>rij "+ eval(n-99)+": rekeningnr mag niet leeg zijn";
          }
          
          var field = $('input[name='+n+'_portefeuille]').attr("name");
          var test = $('input[name='+field+']').val();
          
          if (test.length < 1)
          {
            errorTxt += "<br/>rij "+ eval(n-99)+": portefeuille mag niet leeg zijn";
          }
          
          // var field = $('select[name='+n+'_valuta]').attr("name");
          // var test = $('select[name='+field+']').val();
          //
          // if (test.length < 1)
          // {
          //   errorTxt = errorTxt + "<br/>rij "+ eval(n-99)+": valuta mag niet leeg zijn";
          // }
  
          var field = $('select[name='+n+'_beleggingscategorie]').attr("name");
          var test = String($('select[name='+field+']').val());
          console.log("cat. selectie: "+ field + " value: " + test);
          var testOk = true;
          if (test == null || test == "")
          {
            testOk = false;
          }
          
          if (!testOk && categorie == '1')
          {
            errorTxt = errorTxt + "<br/>rij "+ eval(n-99)+": beleggingscategorie mag niet leeg zijn";
          }
          
          var field = $('select[name='+n+'_attributiecategorie]').attr("name");
          var test = String($('select[name='+field+']').val());
          var testOk = true;
          if (test == null || test == "")
          {
            testOk = false;
          }
          
          if (testOk && attributie == '1')
          {
            errorTxt = errorTxt + "<br/>rij "+ eval(n-99)+": attributiecategorie mag niet leeg zijn";
          }
          
          
        }
        
      }
    }
    
    if (errorTxt.length > 0)
    {
      AEMessage(errorTxt,"Controles");
      // alert(errorTxt);
      return false;
    }
    $("#addRekeningForm").submit();
    return true;
  });
  
  if (t > 0)
  {
    $("#frmAction").hide();
    $("#addRekening").val("1");
    $("#kopje").html("<b>Rekeningen toevoegen</b>");
  }
  $('input[type="checkbox"]').change(function(){
    var t = $('input[type="checkbox"]:checked').length;
    if (t > 0)
    {
      $("#frmAction").hide();
      $("#addRekening").val("1");
      $("#kopje").html("<b>Rekeningen toevoegen</b>");
    }
    else
    {
      $("#frmAction").show(200);
      $("#addRekening").val("0");
      $("#kopje").html("<b>Mutaties verwerken</b>");
    }
  });
  
});
