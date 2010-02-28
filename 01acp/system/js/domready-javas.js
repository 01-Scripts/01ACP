// Ajax-Meldungsboxen ausblenden
var ajaxboxes = $$('div.ajax_box');
ajaxboxes.fade('hide');
ajaxboxes.setStyle('display','block');

// Elemente der Klasse moo_hide ausblenden
var moo_hiddenelements = $$('div.moo_hide');
moo_hiddenelements.fade('hide');
moo_hiddenelements.setStyle('display','block');

// Elemente der Klasse moo_inlinehide ausblenden
var moo_hiddeninlineelements = $$('div.moo_inlinehide');
moo_hiddeninlineelements.fade('hide');
moo_hiddeninlineelements.setStyle('display','inline');