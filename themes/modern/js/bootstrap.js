/* bootstrap */
window.addEvent('load', function() {
  $$('button[data-toggle=collapse]').each(function(btn) {
    var target = $(btn.get('data-target'));
    btn.addEvent('click', function() {
      target.toggle();
    })
  });
})


