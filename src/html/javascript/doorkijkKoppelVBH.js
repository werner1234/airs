/**
 * Created by bdl on 25-10-2017.
 */


$(document).ready(function()
{
    function submitForm()
    {
        //check values ?
        document.editForm.submit();
    }
     
    function getBronKoppeling()
    {
        var action    = 'bronkoppeling';
        var current = $("#bronKoppeling").val();

        var systeem = $('#systeem').val();
        var vermogensbeheerder = $('#Vermogensbeheerder').val();
        var doorkijkCategoriesoort =  $('#doorkijkCategoriesoort').val()
        var doorkijkCategorie = $('#doorkijkCategorie').val();

        $.ajax({
            type: "POST",
        url: "ajax/getDoorkijk.php",
        data: {
        "action": action,
                    "systeem": systeem,
                    "vermogensbeheerder": vermogensbeheerder,
                    "doorkijkCategoriesoort": doorkijkCategoriesoort,
                    "doorkijkCategorie": doorkijkCategorie
        },
        success: function(result)
        {
            //alert('Sukses');
            $("#bronKoppeling").children().remove();
            $("#bronKoppeling").append($('<option>', {value: "",text: "---"}));
            $(result).each(function(){
                $("#bronKoppeling").append($('<option>', {
                    value: this.id,
                        text: this.desc,
                }));
            });

            if ( current.length > 0 ) {
                $("#bronKoppeling").val(current);
            }
    },
    dataType: "json"
});
} // --- end of function getBronKoppeling ---

function selectieChanged()
{
    getdoorkijkCategoriesoort();
    getDoorkijkCategorie();
    getBronKoppeling();
} // --- end of selectieChanged() ---


function getdoorkijkCategoriesoort() {
    var action    = 'catSoort';
    var current = $('#doorkijkCategoriesoort').val();
    console.log(current);
    var systeem = $('#systeem').val();
    var vermogensbeheerder = $('#Vermogensbeheerder').val();
    var doorkijkCategoriesoort = $('#doorkijkCategoriesoort').val();

    $.ajax({
        type: "POST",
    url: "ajax/getDoorkijk.php",
    data: {
                    "action": action,
                    "systeem": systeem,
                    "vermogensbeheerder": vermogensbeheerder
          },
    success: function(result)
    {
        //alert('Sukses');
        $("#doorkijkCategoriesoort").children().remove();
        $("#doorkijkCategoriesoort").append($('<option>', {value: "",text: "---"}));
        $(result).each(function()
        {
            //console.log(result);
            $("#doorkijkCategoriesoort").append($('<option>',
                {
                    value: this.id,
                    text: this.desc,
                }));
            //$("#doorkijkCategoriesoort").val(this.id);
         });
        if ( current.length > 0 ) {
            $("#doorkijkCategoriesoort").val(current);
        }
    },
dataType: "json"
});

} // --- end of getdoorkijkCategoriesoort ---

function getDoorkijkCategorie()
{
    var action    = 'categorie';
    var current = $('#doorkijkCategorie').val();

    var systeem = $('#systeem').val();
    var vermogensbeheerder = $('#Vermogensbeheerder').val();
    var doorkijkCategoriesoort = $('#doorkijkCategoriesoort').val();

    $.ajax({
        type: "POST",
    url: "ajax/getDoorkijk.php",
    data: {
    "action": action,
                    "systeem": systeem,
                    "vermogensbeheerder": vermogensbeheerder,
                    "doorkijkCategoriesoort": doorkijkCategoriesoort
    },
    success: function(result)
    {
        //alert('Sukses');
        $("#doorkijkCategorie").children().remove();
        $("#doorkijkCategorie").append($('<option>', {value: "",text: "---"}));
        $(result).each(function()
        {
            $("#doorkijkCategorie").append($('<option>',
                {
                    value: this.id,
                    text: this.desc,
                }));
        });
        if ( current.length > 0 )
        {
            $("#doorkijkCategorie").val(current);
        }
},
dataType: "json"
});

}	// --- end of getDoorkijkCategorie() ---

selectieChanged();

$("#Vermogensbeheerder").change(function(event)
{
    //alert('Joepie');
    getdoorkijkCategoriesoort();

}); // --- end of change #Vermogensbeheerder ---

$("#systeem").change(function(event) 
{
    getBronKoppeling();

}); // --- end of change #systeem ---


$("#doorkijkCategoriesoort").change(function(event) 
{
    getDoorkijkCategorie();

}); // --- end of change #doorkijkCategoriesoort ---


$("#doorkijkCategorie").change(function(event) 
{
    getBronKoppeling();

}); // --- end of change #doorkijkCategorie ---



}); // ---end of document ready --- 

