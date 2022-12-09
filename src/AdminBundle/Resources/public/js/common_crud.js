(function ($) {

  $(document).on('click', '.btn-delete', function () {
    var href = this.dataset.href;

    if (! href || ! confirm("Are you sure you want to delete?")) {
      return;
    }
    this.setAttribute('disabled', true);

    window.location = href;
  });

})(jQuery);