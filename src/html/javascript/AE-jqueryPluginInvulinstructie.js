(function ( $ ) {

  $.fn.invulInstructie = function( options )
  {
  
    var scripts = document.getElementsByTagName("script");
    var currentScriptUrl = ( document.currentScript || scripts[scripts.length - 1] ).src;
    var scriptN = currentScriptUrl.length > 0 ? currentScriptUrl : scripts[scripts.length - 1].baseURI.split("/").pop();
    var filename = scriptN.split("?")[0];
  
    // This is the easiest way to have default options.
    var settings = $.extend({
      field: $(this).attr("id"),
      value: $(this).val(),
      party: "*",
      script: filename
      
    }, options);
  
    
    // var path = settings.script.split("/");
    // var filename = path[path.length-1];
    //console.log(filename);
    if (options.consoleLog == 1)
    {
      console.log("invul settings --> script: " + settings.script + " | veld: " + settings.field + " | VB: " + settings.party);
    }

  
    $.ajax({
      
      url: "lookups/AE-jqueryPluginInvulinstructieLookup.php",
      type: 'POST',
      dataType: 'json',
      data: {
        script: settings.script,
        field: settings.field,
        value: settings.value,
        party: settings.party
      }
      
    }).done(function (data)
    {
      //console.log("data from lookup_____________________");
      //console.log(data);
    
      if (data.text != undefined)
      {
      
        $("#jqAE-invul-msgHead").html(data.header);
        if (data.class != "")
        {
        
          $("#jqAE-invul-msgHead").attr("class", data.class);
        }
        else
        {
          $("#jqAE-invul-msgHead").removeAttr("class");
        }
      
        $("#jqAE-invul-msgContent").html(data.text.replace(/\n/g, "<br />"));
        $("#jqAE-invul-msg").css('visibility', 'visible');
      }
      else
      {
        $("#jqAE-invul-msg").css('visibility', 'hidden');
      }
    });
  
  };

}( jQuery ));