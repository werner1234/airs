/**
 * Created by bdl on 30-10-2017.
 */
$(document).ready(function() {

    function getDoorkijkCategorie()
    {
        var action    = 'categorie';
        var current = $('#msCategorie').val();
        var msCategoriesoort = $('#msCategoriesoort').val();

        $.ajax({
            type: "POST",
            url: "ajax/getDoorkijkWegingPerFonds.php",
            data: {
                "msCategoriesoort": msCategoriesoort
            },
            success: function(result)
            {
                //alert('Sukses');
                $("#msCategorie").children().remove();
                $("#msCategorie").append($('<option>', {value: "",text: "---"}));
                $(result).each(function()
                {
                    $("#msCategorie").append($('<option>',
                        {
                            value: this.id,
                            text: this.desc,
                        }));
                });
                if ( current.length > 0 )
                {
                    $("#msCategorie").val(current);
                }
            },
            dataType: "json"
        });

    }	// --- end of getDoorkijkCategorie() ---
    getDoorkijkCategorie();

    $("#msCategoriesoort").change(function(event)
    {
        //alert("Oeleboele");
        getDoorkijkCategorie();

    }); // --- end of change #doorkijkCategoriesoort ---







});   // ---end of document ready ---
