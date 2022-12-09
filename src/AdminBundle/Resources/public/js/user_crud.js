(function ($) {
  var $document = $(document);

  $document.on('click', '.user-change-status', function () {
    var $button = $(this);
    $button.attr('disabled', true);

    $.ajax({
      type: 'post',
      url: $button.data('href')
    })
      .always(function () {
        $button.attr('disabled', false)
      })
      .done(function (data) {
        $button.removeClass('btn-success btn-default');

        $button.find('span').hide();

        if (data.enabled) {
          $button.find('.user-enabled').show();
          $button.addClass('btn-success');
        } else {
          $button.find('.user-disabled').show();
          $button.addClass('btn-default');
        }
      });
  });

  $document.on('click', '.user-password-resend', function () {
    if (! confirm("Are you sure you want to generate and resend user's password?")) {
      return;
    }
    var button = this;
    button.setAttribute('disabled', true);

    $.ajax({
      type: 'post',
      url: button.dataset.href
    })
      .always(function () {
        button.removeAttribute('disabled');
      })
      .done(function () {
        alert('Email with new password was sent');
      });
  });

})(jQuery);