/* This beautiful af code: courtesy of E "N" W */
$(document).ready(function() {
  /* On study row data click, we want to expand it to see extra info.
  */
  $('.data-row').click(function() {
    var isRevealed = $(this).hasClass('current');

    // Remove current opened states on all!
    $('.data-row').removeClass('current');
    $('.reveal').removeClass('reveal');

    // Toggle pressed row
    if (!isRevealed) {
      $(this).addClass('current');
      $(this).next().find('.secret').addClass('reveal');
    }
  });

  /* Closes expanded row if you press outside of it.
   * Side effect feature: right clicking works too ha ha.
   */
  $(document).mouseup(function(e) {
    if (!$('.data-row').is(e.target) &&
        $('.data-row').has(e.target).length === 0) {
      $('.data-row').next().find('.reveal').removeClass('reveal');
      $('.data-row').removeClass('current');
    }
  });
});
$(document).ready(function() {
  $(".button-collapse").sideNav(); // Initialize sideNav (materialize).
  $('.joinStudyGroupBtn').click(function() {
    var groupId = $(this).attr('id');
    $.post('php/joinStudyGroup.php', {id: groupId}, function(data) {
      $('#' + groupId).closest('tr').hide();
      $('#' + groupId).closest('tr').prev('tr').hide();
      window.location.replace("chat.php?action=join");
    }).fail(function() {
      $('.alert.hide.alert-danger').removeClass('hide');
    });
  });
});
