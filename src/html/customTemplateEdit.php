<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl'] = "customTemplateList.php";
$__funcvar['location'] = "customTemplateEdit.php";

$data = array_merge($_POST, $_GET);
$action = (isset ($data['action'])?$data['action']:'');


$customTemplate = new AE_CustomTemplate();
$object = new custom_templates($customTemplate);

$AETemplate = new AE_template();
$AEJson = new AE_Json();

if ($action === 'update')
{
  $data['template'] = $customTemplate->json->json_encode($data['templateVars']);
}


$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

//$editcontent['jsincludes'] .= $AETemplate->loadJs('ckeditor_4.14.0/ckeditor');

$editObject->controller($action, $data);

//debug($object);


//debug($data);exit();
$categorie = null;
if (isset ($data['categorie']) && !empty ($data['categorie']))
{
  $categorie = $data['categorie'];
}
else
{
  $categorie = $object->get('categorie');
}

$object->set('categorie', $categorie);

if (isset ($data['naam']))
{
  $object->set('naam', $data['naam']);
}

if ($action === 'edit' || $action === 'new' || $object->error === true)
{
  
  $multiLanguageField = '';
  if ($categorie && $customTemplate->isTranslatable($categorie)) {
    $multiLanguageField = '
      <div class="form-group row">
        <div class="formlinks"><label for="meertalig">' . $object->data['fields']['meertalig']['description'] . '</label> </div>
        <div class="formrechts">
          ' . $editObject->form->makeInput('meertalig', $object) . '
          ' . $object->getError('meertalig') . '
        </div>
      </div>
    
    ';
  }
  

  echo template($__appvar["templateContentHeader"], $editcontent);

  echo '
    <br />
    <div class="container-fluid">
    
    <form   name="editForm" action="' . $__funcvar['location'] . '" method="POST" >
    
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="updateScript" value="' . $__funcvar['location'] . '">
      <input type="hidden" name="returnUrl" value="' . $__funcvar['listurl'] . '">
      <input type="hidden" name="id" value="' . (isset ($data['id'])?$data['id']:'') . '">
    
        <div class="formHolder" >
          <div class="formTitle textB">' . vt('Template') . '</div>
          <div class="formContent padded-10 pl-5">
          
          <div class="row">
            <div class="col-5">
            <div class="form-group row">
                <div class="formlinks"><label for="naam">' . vt($object->data['fields']['naam']['description']) . '</label> </div>
                <div class="formrechts">
                  ' . $editObject->form->makeInput('naam', $object) . '
                  ' . $object->getError('naam') . '
                </div>
              </div>
            
              <div class="form-group row">
                <div class="formlinks"><label for="categorie">' . vt($object->data['fields']['categorie']['description']) . '</label> </div>
                <div class="formrechts">
                  ' . $editObject->form->makeInput('categorie', $object) . '
                  ' . $object->getError('categorie') . '
                </div>
              </div>
              
              ' . $multiLanguageField . '
            </div>
           
           
           <div class="col-4 ">
          <div class="formblock">
            ' . vt('Test pdf alleen via crm sjabloon') . '
          </div>
        
        <div class="formblock">
          <div class="formlinks"><label for="bodyHtml">' . vt('Portefeuille') . '</label></div>
          <div class="formrechts">
            <input name="portefeuille" autocomplete="new-password"  id="portefeuille"  />
          </div>
        </div>
        </div>
            
            
            <div class="col-2">
              <div class=" btn-group-text-left " style="margin-left:10px">
                <div id="topdf" class="btn btn-default" style="width: 140px;" >&nbsp;&nbsp;<i style="color:red" class="fa fa-file-pdf-o fa-fw" aria-hidden="true"></i> ' . vt('Pdf') . '</div>
              </div>
            </div>
            
            </div>
          </div>
        </div>
  ';


  if ($categorie) {
    echo $customTemplate->buildForm ($categorie);
  }
  
  ?>
  
  <script>
    
    $('#langGroup').hide();
    if($('input[name="meertalig"]').is(':checked'))
    {
      $('#langGroup').show();
    }
    
    $(function () {


  $('#topdf').on('click', function (event) {
    event.preventDefault();
    $curAction = $('form[name=editForm]').attr('action');


    $('form[name=editForm]').attr('action', 'customTemplateOutput.php');
    $('form[name=editForm]').attr('target', '_blank');
    $('form[name=editForm]').submit();


    $('form[name=editForm]').attr('action', $curAction);
    $('form[name=editForm]').attr('target', '');

  });



      $('input[name="meertalig"]').change(function () {
        if($('input[name="meertalig"]').is(':checked'))
        {
          $('#langGroup').show();
        } else {
          $('#langGroup').hide();
        }
      })
  
    })
    // bind change event to select
    $('#categorie').on('change', function () {
      var url = "<?=$__funcvar['location']?>";
      url += '?action=<?=$action;?>';
      url += '&id=<?=$object->get('id');?>';
      url += '&categorie=' + $('#categorie').val();
      url += '&naam=' + $('#naam').val();
      console.log(url);
      if (url)
      { // require a URL
        window.location = url; // redirect
      }
      return false;
    });
    
    $(document).on('change', '#templateSelect', function () {
      $.ajax({
        url: "lookups/ajaxLookup.php",
        type: "GET",
        dataType: "json",
        data: {
          fromClass: "AE_CustomTemplate",
          type: "getTemplate",
          templateId: $(this).val()
        },
        success: function (data, textStatus, jqXHR) {
          $.each(data, function (field, fieldValue) {
            $('input[name=' + field + ']').val(fieldValue);
          });
        }
      });
    });
    
    // $('.textEditor').each(function (e) {
    //   CKEDITOR.replace(this.id,
    //     {
    //       enterMode: CKEDITOR.ENTER_BR,
    //       allowedContent: true,
    //       extraPlugins: 'pastebase64'
    //     });
    // });
    //
  </script>
  
  <?php
  
  echo template($__appvar["templateRefreshFooter"], $editcontent);
}


if ($result = $editObject->result)
{
  header("Location: " . $__funcvar['listurl']);
}
else
{
  echo $_error = $editObject->_error;
}