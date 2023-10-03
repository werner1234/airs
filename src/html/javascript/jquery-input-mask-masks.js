//$('.maskValuta2digits').inputmask('mask', {
//  allowMinus : true,
//  insertMode: false,
//  mask: '9{1,*}.99{1,1}',
//  greedy: false,
//  'autoGroup': true,
//  'digitsOptional': false,
//  'placeholder': '0',
//  rightAlign: true,
//});
Inputmask.extendAliases({
    'maskValuta2digits': {
      alias: "numeric",
      allowPlus: false,
      allowMinus: true,
			placeholder: '0',
      digits: 2,
			digitsOptional: false,
			greedy: true,
			autoGroup: true,
      positionCaretOnTab: false
      
    }
});
$('.maskValuta2digits').inputmask("maskValuta2digits");


//$('.maskValuta2digitsPositive').inputmask('mask', {
//  insertMode: false,
//  mask: '9{1,*}.99{1,1}',
//  greedy: false,
//  'autoGroup': true,
//  'allowMinus' : false,
//  'digitsOptional': false,
//  'placeholder': '0',
//  rightAlign: true
//});
Inputmask.extendAliases({
    'maskValuta2digitsPositive': {
      alias: "numeric",
      allowPlus: false,
      allowMinus: false,
			placeholder: '0',
      digits: 2,
			digitsOptional: false,
			greedy: true,
			autoGroup: true,
      positionCaretOnTab: false
    }
});

 $('.maskValuta2digitsPositive').inputmask("maskValuta2digitsPositive");

Inputmask.extendAliases({
  'maskValuta2digitsNegative': {
    alias: "numeric",
    allowPlus: false,
    allowMinus: true,
    placeholder: '0',
    digits: 2,
    digitsOptional: false,
    greedy: true,
    autoGroup: true,
    positionCaretOnTab: false
  }
});

$('.maskValuta2digitsNegative').inputmask("maskValuta2digitsNegative");


Inputmask.extendAliases({
    'maskValuta4digitsPositive': {
      alias: "numeric",
      allowPlus: false,
      allowMinus: false,
			placeholder: '0',
      digits: 4,
			digitsOptional: false,
			greedy: true,
			autoGroup: true,
      positionCaretOnTab: false
    }
});

 $('.maskValuta4digitsPositive').inputmask("maskValuta4digitsPositive");


//$('.maskAmountPN').inputmask('mask', {
//  insertMode: false,
//  mask: '9{1,*}',
//  greedy: false,
//  'autoGroup': true,
//  'allowMinus' : true,
//  'digitsOptional': false,
//  'placeholder': '0',
//  rightAlign: true
//});
Inputmask.extendAliases({
    'maskAmountPN': {
      alias: "numeric",
      allowPlus: false,
      allowMinus: true,
			placeholder: '0',
			digitsOptional: true,
			greedy: true,
			autoGroup: true,
      positionCaretOnTab: false
    }
});
$('.maskAmountPN').inputmask("maskAmountPN");




//$('.maskNumeric').inputmask('mask', {
//  insertMode: false,
//  mask: '9{1,*}',
//  greedy: false,
//  'autoGroup': true,
//  'allowMinus' : false,
//  'digitsOptional': false,
//  'placeholder': '0',
//  rightAlign: true
//});

Inputmask.extendAliases({
    'maskNumeric': {
      alias: "numeric",
      allowPlus: false,
      allowMinus: false,
			placeholder: '0',
      //digits: 6,
			digitsOptional: true,
			greedy: false,
			autoGroup: false,
      positionCaretOnTab: false
    }
});
$('.maskNumeric').inputmask("maskNumeric");

//$('.maskNumeric6Digits').inputmask('mask', {
//  insertMode: false,
//  mask: '9{1,*}.99{1,5}',
//  greedy: false,
//  'autoGroup': true,
//  'digitsOptional': false,
//  'placeholder': '0',
//  rightAlign: true
//});


Inputmask.extendAliases({
  'maskNumeric10Digits': {
    alias: "numeric",
    allowPlus: false,
    allowMinus: false,
    placeholder: '0',
    digits: 10,
    digitsOptional: true,
    greedy: true,
    autoGroup: false,
    positionCaretOnTab: false
  }
});
$('.maskNumeric10Digits').inputmask("maskNumeric10Digits");


Inputmask.extendAliases({
    'maskNumeric6Digits': {
        alias: "numeric",
        allowPlus: false,
        allowMinus: false,
        placeholder: '0',
        digits: 6,
        digitsOptional: true,
        greedy: false,
        autoGroup: false,
      positionCaretOnTab: false
    }
});
$('.maskNumeric6Digits').inputmask("maskNumeric6Digits");

Inputmask.extendAliases({
    'maskNumeric6DigitsAllowNegative': {
        alias: "numeric",
        allowPlus: false,
        allowMinus: true,
        placeholder: '0',
        digits: 6,
        digitsOptional: true,
        greedy: false,
        autoGroup: false,
        rightAlign: true,
      positionCaretOnTab: false
    }
});
$('.maskNumeric6DigitsAllowNegative').inputmask("maskNumeric6DigitsAllowNegative");

//$('.maskValuta').inputmask('mask', {
//  insertMode: false,
//  mask: '9{1,*}.99{1,5}',
//  greedy: false,
//  'autoGroup': true,
//  'allowMinus' : false,
//  'digitsOptional': false,
//  'placeholder': '0',
//  rightAlign: true
//});
Inputmask.extendAliases({
    'maskValuta': {
      alias: "numeric",
      allowPlus: false,
      allowMinus: false,
			placeholder: '0',
      digits: 6,
			digitsOptional: true,
			greedy: false,
			autoGroup: false,
      positionCaretOnTab: false
    }
});
$('.maskValuta').inputmask("maskValuta");

//$('.maskValutaKoers').inputmask('mask', {
//  insertMode: false,
//  mask: '9{1,*}.99{1,7}',
//  greedy: false,
//  'autoGroup': true,
//  'allowMinus' : false,
//  'digitsOptional': false,
//  'placeholder': '0',
//  rightAlign: true
//});
Inputmask.extendAliases({
    'maskValutaKoers': {
      alias: "numeric",
      allowPlus: false,
      allowMinus: false,
			placeholder: '0',
      digits: 8,
			digitsOptional: true,
			greedy: false,
			autoGroup: false,
      greedy: false,
      positionCaretOnTab: false
    }
});
$('.maskValutaKoers').inputmask("maskValutaKoers");


//$('.maskFondsKoers').inputmask('mask', {
//  insertMode: false,
//  mask: '9{0,*}[.99{0,7}]',
//  greedy: false,
//  'autoGroup': true,
//  'allowMinus' : false,
//  'digitsOptional': false,
////  'placeholder': '0',
//  rightAlign: true
//});
Inputmask.extendAliases({
    'maskFondsKoers': {
      alias: "numeric",
      allowPlus: false,
      allowMinus: false,
			placeholder: '0',
      // digits: 8,
			// digitsOptional: true,
			// greedy: false,
			// autoGroup: false,
      positionCaretOnTab: false
    }
});
$('.maskFondsKoers').inputmask("maskFondsKoers");



//$('.maskRekeningMutatieAantal').inputmask('mask', {
//  insertMode: false,
//  mask: '9{0,*}[.99{0,5}]',
//  greedy: false,
//  autoGroup: true,
//  digitsOptional: false,
//  rightAlign: true
//});
Inputmask.extendAliases({
    'maskRekeningMutatieAantal': {
      alias: "numeric",
      allowPlus: false,
      allowMinus: false,
			placeholder: '0',
      digits: 6,
			digitsOptional: true,
			greedy: false,
			autoGroup: false,
      positionCaretOnTab: false
    }
});
$('.maskRekeningMutatieAantal').inputmask("maskRekeningMutatieAantal");



//$('.maskRekeningMutatieAantalEdit').inputmask('mask', {
//  insertMode: false,
//  mask: '9{1,*}.99{1,5}',
//  greedy: false,
//  'autoGroup': true,
//  'allowMinus' : true,
//  'digitsOptional': false,
//  'placeholder': '0',
//  rightAlign: true
//});

Inputmask.extendAliases({
    'maskRekeningMutatieAantalEdit': {
      alias: "numeric",
      allowPlus: false,
      allowMinus: true,
			placeholder: '0',
      digits: 6,
			digitsOptional: true,
			greedy: false,
			autoGroup: false,
      positionCaretOnTab: false
    }
});
$('.maskRekeningMutatieAantalEdit').inputmask("maskRekeningMutatieAantalEdit");